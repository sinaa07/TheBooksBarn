<?php

namespace Database\Factories;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */

 class UserFactory extends Factory
 {
     protected $model = User::class;
 
     protected static ?string $password;
 
     public function definition(): array
     {
         return [
             'username' => $this->faker->unique()->userName(), // unique username
             'email' => $this->faker->unique()->safeEmail(),
             'email_verified_at' => now(),
             'password' => static::$password ??= Hash::make('password'),
             'first_name' => $this->faker->firstName(),
             'last_name' => $this->faker->lastName(),
             'phone' => $this->faker->optional()->numerify('9#########'),
             'is_active' => true,
         ];
     }
 }
