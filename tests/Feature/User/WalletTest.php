<?php

/****************************************************************************************
 * This project is not free and has business trademarks which belongs to Vorna Company. *
 *                                                                                      *
 * Team-lead of software engineers contact information:                                 *
 * Ali Khedmati | +989122958172 | Ali@khedmati.ir                                       *
 * Copyright (c)  2020-2022, Vorna Co.                                                  *
 ****************************************************************************************/

namespace Tests\Feature\User;

use App\Models\Deposit;
use App\Models\Wallet;
use App\Models\Withdraw;
use Illuminate\Http\UploadedFile;
use Storage;
use Tests\UserTestCase;

class WalletTest extends UserTestCase
{
    public function test_get_wallet()
    {
        $response = $this->get(route('user.wallets.index'));

        $response->assertSuccessful()
            ->assertJsonStructure([
                'wallets',
            ]);
    }

    public function test_get_available_wallet()
    {
        Wallet::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->get(route('user.wallets.index'));

        $response->assertSuccessful();

        $this->assertNotEmpty($this->user->wallets);
    }

    public function test_group_by_wallet()
    {
        Wallet::factory()->create([
            'currency' => 'IRR',
            'user_id' => $this->user->id,
        ]);

        Wallet::factory()->create([
            'currency' => 'USDT',
            'user_id' => $this->user->id,
        ]);

        $response = $this->get(route('user.wallets.grouped-by'));

        $response->assertSuccessful()
            ->assertJsonStructure([
                'wallets' => [
                    'IRR',
                    'USDT',
                ],
            ]);
    }

    public function test_get_transactions()
    {
        $this->user->deposits()->save(
            Deposit::factory()->make(),
        );

        $this->user->withdraws()->save(
            Withdraw::factory()->make(),
        );

        $response = $this->get(route('user.wallets.transactions'));

        $response->assertSuccessful()
            ->assertJsonStructure([
                'deposits',
                'withdraws'
            ]);
    }

    public function test_no_permission_deposit_request()
    {
        $response = $this->post(route('user.wallets.deposit-request'));

        $response->assertForbidden();
    }

    public function test_deposit_request()
    {
        $this->user->givePermissionTo('deposit');

        $response = $this->post(route('user.wallets.deposit-request'), [
            'blockchain' => 'BTC',
            'type' => 'crypto',
        ]);

        $response->assertSuccessful();
    }

    public function test_no_permission_manual_deposit_request()
    {
        $response = $this->post(route('user.wallets.manual-deposit-store'));

        $response->assertForbidden();
    }

    public function test_manual_deposit_request()
    {
        Storage::fake();

        $count = $this->user->manualDeposits()->count();

        $this->user->givePermissionTo('deposit');

        $response = $this->post(route('user.wallets.manual-deposit-store'), [
            'amount' => $this->faker->biasedNumberBetween(0, 1200),
            'currency' => 'IRT',
            'ref' => uniqid(),
            'picture' => UploadedFile::fake()->image('teset.png'),
        ]);

        $response->assertSuccessful();

        $this->assertEquals(++$count, $this->user->manualDeposits()->count());
    }

    public function test_no_permission_get_manual_deposits()
    {
        $response = $this->get(route('user.wallets.manual-deposits'));

        $response->assertForbidden();
    }

    public function test_get_manual_deposits()
    {
        $this->user->givePermissionTo('deposit');

        $response = $this->get(route('user.wallets.manual-deposits'));

        $response->assertSuccessful()->assertJsonStructure([
            'manual-deposits',
        ]);
    }

    public function test_no_permission_withdraw_request()
    {
        $response = $this->post(route('user.wallets.withdraw-request'));

        $response->assertForbidden();
    }

    public function test_withdraw_request()
    {
        $this->assertNull($this->user->wallets()->frozen()->first());

        $this->user->givePermissionTo('withdraw');

        $data = Withdraw::factory()->create()->toArray();

        $this->user->wallets()->create([
            'amount' => 12000,
            'type' => 1,
            'currency' => $data['currency'],
        ]);

        $destination_id = $this->user->bankAccounts()->create([
            'type' => 0,
            'card' => $data['destination'],
        ])->id;

        $response = $this->post(
            route('user.wallets.withdraw-request'),
            array_merge(
                $data,
                [
                    'blockchain' => 'BTC',
                    'type' => 'fiat',
                    'bankAccountId' => $destination_id,
                ]
            ),
        );

        $response->assertSuccessful();

        // after withdrawl request a frozen wallet for user should be submitted
        $this->assertNotNull($this->user->wallets()->frozen()->first());

        return $data;
    }

    public function test_no_permission_withdraw_cancel_request()
    {
        $response = $this->post(route('user.wallets.withdraw-cancel'));

        $response->assertForbidden();
    }

    public function test_withdraw_cancel_request()
    {
        $this->user->givePermissionTo('withdraw');

        $withdraw = Withdraw::factory()->create([
            'status' => 1,
            'user_id' => $this->user->id,
            'amount' => 12000,
        ]);

        $this->user->wallets()->create([
            'amount' => 12000,
            'type' => 1,
            'currency' => $withdraw->currency,
        ]);

        $response = $this->post(route('user.wallets.withdraw-cancel'), [
            'withdraw' => (string)$withdraw->hash,
        ]);

        $response->dump()->assertSuccessful();
    }

    public function test_withdraw_assets()
    {
        $this->user->givePermissionTo('withdraw');

        $response = $this->post(route('user.wallets.withdrawable-assets'), [
            'type' => 'crypto',
            'blockchain' => 'BTC',
        ]);

        $response->assertSuccessful()
            ->assertJsonStructure([
                'withdrawable-assets'
            ]);
    }
}
