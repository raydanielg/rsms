<?php

namespace App\Http\Controllers\Emas;

use App\Http\Controllers\Controller;
use App\Models\EmasExam;
use App\Models\EmasResult;
use App\Models\EmasCandidate;
use App\Models\EmasMarkerAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class EmasMarkerDashboardController extends Controller
{
    /**
     * Display the marker dashboard
     */
    public function index()
    {
        $user = auth()->guard('emas')->user();
        
        // Get marker's assigned exams
        $assignedExams = $this->getAssignedExams();
        
        // Get marking statistics
        $stats = $this->getMarkingStats();

        return Inertia::render('Emas/Marker/Dashboard', [
            'assignedExams' => $assignedExams,
            'stats' => $stats,
        ]);
    }

    /**
     * Display marks entry form for a specific exam
     */
    public function enterMarks($examId)
    {
        $exam = EmasExam::findOrFail($examId);
        $user = auth()->guard('emas')->user();
        
        // Check if marker is assigned to this subject
        if (!$user->canMarkSubject($exam->subject)) {
            abort(403, 'You are not assigned to mark this subject: ' . $exam->subject);
        }
        
        // Get all candidates that match the exam's level and class
        $candidates = EmasCandidate::where('level', $exam->level)
            ->where('class_form', $exam->class_form)
            ->where('status', 'active')
            ->with(['results' => function($query) use ($examId) {
                $query->where('exam_id', $examId);
            }])
            ->orderBy('registration_number')
            ->get();

        return Inertia::render('Emas/Marker/EnterMarks', [
            'exam' => $exam,
            'candidates' => $candidates,
            'assignedSubjects' => $user->getAssignedSubjects(),
        ]);
    }

    /**
     * Store or update marks for candidates
     */
    public function storeMarks(Request $request, $examId)
    {
        $request->validate([
            'results' => 'required|array',
            'results.*.candidate_id' => 'required|exists:emas_candidates,id',
            'results.*.score' => 'required|integer|min:0',
            'results.*.grade' => 'nullable|string|max:2',
            'results.*.remarks' => 'nullable|in:Excellent,Very Good,Good,Satisfactory,Fail',
            'results.*.comments' => 'nullable|string|max:500',
        ]);

        $exam = EmasExam::findOrFail($examId);
        $user = auth()->guard('emas')->user();

        DB::beginTransaction();
        try {
            foreach ($request->results as $resultData) {
                // Calculate grade if not provided
                if (!isset($resultData['grade'])) {
                    $resultData['grade'] = $this->calculateGrade($resultData['score'], $exam->total_marks);
                }

                // Determine remarks based on pass/fail
                if (!isset($resultData['remarks'])) {
                    $percentage = ($resultData['score'] / $exam->total_marks) * 100;
                    $resultData['remarks'] = $this->calculateRemarks($percentage);
                }

                EmasResult::updateOrCreate(
                    [
                        'exam_id' => $examId,
                        'candidate_id' => $resultData['candidate_id'],
                    ],
                    [
                        'score' => $resultData['score'],
                        'grade' => $resultData['grade'],
                        'remarks' => $resultData['remarks'],
                        'comments' => $resultData['comments'] ?? null,
                        'uploaded_by' => $user->id,
                    ]
                );
            }

            DB::commit();

            return redirect()->back()->with('success', 'Marks saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to save marks: ' . $e->getMessage()]);
        }
    }

    /**
     * View marking history
     */
    public function history()
    {
        $user = auth()->guard('emas')->user();
        
        $history = EmasResult::where('uploaded_by', $user->id)
            ->with(['exam:id,exam_name,exam_code,subject', 'candidate:id,full_name,registration_number'])
            ->latest()
            ->paginate(20);

        return Inertia::render('Emas/Marker/History', [
            'history' => $history,
        ]);
    }

    /**
     * Show help guide page
     */
    public function help()
    {
        return Inertia::render('Emas/Marker/HelpGuide');
    }

    /**
     * Show contact support page
     */
    public function support()
    {
        return Inertia::render('Emas/Marker/ContactSupport');
    }

    /**
     * Send support message
     */
    public function sendSupport(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|string',
            'message' => 'required|string',
            'priority' => 'required|in:low,normal,high'
        ]);

        // TODO: Implement email sending or ticket creation
        // For now, just return success message
        
        return redirect()->route('emas.marker.support')
            ->with('success', 'Ujumbe wako umetumwa kwa mafanikio! Tutawasiliana nawe hivi karibuni.');
    }

    /**
     * Get assigned exams for the marker
     */
    protected function getAssignedExams()
    {
        $user = auth()->guard('emas')->user();
        
        $query = EmasExam::whereIn('status', ['ongoing', 'completed']);
        
        // Filter by assigned subjects if marker has specific assignments
        $assignedSubjects = $user->getAssignedSubjects();
        if (!empty($assignedSubjects)) {
            $query->whereIn('subject', $assignedSubjects);
        }
        
        return $query->orderBy('exam_date', 'desc')->get();
    }

    /**
     * Get marking statistics for the marker
     */
    protected function getMarkingStats()
    {
        $user = auth()->guard('emas')->user();
        
        return [
            'total_marked' => EmasResult::where('uploaded_by', $user->id)->count(),
            'today_marked' => EmasResult::where('uploaded_by', $user->id)
                ->whereDate('updated_at', today())
                ->count(),
            'assigned_exams' => $this->getAssignedExams()->count(),
            'pending_exams' => EmasExam::where('status', 'ongoing')->count(),
        ];
    }

    /**
     * Calculate grade based on score
     */
    protected function calculateGrade($score, $totalMarks)
    {
        $percentage = ($score / $totalMarks) * 100;
        
        if ($percentage >= 75) return 'A';
        if ($percentage >= 65) return 'B';
        if ($percentage >= 50) return 'C';
        if ($percentage >= 40) return 'D';
        if ($percentage >= 30) return 'E';
        return 'F';
    }

    /**
     * Calculate remarks based on percentage
     */
    protected function calculateRemarks($percentage)
    {
        if ($percentage >= 75) return 'Excellent';
        if ($percentage >= 65) return 'Very Good';
        if ($percentage >= 50) return 'Good';
        if ($percentage >= 40) return 'Satisfactory';
        return 'Fail';
    }
}
