<?php

use App\Http\Resources\FarmerReport\FarmerReportResource;
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

    /**
     * @var Farmer
     */
    $farmer = Farmer::first();

    /**
     * @var Batch
     */
    $batch = Batch::factory()->create();
    $batch->farmers()->attach($farmer->id);

    /**
     * @var Farmland
     */
    $farmland = Farmland::factory()
        ->create([
            'batch_id' => $batch->id,
        ]);

    $farmland->farmers()->attach($farmer->id);
});

it('should retrieve submitted farmer reports', function () {
    /**
     * @var Farmer
     */
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

    $submittedFarmerReports = $farmer->farmerReports;

    $resource = FarmerReportResource::collection($submittedFarmerReports);

    $response = actingAs($farmer, 'api')
        ->getJson(route('api.reports', [
            $farmland->id,
        ]));

    $response
        ->assertExactJson(
            $resource->response()->getData(true)
        )
        ->assertStatus(200);
});
