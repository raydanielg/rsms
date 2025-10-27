<?php

namespace App\Http\Controllers\Emas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmasDashboardController extends Controller
{
    /**
     * Display the EMAS dashboard.
     */
    public function index(): Response
    {
        $stats = [
            'exams' => 0,
            'centers' => 0,
            'candidates' => 0,
            'papers' => 0,
        ];

        return Inertia::render('Emas/Dashboard', [
            'stats' => $stats,
        ]);
    }
}
