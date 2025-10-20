<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\JournalEntryLine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JournalEntryLineTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_journalEntryLines(): void
    {
        JournalEntryLine::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/journal-entry-lines');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_journalEntryLine(): void
    {
        $data = JournalEntryLine::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/journal-entry-lines', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('journal-entry-lines', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_journalEntryLine(): void
    {
        $journalEntryLine = JournalEntryLine::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/journal-entry-lines/{$journalEntryLine->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_journalEntryLine(): void
    {
        $journalEntryLine = JournalEntryLine::factory()->create();
        $updateData = JournalEntryLine::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/journal-entry-lines/{$journalEntryLine->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_journalEntryLine(): void
    {
        $journalEntryLine = JournalEntryLine::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/journal-entry-lines/{$journalEntryLine->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('journal-entry-lines', [
            'id' => $journalEntryLine->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/journal-entry-lines');

        $response->assertUnauthorized();
    }
}
