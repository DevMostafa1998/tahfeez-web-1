<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name'        => fake()->name(),
            'password'         => static::$password ??= \Illuminate\Support\Facades\Hash::make('123456'),
            'id_number'        => fake()->unique()->numerify('#########'),
            'date_of_birth'    => fake()->date(),
            'birth_place'      => fake()->city(),
            'gender'           => fake()->randomElement(['male', 'female']),
            'phone_number'     => fake()->phoneNumber(),
            'wallet_number'    => fake()->numerify('059#######'),
            'whatsapp_number'  => fake()->phoneNumber(),
            'qualification'    => 'Bachelor',
            'specialization'   => 'Islamic Studies',
            'parts_memorized'  => fake()->numberBetween(1, 30),
            'mosque_name'      => 'Al-Rahman Mosque',
            'address'          => fake()->address(),
            'is_displaced'     => false,
            'is_admin'         => false,
            'category_id'      => null,
            'creation_by'      => null,
            'updated_by'       => null,
            'deleted_by'       => null,
        ];
    }

}
