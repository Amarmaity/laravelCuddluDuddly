<?php

namespace App\Mail;

use App\Models\Sellers;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SellerKycStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $seller;
    public $status;
    public $reason;

    public function __construct(Sellers $seller, string $status, string $reason = null)
    {
        $this->seller = $seller;
        $this->status = $status;
        $this->reason = $reason;
    }

    public function build()
    {
        return $this->subject("KYC Application Status: " . ucfirst($this->status))
            ->markdown('emails.seller.kyc-status');
    }
}
