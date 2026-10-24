<?php

namespace App\Services\Payment;

use App\Enums\HttpStatus;
use App\Models\TempUser;
use App\Models\User;
use Illuminate\Http\Request;
use Toneflix\LaravelPayPocket\Exceptions\InsufficientBalanceException;
use Toneflix\LaravelPayPocket\Models\Wallet;
use Toneflix\LaravelPayPocket\Models\WalletsLog;

final class WalletProcessor implements PaymentInterface
{
    protected $request;

    protected $user;

    /**
     * WalletProcessor constructor.
     *
     * Wallet payments require a registered User account — a TempUser has no
     * wallets of its own. Passing one through will fail naturally the moment
     * a wallet method (pay/deposit/walletBalance) is called on it.
     */
    public function __construct(Request $request, User|TempUser|null $user = null)
    {
        $this->request = $request;
        $this->user = $user;
    }

    /**
     * Pay for a service/order out of the user's wallets.
     *
     * @param  int|float  $amount     The amount to initialize the transaction with
     * @param  callable  $successCallback     The callback function to call when the transaction is initialized
     * @param  callable  $errorCallback    The callback function to call when an error occurs
     * @param  bool  $respond     Wether to return a response or not.
     * @return array|object|\App\Services\CustomObject
     */
    public function intent(
        int|float $amount,
        ?callable $successCallback = null,
        ?callable $errorCallback = null,
        ?bool $respond = true
    ) {
        // Wallet payments are deducted immediately; there is no external
        // redirect/callback step like Paystack or Stripe.
        try {
            $logs = $this->user->pay($amount, [], 'Payment for service/order.');
            $tranx = $logs->first();

            $intent = new \App\Services\CustomObject([
                'id' => $tranx->id,
                'code' => HttpStatus::OK,
                'status_code' => HttpStatus::OK,
                'amount' => $amount,
                'status' => 'success',
                'message' => __('Transaction Complete.'),
                'balance' => $this->user->walletBalance,
                'reference' => $tranx->reference ?: $tranx->id,
            ]);

            // Call the callback function
            if ($successCallback && $respond) {
                $intent = $successCallback($intent['reference'], $intent, $amount, $intent['message'], $intent['code']);
            }

            if ($respond) {
                return $intent;
            }
        } catch (InsufficientBalanceException $e) {
            $error = new \App\Services\CustomObject([
                'code' => HttpStatus::BAD_REQUEST,
                'status_code' => HttpStatus::BAD_REQUEST,
                'error' => HttpStatus::BAD_REQUEST->value,
                'status' => 'error',
                'message' => __('You do not have sufficient funds to complete this transaction.'),
            ]);

            if ($errorCallback) {
                $error = $errorCallback($error, $e);
            }

            if ($respond) {
                return $error;
            }
        }
    }

    /**
     * Verify a payment intent
     *
     * @param  callable  $errorCallback   The callback function to call when an error occurs
     * @param  bool  $respond     Wether to return a response or not.
     * @return array|object|\App\Services\CustomObject
     */
    public function verify(
        string $reference,
        ?callable $successCallback = null,
        ?callable $errorCallback = null,
        ?bool $respond = true,
    ) {
        // Try to find the payment log, scoped to this user's own wallets
        $tranx = WalletsLog::where('reference', $reference)
            ->whereHasMorph('loggable', [Wallet::class], function ($query) {
                $query->where('owner_id', $this->user->id)->where('owner_type', $this->user->getMorphClass());
            })
            ->first();

        if ($tranx) {
            $response = new \App\Services\CustomObject([
                'code' => HttpStatus::OK,
                'status_code' => HttpStatus::OK,
                'amount' => $tranx->value,
                'status' => 'success',
                'message' => __('Transaction Verified.'),
                'balance' => $this->user->walletBalance,
                'reference' => $tranx->reference ?: $tranx->id,
                'payment_approved' => true,
            ]);

            // Call the callback function
            if ($successCallback) {
                $response = $successCallback(
                    $reference,
                    $tranx,
                    $tranx->value,
                    $response['message'],
                    $response['code']
                );
            }

            if ($respond) {
                return $response;
            }
        } else {
            $response = [
                'code' => HttpStatus::NOT_FOUND,
                'status_code' => HttpStatus::NOT_FOUND,
                'error' => HttpStatus::NOT_FOUND->value,
                'status' => 'error',
                'message' => __('Transaction not found.'),
            ];

            $response = new \App\Services\CustomObject($response);
            if ($errorCallback) {
                $response = $errorCallback($response);
            }

            if ($respond) {
                return $response;
            }
        }
    }

    /**
     * Deauthorize a transaction payment
     *
     * @param  callable  $successCallback
     * @param  callable  $errorCallback
     * @param  bool  $respond Wether to return a response or not.
     */
    public function deauthorize(
        string $authorization_code,
        ?callable $callback = null,
        ?callable $error_callback = null,
        ?bool $respond = true
    ) {}

    /**
     * Do transfter to the specified user
     *
     * @param  ?string  $reason
     * @param  ?callable  $successCallback
     * @param  ?callable  $errorCallback   The callback function to call when an error occurs
     * @param  bool  $respond     Wether to return a response or not.
     * @return array|object|\App\Services\CustomObject
     */
    public function transfer(
        int|float $amount,
        ?string $reason = null,
        ?callable $successCallback = null,
        ?callable $errorCallback = null,
        ?bool $respond = true
    ) {}
}
