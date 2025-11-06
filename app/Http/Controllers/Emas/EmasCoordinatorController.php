<?php

namespace App\Http\Controllers\Emas;

use App\Http\Controllers\Controller;
use App\Models\EmasCoordinator;
use App\Models\EmasCenter;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Hash;

class EmasCoordinatorController extends Controller
{
    /**
     * Display listing of coordinators
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $centerFilter = $request->input('center');

        $coordinators = EmasCoordinator::with(['centers'])
            ->when($search, function ($query) use ($search) {
                $query->where('full_name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($centerFilter, function ($query) use ($centerFilter) {
                $query->whereHas('centers', function ($q) use ($centerFilter) {
                    $q->where('emas_centers.id', $centerFilter);
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => EmasCoordinator::count(),
            'active' => EmasCoordinator::where('status', 'active')->count(),
            'inactive' => EmasCoordinator::where('status', 'inactive')->count(),
        ];

        $centers = EmasCenter::orderBy('center_name')->get(['id', 'center_name', 'center_code']);

        return Inertia::render('Emas/Coordinators/Index', [
            'coordinators' => $coordinators,
            'stats' => $stats,
            'centers' => $centers,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'center' => $centerFilter,
            ],
        ]);
    }

    /**
     * Show form for creating coordinator
     */
    public function create(): Response
    {
        $centers = EmasCenter::orderBy('center_name')->get(['id', 'center_name', 'center_code']);

        return Inertia::render('Emas/Coordinators/Create', [
            'centers' => $centers,
        ]);
    }

    /**
     * Store a newly created coordinator
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:emas_coordinators,email',
            'phone' => 'nullable|string|max:20',
            'id_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'centers' => 'required|array|min:1',
            'centers.*' => 'exists:emas_centers,id',
        ]);

        // Generate username
        $username = EmasCoordinator::generateUsername($validated['full_name']);

        // Create coordinator
        $coordinator = EmasCoordinator::create([
            'full_name' => $validated['full_name'],
            'username' => $username,
            'password' => Hash::make('emas'), // Default password
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'id_number' => $validated['id_number'] ?? null,
            'address' => $validated['address'] ?? null,
            'status' => 'active',
        ]);

        // Attach centers
        foreach ($validated['centers'] as $index => $centerId) {
            $coordinator->centers()->attach($centerId, [
                'role' => $index === 0 ? 'primary' : 'secondary'
            ]);
        }

        return redirect()->route('emas.coordinators.index')
            ->with('success', 'Coordinator created successfully!')
            ->with('coordinator_username', $username)
            ->with('coordinator_password', 'emas');
    }

    /**
     * Show the form for editing coordinator
     */
    public function edit(EmasCoordinator $coordinator): Response
    {
        $coordinator->load('centers');
        $centers = EmasCenter::orderBy('center_name')->get(['id', 'center_name', 'center_code']);

        return Inertia::render('Emas/Coordinators/Edit', [
            'coordinator' => $coordinator,
            'centers' => $centers,
        ]);
    }

    /**
     * Update the coordinator
     */
    public function update(Request $request, EmasCoordinator $coordinator)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:emas_coordinators,email,' . $coordinator->id,
            'phone' => 'nullable|string|max:20',
            'id_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'centers' => 'required|array|min:1',
            'centers.*' => 'exists:emas_centers,id',
        ]);

        $coordinator->update([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'id_number' => $validated['id_number'] ?? null,
            'address' => $validated['address'] ?? null,
            'status' => $validated['status'],
        ]);

        // Sync centers
        $centersData = [];
        foreach ($validated['centers'] as $index => $centerId) {
            $centersData[$centerId] = ['role' => $index === 0 ? 'primary' : 'secondary'];
        }
        $coordinator->centers()->sync($centersData);

        return redirect()->route('emas.coordinators.index')
            ->with('success', 'Coordinator updated successfully!');
    }

    /**
     * Reset coordinator password
     */
    public function resetPassword(Request $request, EmasCoordinator $coordinator)
    {
        $validated = $request->validate([
            'new_password' => 'required|string|min:4',
        ]);

        $coordinator->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return back()->with('success', 'Password reset successfully!')
            ->with('new_password', $validated['new_password']);
    }

    /**
     * Delete coordinator
     */
    public function destroy(EmasCoordinator $coordinator)
    {
        $coordinator->delete();

        return redirect()->route('emas.coordinators.index')
            ->with('success', 'Coordinator deleted successfully!');
    }
}
