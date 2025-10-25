<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $sex = $request->string('sex')->toString();
        $class = $request->string('class')->toString();
        $schoolNumber = optional($request->user())->school_number;

        $query = Student::query()
            ->when($schoolNumber, function($w) use ($schoolNumber) {
                $w->where('exam_no', 'like', $schoolNumber.'-%');
            });
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('reg_no', 'like', "%$q%")
                  ->orWhere('name', 'like', "%$q%");
            });
        }
        if ($sex !== '') {
            $query->where('sex', $sex);
        }
        if ($class !== '') {
            $query->where('class_name', $class);
        }

        $students = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();
        // derive classes only from scoped students
        $classes = (clone $query)->select('class_name')->distinct()->orderBy('class_name')->pluck('class_name');
        $allClasses = \App\Models\SchoolClass::query()->orderBy('name')->pluck('name');

        return Inertia::render('Students/Index', [
            'students' => $students,
            'classes' => $classes,
            'all_classes' => $allClasses,
            'filters' => [
                'q' => $q,
                'sex' => $sex,
                'class' => $class,
            ],
            'school_number' => optional($request->user())->school_number,
        ]);
    }

    public function store(Request $request)
    {
        $schoolNumber = optional($request->user())->school_number;
        $request->validate([
            'name' => ['required','string','max:255'],
            'sex' => ['required','in:M,F'],
            'class_name' => ['required','string','max:100'],
            'dob' => ['required','date'],
            'academic_year' => ['required','integer','min:2000','max:2100'],
            'guardian_name' => ['required','string','max:255'],
            'guardian_phone' => ['required','string','max:30'],
            'guardian_relation' => ['required','string','max:100'],
            'exam_no' => [
                'required',
                'string',
                'max:50',
                'unique:students,exam_no',
                function($attr,$value,$fail) use ($schoolNumber) {
                    if ($value === null || $value === '') return;
                    if (!$schoolNumber) return; // skip if no school number on user
                    $prefix = $schoolNumber.'-';
                    if (!str_starts_with($value, $prefix)) {
                        $fail('Exam number must start with '.$prefix);
                    }
                },
            ],
            'photo' => ['nullable','file','image','max:2048'],
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('students','public');
        }

        $nextId = (int) (Student::max('id') ?? 0) + 1;
        $regNo = 'RSMS-'.str_pad((string)$nextId, 4, '0', STR_PAD_LEFT);

        Student::create([
            'reg_no' => $regNo,
            'name' => $request->string('name'),
            'sex' => $request->string('sex'),
            'class_name' => $request->string('class_name'),
            'dob' => $request->date('dob'),
            'photo_path' => $photoPath,
            'guardian_name' => $request->string('guardian_name'),
            'guardian_phone' => $request->string('guardian_phone'),
            'guardian_relation' => $request->string('guardian_relation'),
            'exam_no' => $request->string('exam_no'),
            'academic_year' => $request->integer('academic_year'),
        ]);

        return redirect()->route('students.index')->with('success', 'Student added successfully.');
    }

    public function guardianStats(Request $request)
    {
        $phone = trim((string) $request->query('phone', ''));
        if ($phone === '') {
            return response()->json(['count' => 0, 'students' => []]);
        }
        $students = Student::query()
            ->where('guardian_phone', $phone)
            ->select(['id','name','class_name','reg_no'])
            ->orderBy('name')
            ->get();
        return response()->json([
            'count' => $students->count(),
            'students' => $students,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $q = $request->string('q')->toString();
        $sex = $request->string('sex')->toString();
        $class = $request->string('class')->toString();
        $schoolNumber = optional($request->user())->school_number;

        $query = Student::query()
            ->when($schoolNumber, function($w) use ($schoolNumber) {
                $w->where('exam_no', 'like', $schoolNumber.'-%');
            });
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('reg_no', 'like', "%$q%")
                  ->orWhere('name', 'like', "%$q%");
            });
        }
        if ($sex !== '') {
            $query->where('sex', $sex);
        }
        if ($class !== '') {
            $query->where('class_name', $class);
        }

        $filename = 'students_export_'.now()->format('Ymd_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['reg_no', 'name', 'sex', 'class_name']);
            $query->chunk(500, function ($chunk) use ($out) {
                foreach ($chunk as $s) {
                    fputcsv($out, [$s->reg_no, $s->name, $s->sex, $s->class_name]);
                }
            });
            fclose($out);
        }, $filename, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        $handle = fopen($path, 'r');
        if ($handle === false) {
            return Redirect::back()->with('error', 'Could not read the file.');
        }

        $header = fgetcsv($handle);
        $mapped = array_map('strtolower', $header ?? []);
        // Expected base headers: reg_no,name,sex,class_name
        // Optional headers: guardian_name,guardian_phone,guardian_relation,academic_year,dob,exam_no
        $schoolNumber = optional($request->user())->school_number;
        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($mapped, $row);
            if (!$data || empty($data['reg_no'])) {
                continue;
            }
            // Validate optional exam_no prefix if present
            if (!empty($data['exam_no']) && $schoolNumber) {
                $prefix = $schoolNumber.'-';
                if (!str_starts_with($data['exam_no'], $prefix)) {
                    $data['exam_no'] = $prefix . ltrim((string)$data['exam_no'], 'S0123456789-');
                }
            }

            Student::updateOrCreate(
                ['reg_no' => $data['reg_no']],
                [
                    'name' => $data['name'] ?? '',
                    'sex' => $data['sex'] ?? 'M',
                    'class_name' => $data['class_name'] ?? '',
                    'guardian_name' => $data['guardian_name'] ?? null,
                    'guardian_phone' => $data['guardian_phone'] ?? null,
                    'guardian_relation' => $data['guardian_relation'] ?? null,
                    'academic_year' => isset($data['academic_year']) ? (int)$data['academic_year'] : null,
                    'dob' => $data['dob'] ?? null,
                    'exam_no' => $data['exam_no'] ?? null,
                ]
            );
        }
        fclose($handle);

        return Redirect::back()->with('success', 'Students imported successfully.');
    }

    public function template()
    {
        $filename = 'students_template.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        $cols = ['reg_no','name','sex','class_name','guardian_name','guardian_phone','guardian_relation','academic_year','dob','exam_no'];
        return response()->streamDownload(function () use ($cols) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $cols);
            fclose($out);
        }, $filename, $headers);
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if ($q === '') return response()->json([]);
        $schoolNumber = optional($request->user())->school_number;
        $items = Student::query()
            ->when($schoolNumber, function($w) use ($schoolNumber) {
                $w->where('exam_no', 'like', $schoolNumber.'-%');
            })
            ->when($q, function($w) use ($q) {
                $w->where('name','like',"%$q%")
                  ->orWhere('reg_no','like',"%$q%");
            })
            ->orderBy('name')
            ->limit(8)
            ->get(['id','reg_no','name','sex','class_name']);
        return response()->json($items);
    }

    public function showJson(Request $request)
    {
        $id = (int) $request->query('id', 0);
        $reg = (string) $request->query('reg_no', '');
        $schoolNumber = optional($request->user())->school_number;
        $student = Student::query()
            ->when($schoolNumber, function($w) use ($schoolNumber) {
                $w->where('exam_no', 'like', $schoolNumber.'-%');
            })
            ->when($id, fn($q)=>$q->where('id',$id))
            ->when(!$id && $reg !== '', fn($q)=>$q->where('reg_no',$reg))
            ->firstOrFail();
        // fetch subjects assigned to the student's class (robust match by class name)
        $classSubjects = [];
        $classInfo = null;
        if (!empty($student->class_name)) {
            $needle = trim((string)$student->class_name);
            $schoolNumber = optional($request->user())->school_number;
            $base = \App\Models\SchoolClass::query()
                ->when($schoolNumber, function($w) use ($schoolNumber) {
                    $w->whereExists(function($sq) use ($schoolNumber) {
                        $sq->select(\DB::raw(1))
                           ->from('students as s')
                           ->whereColumn('s.class_name','classes.name')
                           ->where('s.exam_no','like',$schoolNumber.'-%');
                    });
                });
            $cls = $base->whereRaw('LOWER(name) = ?', [strtolower($needle)])->first();
            if (!$cls) {
                $cls = $base->where('name', 'like', $needle)->first();
            }
            if ($cls) {
                $classInfo = ['id' => $cls->id, 'name' => $cls->name];
                $classSubjects = $cls->subjects()->orderBy('name')->get(['id','name','code']);
            }
        }
        $payload = $student->toArray();
        $payload['class_subjects'] = $classSubjects;
        $payload['class_info'] = $classInfo;
        return response()->json($payload);
    }

    public function history(Request $request)
    {
        // Placeholder: return empty history array for now
        $id = (int) $request->query('id', 0);
        return response()->json([
            'items' => [],
            'student_id' => $id,
        ]);
    }

    public function behaviours(Request $request, int $id)
    {
        $items = \DB::table('student_behaviours')
            ->where('student_id', $id)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();
        return response()->json(['items' => $items]);
    }

    public function addBehaviour(Request $request, int $id)
    {
        $data = $request->validate([
            'type' => ['required','string','max:100'],
            'severity' => ['nullable','string','max:50'],
            'date' => ['required','date'],
            'description' => ['nullable','string'],
        ]);
        $data['student_id'] = $id;
        \DB::table('student_behaviours')->insert([
            'student_id' => $id,
            'type' => $data['type'],
            'severity' => $data['severity'] ?? null,
            'date' => $data['date'],
            'description' => $data['description'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return response()->json(['ok' => true]);
    }

    public function updateStatus(Request $request, int $id)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['active','suspended','transferred','alumni','expelled'])],
            'status_reason' => ['nullable','string','max:500'],
            'status_date' => ['required','date'],
        ]);
        $student = Student::findOrFail($id);
        $student->update($validated);
        return response()->json(['ok' => true]);
    }

    public function update(Request $request, int $id)
    {
        $student = Student::findOrFail($id);
        $schoolNumber = optional($request->user())->school_number;
        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'sex' => ['required','in:M,F'],
            'class_name' => ['required','string','max:100'],
            'dob' => ['required','date'],
            'academic_year' => ['required','integer','min:2000','max:2100'],
            'guardian_name' => ['required','string','max:255'],
            'guardian_phone' => ['required','string','max:30'],
            'guardian_relation' => ['required','string','max:100'],
            'exam_no' => [
                'required','string','max:50',
                Rule::unique('students','exam_no')->ignore($student->id),
                function($attr,$value,$fail) use ($schoolNumber) {
                    if ($value === null || $value === '') return;
                    if (!$schoolNumber) return;
                    $prefix = $schoolNumber.'-';
                    if (!str_starts_with($value, $prefix)) {
                        $fail('Exam number must start with '.$prefix);
                    }
                },
            ],
            'photo' => ['nullable','file','image','max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('students','public');
        }

        $student->update($validated);
        return redirect()->back()->with('success', 'Student updated successfully.');
    }

    public function destroy(Request $request, int $id)
    {
        $student = Student::findOrFail($id);
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }
}
