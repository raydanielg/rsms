<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\SubjectsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\InformationController;
use App\Http\Controllers\ResultsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TeachersController;
use App\Models\Exam;

Route::get('/', function () {
    return Inertia::render('Home');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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
    Route::inertia('/timetables', 'Timetables/Index')->name('timetables.index');
    Route::inertia('/timetables/create', 'Timetables/Create')->name('timetables.create');
    Route::inertia('/timetables/sitting-plan', 'Timetables/SittingPlan')->name('timetables.sitting');

    // Information
    Route::inertia('/information/announcements', 'Information/Announcements')->name('information.announcements');
    Route::inertia('/information/policies', 'Information/Policies')->name('information.policies');
    Route::inertia('/information/updates', 'Information/Updates')->name('information.updates');
});

require __DIR__.'/auth.php';
