<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'label',
        'sort_order',
    ];

    public function items()
    {
        return $this->hasMany(InspectionChecklistItem::class, 'inspection_category_id');
    }

    public static function definitions(): array
    {
        return [
            ['key' => 'bodywork_check', 'label' => 'Bodywork check', 'sort_order' => 1],
            ['key' => 'traffic_lights_check', 'label' => 'Traffic lights check', 'sort_order' => 2],
            ['key' => 'emergency_lights_check', 'label' => 'Emergency lights check', 'sort_order' => 3],
            ['key' => 'air_pressure_loss_check', 'label' => 'Air pressure system loss check', 'sort_order' => 4],
            ['key' => 'windshield_check', 'label' => 'Windshield check', 'sort_order' => 5],
            ['key' => 'mirrors_check', 'label' => 'Mirrors check', 'sort_order' => 6],
            ['key' => 'tires_check', 'label' => 'Tires check', 'sort_order' => 7],
            ['key' => 'steps_check', 'label' => 'Steps check', 'sort_order' => 8],
            ['key' => 'alternator_load', 'label' => 'Alternator load', 'sort_order' => 9],
            ['key' => 'batteries_visual_inspection', 'label' => 'Batteries (visual inspection)', 'sort_order' => 10],
            ['key' => 'tools_secured_top', 'label' => 'Tools secured on top', 'sort_order' => 11],
            ['key' => 'fuel_level_over_three_quarters', 'label' => 'Fuel level over 3/4', 'sort_order' => 12],
            ['key' => 'engine_oil_level', 'label' => 'Engine oil level', 'sort_order' => 13],
            ['key' => 'engine_coolant_level', 'label' => 'Engine coolant level', 'sort_order' => 14],
            ['key' => 'steering_oil_level', 'label' => 'Steering oil level', 'sort_order' => 15],
            ['key' => 'wiper_blades_condition', 'label' => 'Wiper blades condition', 'sort_order' => 16],
            ['key' => 'adblue_level_over_25', 'label' => 'AdBlue level over 25%', 'sort_order' => 17],
        ];
    }
}
