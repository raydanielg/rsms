<?php

namespace App\Http\Controllers\Emas;

use App\Http\Controllers\Controller;
use App\Models\EmasCenter;
use App\Data\TanzaniaRegions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class EmasCenterController extends Controller
{
    public function index(Request $request): Response
    {
        $query = EmasCenter::withCount('candidates');

        // Filter by region
        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('center_name', 'like', "%{$search}%")
                  ->orWhere('center_code', 'like', "%{$search}%")
                  ->orWhere('district', 'like', "%{$search}%");
            });
        }

        $centers = $query->latest()->paginate(12)->withQueryString();

        // Get statistics
        $stats = [
            'total_centers' => EmasCenter::count(),
            'active_centers' => EmasCenter::where('status', 'active')->count(),
            'total_capacity' => EmasCenter::sum('capacity'),
            'regions_count' => EmasCenter::distinct('region')->count('region'),
        ];

        // Get all regions used in centers with counts
        $regionCounts = EmasCenter::select('region', DB::raw('count(*) as count'))
            ->groupBy('region')
            ->pluck('count', 'region')
            ->toArray();
        
        $usedRegions = array_keys($regionCounts);
        sort($usedRegions);

        return Inertia::render('Emas/Centers/Index', [
            'centers' => $centers,
            'stats' => $stats,
            'allRegions' => TanzaniaRegions::getRegions(),
            'usedRegions' => $usedRegions,
            'regionCounts' => $regionCounts,
            'filters' => $request->only(['search', 'region', 'status']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Emas/Centers/Create', [
            'regions' => TanzaniaRegions::getRegionsWithDistricts(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'center_name' => 'required|string|max:255',
            'center_code' => 'required|string|unique:emas_centers,center_code',
            'address' => 'required|string',
            'district' => 'required|string',
            'region' => 'required|string',
            'ownership' => 'required|in:GOVERNMENT,PRIVATE',
            'phone' => 'required|string',
            'email' => 'nullable|email',
            'capacity' => 'required|integer|min:1',
            'coordinator_name' => 'required|string',
            'coordinator_phone' => 'nullable|string',
        ]);

        // Generate username from coordinator name
        $username = $this->generateUsername($validated['coordinator_name']);
        
        // Set default password
        $validated['coordinator_username'] = $username;
        $validated['coordinator_password'] = bcrypt('emas'); // Default password: emas

        $center = EmasCenter::create($validated);

        return redirect()->route('emas.centers.index')
            ->with('success', 'Exam center created successfully!')
            ->with('coordinator_username', $username)
            ->with('coordinator_password', 'emas');
    }

    public function show(EmasCenter $center): Response
    {
        $center->load(['candidates' => function($query) {
            $query->latest()->limit(50);
        }]);

        // Get center statistics
        $stats = [
            'total_candidates' => $center->candidates()->count(),
            'active_candidates' => $center->activeCandidates()->count(),
            'total_exams' => \App\Models\EmasExam::whereHas('candidates', function($query) use ($center) {
                $query->where('exam_center_code', $center->center_code);
            })->count(),
        ];

        // Get markers assigned to this center
        // Note: For now, get all markers. In future, filter by assigned centers
        $markers = \App\Models\EmasUser::where('role', 'marker')->get();

        // Get subjects available at this center
        $subjects = \App\Models\EmasExam::whereHas('candidates', function($query) use ($center) {
            $query->where('exam_center_code', $center->center_code);
        })->distinct()->pluck('subject');

        return Inertia::render('Emas/Centers/Show', [
            'center' => $center,
            'stats' => $stats,
            'markers' => $markers,
            'subjects' => $subjects,
        ]);
    }

    public function edit(EmasCenter $center): Response
    {
        return Inertia::render('Emas/Centers/Edit', [
            'center' => $center,
            'regions' => TanzaniaRegions::getRegionsWithDistricts(),
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
            'ownership' => 'required|in:GOVERNMENT,PRIVATE',
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

    public function storeCandidate(Request $request, EmasCenter $center)
    {
        try {
            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'registration_number' => 'required|string|unique:emas_candidates,registration_number',
                'gender' => 'required|in:M,F',
                'date_of_birth' => 'nullable|date',
                'exam_center_code' => 'required|string',
                'status' => 'required|in:active,inactive',
            ]);

            // Parse full name into first, middle, last names
            $nameParts = explode(' ', trim($validated['full_name']));
            $firstName = $nameParts[0] ?? '';
            $lastName = count($nameParts) > 1 ? array_pop($nameParts) : '';
            $middleName = count($nameParts) > 1 ? implode(' ', $nameParts) : null;

            $candidate = \App\Models\EmasCandidate::create([
                'first_name' => $firstName,
                'middle_name' => $middleName,
                'last_name' => $lastName,
                'registration_number' => strtoupper($validated['registration_number']),
                'gender' => $validated['gender'],
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'exam_center_code' => $validated['exam_center_code'],
                'status' => $validated['status'],
            ]);

            return back()->with('success', 'Mwanafunzi ameongezwa kwa mafanikio!');
            
        } catch (\Exception $e) {
            \Log::error('Error creating candidate: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Kuna tatizo limetokea. Tafadhali jaribu tena.']);
        }
    }

    public function bulkUploadCandidates(Request $request, EmasCenter $center)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:csv,txt|max:5120',
            ]);

            $file = $request->file('file');
            $csvData = array_map('str_getcsv', file($file->getRealPath()));
            
            if (count($csvData) < 2) {
                return back()->withErrors(['error' => 'File haina data ya kutosha!']);
            }

            $headers = array_map('trim', $csvData[0]);
            $successCount = 0;
            $errors = [];

            for ($i = 1; $i < count($csvData); $i++) {
                try {
                    $row = array_combine($headers, array_map('trim', $csvData[$i]));
                    
                    // Skip empty rows
                    if (empty($row['REGISTRATION_NUMBER']) || empty($row['FULL_NAME'])) {
                        continue;
                    }

                    // Parse full name
                    $nameParts = explode(' ', trim($row['FULL_NAME']));
                    $firstName = $nameParts[0] ?? '';
                    $lastName = count($nameParts) > 1 ? array_pop($nameParts) : '';
                    $middleName = count($nameParts) > 1 ? implode(' ', $nameParts) : null;

                    \App\Models\EmasCandidate::create([
                        'first_name' => $firstName,
                        'middle_name' => $middleName,
                        'last_name' => $lastName,
                        'registration_number' => strtoupper($row['REGISTRATION_NUMBER']),
                        'gender' => strtoupper($row['GENDER'] ?? 'M'),
                        'date_of_birth' => !empty($row['DATE_OF_BIRTH']) ? $row['DATE_OF_BIRTH'] : null,
                        'exam_center_code' => $request->center_code ?? $center->center_code,
                        'status' => strtolower($row['STATUS'] ?? 'active'),
                    ]);

                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($i + 1) . ": " . $e->getMessage();
                }
            }

            $message = "Mafanikio! Wanafunzi {$successCount} wameongezwa.";
            if (count($errors) > 0) {
                $message .= " Lakini kuna makosa " . count($errors) . ".";
            }

            return back()->with('success', $message);
            
        } catch (\Exception $e) {
            \Log::error('Bulk upload error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Kuna tatizo limetokea wakati wa upload: ' . $e->getMessage()]);
        }
    }

    public function destroyCandidate(EmasCenter $center, \App\Models\EmasCandidate $candidate)
    {
        try {
            $candidate->delete();
            return back()->with('success', 'Mwanafunzi amefutwa kwa mafanikio!');
        } catch (\Exception $e) {
            \Log::error('Error deleting candidate: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Kuna tatizo limetokea wakati wa kufuta mwanafunzi.']);
        }
    }

    public function showResults(EmasCenter $center): Response
    {
        // Load all candidates with their results
        $candidates = $center->candidates()->with('results.exam')->latest()->paginate(50);

        // Get all subjects from exams
        $subjects = \App\Models\EmasExam::whereHas('candidates', function($query) use ($center) {
            $query->where('exam_center_code', $center->center_code);
        })->distinct()->pluck('subject');

        // Get statistics
        $stats = [
            'total_candidates' => $center->candidates()->count(),
            'total_exams' => \App\Models\EmasExam::whereHas('candidates', function($query) use ($center) {
                $query->where('exam_center_code', $center->center_code);
            })->count(),
            'average_score' => \App\Models\EmasResult::whereHas('candidate', function($query) use ($center) {
                $query->where('exam_center_code', $center->center_code);
            })->avg('score'),
        ];

        return Inertia::render('Emas/Centers/Results', [
            'center' => $center,
            'candidates' => $candidates,
            'subjects' => $subjects,
            'stats' => $stats,
        ]);
    }

    /**
     * Generate username from coordinator name
     */
    private function generateUsername($name)
    {
        // Remove special characters and convert to lowercase
        $name = strtolower(trim($name));
        $name = preg_replace('/[^a-z0-9\s]/', '', $name);
        
        // Split name into parts
        $parts = explode(' ', $name);
        
        if (count($parts) >= 2) {
            // Use first name and first letter of last name
            $username = $parts[0] . substr($parts[count($parts) - 1], 0, 1);
        } else {
            // Just use the name
            $username = $parts[0];
        }
        
        // Check if username exists and append number if needed
        $originalUsername = $username;
        $counter = 1;
        
        while (EmasCenter::where('coordinator_username', $username)->exists()) {
            $username = $originalUsername . $counter;
            $counter++;
        }
        
        return $username;
    }
}
