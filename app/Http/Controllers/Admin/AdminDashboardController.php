<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): Response
    {
        // Get statistics
        $stats = [
            'users' => DB::table('users')->count(),
            'schools' => DB::table('users')
                ->whereNotNull('school_number')
                ->distinct('school_number')
                ->count('school_number'),
            'students' => DB::table('students')->count(),
            'teachers' => DB::table('teachers')->count(),
        ];

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
        ]);
    }
}
