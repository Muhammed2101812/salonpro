<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Survey;
use App\Repositories\Contracts\SurveyRepositoryInterface;
use App\Services\SurveyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SurveyServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SurveyService $service;
    protected SurveyRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(SurveyRepositoryInterface::class);
        $this->service = new SurveyService($this->repository);
    }

    public function test_can_get_all_surveys(): void
    {
        Survey::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_surveys(): void
    {
        Survey::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_survey(): void
    {
        $data = Survey::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(Survey::class, $result);
        $this->assertDatabaseHas('surveys', ['id' => $result->id]);
    }

    public function test_can_update_survey(): void
    {
        $survey = Survey::factory()->create();
        $updateData = Survey::factory()->make()->toArray();

        $result = $this->service->update($survey->id, $updateData);

        $this->assertInstanceOf(Survey::class, $result);
    }

    public function test_can_delete_survey(): void
    {
        $survey = Survey::factory()->create();

        $result = $this->service->delete($survey->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('surveys', ['id' => $survey->id]);
    }

    public function test_can_find_survey_by_id(): void
    {
        $survey = Survey::factory()->create();

        $result = $this->service->findById($survey->id);

        $this->assertInstanceOf(Survey::class, $result);
        $this->assertEquals($survey->id, $result->id);
    }
}
