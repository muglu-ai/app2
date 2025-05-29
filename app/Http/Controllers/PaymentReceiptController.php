<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PaymentReceiptController extends Controller
{
    //
    public function uploadReceipt_old(Request $request)
    {
        try {
            Log::info('Payment receipt upload request', $request->all());

            $request->validate([
                'invoice_id' => 'required|exists:invoices,invoice_no',
                'user_id' => 'required|exists:users,id',
                'payment_method' => 'required|in:Bank Transfer,Credit Card,UPI,PayPal,Cheque,Cash',
                'amount_paid' => 'required|numeric|min:0',
                'currency' => 'nullable|string|max:10',
                'payment_date' => 'required|date_format:Y-m-d',
                'receipt_image' => 'required|image|mimes:jpg,png,jpeg|max:2048',  // 2MB max file size
                'transaction_no' => 'required|string|max:255',
            ]);
            #Storage::disk('public')->makeDirectory('receipts');
            $receiptPath = $request->file('receipt_image')->storeAs('receipts', $request->transaction_no . '.' . $request->file('receipt_image')->getClientOriginalExtension(), 'public');

            //get the invoice record
            $invoice = Invoice::where('invoice_no', $request->invoice_id)->first();
            if (!$invoice) {
                return response()->json(['message' => 'Invoice not found'], 404);
            }
            //get the invoice_id
            $request->invoice_id = $invoice->id;
            //create new payments record with the uploaded receipt
            $paymentReceipt = Payment::create([
                'invoice_id' => $request->invoice_id,
                'user_id' => $request->user_id,
                'payment_method' => $request->payment_method,
                'amount_paid' => $request->amount_paid,
                'currency' => $request->currency,
                'payment_date' => $request->payment_date,
                'receipt_image' => $receiptPath,
                'amount' => $invoice->amount,
                'transaction_id' => $request->transaction_no,
            ]);

            return response()->json(['message' => 'Payment receipt uploaded successfully', 'data' => $paymentReceipt]);
        } catch (\Exception $e) {
            Log::error('Error uploading payment receipt: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to upload payment receipt'], 500);
        }
    }

    public function uploadReceipt(Request $request)
    {
        try {
            Log::info('Payment receipt upload request', $request->all());

            // Validate request inputs
            $validatedData = $request->validate([
                'app_id' => 'required_without:invoice_id|exists:applications,application_id',
                'invoice_id' => 'required_without:app_id|exists:invoices,invoice_no',
                'user_id' => 'required|exists:users,id',
                'payment_method' => 'required|in:Bank Transfer,Credit Card,UPI,PayPal,Cheque,Cash',
                'amount_paid' => 'required|numeric|min:0',
                'currency' => 'nullable|string|max:10',
                'payment_date' => 'required|date_format:Y-m-d',
                'receipt_image' => 'image|mimes:jpg,png,jpeg,pdf|max:2048',
                'transaction_no' => 'required|string|max:255',
            ]);


            // Store the receipt image
//            $receiptPath = $request->file('receipt_image')->storeAs(
//                'receipts',
//                $request->transaction_no . '.' . $request->file('receipt_image')->getClientOriginalExtension(),
//                'public'
//            );

            //if app_id comes then take find the invoice id from using application_no
            if ($request->app_id) {
                $invoice = Invoice::where('application_no', $request->app_id)->first();

                if (!$invoice) {
                    return response()->json(['error' => ['app_id' => 'Application not found']], 404);
                }

            }
            else {
                $invoice = Invoice::where('invoice_no', $request->invoice_id)->first();
                if (!$invoice) {
                    return response()->json(['error' => ['invoice_id' => 'Invoice not found']], 404);
                }
            }

            // Fetch the invoice record


            // Create new payment record
            $paymentReceipt = Payment::create([
                'invoice_id' => $invoice->id,
                'user_id' => $request->user_id,
                'payment_method' => $request->payment_method,
                'amount_paid' => $request->amount_paid,
                'currency' => $request->currency,
                'payment_date' => $request->payment_date,
                //'receipt_image' => $receiptPath,
                'amount' => $invoice->amount,
                'transaction_id' => $request->transaction_no,
            ]);

            // get the payment_id after creating the payment record
            $payment_id = $paymentReceipt->id;

             $payment = Payment::findOrFail($payment_id);

                    if($payment->verification_status === 'Verified'){
                        return response()->json(['message' => 'Payment already verified.'], 400);
                    }

                    $invoice = Invoice::where('id', $payment->invoice_id)->firstOrFail();
                    Log::info('Invoice ID: ' . $invoice->id . ' Application ID: ' . $invoice->application_id);


                    DB::transaction(function () use ($payment, $invoice, $request) {
                        $amountPaid = $payment->amount_paid;
                        $invoice->amount_paid += $amountPaid;
                        $invoice->pending_amount = $invoice->amount - $payment->amount_paid;


                        if ($invoice->pending_amount <= 0) {
                            $invoice->payment_status = 'paid';
                            $invoice->pending_amount = 0;
                        } elseif ($invoice->amount_paid > 0 && $invoice->pending_amount > 0) {
                            $invoice->payment_status = 'partial';
                            $invoice->pending_amount = $invoice->amount - $invoice->amount_paid;
                            if ($invoice->pending_amount < 0) {
                                $invoice->pending_amount = 0;
                            }
                            $invoice->payment_due_date = now()->addDays(30);
                        }

                        $invoice->save();

                        //check if the amount and amount_paid is equal then set pending amount to 0
                        if ($invoice->amount_paid == $invoice->amount) {
                            $invoice->pending_amount = 0;
                            $invoice->payment_status = 'paid';
                            $invoice->save();
                        }

                        $payment->update([
                            'status' => 'successful',
                            'verification_status' => 'Verified',
                            'remarks' => 'Payment verified successfully',
                            'verified_by' => auth()->user()->name,
                            'verified_at' => now(),
                        ]);

                        if ($invoice->amount_paid == $invoice->total_final_price) {
                            $invoice->pending_amount = 0;
                        }
                    });
                    Log::info('Payment verified successfully. Payment ID: ' . $invoice->application_id);

                    // Handle pass allocation
                    $exhibitionController = new ExhibitionController();
                    $exhibitionController->handlePaymentSuccess($invoice->application_id);

                    //return response()->json(['message' => 'Payment verified and processed successfully.'], 200);

            return response()->json([
                'message' => 'Payment uploaded successfully and Exhibitor Onboarded.',
                'data' => $paymentReceipt
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Catch validation errors and return error messages for each invalid field
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error uploading payment receipt: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to upload payment receipt'], 500);
        }
    }


    public function uploadReceipt_user(Request $request)
    {
        Log::info('Payment receipt upload request', $request->all());
        try {
            Log::info('Payment receipt upload request', $request->all());

            // Validate request inputs
            $validatedData = $request->validate([
                'app_id' => 'required_without:invoice_id|exists:applications,application_id',
                'invoice_id' => 'required_without:app_id|exists:invoices,invoice_no',
                'user_id' => 'required|exists:users,id',
                'payment_method' => 'required|in:Bank Transfer,Credit Card,UPI,PayPal,Cheque,Cash',
                'amount_paid' => 'required|numeric|min:0',
                'currency' => 'nullable|string|max:10',
                'payment_date' => 'required|date_format:Y-m-d',
                'receipt_image' => 'required|image|mimes:jpg,png,jpeg,pdf|max:2048',
                'transaction_no' => 'required|string|max:255',
            ]);

            // Store the receipt image
            $receiptPath = $request->file('receipt_image')->storeAs(
                'receipts',
                $request->transaction_no . '.' . $request->file('receipt_image')->getClientOriginalExtension(),
                'public'
            );

            //if app_id comes then take find the invoice id from using application_no
            if ($request->app_id) {
                $invoice = Invoice::where('application_no', $request->app_id)->first();
                if (!$invoice) {
                    return response()->json(['error' => ['app_id' => 'Application not found']], 404);
                }

            }
            else {
                $invoice = Invoice::where('invoice_no', $request->invoice_id)->first();
                if (!$invoice) {
                    return response()->json(['error' => ['invoice_id' => 'Invoice not found']], 404);
                }
            }

            // Fetch the invoice record


            // Create new payment record
            $paymentReceipt = Payment::create([
                'invoice_id' => $invoice->id,
                'user_id' => $request->user_id,
                'payment_method' => $request->payment_method,
                'amount_paid' => $request->amount_paid,
                'currency' => $request->currency,
                'payment_date' => $request->payment_date,
                'receipt_image' => $receiptPath,
                'amount' => $invoice->amount,
                'transaction_id' => $request->transaction_no,
            ]);

            return response()->json([
                'message' => 'Payment receipt uploaded successfully',
                'data' => $paymentReceipt
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Catch validation errors and return error messages for each invalid field
            Log::info('Error uploading payment receipt: ' . $e->getMessage());
            Log::info('Payment receipt upload request', $request->all());
            return response()->json(['error' => $e->errors(), 'request' => $request->all()], 422);
        } catch (\Exception $e) {
            Log::error('Error uploading payment receipt: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to upload payment receipt'], 500);
        }
    }


}
