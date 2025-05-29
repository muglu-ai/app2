<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\RequirementOrderItem;
use App\Models\RequirementsOrder;
use App\Models\BillingDetail;
use App\Models\CoExhibitor;
use Illuminate\Support\Collection;

class ExtraRequirementsMailService
{
    public function prepareMailData(string $invoiceId): array
    {
        $invoice = Invoice::where('invoice_no', $invoiceId)->first();

        if (!$invoice) {
            return [
                'invoice' => null,
                'orderItems' => collect(),
                'subtotal' => 0,
                'processingCharge' => 0,
                'discount' => 0,
                'gst' => 0,
                'finalTotalPrice' => 0,
                'pendingAmount' => 0,
                'total_received' => 0,
                'billingCompany' => 'N/A',
                'billingEmail' => 'N/A',
                'invoice_Id' => 'N/A',
                'order_date' => 'N/A',
                'currency' => 'INR',
                'usdTotal' => 0,
            ];
        }

        $billingCompany = 'N/A';
        $billingEmail = 'N/A';

        if (!empty($invoice->co_exhibitorID)) {
            $coExhibitor = CoExhibitor::where('application_id', $invoice->application_id)->first();
            if ($coExhibitor) {
                $billingCompany = $coExhibitor->co_exhibitor_name;
                $billingEmail = $coExhibitor->email;
            }
        } else {
            $billingDetail = BillingDetail::where('application_id', $invoice->application_id)->first();
            if ($billingDetail) {
                $billingCompany = $billingDetail->billing_company;
                $billingEmail = $billingDetail->email;
            }
        }

        $order = RequirementsOrder::where('invoice_id', $invoice->id)->first();
        $orderItems = collect();
        $subtotal = 0;

        if ($order) {
            $orderItems = RequirementOrderItem::where('requirements_order_id', $order->id)
                ->join('extra_requirements', 'requirement_order_items.requirement_id', '=', 'extra_requirements.id')
                ->select(
                    'extra_requirements.item_name',
                    'requirement_order_items.unit_price',
                    'requirement_order_items.quantity',
                    \DB::raw('(requirement_order_items.unit_price * requirement_order_items.quantity) as total_price')
                )
                ->get();

            $subtotal = $orderItems->sum('total_price');
        }

        return [
            'invoice' => $invoice,
            'orderItems' => $orderItems,
            'subtotal' => $subtotal,
            'processingCharge' => $invoice->processing_charges,
            'discount' => $invoice->discount,
            'gst' => $invoice->gst,
            'finalTotalPrice' => $invoice->amount,
            'pendingAmount' => $invoice->pending_amount,
            'total_received' => $invoice->amount_paid,
            'billingCompany' => $billingCompany,
            'billingEmail' => $billingEmail,
            'invoice_Id' => $invoice->invoice_no,
            'order_date' => $invoice->updated_at,
            'currency' => $invoice->currency,
            'usdTotal' => $invoice->int_amount_value,
        ];
    }
}

