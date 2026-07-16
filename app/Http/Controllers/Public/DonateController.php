<?php



namespace App\Http\Controllers\Public;



use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StoreDonationRequest;
use App\Models\Campaign;
use App\Models\Donation;
use App\Services\Campaigns\CampaignService;
use App\Services\Donations\DonationService;
use App\Support\DonationSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DonateController extends Controller
{
    public function __construct(
        private readonly DonationService $donations,
        private readonly DonationSettings $donationSettings,
        private readonly CampaignService $campaigns,
    ) {}

    public function index(Request $request): View
    {
        $campaign = null;
        $campaignSlug = trim((string) $request->query('campaign', ''));

        if ($campaignSlug !== '') {
            $campaign = $this->campaigns->findPublicBySlug($campaignSlug);
        }

        return view('public.donate.index', [
            'donationSettings' => $this->donationSettings,
            'bankDetails' => $this->donationSettings->bankDetails(),
            'amountPresets' => $this->donationSettings->amountPresets(),
            'currency' => $this->donationSettings->currency(),
            'currencyMeta' => config('donations.currencies.'.$this->donationSettings->currency(), []),
            'donationsEnabled' => $this->donationSettings->enabled(),
            'stripeEnabled' => $this->donationSettings->stripeEnabled(),
            'paypalEnabled' => $this->donationSettings->paypalEnabled(),
            'bankEnabled' => $this->donationSettings->bankTransferEnabled() && $this->donationSettings->hasBankDetails(),
            'campaign' => $campaign,
        ]);
    }



    public function store(StoreDonationRequest $request): RedirectResponse

    {

        if ($request->filled('website')) {

            abort(422);

        }



        $donation = $this->donations->create([

            ...$request->validated(),

            'currency' => $this->donationSettings->currency(),

            'locale' => app()->getLocale(),

        ], $request->ip());



        if ($donation->payment_method === Donation::METHOD_BANK) {

            return redirect()->route('donate.thank-you', ['reference' => $donation->reference]);

        }



        try {

            $result = $this->donations->initiateGatewayCheckout($donation);



            if (! $result->success || ! filled($result->checkoutUrl)) {

                throw new \RuntimeException($result->error ?? 'Checkout failed.');

            }



            return redirect()->away($result->checkoutUrl);

        } catch (\Throwable) {

            $this->donations->markFailed($donation);



            return back()

                ->withInput()

                ->withErrors(['payment_method' => __('public.donate.gateway_unavailable')]);

        }

    }



    public function success(Request $request): View|RedirectResponse

    {

        $gateway = (string) $request->query('gateway', 'stripe');

        $gatewayReference = $gateway === 'paypal'

            ? (string) $request->query('token', '')

            : (string) $request->query('session_id', '');



        if ($gatewayReference === '') {

            return redirect()->route('donate');

        }



        $donation = $this->donations->completeGatewayReturn($gateway, $gatewayReference);



        if (! $donation) {

            return redirect()->route('donate')->with('donate_error', __('public.donate.payment_not_found'));

        }

        if (! $donation->isPaid() || $donation->paid_at === null) {

            return redirect()

                ->route('donate')

                ->with('donate_error', __('public.donate.payment_not_found'));

        }



        return view('public.donate.success', [

            'donation' => $donation,

        ]);

    }



    public function complete(string $reference): View|RedirectResponse
    {
        $donation = Donation::query()
            ->where('reference', $reference)
            ->first();

        if (
            ! $donation
            || $donation->payment_method !== Donation::METHOD_PAYPAL
            || $donation->status !== Donation::STATUS_PAID
            || $donation->paid_at === null
        ) {
            return redirect()
                ->route('donate')
                ->with('donate_error', __('public.donate.payment_not_found'));
        }

        return view('public.donate.success', [
            'donation' => $donation,
        ]);
    }

    public function thankYou(string $reference): View|RedirectResponse

    {

        $donation = $this->donations->findByReference($reference);



        if (! $donation || $donation->payment_method !== Donation::METHOD_BANK) {

            return redirect()->route('donate');

        }



        return view('public.donate.thank-you', [

            'donation' => $donation,

            'bankDetails' => $this->donationSettings->bankDetails(),

        ]);

    }



    public function cancel(string $reference): RedirectResponse

    {

        $donation = $this->donations->findByReference($reference);



        if ($donation) {

            $this->donations->markCancelled($donation);

        }



        return redirect()

            ->route('donate')

            ->with('donate_error', __('public.donate.payment_cancelled'));

    }

}

