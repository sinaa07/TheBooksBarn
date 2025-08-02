<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // creates and assigns a new user
            'name' => $this->faker->name(),
            'phone' => $this->faker->numerify('9#########'), // 10-digit Indian-style number
            'address_line_1' => $this->faker->streetAddress(),
            'address_line_2' => $this->faker->optional()->secondaryAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'postal_code' => $this->faker->postcode(),
            'country' => 'India',
            'address_type' => $this->faker->randomElement(['billing', 'shipping', 'both']),
            'is_default' => $this->faker->boolean(20), // 20% chance to be default
        ];
    }
}