<?php

namespace App\Http\Controllers\Emas;

use App\Http\Controllers\Controller;
use App\Models\EmasSchool;
use App\Models\EmasCenter;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmasSchoolController extends Controller
{
    /**
     * Display a listing of schools
     */
    public function index(Request $request): Response
    {
        // Auto-sync centers to schools on page load
        $this->syncCentersToSchools();

        $region = $request->input('region');
        $council = $request->input('council');
        $ownership = $request->input('ownership');

        $schools = EmasSchool::query()
            ->byRegion($region)
            ->byCouncil($council)
            ->byOwnership($ownership)
            ->orderBy('council')
            ->orderBy('centre_name')
            ->get();

        $stats = EmasSchool::getStatsByRegion($region);

        // Get unique regions and councils for filters
        $regions = EmasSchool::distinct()->pluck('region')->sort()->values();
        $councils = EmasSchool::when($region, function ($query) use ($region) {
            return $query->where('region', $region);
        })->distinct()->pluck('council')->sort()->values();

        return Inertia::render('Emas/Schools/Index', [
            'schools' => $schools,
            'stats' => $stats,
            'filters' => [
                'regions' => $regions,
                'councils' => $councils,
                'selected_region' => $region,
                'selected_council' => $council,
                'selected_ownership' => $ownership,
            ],
        ]);
    }

    /**
     * Sync EMAS Centers to Schools table
     */
    private function syncCentersToSchools()
    {
        $centers = EmasCenter::all();

        foreach ($centers as $center) {
            // Get candidates count by gender
            $femaleCount = $center->candidates()->where('gender', 'Female')->count();
            $maleCount = $center->candidates()->where('gender', 'Male')->count();

            EmasSchool::updateOrCreate(
                ['centre_number' => $center->center_code],
                [
                    'centre_name' => $center->center_name,
                    'region' => $center->region ?: 'Unknown',
                    'council' => $center->district ?: 'Unknown',
                    'ward' => null,
                    'ownership' => $center->ownership ?: 'GOVERNMENT', // Use center's ownership
                    'female_students' => $femaleCount,
                    'male_students' => $maleCount,
                    'contact_person' => $center->coordinator_name,
                    'phone' => $center->phone,
                    'email' => $center->email,
                    'address' => $center->address,
                    'status' => $center->status === 'active' ? 'active' : 'inactive',
                ]
            );
        }
    }

    /**
     * API endpoint for live data
     */
    public function getData(Request $request)
    {
        $region = $request->input('region');
        $council = $request->input('council');
        $ownership = $request->input('ownership');

        $schools = EmasSchool::query()
            ->byRegion($region)
            ->byCouncil($council)
            ->byOwnership($ownership)
            ->orderBy('council')
            ->orderBy('centre_name')
            ->get();

        $stats = EmasSchool::getStatsByRegion($region);

        // Get councils for the selected region
        $councils = EmasSchool::when($region, function ($query) use ($region) {
            return $query->where('region', $region);
        })->distinct()->pluck('council')->sort()->values();

        return response()->json([
            'schools' => $schools,
            'stats' => $stats,
            'councils' => $councils,
        ]);
    }

    /**
     * Show the form for registering a new school
     */
    public function create(): Response
    {
        return Inertia::render('Emas/Schools/Register');
    }

    /**
     * Store a newly created school
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'centre_number' => 'required|string|unique:emas_schools,centre_number',
            'centre_name' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'council' => 'required|string|max:255',
            'ward' => 'nullable|string|max:255',
            'ownership' => 'required|in:GOVERNMENT,PRIVATE',
            'female_students' => 'required|integer|min:0',
            'male_students' => 'required|integer|min:0',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        EmasSchool::create($validated);

        return redirect()->route('emas.schools.index')
            ->with('success', 'School registered successfully!');
    }

    /**
     * Update school information
     */
    public function update(Request $request, EmasSchool $school)
    {
        $validated = $request->validate([
            'centre_name' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'council' => 'required|string|max:255',
            'ward' => 'nullable|string|max:255',
            'ownership' => 'required|in:GOVERNMENT,PRIVATE',
            'female_students' => 'required|integer|min:0',
            'male_students' => 'required|integer|min:0',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $school->update($validated);

        return redirect()->route('emas.schools.index')
            ->with('success', 'School updated successfully!');
    }

    /**
     * Remove the specified school
     */
    public function destroy(EmasSchool $school)
    {
        $school->delete();

        return redirect()->route('emas.schools.index')
            ->with('success', 'School deleted successfully!');
    }

    /**
     * Manually sync centers to schools
     */
    public function syncCenters()
    {
        $centersCount = EmasCenter::count();
        $this->syncCentersToSchools();
        
        return redirect()->route('emas.schools.index')
            ->with('success', "Successfully synced {$centersCount} centers to schools registration!");
    }
}
