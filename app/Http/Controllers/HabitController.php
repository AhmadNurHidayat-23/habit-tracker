<?php

namespace App\Http\Controllers;

use App\Http\Requests\HabitRequest;
use App\Models\Habit;
use App\Services\HabitProgress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class HabitController extends Controller
{
    public function __construct(private HabitProgress $progress) {}

    public function index(): View
    {
        $today = $this->progress->today();
        $habits = Habit::with('completions')->latest()->get();
        $summaries = $habits->mapWithKeys(fn ($habit) => [$habit->id => $this->progress->summary($habit, $today)]);
        $done = $summaries->where('done', true)->count();
        $best = (int) ($summaries->max('streak') ?? 0);

        return view('habits.index', compact('habits', 'summaries', 'today', 'done', 'best') + ['stage' => $this->progress->stage($best)]);
    }

    public function create(): View
    {
        return view('habits.form', ['habit' => new Habit]);
    }

    public function store(HabitRequest $request): RedirectResponse
    {
        $habit = Habit::create($request->validated());

        return to_route('habits.show', $habit)->with('status', 'Kebiasaan berhasil ditambahkan. Mulai langkah pertama hari ini.');
    }

    public function show(Request $request, Habit $habit): View
    {
        $today = $this->progress->today();
        $validation = Validator::make(
            ['month' => $request->query('month', $today->format('Y-m'))],
            ['month' => ['required', 'date_format:Y-m', 'regex:/\A[1-9][0-9]{3}-(0[1-9]|1[0-2])\z/D', 'before_or_equal:'.$today->format('Y-m')]],
        );
        abort_if($validation->fails(), 422, 'Bulan kalender tidak valid.');
        $habit->load('completions');

        return view('habits.show', ['habit' => $habit, 'today' => $today, 'summary' => $this->progress->summary($habit, $today), 'calendar' => $this->progress->calendar($habit, $today, $request->input('month', $today->format('Y-m')))]);
    }

    public function edit(Habit $habit): View
    {
        return view('habits.form', compact('habit'));
    }

    public function update(HabitRequest $request, Habit $habit): RedirectResponse
    {
        $habit->update($request->validated());

        return to_route('habits.show', $habit)->with('status', 'Kebiasaan berhasil diperbarui.');
    }

    public function destroy(Habit $habit): RedirectResponse
    {
        $habit->delete();

        return to_route('habits.index')->with('status', 'Kebiasaan dan riwayatnya berhasil dihapus.');
    }
}
