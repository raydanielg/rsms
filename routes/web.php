<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\SubjectsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\InformationController;
use App\Http\Controllers\ResultsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TeachersController;
use App\Http\Controllers\TimetablesController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Emas\EmasAuthController;
use App\Http\Controllers\Emas\EmasDashboardController;
use App\Models\Exam;

Route::get('/', function () {
    return Inertia::render('Home');
});

// Public: About Us page
Route::inertia('/about', 'About')->name('about');

// Public: Contact Us page
Route::inertia('/contact', 'Contact')->name('contact');

// Public: Track student page
Route::inertia('/track', 'Results/Track')->name('public.track');

// Public API: Student search (AJAX suggestions)
Route::get('/api/public/students/search', function () {
    $q = trim((string)request()->query('query', ''));
    if ($q === '') return response()->json([]);
    $rows = DB::table('students as s')
        ->leftJoin('classes as c', 'c.name', '=', 's.class_name')
        ->leftJoin('users as u', 'u.school_number', '=', 'c.school_number')
        ->where(function($w) use ($q) {
            $w->where('s.name', 'like', "%$q%")
              ->orWhere('s.reg_no', 'like', "%$q%");
        })
        ->orderBy('s.name')
        ->limit(10)
        ->get(['s.id','s.name','s.reg_no','s.class_name','s.sex','u.school_name','u.school_number']);
    return response()->json($rows);
})->name('api.public.students.search');

// Public API: Student details
Route::get('/api/public/students/{id}', function (int $id) {
    $row = DB::table('students as s')
        ->leftJoin('classes as c', 'c.name', '=', 's.class_name')
        ->leftJoin('users as u', 'u.school_number', '=', 'c.school_number')
        ->where('s.id', $id)
        ->first(['s.id','s.name','s.reg_no','s.class_name','s.sex','u.school_name','u.school_number']);
    if (!$row) return response()->json([], 404);
    return response()->json($row);
})->name('api.public.students.show');

