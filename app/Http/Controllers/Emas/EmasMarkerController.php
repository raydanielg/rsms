<?php

namespace App\Http\Controllers\Emas;

use App\Http\Controllers\Controller;
use App\Models\EmasMarker;
use App\Models\EmasMarkerAssignment;
use App\Models\EmasExam;
use App\Models\EmasCenter;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Validator;

class EmasMarkerController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $centerFilter = $request->input('center');

        $markers = EmasMarker::with(['center'])
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('marker_code', 'like', "%{$search}%");
            })
            ->when($centerFilter, function ($query) use ($centerFilter) {
                $query->where('center_id', $centerFilter);
            })
            ->latest()
            ->paginate(20);

        $centers = EmasCenter::orderBy('center_name')->get(['id', 'center_name', 'center_code']);

        return Inertia::render('Emas/Markers/Index', [
            'markers' => $markers,
            'centers' => $centers,
            'filters' => [
                'search' => $search,
                'center' => $centerFilter,
            ],
        ]);
    }

    public function create(): Response
    {
        $centers = EmasCenter::orderBy('center_name')->get(['id', 'center_name', 'center_code']);

        return Inertia::render('Emas/Markers/Create', [
            'centers' => $centers,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:emas_markers,email',
            'phone' => 'required|string',
            'marker_code' => 'required|string|unique:emas_markers,marker_code',
            'center_id' => 'required|exists:emas_centers,id',
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'required|string',
            'district' => 'nullable|string',
            'region' => 'required|string',
            'scope' => 'required|in:district,region',
            'qualifications' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        EmasMarker::create($validated);

        return redirect()->route('emas.markers.index')
            ->with('success', 'Marker added successfully!');
    }

    public function show(EmasMarker $marker): Response
    {
        $marker->load(['assignments.exam']);

        return Inertia::render('Emas/Markers/Show', [
            'marker' => $marker,
        ]);
    }

    public function edit(EmasMarker $marker): Response
    {
        $marker->load('center');
        $centers = EmasCenter::orderBy('center_name')->get(['id', 'center_name', 'center_code']);

        return Inertia::render('Emas/Markers/Edit', [
            'marker' => $marker,
            'centers' => $centers,
        ]);
    }

    public function update(Request $request, EmasMarker $marker)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:emas_markers,email,' . $marker->id,
            'phone' => 'required|string',
            'marker_code' => 'required|string|unique:emas_markers,marker_code,' . $marker->id,
            'center_id' => 'required|exists:emas_centers,id',
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'required|string',
            'district' => 'nullable|string',
            'region' => 'required|string',
            'scope' => 'required|in:district,region',
            'status' => 'required|in:active,inactive,suspended',
            'qualifications' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $marker->update($validated);

        return redirect()->route('emas.markers.index')
            ->with('success', 'Marker updated successfully!');
    }

    public function destroy(EmasMarker $marker)
    {
        $marker->delete();

        return redirect()->route('emas.markers.index')
            ->with('success', 'Marker deleted successfully!');
    }

    public function assign(Request $request)
    {
        $validated = $request->validate([
            'marker_id' => 'required|exists:emas_markers,id',
            'exam_id' => 'required|exists:emas_exams,id',
            'papers_assigned' => 'required|integer|min:1',
        ]);

        $assignment = EmasMarkerAssignment::updateOrCreate(
            [
                'marker_id' => $validated['marker_id'],
                'exam_id' => $validated['exam_id'],
            ],
            [
                'papers_assigned' => $validated['papers_assigned'],
                'papers_pending' => $validated['papers_assigned'],
                'assigned_at' => now(),
            ]
        );

        return redirect()->back()
            ->with('success', 'Exam assigned to marker successfully!');
    }

    public function assignments(): Response
    {
        $assignments = EmasMarkerAssignment::with(['marker', 'exam'])
            ->latest()
            ->paginate(15);

        return Inertia::render('Emas/Markers/Assignments', [
            'assignments' => $assignments,
        ]);
    }

    /**
     * Bulk upload markers from CSV/Excel
     */
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx|max:2048',
            'center_id' => 'required|exists:emas_centers,id',
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        
        $markers = [];
        $errors = [];
        $successCount = 0;
        
        if ($extension === 'csv' || $extension === 'txt') {
            // Read CSV file
            $csvData = array_map('str_getcsv', file($file->getPathname()));
            $headers = array_shift($csvData); // Remove headers
            
            foreach ($csvData as $index => $row) {
                $rowNumber = $index + 2; // +2 because of header and 0-index
                
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }
                
                // Validate row has required columns
                if (count($row) < 5) {
                    $errors[] = "Row {$rowNumber}: Insufficient columns";
                    continue;
                }
                
                $markerData = [
                    'name' => trim($row[0]),
                    'email' => trim($row[1]),
                    'phone' => trim($row[2]),
                    'marker_code' => trim($row[3]),
                    'center_id' => $request->center_id,
                    'subjects' => array_map('trim', explode(',', $row[4])),
                    'region' => trim($row[5] ?? ''),
                    'district' => trim($row[6] ?? ''),
                    'scope' => trim($row[7] ?? 'district'),
                    'qualifications' => trim($row[8] ?? ''),
                    'status' => 'active',
                ];
                
                // Validate data
                $validator = Validator::make($markerData, [
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|unique:emas_markers,email',
                    'phone' => 'required|string',
                    'marker_code' => 'required|string|unique:emas_markers,marker_code',
                    'center_id' => 'required|exists:emas_centers,id',
                    'subjects' => 'required|array|min:1',
                    'region' => 'required|string',
                    'scope' => 'required|in:district,region',
                ]);
                
                if ($validator->fails()) {
                    $errors[] = "Row {$rowNumber}: " . implode(', ', $validator->errors()->all());
                    continue;
                }
                
                try {
                    EmasMarker::create($markerData);
                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Row {$rowNumber}: " . $e->getMessage();
                }
            }
        }
        
        $message = "{$successCount} markers uploaded successfully";
        if (count($errors) > 0) {
            $message .= ". " . count($errors) . " errors occurred.";
        }
        
        return back()->with([
            'success' => $message,
            'upload_errors' => $errors,
            'uploaded_count' => $successCount,
        ]);
    }
}
