<?php

namespace App\Mail;

use App\Models\Car;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorReportLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Vendor $vendor,
        public ?Car $car,
        public string $url,
        public Carbon $expiresAt
    ) {}

    public function build()
    {
        return $this->subject('Link para completar reporte de Material Mayor')
            ->view('emails.vendor_report_link');
    }
}
