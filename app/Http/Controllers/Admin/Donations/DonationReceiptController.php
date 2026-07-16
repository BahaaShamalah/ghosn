<?php

namespace App\Http\Controllers\Admin\Donations;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\Response;
use Illuminate\View\View;

class DonationReceiptController extends Controller
{
    public function show(Donation $donation): View
    {
        return view('admin.donations.receipt', $this->receiptData($donation, false));
    }

    public function print(Donation $donation): View
    {
        return view('admin.donations.receipt', $this->receiptData($donation, true, true));
    }

    public function download(Donation $donation): Response
    {
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.donations.receipt', $this->receiptData($donation, true));

            return $pdf->download($this->filename($donation));
        }

        $html = view('admin.donations.receipt', $this->receiptData($donation, true))->render();

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$this->filename($donation, 'html').'"',
        ]);
    }

    /**
     * @return array{donation: Donation, printMode: bool, autoPrint: bool}
     */
    private function receiptData(Donation $donation, bool $printMode, bool $autoPrint = false): array
    {
        return [
            'donation' => $donation,
            'printMode' => $printMode,
            'autoPrint' => $autoPrint,
        ];
    }

    private function filename(Donation $donation, string $extension = 'pdf'): string
    {
        return 'ghosn-receipt-'.$donation->reference.'.'.$extension;
    }
}
