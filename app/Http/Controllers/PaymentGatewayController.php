<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\BillingDetail;
use App\Models\RequirementsOrder;
use App\Mail\ExtraRequirementsMail;
use App\Mail\ExhibitorInvoice;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Services\ExtraRequirementsMailService;
use Illuminate\Support\Facades\Response;
use App\Models\Application;


class PaymentGatewayController extends Controller
{
    //

    private $merchantId;
    private $accessCode;
    private $workingKey;
    private $redirectUrl;
    private $cancelUrl;


    public function __construct()
    {
        $this->merchantId = env('CCAVENUE_MERCHANT_ID');
        $this->accessCode = env('CCAVENUE_ACCESS_CODE');
        $this->workingKey = env('CCAVENUE_WORKING_KEY');
        $this->redirectUrl = config('constants.CCAVENUE_REDIRECT_URL');
        $this->cancelUrl = config('constants.CCAVENUE_REDIRECT_URL');
    }


    public function handleResponse(Request $request)
    {
        $encResponse = $request->input('encResp');
        $decryptedResponse = $this->decrypt($encResponse, $this->workingKey);
        parse_str($decryptedResponse, $responseArray);

        return response()->json($responseArray);
    }

    private function encrypt($plainText, $key)
    {
        $key = pack('H*', md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $encryptedText = bin2hex(openssl_encrypt($plainText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector));
        return $encryptedText;
    }

    private function decrypt($encryptedText, $key)
    {
        $key = pack('H*', md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $encryptedText = pack("H*", $encryptedText);
        return openssl_decrypt($encryptedText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
    }

    public function ccAvenuePayment($orderID, Request $request)
    {
        if (!$orderID) {
            return redirect()->route('exhibitor.orders');
        }

        // get the invoice details from the Invoice model where invoice_no = $id
        $invoice = Invoice::where('invoice_no', $orderID)->first();

        //if invoice not found then redirect to route exhibitor.orders
        if (!$invoice) {
            return redirect()->route('exhibitor.orders');
        }

        //if invoice is already paid then redirect to route exhibitor.orders
        if ($invoice->payment_status == 'paid') {
            return redirect()->route('exhibitor.orders');
        }

        //fetch the BillingDetail details from the model BillingDetail where application_id = $invoice->application_id
        $billingDetail = BillingDetail::where('application_id', $invoice->application_id)->first();


        $data = [
            'merchant_id' => $this->merchantId,
            'order_id' => $orderID . '_' . time(),
            'currency' => 'INR',
            'amount' => $invoice->total_final_price,
            'redirect_url' => $this->redirectUrl,
            'cancel_url' => $this->cancelUrl,
            'language' => 'EN',
            'billing_name' => $billingDetail->contact_name,
            'billing_address' => $billingDetail->address,
            'billing_city' => $billingDetail->city_id,
            'billing_state' => $billingDetail->state->name,
            'billing_zip' => $billingDetail->postal_code,
            'billing_country' => $billingDetail->country->name,
            'billing_tel' => $billingDetail->phone,
            'billing_email' => $billingDetail->email,
        ];

        $merchantData = json_encode($data);

        //insert into payment_gateway_response table
        \DB::table('payment_gateway_response')->insert([
            'merchant_data' => $merchantData,
            'order_id' => $data['order_id'],
            'amount' => $data['amount'],
            'status' => 'Pending',
            'gateway' => 'CCAvenue',
            'currency' => 'INR',
            'email' => $data['billing_email'],
            'created_at' => now(),
        ]);

        // dd($data);

        $queryString = http_build_query($data);
        $encryptedData = $this->encrypt($queryString, $this->workingKey);

        return view('pgway.ccavenue', compact('encryptedData'));
    }

    //


    public function Exhibitor_ccAvenuePayment($orderID, Request $request)
    {
        if (!$orderID) {
            return redirect()->route('exhibitor.orders');
        }

        // get the invoice details from the Invoice model where invoice_no = $id
        $invoice = Invoice::where('invoice_no', $orderID)->first();

        //if invoice not found then redirect to route exhibitor.orders
        if (!$invoice) {
            return redirect()->route('exhibitor.orders');
        }

        //if invoice is already paid then redirect to route exhibitor.orders
        if ($invoice->payment_status == 'paid') {
            return redirect()->route('exhibitor.orders');
        }

        //fetch the BillingDetail details from the model BillingDetail where application_id = $invoice->application_id
        $billingDetail = BillingDetail::where('application_id', $invoice->application_id)->first();


        $data = [
            'merchant_id' => $this->merchantId,
            'order_id' => $orderID . '_' . time(),
            'currency' => 'INR',
            'amount' => $invoice->total_final_price,
            'redirect_url' => $this->redirectUrl,
            'cancel_url' => $this->cancelUrl,
            'language' => 'EN',
            'billing_name' => $billingDetail->contact_name,
            'billing_address' => $billingDetail->address,
            'billing_city' => $billingDetail->city_id,
            'billing_state' => $billingDetail->state->name,
            'billing_zip' => $billingDetail->postal_code,
            'billing_country' => $billingDetail->country->name,
            'billing_tel' => $billingDetail->phone,
            'billing_email' => $billingDetail->email,
        ];

        //dd($data);

        $merchantData = json_encode($data);

        //insert into payment_gateway_response table
        \DB::table('payment_gateway_response')->insert([
            'merchant_data' => $merchantData,
            'order_id' => $data['order_id'],
            'amount' => $data['amount'],
            'status' => 'Pending',
            'gateway' => 'CCAvenue',
            'currency' => 'INR',
            'email' => $data['billing_email'],
            'created_at' => now(),
        ]);

        // dd($data);

        $queryString = http_build_query($data);
        $encryptedData = $this->encrypt($queryString, $this->workingKey);

        return view('pay.exhibitor_ccavenue', compact('encryptedData'));
    }

    // payment success test function
    public function ccAvenueTestSuccess(Request $request)
    {

        // dd($request->all());

        // return "Testing payment success";
        // get the order_id from the post request
        // For testing purposes, we will simulate a successful payment response

        // 'order_id' => $request->input('order_id');
        // Simulate a successful payment response


        //dd($request->all());
        $responseArray = [
            'order_status' => 'Success',
            'order_id' => $request->input('order_id'),
            'tracking_id' => '67890',
            'bank_ref_no' => 'REF123',
            'mer_amount' => 1000,
            'payment_mode' => 'Credit Card',
        ];
        // print_r($responseArray);
        // exit;

        // dd($responseArray);

        // Simulate a successful payment response
        $trans_date = Carbon::now()->format('Y-m-d H:i:s');
        // Update database with successful payment
        \DB::table('payment_gateway_response')
            ->where('order_id', $responseArray['order_id'])
            ->update([
                'amount' => $responseArray['mer_amount'],
                'transaction_id' => $responseArray['tracking_id'],
                'payment_method' => $responseArray['payment_mode'],
                'trans_date' => $trans_date,
                'reference_id' => $responseArray['bank_ref_no'],
                'response_json' => json_encode($responseArray),
                'status' => 'Success',
                'updated_at' => now(),
            ]);
        $order_id = explode('_', $responseArray['order_id'])[0];
        $invoice = Invoice::where('invoice_no', $order_id)->first();
        //update the invoice table with the status as paid
        if ($responseArray['order_status'] == "Success") {
            $invoice->update([
                'payment_status' => 'paid',
                'amount_paid' => 0,
                'updated_at' => now(),
                'pending_amount' => 0,
                'currency' => 'INR',
            ]);
            //
            // $service = new ExhibitorInvoice($$order_id, 'Thank you for making payment at ' . config('constants.event_name') . ' - ' . $order_id);
            // $data = $service->prepareMailData($order_id);
            $application_id = $invoice->application_id;


            $application = Application::find($application_id);

            $email = $application->billingDetail->email;



            // $application = Application::where('application_id', $order_id)
            //     ->first();


            // die;

            // Mail::to($email)
            //     ->bcc(array_merge(['test.interlinks@gmail.com'], config('constants.payment_emails.admin_emails', [])))
            //     ->queue(new ExhibitorInvoice($order_id, 'Thank you for making payment at ' . config('constants.event_name') . ' - ' . $order_id));


            $exhibitionHandle = new ExhibitionController();
            $exhibitionHandle->handlePaymentSuccess($application_id);

            print_r($exhibitionHandle->toArray());
            die;
        }

        // if


        // return to paymentPage with id = $application_id

        return redirect()->route('paymentPage', ['id' => $order_id, 'status' => 'success'])
            ->with('message', 'Payment successful! Your order ID is ' . $order_id);
    }







    public function ccAvenueSuccess(Request $request)
    {


        //dd($request->all());
        // Decrypt response
        $workingKey = env('CCAVENUE_WORKING_KEY');
        $encResponse = $request->input("encResp");


        $decryptedResponse = $this->decrypt($encResponse, $this->workingKey);
        parse_str($decryptedResponse, $responseArray);



        //dd($responseArray);
        if ($responseArray['order_status'] == "Success") {
            $trans_date = Carbon::createFromFormat('d/m/Y H:i:s', $responseArray['trans_date'])->format('Y-m-d H:i:s');
            // Update database with successful payment
            \DB::table('payment_gateway_response')
                ->where('order_id', $responseArray['order_id'])
                ->update([
                    'amount' => $responseArray['mer_amount'],
                    'transaction_id' => $responseArray['tracking_id'],
                    'payment_method' => $responseArray['payment_mode'],
                    'trans_date' => $trans_date,
                    'reference_id' => $responseArray['bank_ref_no'],
                    'response_json' => json_encode($responseArray),
                    'status' => 'Success',
                    'updated_at' => now(),
                ]);

            $order_id = explode('_', $responseArray['order_id'])[0];

            $invoice = Invoice::where('invoice_no', $order_id)->first();

            //update the invoice table with the status as paid
            if ($responseArray['order_status'] == "Success") {
                $invoice->update([
                    'payment_status' => 'paid',
                    'amount_paid' => $responseArray['mer_amount'],
                    'updated_at' => now(),
                    'pending_amount' => 0,
                    'currency' => 'INR',
                ]);
                //
                $service = new ExtraRequirementsMailService();
                $data = $service->prepareMailData($order_id);
                $email = $data['billingEmail'];

                Mail::to($email)
                    ->bcc(array_merge(['test.interlinks@gmail.com'], config('constants.payment_emails.admin_emails', [])))
                    ->queue(new ExtraRequirementsMail($data));
            }
            //reutn to route exhibitor.orders
            return redirect()->route('exhibitor.orders');
            return response()->json($responseArray);
            return redirect('/payment-success');
        } elseif (isset($responseArray)) {
            //update the table with failed payment details
            if (!empty($responseArray['trans_date'])) {
                $trans_date = Carbon::createFromFormat('d/m/Y H:i:s', $responseArray['trans_date'])->format('Y-m-d H:i:s');
            } else {
                $trans_date = now();
            }

            \DB::table('payment_gateway_response')
                ->where('order_id', $responseArray['order_id'])
                ->update([
                    'amount' => $responseArray['mer_amount'] ?? 0,
                    'transaction_id' => $responseArray['tracking_id'] ?? null,
                    'payment_method' => $responseArray['payment_mode'] ?? null,
                    'trans_date' => $trans_date,
                    'reference_id' => $responseArray['bank_ref_no'] ?? null,
                    'response_json' => json_encode($responseArray),
                    'status' => 'Failed',
                    'updated_at' => now(),
                ]);

            //order_id
            $order_id = explode('_', $responseArray['order_id'])[0];

            return redirect('/payment/' . $order_id . '?status=failed');

            //return to /payment/{id}
        } else {
            //update the table with failed payment details
            \DB::table('payment_gateway_response')
                ->where('order_id', $responseArray['order_id'])
                ->update([
                    'status' => 'Failed',
                    'updated_at' => now(),
                ]);
        }

        return redirect('/payment-failed');
    }

    public function sendInvoice($invoiceId)
    {
        $start = microtime(true);
        $service = new ExtraRequirementsMailService();
        $data = $service->prepareMailData($invoiceId);

        // Log::info('Invoice email data: ' . json_encode($data));
        return response()->json($data);
        // Mail::to($toEmail)->send(new ExtraRequirementsMail($invoiceId));
        $email = $data['billingEmail'];
        $email = "manish.sharma@interlinks.in";
        Mail::to($email)->send(new ExtraRequirementsMail($data));
        $end = microtime(true);
        return response()->json(['message' => 'Invoice email sent successfully!' . $end - $start]);
    }
}