// Public API: Contact form submissions (best-effort store)
Route::post('/api/public/contact', function () {
    $data = request()->validate([
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:50',
        'message' => 'required|string|max:5000',
    ]);
    if (\Illuminate\Support\Facades\Schema::hasTable('feedback')) {
        DB::table('feedback')->insert([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'message' => $data['message'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    return response()->json(['ok' => true]);
})->name('api.public.contact');

// Public API: Student results (by exams)
Route::get('/api/public/students/{id}/results', function (int $id) {
    $rows = DB::table('exam_marks as m')
        ->join('exams as e', 'e.id', '=', 'm.exam_id')
        ->join('subjects as sub', 'sub.id', '=', 'm.subject_id')
        ->where('m.student_id', $id)
        ->orderByDesc('e.year')->orderByDesc('e.term')->orderByDesc('e.id')
        ->get(['e.id as exam_id','e.name as exam_name','e.type','e.year','e.term','sub.code','sub.name as subject_name','m.marks']);

    $byExam = [];
    foreach ($rows as $r) {
        $exid = $r->exam_id;
        if (!isset($byExam[$exid])) {
            $byExam[$exid] = [
                'id' => $r->exam_id,
                'name' => $r->exam_name,
                'type' => $r->type,
                'year' => $r->year,
                'term' => $r->term,
                'subjects' => [],
                'total' => 0,
                'count' => 0,
            ];
        }
        $m = (int)($r->marks ?? 0);
        $grade = $m >= 75 ? 'A' : ($m >= 65 ? 'B' : ($m >= 55 ? 'C' : ($m >= 40 ? 'D' : 'F')));
        $byExam[$exid]['subjects'][] = [ 'code' => $r->code ?: $r->subject_name, 'name' => $r->subject_name, 'marks' => $m, 'grade' => $grade ];
        $byExam[$exid]['total'] += $m;
        $byExam[$exid]['count'] += 1;
    }

    $exams = [];
    foreach ($byExam as $ex) {
        $avg = $ex['count'] ? round($ex['total'] / $ex['count']) : 0;
        $div = $avg >= 75 ? 'I' : ($avg >= 65 ? 'II' : ($avg >= 55 ? 'III' : ($avg >= 40 ? 'IV' : '0')));
        $ex['aggregate'] = $avg;
        $ex['division'] = $div;
        unset($ex['total'],$ex['count']);
        $exams[] = $ex;
    }

    // Sort exams newest first already applied in query ordering
    return response()->json([ 'exams' => $exams ]);
})->name('api.public.students.results');

// Public API: Student behaviours (best-effort; guarded by table existence)
Route::get('/api/public/students/{id}/behaviours', function (int $id) {
    if (\Illuminate\Support\Facades\Schema::hasTable('behaviours')) {
        $rows = DB::table('behaviours')->where('student_id', $id)
            ->orderByDesc('id')->limit(10)
            ->get(['id','type','note','created_at']);
        return response()->json($rows);
    }
    return response()->json([]);
})->name('api.public.students.behaviours');

// Public API: Classes assigned to an exam for a specific school
Route::get('/api/public/schools/{id}/exams/{examId}/classes', function ($id, int $examId) {
    $query = DB::table('exam_class as ec')
        ->join('classes as c', 'c.id', '=', 'ec.school_class_id')
        ->where('ec.exam_id', $examId)
        ->where('c.school_number', $id)
        ->orderBy('c.name')
        ->selectRaw('TRIM(c.name) as name');

    $rows = $query->pluck('name')->filter()->unique()->values();

    // Fallback: if no classes match the school filter (e.g., school_number not set), return all classes for the exam
    if ($rows->isEmpty()) {
        $rows = DB::table('exam_class as ec')
            ->join('classes as c', 'c.id', '=', 'ec.school_class_id')
            ->where('ec.exam_id', $examId)
            ->orderBy('c.name')
            ->selectRaw('TRIM(c.name) as name')
            ->pluck('name')->filter()->unique()->values();
    }

    return response()->json($rows);
});

// Public API: Computed results for a school's exam
Route::get('/api/public/schools/{id}/exams/{examId}/results', function ($id, int $examId) {
    $className = request()->query('class_name');
    // School info
    $school = DB::table('users')
        ->where('school_number', $id)
        ->orderBy('id')
        ->first(['school_name','school_number','region']);

    // Pull raw marks for this exam scoped to the school's classes
    $rows = DB::table('exam_marks as m')
        ->join('students as s', 's.id', '=', 'm.student_id')
        ->join('subjects as sub', 'sub.id', '=', 'm.subject_id')
        ->join('classes as c', 'c.name', '=', 'm.class_name')
        ->where('m.exam_id', $examId)
        ->where('c.school_number', $id);
    if ($className) { $rows->where('c.name', $className); }
    $rows = $rows->get(['s.id as student_id','s.reg_no','s.name as student_name','s.sex','sub.code as subject_code','sub.name as subject_name','m.marks']);

    // Group by student
    $byStudent = [];
    foreach ($rows as $r) {
        $sid = $r->student_id;
        if (!isset($byStudent[$sid])) {
            $byStudent[$sid] = [
                'reg_no' => $r->reg_no,
                'name' => $r->student_name,
                'sex' => strtoupper((string)$r->sex),
                'subjects' => [],
                'total' => 0,
                'count' => 0,
            ];
        }
        $grade = null; // simple letter banding
        $m = (int)($r->marks ?? 0);
        if ($m >= 75) $grade = 'A'; elseif ($m >= 65) $grade = 'B'; elseif ($m >= 55) $grade = 'C'; elseif ($m >= 40) $grade = 'D'; else $grade = 'F';
        $byStudent[$sid]['subjects'][] = [ 'code' => $r->subject_code ?: $r->subject_name, 'name' => $r->subject_name, 'grade' => $grade, 'marks' => $m ];
        $byStudent[$sid]['total'] += $m;
        $byStudent[$sid]['count'] += 1;
    }

    // Compute division via average thresholds (simple model)
    $students = [];
    $summary = [ 'F' => ['I'=>0,'II'=>0,'III'=>0,'IV'=>0,'0'=>0], 'M' => ['I'=>0,'II'=>0,'III'=>0,'IV'=>0,'0'=>0], 'T' => ['I'=>0,'II'=>0,'III'=>0,'IV'=>0,'0'=>0] ];
    foreach ($byStudent as $sid => $st) {
        $avg = $st['count'] ? $st['total'] / $st['count'] : 0;
        $div = '0';
        if ($avg >= 75) $div = 'I';
        elseif ($avg >= 65) $div = 'II';
        elseif ($avg >= 55) $div = 'III';
        elseif ($avg >= 40) $div = 'IV';
        else $div = '0';
        $sex = in_array($st['sex'], ['F','M']) ? $st['sex'] : 'T';
        $summary[$sex][$div] += 1;
        $summary['T'][$div] += 1;
        $students[] = [
            'reg_no' => $st['reg_no'],
            'name' => $st['name'],
            'sex' => $st['sex'] ?: '-',
            'aggregate' => round($avg),
            'division' => $div,
            'subjects' => $st['subjects'],
        ];
    }

    // Sort students by reg_no or name
    usort($students, function ($a,$b) { return strcmp($a['reg_no'], $b['reg_no']); });

    // Get assigned classes for this exam/school
    $assignedClasses = DB::table('exam_class as ec')
        ->join('classes as c', 'c.id', '=', 'ec.school_class_id')
        ->where('ec.exam_id', $examId)
        ->where('c.school_number', $id)
        ->orderBy('c.name')
        ->pluck('c.name');

    return response()->json([
        'school' => $school,
        'classes' => $assignedClasses,
        'summary' => $summary,
        'students' => $students,
    ]);
});

Route::get('/blog', function () {
    return Inertia::render('Blog/Index');
})->name('blog.index');

Route::get('/blog/{id}', function (int $id) {
    return Inertia::render('Blog/Show', [
        'id' => $id,
    ]);
})->name('blog.show');

// Public API: Schools list
Route::get('/api/public/schools', function () {
    // Use users table as authoritative source for school_name and school_number
    $rows = DB::table('users')
        ->selectRaw('TRIM(school_number) as school_number, TRIM(school_name) as school_name')
        ->whereNotNull('school_number')
        ->whereNotNull('school_name')
        ->whereRaw("TRIM(school_number) <> ''")
        ->whereRaw("TRIM(school_name) <> ''")
        ->groupBy('school_number','school_name')
        ->orderBy('school_name')
        ->get();
    return response()->json($rows);
});

// Public Results landing (Schools)
Route::inertia('/results/schools', 'Results/Schools')->name('results.schools');
Route::get('/results/schools/{id}', function ($id) {
    return Inertia::render('Results/School', [
        'id' => $id,
    ]);
})->name('results.schools.show');

Route::get('/results/schools/{id}/exams/{examId}', function ($id, int $examId) {
    return Inertia::render('Results/View', [
        'schoolId' => $id,
        'examId' => $examId,
    ]);
})->name('results.schools.exam');

// Public API: Exams for a school_number
Route::get('/api/public/schools/{id}/exams', function ($id) {
    $year = request()->integer('year');
    $q = DB::table('exams as e')
        ->join('users as u', 'u.id', '=', 'e.created_by')
        ->where('u.school_number', $id)
        ->select('e.id', 'e.name', 'e.type', 'e.year', 'e.term', 'e.published_at')
        ->orderByDesc('e.year')->orderByDesc('e.term')->orderByDesc('e.id');
    if ($year) { $q->where('e.year', $year); }
    return response()->json($q->get());
});

Route::get('/results/schools/{id}/details', function ($id) {
    $school = DB::table('users')
        ->where('school_number', $id)
        ->orderBy('id')
        ->first(['name','username','email','phone','region','school_name','school_number']);

    $counts = [
        'teachers' => DB::table('teachers')->where('school_number', $id)->count(),
        'classes' => DB::table('classes')->where('school_number', $id)->count(),
        'students' => DB::table('students')
            ->join('classes', 'students.class_name', '=', 'classes.name')
            ->where('classes.school_number', $id)
            ->count(),
        'subjects' => DB::table('subjects')->where('school_number', $id)->count(),
    ];

    return Inertia::render('Results/SchoolDetails', [
        'id' => (string)$id,
        'school' => $school,
        'counts' => $counts,
    ]);
})->name('results.schools.details');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/year', [ProfileController::class, 'setYear'])->name('profile.setYear');
    Route::post('/account/delete-request', [AccountController::class, 'requestDeletion'])->name('account.delete.request');

    // Students
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::patch('/students/{id}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{id}', [StudentController::class, 'destroy'])->name('students.destroy');
    Route::get('/students/export', [StudentController::class, 'export'])->name('students.export');
    Route::post('/students/import', [StudentController::class, 'import'])->name('students.import');
    Route::get('/students/guardian-stats', [StudentController::class, 'guardianStats'])->name('students.guardian.stats');
    Route::get('/students/template', [StudentController::class, 'template'])->name('students.template');
    Route::get('/students/search', [StudentController::class, 'search'])->name('students.search');
    Route::get('/students/show-json', [StudentController::class, 'showJson'])->name('students.show.json');
    Route::get('/students/history', [StudentController::class, 'history'])->name('students.history');
    Route::get('/students/{id}/behaviours', [StudentController::class, 'behaviours'])->name('students.behaviours');
    Route::post('/students/{id}/behaviours', [StudentController::class, 'addBehaviour'])->name('students.behaviours.add');
    Route::patch('/students/{id}/status', [StudentController::class, 'updateStatus'])->name('students.status.update');
    Route::get('/students/{student}/subjects', [StudentController::class, 'subjects'])->name('students.subjects');
    Route::post('/students/{student}/subjects', [StudentController::class, 'attachSubject'])->name('students.subjects.attach');
    Route::delete('/students/{student}/subjects/{subject}', [StudentController::class, 'detachSubject'])->name('students.subjects.detach');

    // Classes
    Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
    Route::get('/classes/search', [ClassController::class, 'search'])->name('classes.search');
    Route::get('/classes/show-json', [ClassController::class, 'showJson'])->name('classes.show.json');
    Route::get('/classes/{id}/subjects', [ClassController::class, 'subjects'])->name('classes.subjects');
    Route::get('/classes/{id}', [ClassController::class, 'show'])->name('classes.show');
    Route::post('/classes', [ClassController::class, 'store'])->name('classes.store');
    Route::patch('/classes/{id}', [ClassController::class, 'update'])->name('classes.update');
    Route::delete('/classes/{id}', [ClassController::class, 'destroy'])->name('classes.destroy');

    // Subjects
    Route::get('/subjects', [SubjectsController::class, 'index'])->name('subjects.index');
    Route::get('/subjects/{id}', [SubjectsController::class, 'show'])->name('subjects.show');
    Route::get('/subjects/search', [SubjectsController::class, 'search'])->name('subjects.search');
    Route::get('/subjects/show-json', [SubjectsController::class, 'showJson'])->name('subjects.show.json');
    Route::post('/subjects', [SubjectsController::class, 'store'])->name('subjects.store');
    Route::patch('/subjects/{id}', [SubjectsController::class, 'update'])->name('subjects.update');
    Route::delete('/subjects/{id}', [SubjectsController::class, 'destroy'])->name('subjects.destroy');

    // Teacher-focused exam view
    Route::get('/teachers/{id}/exam/{examId}', [TeachersController::class, 'exam'])->name('teachers.exam');

    // Teachers
    Route::get('/teachers', [TeachersController::class, 'index'])->name('teachers.index');
    Route::post('/teachers', [TeachersController::class, 'store'])->name('teachers.store');
    Route::get('/teachers/show-json', [TeachersController::class, 'showJson'])->name('teachers.show.json');
    Route::get('/teachers/{id}', [TeachersController::class, 'show'])->name('teachers.show');
    Route::delete('/teachers/{id}', [TeachersController::class, 'destroy'])->name('teachers.destroy');

    // Results
    Route::get('/results', [ResultsController::class, 'index'])->name('results.index');
    Route::post('/results/exams', [ResultsController::class, 'storeExam'])->name('results.exams.store');
    Route::get('/results/exams/show-json', [ResultsController::class, 'showJson'])->name('results.exams.show.json');
    Route::get('/results/exams/{examId}/marks', [ResultsController::class, 'getMarks'])->name('results.exams.marks');
    Route::post('/results/exams/{examId}/marks', [ResultsController::class, 'saveMarks'])->name('results.exams.marks.save');
    Route::get('/results/exams/{id}', [ResultsController::class, 'show'])->name('results.exams.show');
    Route::patch('/results/exams/{id}', [ResultsController::class, 'update'])->name('results.exams.update');
    Route::delete('/results/exams/{id}', [ResultsController::class, 'destroy'])->name('results.exams.destroy');
    // Marking
    Route::get('/marking', function () {
        $exams = Exam::query()->orderByDesc('year')->orderByDesc('term')->orderByDesc('id')
            ->get(['id','name','type','year','term']);
        return Inertia::render('Marking/Home', [
            'exams' => $exams,
        ]);
    })->name('marking.index');
    Route::get('/marking/{examId}', function (int $examId) {
        return redirect()->route('marks.show', $examId);
    })->name('marking.show');
    Route::get('/marks/{examId}', function (int $examId) {
        $exam = Exam::with('classes:id,name')->findOrFail($examId);
        return Inertia::render('Marking/Marks', [
            'exam' => [
                'id' => $exam->id,
                'name' => $exam->name,
                'type' => $exam->type,
                'year' => $exam->year,
                'term' => $exam->term,
                'classes' => $exam->classes->map->only(['id','name']),
            ],
        ]);
    })->name('marks.show');
    Route::inertia('/results/manage', 'Results/Manage')->name('results.manage');

    // Reports
    Route::inertia('/reports/teachers', 'Reports/Teachers')->name('reports.teachers');
    Route::inertia('/reports/students', 'Reports/Students')->name('reports.students');
    Route::inertia('/reports/school', 'Reports/School')->name('reports.school');
    // JSON
    Route::get('/api/reports/students', [ReportsController::class, 'students'])->name('api.reports.students');
    Route::get('/api/reports/school', [ReportsController::class, 'school'])->name('api.reports.school');
    Route::get('/api/reports/classes-coverage', [ReportsController::class, 'classesCoverage'])->name('api.reports.classes');

    // Information
    Route::get('/information', [InformationController::class, 'index'])->name('information.index');
    Route::post('/information/feedback', [InformationController::class, 'storeFeedback'])->name('information.feedback.store');
    Route::post('/information/updates/{id}/react', [InformationController::class, 'reactUpdate'])->name('information.updates.react');

    // Timetables
    Route::get('/timetables', [TimetablesController::class, 'index'])->name('timetables.index');
    Route::get('/timetables/class', [TimetablesController::class, 'classIndex'])->name('timetables.class.index');
    Route::post('/timetables', [TimetablesController::class, 'store'])->name('timetables.store');
    Route::inertia('/timetables/create', 'Timetables/Create')->name('timetables.create');
    Route::inertia('/timetables/sitting-plan', 'Timetables/SittingPlan')->name('timetables.sitting');

    // Information
    Route::inertia('/information/announcements', 'Information/Announcements')->name('information.announcements');
    Route::inertia('/information/policies', 'Information/Policies')->name('information.policies');
    Route::inertia('/information/updates', 'Information/Updates')->name('information.updates');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
});

// EMAS Routes
Route::prefix('emas')->name('emas.')->group(function () {
    // EMAS Authentication Routes
    Route::get('/login', [EmasAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [EmasAuthController::class, 'login']);
    Route::get('/register', [EmasAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [EmasAuthController::class, 'register']);
    Route::post('/logout', [EmasAuthController::class, 'logout'])->name('logout');

    // Root redirect
    Route::get('/', function () {
        if (auth()->guard('emas')->check()) {
            return redirect()->route('emas.dashboard');
        }
        return redirect()->route('emas.login');
    });

    // EMAS Protected Routes
    Route::middleware('emas')->group(function () {
        Route::get('/dashboard', [EmasDashboardController::class, 'index'])->name('dashboard');
        
        // Exams Management
        Route::resource('exams', \App\Http\Controllers\Emas\EmasExamController::class)->names([
            'index' => 'exams.index',
            'create' => 'exams.create',
            'store' => 'exams.store',
            'show' => 'exams.show',
            'edit' => 'exams.edit',
            'update' => 'exams.update',
            'destroy' => 'exams.destroy',
        ]);

        // Candidates Management
        Route::resource('candidates', \App\Http\Controllers\Emas\EmasCandidateController::class)->names([
            'index' => 'candidates.index',
            'create' => 'candidates.create',
            'store' => 'candidates.store',
            'show' => 'candidates.show',
            'edit' => 'candidates.edit',
            'update' => 'candidates.update',
            'destroy' => 'candidates.destroy',
        ]);

        // Results Management
        Route::prefix('results')->group(function () {
            Route::get('/', [\App\Http\Controllers\Emas\EmasResultController::class, 'index'])->name('results.index');
            Route::get('/upload', [\App\Http\Controllers\Emas\EmasResultController::class, 'upload'])->name('results.upload');
            Route::post('/store', [\App\Http\Controllers\Emas\EmasResultController::class, 'store'])->name('results.store');
            Route::get('/{result}', [\App\Http\Controllers\Emas\EmasResultController::class, 'show'])->name('results.show');
            Route::delete('/{result}', [\App\Http\Controllers\Emas\EmasResultController::class, 'destroy'])->name('results.destroy');
        });

        // Centers Management
        Route::resource('centers', \App\Http\Controllers\Emas\EmasCenterController::class)->names([
            'index' => 'centers.index',
            'create' => 'centers.create',
            'store' => 'centers.store',
            'show' => 'centers.show',
            'edit' => 'centers.edit',
            'update' => 'centers.update',
            'destroy' => 'centers.destroy',
        ]);

        // Reports & Analytics
        Route::prefix('reports')->group(function () {
            Route::get('/', [\App\Http\Controllers\Emas\EmasReportController::class, 'index'])->name('reports.index');
            Route::post('/generate', [\App\Http\Controllers\Emas\EmasReportController::class, 'generate'])->name('reports.generate');
        });

        // Markers Management
        Route::resource('markers', \App\Http\Controllers\Emas\EmasMarkerController::class)->names([
            'index' => 'markers.index',
            'create' => 'markers.create',
            'store' => 'markers.store',
            'show' => 'markers.show',
            'edit' => 'markers.edit',
            'update' => 'markers.update',
            'destroy' => 'markers.destroy',
        ]);
        Route::post('/markers/assign', [\App\Http\Controllers\Emas\EmasMarkerController::class, 'assign'])->name('markers.assign');
        Route::get('/markers-assignments', [\App\Http\Controllers\Emas\EmasMarkerController::class, 'assignments'])->name('markers.assignments');
    });
});

require __DIR__.'/auth.php';
