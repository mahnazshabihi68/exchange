<?php

/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace Database\Factories;

use App\Models\Withdraw;
use Illuminate\Database\Eloquent\Factories\Factory;

class WithdrawFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Withdraw::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'amount' => $this->faker->biasedNumberBetween(0, 1000),
            'currency' => 'BTC',
            'ref' => hash('md5', 'verified'),
            'hash' => hash('md5', $this->faker->password()),
            'admin_id' => 1,
            'user_id' => 1,
            'destination' => $this->faker->creditCardNumber(),
            'status' => rand(1, 3),
        ];
    }
}
