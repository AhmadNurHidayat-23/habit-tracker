<?php

namespace Tests\Feature;

use App\Models\Completion;
use App\Models\Habit;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class HabitTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_home_has_no_dummy_habits_or_completions(): void
    {
        $this->get('/')->assertOk()->assertSee('Bangun Kebiasaan Baik.')->assertSee('Belum ada aktivitas yang tercatat.')->assertSee('Tahap tanaman: Benih');
        $this->assertDatabaseCount('habits', 0);
        $this->assertDatabaseCount('completions', 0);
    }

    public function test_creates_updates_reads_and_deletes_habit_with_history(): void
    {
        $this->post(route('habits.store'), ['name' => 'Membaca', 'description' => '15 menit setiap hari'])->assertRedirect();
        $habit = Habit::firstOrFail();
        $this->get(route('habits.show', $habit))->assertOk()->assertSee('Membaca')->assertSee('15 menit setiap hari');
        $this->get(route('habits.edit', $habit))->assertOk();
        $this->put(route('habits.update', $habit), ['name' => 'Membaca buku', 'description' => '10 halaman'])->assertRedirect(route('habits.show', $habit));
        $this->assertDatabaseHas('habits', ['id' => $habit->id, 'name' => 'Membaca buku', 'description' => '10 halaman']);
        Completion::factory()->for($habit)->create();
        $this->delete(route('habits.destroy', $habit))->assertRedirect(route('habits.index'));
        $this->assertModelMissing($habit);
        $this->assertDatabaseCount('completions', 0);
    }

    public function test_validates_name_description_and_does_not_mutate_on_failure(): void
    {
        $this->post(route('habits.store'), ['name' => ''])->assertSessionHasErrors('name');
        $this->post(route('habits.store'), ['name' => str_repeat('a', 101), 'description' => str_repeat('b', 501)])->assertSessionHasErrors(['name', 'description']);
        $this->assertDatabaseCount('habits', 0);
        $habit = Habit::factory()->create();
        $this->put(route('habits.update', $habit), ['name' => ['bad']])->assertSessionHasErrors('name');
        $this->assertSame($habit->name, $habit->fresh()->name);
    }

    public function test_mark_is_idempotent_and_can_be_cancelled_only_today(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-11-04 12:00:00', 'Asia/Jakarta'));
        $habit = Habit::factory()->create();
        Completion::factory()->for($habit)->create(['completed_on' => '2026-11-03']);
        $url = route('completions.store', $habit);
        $this->put($url, ['date' => '2026-11-04'])->assertRedirect();
        $this->put($url, ['date' => '2026-11-04'])->assertRedirect();
        $this->assertSame(1, $habit->completions()->where('completed_on', '2026-11-04')->count());
        $this->get('/')->assertOk()->assertSee('Selesai hari ini')->assertSee('2 hari berturut-turut');
        $this->delete($url, ['date' => '2026-11-04'])->assertRedirect();
        $this->delete($url, ['date' => '2026-11-04'])->assertRedirect();
        $this->assertDatabaseMissing('completions', ['habit_id' => $habit->id, 'completed_on' => '2026-11-04']);
        $this->assertDatabaseHas('completions', ['habit_id' => $habit->id, 'completed_on' => '2026-11-03']);
    }

    #[TestWith(['2026-11-03'])]
    #[TestWith(['2026-11-05'])]
    #[TestWith(['invalid'])]
    public function test_rejects_backdating_future_dates_and_invalid_dates(string $date): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-11-04 12:00:00', 'Asia/Jakarta'));
        $habit = Habit::factory()->create();
        Completion::factory()->for($habit)->create(['completed_on' => '2026-11-03']);
        $this->put(route('completions.store', $habit), ['date' => $date])->assertSessionHasErrors('date');
        $this->delete(route('completions.destroy', $habit), ['date' => $date])->assertSessionHasErrors('date');
        $this->assertDatabaseCount('completions', 1);
    }

    public function test_midnight_jakarta_rejects_stale_form_and_starts_new_day(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-11-04 16:59:59', 'UTC'));
        $habit = Habit::factory()->create();
        $this->put(route('completions.store', $habit), ['date' => '2026-11-04'])->assertRedirect();
        $this->travelTo(CarbonImmutable::parse('2026-11-04 17:00:00', 'UTC'));
        $this->put(route('completions.store', $habit), ['date' => '2026-11-04'])->assertSessionHasErrors('date');
        $this->get('/')->assertOk()->assertSee('Belum selesai hari ini')->assertSee('1 hari berturut-turut');
        $this->put(route('completions.store', $habit), ['date' => '2026-11-05'])->assertRedirect();
        $this->assertDatabaseCount('completions', 2);
        $this->get('/')->assertOk()->assertSee('2 hari berturut-turut');
    }

    public function test_database_constraint_rejects_duplicate_habit_day(): void
    {
        $habit = Habit::factory()->create();
        Completion::factory()->for($habit)->create(['completed_on' => '2026-11-04']);
        $this->expectException(QueryException::class);
        Completion::factory()->for($habit)->create(['completed_on' => '2026-11-04']);
    }

    public function test_same_day_completion_is_independent_for_each_habit(): void
    {
        $habits = Habit::factory()->count(2)->create();
        $date = now('Asia/Jakarta')->toDateString();
        foreach ($habits as $habit) {
            $this->put(route('completions.store', $habit), ['date' => $date])->assertRedirect();
        }
        $this->assertDatabaseCount('completions', 2);
    }

    public function test_escapes_names_and_descriptions(): void
    {
        $habit = Habit::factory()->create(['name' => '<script>alert(1)</script>', 'description' => '<img src=x onerror=alert(1)>']);
        $this->get('/')->assertOk()->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false)->assertDontSee('<img src=x', false);
        $this->get(route('habits.show', $habit))->assertOk()->assertSee('&lt;img', false);
    }

    public function test_calendar_and_plant_match_completion_history(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-11-04 12:00:00', 'Asia/Jakarta'));
        $habit = Habit::factory()->create(['created_at' => now('UTC')->subDays(5)]);
        foreach (['2026-11-02', '2026-11-03', '2026-11-04'] as $date) {
            Completion::factory()->for($habit)->create(['completed_on' => $date]);
        }
        $this->get(route('habits.show', $habit))->assertOk()->assertSee('Tahap tanaman: Tanaman kecil')->assertSee('04 November 2026: Selesai')->assertSee('05 November 2026: Belum tersedia')->assertSee('3 / 3 hari');
        $this->get(route('habits.show', [$habit, 'month' => '2026-10']))->assertOk()->assertSee('Oktober 2026');
        $this->getJson(route('habits.show', [$habit, 'month' => '2026-13']))->assertUnprocessable();
    }

    public function test_nonlocal_access_and_host_rebinding_are_forbidden(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.5'])->get('/')->assertForbidden();
        $this->withServerVariables(['REMOTE_ADDR' => '127.0.0.1'])->get('http://attacker.example/')->assertForbidden();
    }

    public function test_missing_habit_returns_404(): void
    {
        $this->get('/habits/999')->assertNotFound();
        $this->put('/habits/999/completion', ['date' => now('Asia/Jakarta')->toDateString()])->assertNotFound();
    }
}
