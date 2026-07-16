<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Services\Donations\DonationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class StripeWebhookController extends Controller
{
    public function __construct(
        private readonly DonationService $donations,
    ) {}

    public function __invoke(Request $request): Response
    {
        $result = $this->donations->processWebhook(
            'stripe',
            $request->getContent(),
            $this->normalizeHeaders($request),
        );

        if (! $result->accepted) {
            throw new AccessDeniedHttpException();
        }

        return response('OK', 200);
    }

    /**
     * @return array<string, string|null>
     */
    private function normalizeHeaders(Request $request): array
    {
        $headers = [];

        foreach ($request->headers->all() as $key => $values) {
            $headers[strtolower($key)] = $values[0] ?? null;
        }

        return $headers;
    }
}
