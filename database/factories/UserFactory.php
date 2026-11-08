<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * Columns match the `Users` table defined in the schema migration:
     * full_name, email, password, phone_number, ID, role_id, section_id.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'full_name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'), // default password: "password"
            'phone_number' => '07' . $this->faker->unique()->numerify('########'), // e.g. 0771234567
            'ID' => strtoupper($this->faker->unique()->bothify('##???###')),
            'role_id' => 4, // Student by default
            'section_id' => null,
        ];
    }
}
