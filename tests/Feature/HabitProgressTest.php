<?php

namespace Tests\Feature;

use App\Models\Completion;
use App\Models\Habit;
use App\Services\HabitProgress;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class HabitProgressTest extends TestCase
{
    use RefreshDatabase;

    #[TestWith([[], 0])]
    #[TestWith([['2026-11-04'], 1])]
    #[TestWith([['2026-11-03'], 1])]
    #[TestWith([['2026-11-02'], 0])]
    #[TestWith([['2026-11-04', '2026-11-03', '2026-11-02'], 3])]
    #[TestWith([['2026-11-03', '2026-11-02', '2026-10-31'], 2])]
    #[TestWith([['2026-11-04', '2026-11-02'], 1])]
    public function test_streak_is_contiguous_and_keeps_yesterday_until_end_of_today(array $dates, int $expected): void
    {
        $today = CarbonImmutable::parse('2026-11-04', 'Asia/Jakarta');
        $this->assertSame($expected, (new HabitProgress)->streak($dates, $today));
    }

    #[TestWith([0, 'Benih'])]
    #[TestWith([1, 'Tunas'])]
    #[TestWith([2, 'Tunas'])]
    #[TestWith([3, 'Tanaman kecil'])]
    #[TestWith([6, 'Tanaman kecil'])]
    #[TestWith([7, 'Tanaman berkembang'])]
    #[TestWith([13, 'Tanaman berkembang'])]
    #[TestWith([14, 'Tanaman dewasa'])]
    #[TestWith([100, 'Tanaman dewasa'])]
    public function test_growth_stages_have_consistent_boundaries(int $streak, string $name): void
    {
        $this->assertSame($name, (new HabitProgress)->stage($streak)['name']);
    }

    public function test_missed_day_resets_plant_but_preserves_total_history(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-11-04 17:00:00', 'UTC'));
        $habit = Habit::factory()->create(['created_at' => now('UTC')->subDays(30)]);
        foreach (['2026-11-01', '2026-11-02', '2026-11-03'] as $date) {
            Completion::factory()->for($habit)->create(['completed_on' => $date]);
        }
        $progress = new HabitProgress;
        $summary = $progress->summary($habit->load('completions'), $progress->today());
        $this->assertSame(0, $summary['streak']);
        $this->assertSame(3, $summary['total']);
        $this->assertSame('Benih', $summary['stage']['name']);
        $this->assertDatabaseCount('completions', 3);
    }

    public function test_weekly_progress_counts_only_eligible_days_and_calendar_starts_monday(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-11-04 12:00:00', 'Asia/Jakarta'));
        $habit = Habit::factory()->create(['created_at' => CarbonImmutable::parse('2026-11-03', 'Asia/Jakarta')->utc()]);
        Completion::factory()->for($habit)->create(['completed_on' => '2026-11-04']);
        $progress = new HabitProgress;
        $today = $progress->today();
        $habit->load('completions');
        $summary = $progress->summary($habit, $today);
        $this->assertSame(2, $summary['weeks'][0]['possible']);
        $this->assertSame(1, $summary['weeks'][0]['done']);
        $this->assertSame(0, $summary['weeks'][1]['possible']);
        $calendar = $progress->calendar($habit, $today, '2026-11');
        $this->assertNull($calendar['rows'][0][0]);
        $this->assertSame(1, $calendar['rows'][0][6]['day']);
    }

    public function test_contiguous_streak_crosses_month_year_and_leap_day(): void
    {
        $progress = new HabitProgress;
        $this->assertSame(3, $progress->streak(['2026-01-01', '2025-12-31', '2025-12-30'], CarbonImmutable::parse('2026-01-01', 'Asia/Jakarta')));
        $this->assertSame(3, $progress->streak(['2024-03-01', '2024-02-29', '2024-02-28'], CarbonImmutable::parse('2024-03-01', 'Asia/Jakarta')));
    }
}
