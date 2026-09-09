<?php

namespace Database\Factories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CargoDetailFactory extends Factory
{
    public function definition()
    {
        return [
            'veh_reg_no' => strtoupper($this->faker->bothify('??##??####')),
            'cargo_unit_serial_no' => strtoupper($this->faker->bothify('????#######')),
            'dispatch_id' => 'DISP-' . Str::upper(Str::random(8)),
            'group_id' => Group::factory(),
            'date_transit' => now()->toDateString(),
            'pending_servey' => 0,
        ];
    }
}
