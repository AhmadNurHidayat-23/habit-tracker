<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompletionRequest;
use App\Models\Habit;
use Illuminate\Http\RedirectResponse;

class CompletionController extends Controller
{
    public function store(CompletionRequest $request, Habit $habit): RedirectResponse
    {
        $date = $request->validated('date');
        $habit->completions()->insertOrIgnore(['habit_id' => $habit->id, 'completed_on' => $date, 'created_at' => now('UTC'), 'updated_at' => now('UTC')]);

        return back()->with('status', 'Kebiasaan ditandai selesai untuk hari ini.');
    }

    public function destroy(CompletionRequest $request, Habit $habit): RedirectResponse
    {
        $habit->completions()->where('completed_on', $request->validated('date'))->delete();

        return back()->with('status', 'Tanda selesai hari ini dibatalkan.');
    }
}
