<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\SurveyResponse;
use App\Repositories\Contracts\SurveyResponseRepositoryInterface;
use App\Services\SurveyResponseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SurveyResponseServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SurveyResponseService $service;
    protected SurveyResponseRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(SurveyResponseRepositoryInterface::class);
        $this->service = new SurveyResponseService($this->repository);
    }

    public function test_can_get_all_surveyResponses(): void
    {
        SurveyResponse::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_surveyResponses(): void
    {
        SurveyResponse::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_surveyResponse(): void
    {
        $data = SurveyResponse::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(SurveyResponse::class, $result);
        $this->assertDatabaseHas('surveyResponses', ['id' => $result->id]);
    }

    public function test_can_update_surveyResponse(): void
    {
        $surveyResponse = SurveyResponse::factory()->create();
        $updateData = SurveyResponse::factory()->make()->toArray();

        $result = $this->service->update($surveyResponse->id, $updateData);

        $this->assertInstanceOf(SurveyResponse::class, $result);
    }

    public function test_can_delete_surveyResponse(): void
    {
        $surveyResponse = SurveyResponse::factory()->create();

        $result = $this->service->delete($surveyResponse->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('surveyResponses', ['id' => $surveyResponse->id]);
    }

    public function test_can_find_surveyResponse_by_id(): void
    {
        $surveyResponse = SurveyResponse::factory()->create();

        $result = $this->service->findById($surveyResponse->id);

        $this->assertInstanceOf(SurveyResponse::class, $result);
        $this->assertEquals($surveyResponse->id, $result->id);
    }
}
