<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Subject;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    private const MAX_CORE_SUBJECTS = 7;

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

        $students = $query
            ->with(['subjects' => function ($q) {
                $q->select('subjects.id', 'subjects.name', 'subjects.code');
            }])
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();
        // derive classes only from scoped students
        $classes = (clone $query)->select('class_name')->distinct()->orderBy('class_name')->pluck('class_name');
        $allClasses = \App\Models\SchoolClass::query()->orderBy('name')->pluck('name');

        $classSubjects = [];
        $classNames = collect($students->items())->pluck('class_name')->filter()->unique();
        if ($classNames->isNotEmpty()) {
            $classSubjects = SchoolClass::query()
                ->when(optional($request->user())->school_number, function ($q, $schoolNumber) {
                    $q->where('school_number', $schoolNumber);
                })
                ->whereIn('name', $classNames)
                ->with(['subjects' => function ($q) {
                    $q->orderBy('name')->select('subjects.id', 'subjects.name', 'subjects.code');
                }])
                ->get()
                ->mapWithKeys(function ($cls) {
                    $subjects = $cls->subjects->values();
                    $mapped = $subjects->map(function ($sub, $index) {
                        return [
                            'id' => $sub->id,
                            'name' => $sub->name,
                            'code' => $sub->code,
                            'is_core' => $index < self::MAX_CORE_SUBJECTS,
                        ];
                    });

                    return [
                        $cls->name => $mapped->all(),
                    ];
                })
                ->toArray();
        }

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
            'class_subjects' => $classSubjects,
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

        // Generate per-school reg no: SCHOOL-XXXX
        $regNo = $this->nextRegNo($schoolNumber);

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
                // If no reg_no provided, auto-generate using current user's school number
                $data = $data ?: [];
                $data['reg_no'] = $this->nextRegNo($schoolNumber);
            }
            // Normalize reg_no and validate optional exam_no prefix if present
            if (!empty($data['exam_no']) && $schoolNumber) {
                $prefix = $schoolNumber.'-';
                if (!str_starts_with($data['exam_no'], $prefix)) {
                    $data['exam_no'] = $prefix . ltrim((string)$data['exam_no'], 'S0123456789-');
                }
            }
            if ($schoolNumber) {
                $regPrefix = $schoolNumber.'-';
                // If reg_no lacks school prefix, coerce it
                if (!str_starts_with((string)$data['reg_no'], $regPrefix)) {
                    $suffix = preg_replace('/[^0-9]/', '', (string)$data['reg_no']);
                    $suffix = str_pad(substr($suffix ?? '', -4), 4, '0', STR_PAD_LEFT);
                    $data['reg_no'] = $regPrefix.$suffix;
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
        $schoolNumber = optional($request->user())->school_number;
        [$classInfo, $classSubjects] = $this->resolveClassContext($student->class_name, $schoolNumber);
        $optionalSubjects = $student->subjects()->orderBy('name')->get(['subjects.id', 'subjects.name', 'subjects.code', 'student_subject.is_optional']);
        $payload = $student->toArray();
        $payload['class_subjects'] = $classSubjects;
        $payload['class_info'] = $classInfo;
        $payload['optional_subjects'] = $optionalSubjects;
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

    public function subjects(Request $request, Student $student)
    {
        $schoolNumber = optional($request->user())->school_number;
        $this->ensureStudentAccess($schoolNumber, $student);
        return response()->json($this->buildStudentSubjectPayload($student, $schoolNumber));
    }

    public function attachSubject(Request $request, Student $student)
    {
        $schoolNumber = optional($request->user())->school_number;
        $this->ensureStudentAccess($schoolNumber, $student);

        $data = $request->validate([
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
        ]);

        $subject = Subject::findOrFail($data['subject_id']);
        if ($schoolNumber && $subject->school_number && $subject->school_number !== $schoolNumber) {
            abort(403, 'Subject does not belong to your school');
        }

        [$classInfo, $classSubjects] = $this->resolveClassContext($student->class_name, $schoolNumber);
        $coreIds = collect($classSubjects)->pluck('id')->filter()->all();
        if (in_array($subject->id, $coreIds, true)) {
            return response()->json(['message' => 'Subject already assigned via class'], 422);
        }

        if ($student->subjects()->where('subject_id', $subject->id)->exists()) {
            return response()->json(['message' => 'Subject already assigned'], 422);
        }

        $student->subjects()->attach($subject->id, ['is_optional' => true]);
        $student->refresh();

        return response()->json($this->buildStudentSubjectPayload($student, $schoolNumber));
    }

    public function detachSubject(Request $request, Student $student, Subject $subject)
    {
        $schoolNumber = optional($request->user())->school_number;
        $this->ensureStudentAccess($schoolNumber, $student);

        if ($schoolNumber && $subject->school_number && $subject->school_number !== $schoolNumber) {
            abort(403, 'Subject does not belong to your school');
        }

        if (!$student->subjects()->where('subject_id', $subject->id)->exists()) {
            return response()->json(['message' => 'Subject not assigned'], 404);
        }

        $student->subjects()->detach($subject->id);
        $student->refresh();

        return response()->json($this->buildStudentSubjectPayload($student, $schoolNumber));
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

    private function nextRegNo(?string $schoolNumber): string
    {
        $prefix = ($schoolNumber ?: 'S0000') . '-';
        // Find last existing reg_no for this school and increment
        $last = Student::query()
            ->where('reg_no', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->value('reg_no');
        $next = 1;
        if ($last) {
            if (preg_match('/^(?:'.preg_quote($prefix,'/').')([0-9]{1,})$/', $last, $m)) {
                $next = ((int)$m[1]) + 1;
            } else {
                // Fallback: count existing for this school
                $count = Student::where('reg_no', 'like', $prefix.'%')->count();
                $next = $count + 1;
            }
        }
        return $prefix . str_pad((string)$next, 4, '0', STR_PAD_LEFT);
    }

    private function ensureStudentAccess(?string $schoolNumber, Student $student): void
    {
        if (!$schoolNumber) {
            return;
        }

        $examNo = (string) $student->exam_no;
        if ($examNo === '' || !str_starts_with($examNo, $schoolNumber.'-')) {
            abort(403, 'Unauthorized student access');
        }
    }

    private function resolveClassContext(?string $className, ?string $schoolNumber): array
    {
        $classSubjects = collect();
        $classInfo = null;

        if (!$className) {
            return [$classInfo, $classSubjects];
        }

        $needle = trim((string) $className);
        $base = SchoolClass::query()
            ->when($schoolNumber, function ($w) use ($schoolNumber) {
                $w->whereExists(function ($sq) use ($schoolNumber) {
                    $sq->select(\DB::raw(1))
                        ->from('students as s')
                        ->whereColumn('s.class_name', 'classes.name')
                        ->where('s.exam_no', 'like', $schoolNumber.'-%');
                });
            });

        $cls = $base->whereRaw('LOWER(name) = ?', [strtolower($needle)])->first();
        if (!$cls) {
            $cls = (clone $base)->where('name', 'like', $needle)->first();
        }

        if ($cls) {
            $classInfo = ['id' => $cls->id, 'name' => $cls->name];
            $classSubjects = $cls->subjects()->orderBy('name')->get(['subjects.id', 'subjects.name', 'subjects.code']);
        }

        return [$classInfo, $classSubjects];
    }

    private function buildStudentSubjectPayload(Student $student, ?string $schoolNumber): array
    {
        [$classInfo, $classSubjects] = $this->resolveClassContext($student->class_name, $schoolNumber);

        $student->load(['subjects' => function ($q) {
            $q->orderBy('name');
        }]);

        $optional = $student->subjects->map(function ($sub) {
            return [
                'id' => $sub->id,
                'name' => $sub->name,
                'code' => $sub->code,
                'is_optional' => (bool) ($sub->pivot->is_optional ?? true),
            ];
        })->values();

        $coreCollection = collect($classSubjects);
        $core = $coreCollection->filter(fn ($sub) => (bool)($sub['is_core'] ?? false))->values();
        $classOptional = $coreCollection->filter(fn ($sub) => !($sub['is_core'] ?? false))->values();

        $coreIds = $core->pluck('id')->filter()->all();
        $assignedIds = $optional->pluck('id')->filter()->all();

        $available = Subject::query()
            ->when($schoolNumber, function ($q) use ($schoolNumber) {
                $q->where(function ($inner) use ($schoolNumber) {
                    $inner->whereNull('school_number')->orWhere('school_number', $schoolNumber);
                });
            })
            ->when($coreIds, function ($q) use ($coreIds) {
                $q->whereNotIn('id', $coreIds);
            })
            ->when($assignedIds, function ($q) use ($assignedIds) {
                $q->whereNotIn('id', $assignedIds);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'code'])
            ->map(function ($sub) {
                return [
                    'id' => $sub->id,
                    'name' => $sub->name,
                    'code' => $sub->code,
                ];
            })
            ->values();

        return [
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'reg_no' => $student->reg_no,
                'class_name' => $student->class_name,
            ],
            'class_info' => $classInfo,
            'core' => $core->map(function ($sub) {
                return [
                    'id' => $sub['id'],
                    'name' => $sub['name'],
                    'code' => $sub['code'],
                ];
            })->values()->all(),
            'class_optional' => $classOptional->map(function ($sub) {
                return [
                    'id' => $sub['id'],
                    'name' => $sub['name'],
                    'code' => $sub['code'],
                ];
            })->values()->all(),
            'optional' => $optional->all(),
            'available' => $available->all(),
        ];
    }
}
