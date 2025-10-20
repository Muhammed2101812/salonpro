<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\DocumentTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentTemplateTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_documentTemplates(): void
    {
        DocumentTemplate::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/document-templates');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_documentTemplate(): void
    {
        $data = DocumentTemplate::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/document-templates', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('document-templates', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_documentTemplate(): void
    {
        $documentTemplate = DocumentTemplate::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/document-templates/{$documentTemplate->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_documentTemplate(): void
    {
        $documentTemplate = DocumentTemplate::factory()->create();
        $updateData = DocumentTemplate::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/document-templates/{$documentTemplate->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_documentTemplate(): void
    {
        $documentTemplate = DocumentTemplate::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/document-templates/{$documentTemplate->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('document-templates', [
            'id' => $documentTemplate->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/document-templates');

        $response->assertUnauthorized();
    }
}
