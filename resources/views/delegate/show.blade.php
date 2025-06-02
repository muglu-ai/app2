@extends('layouts.dashboard')

@section('content')
    <h2 class="text-xl font-bold mb-4">Organization Details</h2>

    <div class="bg-white shadow rounded p-6 mb-6">
        <h3 class="text-lg font-semibold mb-2">Organization Info</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><strong>Registration Date:</strong> {{ \Carbon\Carbon::parse($organization->reg_date)->format('d M Y') }}
            </div>
            <div><strong>TIN No:</strong> {{ $organization->tin_no }}</div>
            <div><strong>PIN No:</strong> {{ $organization->pin_no ?? 'N/A' }}</div>
            <div><strong>Organization Name:</strong> {{ $organization->org_name }}</div>
            <div><strong>Org Type:</strong> {{ $organization->org_type }}</div>
            <div><strong>Sector:</strong> {{ $organization->sector }}</div>
            <div><strong>Nationality:</strong> {{ $organization->nationality }}</div>
            <div><strong>Payment Status:</strong> {{ $organization->pay_status }}</div>
            <div><strong>Amount:</strong> ₹{{ number_format($organization->total, 2) }}</div>
            <div><strong>Promo Code:</strong> {{ $organization->promo_code ?? 'N/A' }}</div>
            <div><strong>Tax:</strong> ₹{{ $organization->tax }}</div>
            <div><strong>Processing Charge:</strong> ₹{{ $organization->processing_charge }}</div>
            <div><strong>Total Received:</strong> ₹{{ $organization->total_amt_received ?? 'N/A' }}</div>
            <div><strong>Contact Person:</strong> {{ $organization->cp_fname }} {{ $organization->cp_lname }}</div>
            <div><strong>Email:</strong> {{ $organization->cp_email }}</div>
            <div><strong>Mobile:</strong> {{ $organization->cp_mobile }}</div>
            <div><strong>GST Invoice Needed:</strong> {{ $organization->is_gst_invoice_needed }}</div>
            <div><strong>GST No:</strong> {{ $organization->gst_inv_reg_no ?? 'N/A' }}</div>
        </div>
    </div>

    <div class="bg-white shadow rounded p-6">
        <h3 class="text-lg font-semibold mb-2">Delegates ({{ $organization->delegates->count() }})</h3>

        <table class="table-auto w-full border border-gray-200 mt-2 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 text-left">Name</th>
                    <th class="p-2 text-left">Email</th>
                    <th class="p-2 text-left">Mobile</th>
                    <th class="p-2 text-left">Designation</th>
                    <th class="p-2 text-left">Ticket Category</th>
                    <th class="p-2 text-left">Ticket Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($organization->delegates as $delegate)
                    <tr class="border-t">
                        <td class="p-2">{{ $delegate->title }} {{ $delegate->first_name }} {{ $delegate->last_name }}</td>
                        <td class="p-2">{{ $delegate->email }}</td>
                        <td class="p-2">{{ $delegate->mobile }}</td>
                        <td class="p-2">{{ $delegate->designation }}</td>
                        <td class="p-2">{{ $delegate->ticket_category }}</td>
                        <td class="p-2">₹{{ number_format($delegate->ticket_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
