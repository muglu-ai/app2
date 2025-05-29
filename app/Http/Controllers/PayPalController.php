<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PaypalServerSdkLib\PaypalServerSdkClientBuilder;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use PaypalServerSdkLib\Environment;
use PaypalServerSdkLib\PaypalServerSdkClient;
use PaypalServerSdkLib\Models\Builders\OrderRequestBuilder;
use PaypalServerSdkLib\Models\CheckoutPaymentIntent;
use PaypalServerSdkLib\Models\Builders\PurchaseUnitRequestBuilder;
use PaypalServerSdkLib\Models\Builders\AmountWithBreakdownBuilder;
use App\Models\Invoice;
use App\Models\BillingDetail;
use App\Models\RequirementsOrder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Mail\ExtraRequirementsMail;
use Illuminate\Support\Facades\Mail;
use App\Services\ExtraRequirementsMailService;
use Illuminate\Support\Facades\Response;

use App\Models\PaymentGatewayResponse;

class PayPalController extends Controller
{
    // Step 1: Show Payment Form
    public function showPaymentForm($id)
    {
        //if not id then redirect to route exhibitor.orders
        if (!$id) {
            return redirect()->route('exhibitor.orders');
        }

        // get the invoice details from the Invoice model where invoice_no = $id
        $invoice = Invoice::where('invoice_no', $id)->first();

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

        //find the orderItems from the
        $orders = RequirementsOrder::where('invoice_id', $invoice->id)
            ->with(['invoice', 'orderItems.requirement'])
            ->orderBy('created_at', 'desc')
            ->get();



        //if invoice is not paid then show the payment form
        return view('paypal.payment-form', compact('invoice', 'billingDetail', 'orders'));
    }

    private $client;

    public function __construct()
    {
        $this->client = PaypalServerSDKClientBuilder::init()
            ->clientCredentialsAuthCredentials(
                ClientCredentialsAuthCredentialsBuilder::init(
                    env('PAYPAL_CLIENT_ID'),
                    env('PAYPAL_SECRET')
                )
            )
            ->environment(env('PAYPAL_ENV', Environment::SANDBOX))

            ->build();
    }

    public function checkoutForm()
    {
        return view('paypal.payment-form');
    }

    public function createOrder(Request $request)
    {

        Log::info($request->all());
        // Validate the request
        $request->validate([
            'invoice' => 'required|string|exists:invoices,invoice_no',
        ]);

        // Check if the invoice number exists in the Invoice model
        $invoice = Invoice::where('invoice_no', $request->invoice)->first();
        if (!$invoice) {
            return response()->json(['error' => 'Invoice not found'], 404);
        }

        $billingDetail = BillingDetail::where('application_id', $invoice->application_id)->first();
        $order_ID = $invoice->invoice_no . '_' . substr(uniqid(), -5);
        $order = $order_ID;
        $amount = $invoice->int_amount_value;

        $email = $billingDetail->email;
        $company = $billingDetail->billing_company;

        $purchaseUnit = PurchaseUnitRequestBuilder::init(
            AmountWithBreakdownBuilder::init('USD', $amount)->build()
        )
            ->description('Extra Requirements for ' . $company)   // Optional description
            ->invoiceId($order_ID)            // PayPal invoice tracking
            ->build();


        $orderBody = [
            'body' => OrderRequestBuilder::init(
                CheckoutPaymentIntent::CAPTURE,
                [$purchaseUnit]
            )->build()
        ];

        try {
            $apiResponse = $this->client->getOrdersController()->ordersCreate($orderBody);
            //get the id from the response
            $order_ID = $apiResponse->getResult()->getId();
            //insert into payment response table
            $data = [
                'merchant_id' => null,
                'payment_id' => $order_ID,
                'order_id' => $order,
                'currency' => 'USD',
                'amount' => $invoice->int_amount_value,
                'redirect_url' => null,
                'cancel_url' => null,
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

            \DB::table('payment_gateway_response')->insert([
                'merchant_data' => $merchantData,
                'order_id' => $data['order_id'],
                'payment_id' => $data['payment_id'],
                'amount' => $data['amount'],
                'status' => 'Pending',
                'gateway' => 'Paypal',
                'currency' => 'USD',
                'email' => $data['billing_email'],
                'created_at' => now(),
            ]);


            return response()->json($apiResponse->getResult());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function captureOrder($orderId)
    {
        try {
            $captureBody = ['id' => $orderId];
            $apiResponse = $this->client->getOrdersController()->ordersCapture($captureBody);
            //store the response in the payment_gateway_response table with insert
            \DB::table('payment_gateway_response')
                ->where('payment_id', $orderId)
                ->update([
                    'status' => 'Completed',
                    'response_json' => json_encode($apiResponse->getResult()),
                    'updated_at' => now(),
                ]);

            $apiEncodedResponse = json_encode($apiResponse->getResult());
            //decode the response
            //get the amount from the response
            $apiDecodedResponse = json_decode($apiEncodedResponse, true);
            $amountPaid = $apiDecodedResponse['purchase_units'][0]['payments']['captures'][0]['amount']['value'];
            //get the order_id from the response
            $pg_status = $apiDecodedResponse['status'];
            //if status is completed then mark it paid else mark it as failed
            if ($pg_status == 'COMPLETED') {
                $conf_status = 'paid';
            } else {
                $conf_status = 'failed';
            }



            //store the amount from the above json encode value

            $orderData = \DB::table('payment_gateway_response')
                ->where('payment_id', $orderId)
                ->select('order_id')
                ->first();

            // explode the orderData after _ and get the values
            $orderID = explode('_', $orderData->order_id);
            //find the
            $invoice = Invoice::where('invoice_no', $orderID)->first();



            //update the invoice table with the status as paid
            if ($conf_status == 'paid') {
                $invoice->update([
                    'payment_status' => $conf_status,
                    'amount_paid' => $amountPaid,
                    'updated_at' => now(),
                    'pending_amount' => 0,
                    'currency' => 'USD',
                ]);

                $service = new ExtraRequirementsMailService();
                $data = $service->prepareMailData($orderID[0]);
                $email = $data['billingEmail'];

                Mail::to($email)
                    ->bcc(array_merge(['test.interlinks@gmail.com'], config('constants.payment_emails.admin_emails', [])))
                    ->queue(new ExtraRequirementsMail($data));


            }





            //find the invoice from the payment_gateway_response table where payment_id = $orderId


            return response()->json($apiResponse->getResult());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    //webhoook insert into payment_gateway_response table
    public function webhook(Request $request)
    {
        Log::info('Paypal Webhook');
        $data = $request->all();
        Log::info('data ' . $data);
        try {
            \DB::table('payment_gateway_response')->insert([
                'merchant_data' => json_encode($data),
                'order_id' => $data['resource']['supplementary_data']['related_ids']['order_id'] ?? 'test',
                'amount' => $data['resource']['amount']['value'] ?? '0.00',
                'status' => $data['resource']['status'] ?? 'test',
                'gateway' => 'Paypal',
                'currency' => $data['resource']['amount']['currency_code'] ?? 'USD',
                'email' => $data['resource']['payer']['email_address'] ?? 'unknown@example.com',
                'response_json' => json_encode($data),
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error inserting payment gateway response: ' . $e->getMessage());
        }
    }


}
