<?php

use App\Http\Resources\Crop\SeedStageResource;
use App\Models\Batch\Batch;
use App\Models\Crop\Crop;
use App\Models\Crop\SeedStage;
use App\Models\Farmer\Farmer;
use App\Models\FarmerReport\FarmerReport;
use App\Models\Farmland\Farmland;
use Database\Seeders\Admin\AdminSeeder;
use Database\Seeders\Crop\CropSeeder;
use Database\Seeders\Farmer\FarmerSeeder;
use Database\Seeders\Farmland\FarmlandStatusSeeder;
use Database\Seeders\Farmland\FarmlandTypeSeeder;
use Database\Seeders\Farmland\WateringSystemSeeder;
use Database\Seeders\Site\SiteSeeder;
use Database\Seeders\User\RoleSeeder;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\seed;

beforeEach(function () {
    seed(SiteSeeder::class);
    seed(RoleSeeder::class);
    seed(FarmerSeeder::class);
    seed(AdminSeeder::class);
    seed(CropSeeder::class);
    seed(FarmlandTypeSeeder::class);
    seed(FarmlandStatusSeeder::class);
    seed(WateringSystemSeeder::class);

    $farmer = Farmer::first();

    $batch = Batch::factory()->create();
    $batch->farmers()->attach($farmer->id);

    $farmland = Farmland::factory()
        ->create([
            'batch_id' => $batch->id,
        ]);

    $farmland->farmers()->attach($farmer->id);
});

it('should retrieve no current seed stage if no submitted reports', function () {
    $farmer = Farmer::first();
    $farmland = Farmland::first();

    $response = actingAs($farmer, 'api')
        ->postJson(
            route('api.seeds.current-stage'),
            [
                'farmlandId' => $farmland->id,
            ]
        );

    $response
        ->assertExactJson([])
        ->assertStatus(200);
});

it('should retrieve current seed stage if submitted a report', function () {
    $farmer = Farmer::first();
    $farmland = Farmland::first();
    $seedStage = SeedStage::initialStage();
    $crop = Crop::first();

    FarmerReport::factory()->create([
        'reported_by' => $farmer,
        'farmland_id' => $farmland,
        'seed_stage_id' => $seedStage,
        'crop_id' => $crop,
    ]);

    $response = actingAs($farmer, 'api')
        ->postJson(
            route('api.seeds.current-stage'),
            [
                'farmlandId' => $farmland->id,
            ]
        );

    $resource = SeedStageResource::make($seedStage);

    $response
        ->assertExactJson(
            $resource->response()->getData(true)
        )
        ->assertStatus(200);
});

it('should return error if retrieving stage from non-existent farmland', function () {
    $farmer = Farmer::first();

    $response = actingAs($farmer, 'api')
        ->postJson(
            route('api.seeds.current-stage'),
            [
                'farmlandId' => -1,
            ]
        );

    $response
        ->assertJson([
            'message' => 'The selected farmland id is invalid.',
        ])
        ->assertStatus(422);
});

it('should return error on non-belonging farmland', function () {
    $farmer = Farmer::first();
    $batch = Batch::first();

    $nonBelongingFarmland = Farmland::factory()
        ->create([
            'batch_id' => $batch->id,
        ]);

    $response = actingAs($farmer, 'api')
        ->postJson(
            route('api.seeds.current-stage'),
            [
                'farmlandId' => $nonBelongingFarmland->id,
            ]
        );

    $response
        ->assertJson([
            'message' => 'Farmer does not belong to farmland',
        ])
        ->assertStatus(500);
});