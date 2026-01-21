<?php

namespace Database\Seeders;

use App\Models\InspectionCategory;
use Illuminate\Database\Seeder;

class InspectionCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (InspectionCategory::definitions() as $definition) {
            InspectionCategory::updateOrCreate(
                ['key' => $definition['key']],
                [
                    'label' => $definition['label'],
                    'sort_order' => $definition['sort_order'],
                ]
            );
        }
    }
}
