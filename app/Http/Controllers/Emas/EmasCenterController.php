<?php

namespace App\Http\Controllers\Emas;

use App\Http\Controllers\Controller;
use App\Models\EmasCenter;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmasCenterController extends Controller
{
    public function index(): Response
    {
        $centers = EmasCenter::withCount('candidates')->latest()->paginate(12);

        return Inertia::render('Emas/Centers/Index', [
            'centers' => $centers,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Emas/Centers/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'center_name' => 'required|string|max:255',
            'center_code' => 'required|string|unique:emas_centers,center_code',
            'address' => 'required|string',
            'district' => 'required|string',
            'region' => 'required|string',
            'phone' => 'required|string',
            'email' => 'nullable|email',
            'capacity' => 'required|integer|min:1',
            'coordinator_name' => 'nullable|string',
            'coordinator_phone' => 'nullable|string',
        ]);

        EmasCenter::create($validated);

        return redirect()->route('emas.centers.index')
            ->with('success', 'Exam center created successfully!');
    }

    public function show(EmasCenter $center): Response
    {
        $center->load(['candidates' => function($query) {
            $query->latest()->limit(10);
        }]);

        return Inertia::render('Emas/Centers/Show', [
            'center' => $center,
        ]);
    }

    public function edit(EmasCenter $center): Response
    {
        return Inertia::render('Emas/Centers/Edit', [
            'center' => $center,
        ]);
    }

    public function update(Request $request, EmasCenter $center)
    {
        $validated = $request->validate([
            'center_name' => 'required|string|max:255',
            'center_code' => 'required|string|unique:emas_centers,center_code,' . $center->id,
            'address' => 'required|string',
            'district' => 'required|string',
            'region' => 'required|string',
            'phone' => 'required|string',
            'email' => 'nullable|email',
            'capacity' => 'required|integer|min:1',
            'coordinator_name' => 'nullable|string',
            'coordinator_phone' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $center->update($validated);

        return redirect()->route('emas.centers.index')
            ->with('success', 'Exam center updated successfully!');
    }

    public function destroy(EmasCenter $center)
    {
        $center->delete();

        return redirect()->route('emas.centers.index')
            ->with('success', 'Exam center deleted successfully!');
    }
}
