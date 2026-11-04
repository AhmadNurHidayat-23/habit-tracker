<?php

namespace App\Services;

use App\Models\Habit;
use Carbon\CarbonImmutable;

class HabitProgress
{
    public const STAGES = [
        ['name' => 'Benih', 'image' => 'seed', 'range' => '0 hari'],
        ['name' => 'Tunas', 'image' => 'sprout', 'range' => '1–2 hari'],
        ['name' => 'Tanaman kecil', 'image' => 'small', 'range' => '3–6 hari'],
        ['name' => 'Tanaman berkembang', 'image' => 'growing', 'range' => '7–13 hari'],
        ['name' => 'Tanaman dewasa', 'image' => 'mature', 'range' => '14+ hari'],
    ];

    public function today(): CarbonImmutable
    {
        return CarbonImmutable::now('Asia/Jakarta')->startOfDay();
    }

    public function dates(Habit $habit, CarbonImmutable $today): array
    {
        return $habit->completions->pluck('completed_on')->filter(fn ($date) => $date <= $today->toDateString())->unique()->values()->all();
    }

    public function streak(array $dates, CarbonImmutable $today): int
    {
        $completed = array_fill_keys($dates, true);
        $day = $today;
        if (! isset($completed[$day->toDateString()])) {
            $day = $day->subDay();
        }
        $streak = 0;
        while (isset($completed[$day->toDateString()])) {
            $streak++;
            $day = $day->subDay();
        }

        return $streak;
    }

    public function stage(int $streak): array
    {
        $index = match (true) {
            $streak >= 14 => 4,$streak >= 7 => 3,$streak >= 3 => 2,$streak >= 1 => 1,default => 0
        };

        return ['index' => $index, ...self::STAGES[$index]];
    }

    public function summary(Habit $habit, CarbonImmutable $today): array
    {
        $dates = $this->dates($habit, $today);
        $streak = $this->streak($dates, $today);

        return ['done' => in_array($today->toDateString(), $dates, true), 'streak' => $streak, 'total' => count($dates), 'stage' => $this->stage($streak), 'weeks' => $this->weeks($habit, $dates, $today)];
    }

    public function weeks(Habit $habit, array $dates, CarbonImmutable $today): array
    {
        $created = CarbonImmutable::instance($habit->created_at)->timezone('Asia/Jakarta')->startOfDay();
        $weeks = [];
        for ($offset = 0; $offset < 4; $offset++) {
            $start = $today->startOfWeek(1)->subWeeks($offset);
            $end = $start->addDays(6);
            $eligibleStart = $start->max($created);
            $eligibleEnd = $end->min($today);
            $possible = $eligibleEnd->lessThan($eligibleStart) ? 0 : (int) $eligibleStart->diffInDays($eligibleEnd) + 1;
            $count = count(array_filter($dates, fn ($d) => $d >= $eligibleStart->toDateString() && $d <= $eligibleEnd->toDateString()));
            $weeks[] = ['label' => $offset === 0 ? 'Minggu ini' : ($offset === 1 ? 'Minggu lalu' : $offset.' minggu lalu'), 'start' => $start->locale('id')->translatedFormat('d M'), 'end' => $end->locale('id')->translatedFormat('d M'), 'done' => $count, 'possible' => $possible];
        }

        return $weeks;
    }

    public function calendar(Habit $habit, CarbonImmutable $today, string $month): array
    {
        $start = CarbonImmutable::createFromFormat('!Y-m', $month, 'Asia/Jakarta');
        $dates = array_fill_keys($this->dates($habit, $today), true);
        $created = CarbonImmutable::instance($habit->created_at)->timezone('Asia/Jakarta')->toDateString();
        $cells = array_fill(0, $start->dayOfWeekIso - 1, null);
        for ($day = 1; $day <= $start->daysInMonth; $day++) {
            $date = $start->setDay($day);
            $key = $date->toDateString();
            $state = isset($dates[$key]) ? 'done' : ($key > $today->toDateString() || $key < $created ? 'unavailable' : ($key === $today->toDateString() ? 'today' : 'missed'));
            $label = ['done' => 'Selesai', 'unavailable' => 'Belum tersedia', 'today' => 'Hari ini, belum selesai', 'missed' => 'Tidak selesai'][$state];
            $cells[] = ['day' => $day, 'date' => $key, 'state' => $state, 'label' => $date->locale('id')->translatedFormat('d F Y').': '.$label];
        }
        while (count($cells) % 7) {
            $cells[] = null;
        }

        return ['rows' => array_chunk($cells, 7), 'label' => $start->locale('id')->translatedFormat('F Y'), 'previous' => $start->subMonth()->format('Y-m'), 'next' => $start->isSameMonth($today) ? null : $start->addMonth()->format('Y-m')];
    }
}
