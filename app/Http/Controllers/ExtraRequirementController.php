<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\ExtraRequirement;
use Illuminate\Support\Facades\Log;
use App\Models\RequirementOrderItem;
use App\Models\RequirementsOrder;
use Illuminate\Support\Facades\Validator;
use App\Models\BillingDetail;
//use coexhibitor model
use App\Models\CoExhibitor;

class ExtraRequirementController extends Controller
{
    // Show all items
    public function index()
    {
        //if user is not logged in, redirect to login page
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        //if user is admin then return to extra_requirements.admin
        if (auth()->user()->role == 'admin') {
            return redirect()->route('extra_requirements.admin');
        }
        $items = ExtraRequirement::all();
        return view('extra_requirements.index', compact('items'));
    }

    // Show form to create new item
    public function create()
    {
        return view('extra_requirements.create');
    }

    //list of all requirements in json format
    public function list()
    {
        $items = ExtraRequirement::all();
        return response()->json($items);
    }

    // Store new item
    public function store0(Request $request)
    {
        //as data is in array format, we need to loop through each item and store it in database
        foreach ($request->all() as $item) {
            Log::info('items', $item);
            $validator = \Validator::make($item, [
                'item_id' => 'required|integer|exists:extra_requirements,id',
                'quantity' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            Log::info($item);
            //ExtraRequirement::create($item);
        }
        //return json response with  message data received successfully
        return response()->json(['message' => 'Data received successfully']);

        return redirect()->route('extra_requirements.index')->with('success', 'Item added successfully!');
    }

    public function store1(Request $request)
    {
        // Retrieve 'items' array from the request
        $items = $request->input('items', []);

        // Check if items exist and are in the expected format
        if (!is_array($items) || empty($items)) {
            return response()->json(['error' => 'Invalid input format'], 422);
        }

        foreach ($items as $item) {
            Log::info('Processing item:', $item);

            // Ensure $item is an array before validating
            if (!is_array($item)) {
                return response()->json(['error' => 'Invalid item format'], 422);
            }

            $validator = \Validator::make($item, [
                'item_id' => 'required|integer|exists:extra_requirements,id',
                'quantity' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }



            Log::info('Validated item:', $item);
            // ExtraRequirement::create($item);
        }

        return response()->json(['message' => 'Data received successfully']);
    }


    // Generate a unique invoice number 
    private function generateUniqueInvoiceNo()
    {
        $invoice_no = 'INV-SEMI25-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        if (Invoice::where('invoice_no', $invoice_no)->exists()) {
            return $this->generateUniqueInvoiceNo();
        }
        return $invoice_no;
    }


    public function store(Request $request)
    {
        // Validate the request format
        $items = $request->input('items', []);
        if (!is_array($items) || empty($items)) {
            return response()->json(['error' => 'Invalid input format'], 422);
        }

        //if user is admin then return to extra_requirements.admin
        //        if (auth()->user()->role == 'admin') {
        //            return redirect()->route('extra_requirements.admin');
        //        }
        //
        //

        // Ensure user and application exist
        //$application_id = $request->input('application_id');

        $user_id = auth()->id();

        //if the user role is co-exhibitor, get the application_id from co_exhibitors table and get the application_id and find the application from that id
        if (auth()->user()->role == 'co-exhibitor') {
            // Log::info('User is a co-exhibitor', ['user_id' => $user_id]);
            $email = auth()->user()->email;
            $CoExh = CoExhibitor::where('email', $email)->first();
            if (!$CoExh) {
                return response()->json(['error' => 'CoExhibitor record not found'], 404);
            }
            //Log::info('CoExhibitor record found', ['CoExh' => $CoExh]);
            $application_id = $CoExh->application->id;
            //Log::info('CoExhibitor application_id', ['application_id' => $application_id]);
        } else {
            //get the user application_id from the application table where user_id is equal to the user_id
            $application_id = Application::where('user_id', $user_id)->first()->id;
        }
        //get the user application_id from the application table where user_id is equal to the user_id
        //$application_id = Application::where('user_id', $user_id)->first()->id;

        if (!$application_id || !$user_id) {
            return response()->json(['error' => 'Application ID and User ID are required'], 422);
        }

        $country = BillingDetail::where('application_id', $application_id)->first()->country->name;
        //Log::info('Country:', $country);

        // Create an invoice for the order
        $invoice = Invoice::create([
            'application_id' => $application_id,
            'amount' => 0, // Will update after item calculations
            'currency' => 'INR',
            'payment_status' => 'unpaid',
            'payment_due_date' => now()->addDays(7),
            'discount_per' => 0,
            'discount' => 0,
            'gst' => 18, // GST 18%
            'price' => 0,
            'processing_charges' => 0,
            'total_final_price' => 0,
            'partial_payment_percentage' => 0,
            'pending_amount' => 0,
            'type' => 'extra_requirement',
            'invoice_no' => $this->generateUniqueInvoiceNo(),
            'amount_paid' => 0,
        ]);

        // Create the order`
        $order = RequirementsOrder::create([
            'application_id' => $application_id,
            'invoice_id' => $invoice->id,
            'user_id' => $user_id,
        ]);

        $total_price = 0;

        foreach ($items as $item) {
            //Log::info('Processing item:', $item);

            // Validate item data
            $validator = Validator::make($item, [
                'item_id' => 'required|integer|exists:extra_requirements,id',
                'quantity' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $requirement = ExtraRequirement::find($item['item_id']);
            $unit_price = $requirement->price_for_expo ?? 0; // Assume the requirement has a price field

            $subtotal = $unit_price * $item['quantity'];

            $total_price += $subtotal;

            // Store order items
            RequirementOrderItem::create([
                'requirements_order_id' => $order->id,
                'requirement_id' => $item['item_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $unit_price,
            ]);
        }

        $item_price = $total_price;

        //calculate the processing charges and add it to the total price
        $processing_charges = 0;
        //Log::info('Country: ' . $country);
        //if billing_country is not India then processing charges is 9% of total price else 2% of total price
        if ($country != 'India') {
            $processing_charges = round(($total_price * 9) / 100, 2);
        } else {
            $processing_charges = round(($total_price * 2) / 100, 2);
        }

        // if ($total_price > 0) {
        //     $processing_charges = round(($total_price * 2) / 100, 2);
        // }

        $total_price += $processing_charges;




        // Update invoice amounts
        $gst_amount = round(($total_price * 18) / 100);




        $final_total_price = round($total_price + $gst_amount, 2);

        //3550.62 round off to 3551
        $final_total_price = round($final_total_price);

        if ($country != 'India') {
            // Path to store the last successful exchange rate
            $rate_file = "exchange_rate.json";

            // Function to get the last stored rate
            function get_last_stored_rate($rate_file)
            {
                if (file_exists($rate_file)) {
                    $stored_data = json_decode(file_get_contents($rate_file), true);
                    if (isset($stored_data["INR"])) {
                        return $stored_data["INR"];
                    }
                }
                return null; // Return null if no stored rate exists
            }

            // Fetch the latest exchange rate from API
            $api_url = "https://v6.exchangerate-api.com/v6/303f4de10b784cbb27e4a065/latest/USD";
            $response = @file_get_contents($api_url); // Suppress errors if API fails
            $data = $response ? json_decode($response, true) : null;

            // Check if API call was successful
            if ($data && isset($data["conversion_rates"]["INR"])) {
                $inr_to_usd_rate = $data["conversion_rates"]["INR"];

                // Save the latest rate to file
                file_put_contents($rate_file, json_encode(["INR" => $inr_to_usd_rate]));
            } else {
                // Use last stored rate if API fails
                $inr_to_usd_rate = get_last_stored_rate($rate_file);

                if (!$inr_to_usd_rate) {
                    Log::info("Error: Unable to fetch exchange rates, and no stored rate available.");
                }
            }

            // Convert INR to USD
            $final_total_price_usd = $final_total_price / $inr_to_usd_rate;
            $final_total_price_usd = round($final_total_price_usd, 2); // Round to 2 decimal places
        }

        // Log::info('Final total price:', [
        //     'total_price' => $total_price,
        //     'processing_charges' => $processing_charges,
        //     'gst_amount' => $gst_amount,
        //     'final_total_price' => $final_total_price,
        //     'final_total_price_usd' => $final_total_price_usd ?? null,
        //     'co_exhibitorID' => $CoExh->id ?? 'test',
        //     'usd_rate' => $inr_to_usd_rate ?? 'test',
        // ]);
        $invoice->update([
            'processing_charges' => $processing_charges,
            'price' => $item_price,
            'amount' => $final_total_price,
            'gst' => $gst_amount,
            'total_final_price' => $final_total_price,
            'int_amount_value' => $final_total_price_usd,
            'pending_amount' => $final_total_price,
            'co_exhibitorID' => $CoExh->id ?? 'test',
            'usd_rate' => $inr_to_usd_rate ?? 'test',
        ]);

        return response()->json([
            'message' => 'Order created successfully',
            'order_id' => $order->id,
            'invoice_id' => $invoice->id,
            'total_price' => $final_total_price,
        ]);
    }

    public function userOrders(Request $request)
    {
        //if user is not logged in, redirect to login page
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        $user_id = auth()->id();

        // Fetch all orders placed by the user along with related data
        $orders = RequirementsOrder::where('user_id', $user_id)
            ->with(['invoice', 'orderItems.requirement'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('extra_requirements.orders', compact('orders'));
        //        return response()->json($orders);
    }



    public function allOrders()
    {



        //if user type is admin then route to extra_requirements.admin
        //        if (auth()->user()->role == 'admin') {
        //            return redirect()->route('extra_requirements.admin');
        //        }
        // Fetch all orders with related user, invoice, and order items
        $orders = RequirementsOrder::with(['user', 'invoice', 'orderItems.requirement'])
            ->orderBy('created_at', 'desc')
            ->get();



        return view('extra_requirements.admin.list', compact('orders'));
    }



    // Show a single item
    public function show(ExtraRequirement $extraRequirement)
    {
        return view('extra_requirements.show', compact('extraRequirement'));
    }

    // Show form to edit item
    public function edit(ExtraRequirement $extraRequirement)
    {
        return view('extra_requirements.edit', compact('extraRequirement'));
    }

    // Update item details
    public function update(Request $request, ExtraRequirement $extraRequirement)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'days' => 'required|integer|min:1',
            'price_for_expo' => 'required|numeric|min:0',
            'image_quantity' => 'nullable|integer|min:0',
            'available_quantity' => 'nullable|integer|min:0',
            'status' => 'required|in:available,out_of_stock',
        ]);

        $extraRequirement->update($request->all());
        return redirect()->route('extra_requirements.index')->with('success', 'Item updated successfully!');
    }

    // Delete item
    public function destroy(ExtraRequirement $extraRequirement)
    {
        $extraRequirement->delete();
        return redirect()->route('extra_requirements.index')->with('success', 'Item deleted successfully!');
    }
}
