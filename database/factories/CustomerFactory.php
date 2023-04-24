<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $organization = Organization::first();

        return [
            'organization_id' => $organization->id,
            'name' => $this->faker->name,
            'nic_no' => $this->faker->randomNumber(9) . 'V',
            'address' => $this->faker->address,
            'telephone' => '0' . $this->faker->numberBetween(100000000, 999999999),
            'email' => $this->faker->email,
            'location' => $this->faker->city,
        ];
    }
}
