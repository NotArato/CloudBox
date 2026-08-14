<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use App\Services\PayWayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected PayWayService $payWayService;

    public function __construct(PayWayService $payWayService)
    {
        $this->payWayService = $payWayService;
    }

    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        // ABA PayWay
        $merchant_id          = config('payway.merchant_id', 'ec463509');
        $req_time             = time();
        $tranId               = 'CB-' . $req_time;
        $amount               = '5.00';
        $currency             = 'USD';
        $payment_option       = 'abapay_khqr';
        $continue_success_url = route('payment.check');

        $hash = $this->payWayService->getHash(
            $req_time .
            $merchant_id .
            $tranId .
            $amount .
            $payment_option .
            $continue_success_url .
            $currency
        );

        // Store transaction ID
        session(['tran_id' => $tranId]);

        return view('payment.upgrade', compact(
            'user',
            'hash',
            'tranId',
            'amount',
            'payment_option',
            'merchant_id',
            'req_time',
            'continue_success_url',
            'currency'
        ));
    }
    public function checkTransaction(Request $request)
    {
        Log::info('CHECK TRANSACTION HIT', [
            'query'        => $request->query(),
            'session_tran' => session('tran_id'),
            'full_url'     => $request->fullUrl(),
        ]);

        $tranId = $request->query('tran_id') ?? session('tran_id');

        if (!$tranId) {
            return redirect()->route('upgrade')
                ->with('error', 'Transaction ID missing.');
        }

        if ($this->checkAbaApproved($tranId)) {
            /** @var User $user */
            $user = Auth::user();

            if ($user) {
                $this->completePaymentAndUpgrade($user, $tranId);
            }

            session()->forget('tran_id');

            return redirect()->route('dashboard')
                ->with('success', '🎉 Payment verified! Your account has been upgraded to Premium (5 GB storage capacity).');
        }

        return redirect()->route('upgrade')
            ->with('error', 'Payment not completed yet. Please complete the ABA PayWay payment.');
    }

    /**
     * ABA Pushback Webhook (Server-to-Server notification)
     */
    public function pushback(Request $request)
    {
        Log::info('ABA Pushback received', $request->all());

        $validated = $request->validate([
            'tran_id' => 'required|string',
            'status'  => 'required|string',
            'hash'    => 'required|string',
        ]);

        $expectedHash = $this->payWayService->getHash(
            $validated['tran_id'] . $validated['status']
        );

        if ($validated['hash'] !== $expectedHash) {
            Log::warning('Invalid ABA pushback hash', [
                'tran_id' => $validated['tran_id'],
            ]);

            return response()->json(['message' => 'Invalid hash'], 400);
        }

        if ($this->checkAbaApproved($validated['tran_id'])) {
            $payment = Payment::where('transaction_id', $validated['tran_id'])->first();
            if ($payment && $payment->user) {
                $this->completePaymentAndUpgrade($payment->user, $validated['tran_id']);
            }

            return response()->json(['message' => 'Payment approved']);
        }

        return response()->json(['message' => 'Payment not approved'], 400);
    }

    /**
     * Confirm simulation payment (manual demo trigger)
     */
    public function confirmPayment(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'transaction_id' => ['required', 'string'],
        ]);

        $this->completePaymentAndUpgrade($user, $request->transaction_id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment verified! Your account has been upgraded to Premium (5 GB storage).',
            ]);
        }

        return redirect()->route('dashboard')->with('success', '🎉 Payment successful! Your account has been upgraded to Premium with 5 GB storage capacity.');
    }

    /**
     * Revert user account back to Free tier
     */
    public function revertFree(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // Revert user back to Free tier (100 MB = 104,857,600 bytes)
        $user->update([
            'is_premium' => false,
            'storage_limit' => 104857600,
        ]);

        return redirect()->route('dashboard')->with('success', 'Account reverted back to Free Tier (100 MB storage capacity).');
    }

    /**
     * Verify transaction with ABA PayWay API
     */
    private function checkAbaApproved(string $tranId): bool
    {
        $merchantId = config('payway.merchant_id', 'ec463509');
        $publicKey  = config('payway.api_key', '1cb54f9442ec911e271b1774a995d39ecbfb28cc');
        $reqTime    = time();

        $hash = base64_encode(hash_hmac(
            'sha512',
            $reqTime . $merchantId . $tranId,
            $publicKey,
            true
        ));

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'User-Agent'   => 'CloudBox/1.0 (Laravel)',
            ])->post(
                'https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/check-transaction-2',
                [
                    'req_time'    => $reqTime,
                    'merchant_id' => $merchantId,
                    'tran_id'     => $tranId,
                    'hash'        => $hash,
                ]
            );

            if (!$response->successful()) {
                Log::warning('ABA check transaction failed', [
                    'tran_id' => $tranId,
                    'body'    => $response->body(),
                ]);
                return false;
            }

            $data = $response->json()['data'] ?? null;
            if (!$data) return false;

            $paymentStatus = $data['payment_status'] ?? null;
            $status        = $data['status'] ?? null;

            return ($paymentStatus === 'APPROVED') || ($status === 'success');
        } catch (\Exception $e) {
            Log::error('ABA verification error', [
                'tran_id' => $tranId,
                'error'   => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Record completed payment and update user storage limit to Premium (5 GB)
     */
    private function completePaymentAndUpgrade(User $user, string $tranId): void
    {
        Payment::firstOrCreate(
            ['transaction_id' => $tranId],
            [
                'user_id'        => $user->id,
                'amount'         => 5.00,
                'currency'       => 'USD',
                'payment_method' => 'ABA_KHQR',
                'status'         => 'completed',
            ]
        );

        $user->update([
            'is_premium'    => true,
            'storage_limit' => 5368709120, // 5 GB
        ]);
    }
}
