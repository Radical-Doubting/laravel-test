<?php

use App\Models\Farmer\Farmer;
use App\Models\Farmer\FarmerPreference;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\seed;

beforeEach(function () {
    seed();
});

it('should update language preference', function () {
    /**
     * @var Farmer
     */
    $farmer = Farmer::first();

    $response = actingAs($farmer, 'api')
        ->patchJson('/api/farmers/language', [
            'language' => 'fil_PH',
        ]);

    $response
        ->assertStatus(200)
        ->assertJson([
            'message' => 'Successfully updated language preference',
        ]);

    /**
     * @var FarmerPreference
     */
    $preference = $farmer->profile->preference;

    expect($preference->language)->toBe('fil_PH');
});