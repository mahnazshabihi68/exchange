<?php

/****************************************************************************************
 * This project is not free and has business trademarks which belongs to Vorna Company. *
 *                                                                                      *
 * Team-lead of software engineers contact information:                                 *
 * Ali Khedmati | +989122958172 | Ali@khedmati.ir                                       *
 * Copyright (c)  2020-2022, Vorna Co.                                                  *
 ****************************************************************************************/

namespace Tests\Feature\User;

use App\Models\Order;
use Arr;
use Tests\UserTestCase;

class ExchangeTest extends UserTestCase
{
    public function test_can_submit_order()
    {
        $this->user->givePermissionTo('trade');
        $count = Order::count();

        $data = [
            'symbol' => 'ETH',
            'type' => 'MARKET',
            'side' => 'BUY',
            'quantity' => 30,
            'stop_price' => 3502000,
            'is_virtual' => false,
        ];

        $response = $this->post(route('user.exchange.submit-order'), $data);

        $response->assertSuccessful();

        $this->assertNotSame($count, Order::count());
    }

    public function test_no_permission_submit_order()
    {
        $count = Order::count();

        $data = [
            'symbol' => 'ETH',
            'type' => 'MARKET',
            'side' => 'BUY',
            'quantity' => 30,
            'stop_price' => 3502000,
            'is_virtual' => false,
        ];

        $response = $this->post(route('user.exchange.submit-order'), $data);

        $response->assertForbidden();

        $this->assertSame($count, Order::count());
    }

    public function test_can_cancel_order()
    {
        $response = $this->delete(route('user.exchange.cancel-order', $order = Order::factory()->create([
            'user_id' => $this->user->id,
        ])));

        $response->assertSuccessful();

        $this->assertSame($order->fresh()->status, 'PENDING_CANCELED');
    }

    public function test_can_cancell_all_orders()
    {
        Order::factory()->count(5)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->delete(route('user.exchange.cancel-all-orders'));

        $response->assertSuccessful();

        $this->assertEmpty(Order::whereStatus('PENDING')->get()->toArray());
    }
}
