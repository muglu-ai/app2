<?php

namespace App\Exports;

use App\Models\Invoice;
use App\Models\BillingDetail;
use App\Models\RequirementsOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\RequirementOrderItem;
use Illuminate\Support\Facades\Log;
use App\Models\CoExhibitor;

class InvoicesExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
     * Fetch invoices of type "extra_requirement" and process the details for export.
     */
    public function collection()
    {
        $invoices = Invoice::where('type', 'extra_requirement')->get();
        
        $data = []; // Initialize an array to store the data for export

        foreach ($invoices as $invoice) {
            // If invoice is already paid, skip to the next one
            // if ($invoice->payment_status == 'paid') {
            //     continue;
            // }

            // Fetch the BillingDetail details where application_id = $invoice->application_id
            $billingDetail = BillingDetail::where('application_id', $invoice->application_id)->first();

            //get the stallNumber from the application
            $application = $invoice->application;
            $stallNumber = $application->stallNumber ?? 'N/A';

            //if invoice has co_exhibitorID then fetch the company name of co exhibitor from co_exhibitors table
            if($invoice->co_exhibitorID){
                $co_exhibitor = CoExhibitor::where('id', $invoice->co_exhibitorID)->first();
                $billingDetail->billing_company = $co_exhibitor->co_exhibitor_name;
            }


            // Find the orders related to the current invoice and eager load related order items and requirements
            $orders = RequirementsOrder::where('invoice_id', $invoice->id)
                ->with(['orderItems.requirement']) // Eager load orderItems and their corresponding extra requirements
                ->orderBy('created_at', 'desc')
                ->get();

            // Loop through orders to get the required order items and their extra requirements
            foreach ($orders as $order) {
                foreach ($order->orderItems as $item) {
                    $requirement = $item->requirement; // Fetch the associated extra requirement
                    Log::info($requirement);

                    if ($requirement) {
                        // Add the relevant information (name, quantity, unit price) to the data array
                        $data[] = [
                            'Invoice No' => $invoice->invoice_no,
                            'Company' => $billingDetail->billing_company ?? 'N/A',
                            'Address' => $billingDetail->address ?? 'N/A',
                            'Country' => $billingDetail->country->name ?? 'N/A',
                            'Email' => $billingDetail->email ?? 'N/A',
                            'Phone' => $billingDetail->phone ?? 'N/A',
                            'Stall Number' => $stallNumber,
                            'Requirement Name' => $requirement->item_name,
                            'Quantity' => $item->quantity,
                            'Unit Price' => $item->unit_price,
                            'Total Price' => $item->quantity * $item->unit_price, // Calculate total price for this item
                            'Pay Status' => $invoice->payment_status,
                            'Amount Paid' => $invoice->amount_paid,
                        ];
                    }
                }
            }
        }

        // Return the collected data as a collection
        return collect($data);
    }

    /**
     * Set the headings for the Excel export.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Invoice No',
            'Company',
            'Address',
            'Country',
            'Email',
            'Phone',
            'Stall Number',
            'Requirement Name',
            'Quantity',
            'Unit Price',
            'Total Price',
            'Payment Status',
            'Amount Paid',

        ];
    }
}
