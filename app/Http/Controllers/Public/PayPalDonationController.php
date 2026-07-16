<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\PayPalCaptureOrderRequest;
use App\Http\Requests\Public\PayPalCreateOrderRequest;
use App\Models\Donation;
use App\Services\Donations\DonationService;
use App\Services\Payments\Gateways\PayPalBusinessGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PayPalDonationController extends Controller
{
    public function __construct(
        private readonly DonationService $donations,
        private readonly PayPalBusinessGateway $paypal,
    ) {}

    public function createOrder(PayPalCreateOrderRequest $request): JsonResponse
    {
        if ($request->filled('website')) {
            abort(422);
        }

        try {
            $result = $this->donations->createPayPalOrder([
                ...$request->validated(),
                'locale' => app()->getLocale(),
            ], $request->ip());

            return response()->json($result);
        } catch (\Throwable $exception) {
            Log::warning('PayPal create-order failed.', [
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => __('public.donate.gateway_unavailable'),
            ], 422);
        }
    }

    public function captureOrder(PayPalCaptureOrderRequest $request): JsonResponse
    {
        if ($request->filled('website')) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => ['website' => ['Invalid submission.']],
            ], 422);
        }

        Log::info('PayPal capture-order request received', [
            'order_id' => $request->input('order_id'),
            'donation_id' => $request->input('donation_id'),
            'reference' => $request->input('reference'),
        ]);

        $orderId = (string) $request->input('order_id');
        $donation = $this->resolveDonation($request);

        if (! $donation) {
            Log::warning('PayPal capture-order donation not found.', [
                'donation_id' => $request->input('donation_id'),
                'reference' => $request->input('reference'),
                'order_id' => $orderId,
            ]);

            return response()->json([
                'message' => __('public.donate.payment_not_found'),
            ], 422);
        }

        try {
            $donation = $this->donations->capturePayPalOrder($donation, $orderId);

            if (! $donation->isPaid()) {
                Log::warning('PayPal capture-order completed without paid donation.', [
                    'donation_id' => $donation->id,
                    'reference' => $donation->reference,
                    'order_id' => $orderId,
                    'status' => $donation->status,
                ]);

                return response()->json([
                    'message' => __('public.donate.gateway_unavailable'),
                ], 422);
            }

            return response()->json([
                'success' => true,
                'paid' => true,
                'reference' => $donation->reference,
                'redirect_url' => route('donate.complete', ['reference' => $donation->reference]),
            ]);
        } catch (\InvalidArgumentException $exception) {
            Log::warning('PayPal capture-order rejected.', [
                'donation_id' => $donation->id,
                'order_id' => $orderId,
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        } catch (\RuntimeException $exception) {
            Log::warning('PayPal capture-order failed.', [
                'donation_id' => $donation->id,
                'order_id' => $orderId,
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => $exception->getMessage() ?: __('public.donate.gateway_unavailable'),
            ], 422);
        }
    }

    private function resolveDonation(PayPalCaptureOrderRequest $request): ?Donation
    {
        $donationId = $request->integer('donation_id');

        if ($donationId > 0) {
            $donation = Donation::query()->find($donationId);

            if ($donation) {
                return $donation;
            }
        }

        if ($request->filled('reference')) {
            $donation = Donation::query()
                ->where('reference', $request->input('reference'))
                ->first();

            if ($donation) {
                return $donation;
            }
        }

        return $this->paypal->findDonationByPayPalOrderId((string) $request->input('order_id'));
    }
}
