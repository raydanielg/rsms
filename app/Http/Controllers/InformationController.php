<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Policy;
use App\Models\Update;
use App\Models\UpdateReaction;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class InformationController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'announcements');
        $ann = Announcement::orderByDesc('published_at')->orderByDesc('id')->paginate(6, ['*'], 'ann_page');
        $pol = Policy::orderByDesc('published_at')->orderByDesc('id')->paginate(6, ['*'], 'pol_page');
        $upd = Update::withCount([
                'reactions as accepts_count' => function($q){ $q->where('reaction','accepted'); },
                'reactions as rejects_count' => function($q){ $q->where('reaction','rejected'); },
            ])
            ->orderByDesc('published_at')->orderByDesc('id')
            ->paginate(6, ['*'], 'upd_page');
        return Inertia::render('Information/Index', [
            'tab' => $tab,
            'announcements' => $ann,
            'policies' => $pol,
            'updates' => $upd,
        ]);
    }

    public function storeFeedback(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'contact' => ['nullable','string','max:255'],
            'message' => ['required','string','max:2000'],
        ]);
        Feedback::create($data);
        return Redirect::back()->with('success', 'Thank you for your feedback.');
    }

    public function reactUpdate(Request $request, int $id)
    {
        $request->validate(['reaction' => ['required','in:accepted,rejected']]);
        $update = Update::findOrFail($id);
        UpdateReaction::create([
            'update_id' => $update->id,
            'user_id' => optional($request->user())->id,
            'reaction' => $request->input('reaction'),
        ]);
        return Redirect::back()->with('success', 'Reaction recorded');
    }
}
