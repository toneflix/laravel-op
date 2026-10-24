<?php

namespace Tests\Feature;

use App\Enums\HttpStatus;
use App\Models\Transaction;
use App\Models\User;
use App\Traits\ControllerCanExtend;
use Illuminate\Foundation\Http\FormRequest;
use Tests\TestCase;

class WalletPaymentTest extends TestCase
{
    use ControllerCanExtend;

    public function testUserCanInitializeAndVerifyWalletPayment(): void
    {
        $request = new FormRequest();
        $user = User::factory()->create();
        $user->deposit('wallet_main', 100, 'seed balance');

        $pay = $this->payWith('wallet')->makePayment($request, $user, 30);
        $this->assertEquals(HttpStatus::OK, $pay['status_code'] ?? null);

        $this->assertDatabaseHas(Transaction::class, [
            'reference' => $pay['reference'],
            'method' => 'wallet',
            'status' => 'pending',
        ]);

        $verify = $this->payWith('wallet')->verifyPayment($request, $user, $pay['reference']);
        $this->assertEquals(HttpStatus::OK, $verify['status_code'] ?? null);

        $this->assertDatabaseHas(Transaction::class, [
            'reference' => $pay['reference'],
            'status' => 'complete',
        ]);

        $user->refresh();
        $this->assertEquals(70, $user->walletBalance);
    }

    public function testWalletPaymentFailsWithInsufficientBalance(): void
    {
        $request = new FormRequest();
        $user = User::factory()->create();
        $user->deposit('wallet_main', 10, 'seed balance');

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        $this->payWith('wallet')->makePayment($request, $user, 1000);
    }
}
