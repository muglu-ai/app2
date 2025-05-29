<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\RequirementOrderItem;
use App\Models\RequirementsOrder;
use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
use App\Models\BillingDetail;
use App\Models\CoExhibitor;
use Illuminate\Support\Facades\Log;



class ExtraRequirementsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $orderItems;
    public $subtotal;
    public $processingCharge;
    public $orderTotal;
    public $discount;
    public $gst;
    public $finalTotalPrice;
    public $pendingAmount;
    public $total_received;

    public $billingCompany;
    public $billingEmail;
    public $invoice_Id;

    public $order_date;
    public $currency;

    public $usdTotal;


    /**
     * Create a new message instance.
     */
    public function __construct(array $data)
    {
       // Log::info("message data: " . json_encode($data));
        $this->invoice = $data['invoice'];
        $this->orderItems = $data['orderItems'];
        $this->subtotal = $data['subtotal'];
        $this->processingCharge = $data['processingCharge'];
        $this->discount = $data['discount'];
        $this->gst = $data['gst'];
        $this->finalTotalPrice = $data['finalTotalPrice'];
        $this->pendingAmount = $data['pendingAmount'];
        $this->total_received = $data['total_received'];
        $this->billingCompany = $data['billingCompany'];
        $this->billingEmail = $data['billingEmail'];
        $this->invoice_Id = $data['invoice_Id'];
        $this->order_date = $data['order_date'];
        $this->currency = $data['currency'];
        $this->usdTotal = $data['usdTotal'];

    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Extra Requirements Order  -' . config('constants.APP_NAME'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.extra_requirements_mail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
