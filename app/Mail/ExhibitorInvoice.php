<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Application;

class ExhibitorInvoice extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    protected $subjectLine;
    protected $application;
    protected $invoice;

    /**
     * ExhibitorInvoice constructor.
     *
     * @param string $application_id
     * @param string $subjectLine
     */

    // Pass the application varibale in the email body view as it producing error
    // when trying to access the invoice in the view



    public function __construct($application_id, $subjectLine)
    {
        $application = Application::where('application_id', $application_id)
            ->first();
        $invoice = Invoice::where('invoice_no', $application_id)
            ->where('type', 'Stall Booking')
            ->first();
        // print_r($application);
        // print_r($invoice);
        // die;
        $application->invoice = $invoice;
        $this->application = $application;
        $this->invoice = $invoice;
        $this->subjectLine = $subjectLine;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.exhibitor_invoice',
            with: [
                'application' => $this->application,
                'invoice' => $this->invoice
            ],
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
