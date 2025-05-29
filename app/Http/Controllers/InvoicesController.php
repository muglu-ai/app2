<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Invoice;
use App\Models\Sponsorship;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicesController extends Controller
{
    //
    //list all invoices for admin
    public function index()
    {
        //check user is logged in or not
        if (!auth()->check()) {
            return redirect('/login');
        }
        // Get only applications that have invoices
        $applications = Application::has('invoices')->with(['invoices' => function($query) {
            $query->where('type', '!=', 'extra_requirement');
        }, 'billingDetail'])->get();

         // if application is null then pass null to view
         if ($applications->isEmpty()) {
            $applications = null;
        }
        return view('invoices.index', compact('applications', 'applications'));
    }

    //show invoice details for individual invoice
    public function show($id)
    {
        //check user is logged in or not
        if (!auth()->check()) {
            return redirect('/login');
        }
        $invoice = Invoice::where('invoice_no', $id)->first();
       // dd($invoice);
        // Get only applications that have invoices


        //dd($invoice->application_id);
        // Get the payments related to the invoice
        $payments = $invoice->payments;

        // Find the application with the same application_no in both invoices and applications tables
        $applications = Application::where('id', $invoice->application_id)->first();
//dd($applications);


        return view('invoices.invoice_details', compact('applications', 'invoice', 'payments'));
    }

    //print invoice
    public function view(Request $request)
    {

        // Ensure the user is logged in (uncomment if authentication is required)
         if (!auth()->check()) {
             return redirect('/login');
         }

        // Retrieve invoice based on invoice number from the request
        $invoice_no = $request->no;
        $invoice = Invoice::where('invoice_no', $invoice_no)->firstOrFail(); // Fail gracefully if not found

        // Retrieve the related application
        $applications = Application::where('application_id', $invoice->application_no)->firstOrFail();

        // Retrieve sponsorship details if available
        $sponsor = Sponsorship::where('application_id', $applications->id)->first();

        // Get billing details
        $billing = $applications->billingDetail;

        // Determine product details based on sponsorship presence
        $products = $sponsor ? [
            'item' => $sponsor->sponsorship_item,
            'price' => $sponsor->price,
            'gst' => $invoice->gst,
            'quantity' => 1,
            'total' => $invoice->amount,
            'due' => $invoice->amount - $invoice->payments->sum('amount'),
        ] : [
            'item' => $applications->stall_category . ' Stall',
            'price' => $invoice->amount,
            'quantity' => $applications->allocated_sqm . ' (sqm)',
            'gst' => $invoice->gst,
            'total' => $invoice->amount,
            'due' => $invoice->amount - $invoice->payments->sum('amount'),
        ];

        // Return the view with necessary data
        return view('bills.invoice', compact('applications', 'invoice', 'billing', 'sponsor', 'products'));
    }



    public function generatePDF(Request $request)
    {
        // Get the invoice number from the request
        $invoice_no = $request->no;

        // Find the invoice
        $invoice = Invoice::where('invoice_no', $invoice_no)->firstOrFail();

        // Get application details
        $applications = Application::where('application_id', $invoice->application_no)->firstOrFail();

        // Get sponsor details
        $sponsor = Sponsorship::where('application_id', $applications->id)->first();



        // Billing details
        $billing = $applications->billingDetail;

        // Determine product details based on sponsorship availability
        $products = $sponsor ? [
            'item' => $sponsor->sponsorship_item,
            'price' => $sponsor->price,
            'gst' => $invoice->gst,
            'quantity' => 1,
            'total' => $invoice->amount,
            'due' => $invoice->amount - $invoice->payments->sum('amount'),
        ] : [
            'item' => $applications->stall_category . ' Stall',
            'price' => $invoice->amount,
            'quantity' => $applications->allocated_sqm . ' (sqm)',
            'gst' => $invoice->gst,
            'total' => $invoice->amount,
            'due' => $invoice->amount - $invoice->payments->sum('amount'),
        ];

        // Load the PDF view
       $pdf = Pdf::loadView('bills.invoice', compact('applications', 'invoice', 'billing', 'sponsor', 'products'))->setPaper('a4');

        // Set the filename dynamically based on invoice number
        $filename = "Invoice_{$invoice_no}.pdf";

        // Return the PDF for download
        return $pdf->download($filename);
    }


}
