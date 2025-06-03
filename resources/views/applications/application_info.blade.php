@extends('layouts.users')
@section('title', 'Application Info')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-3 border-bottom pb-2">
        <div class="col-12">
            <h4 class="fw-bold">Application Info</h4>
            <p class="text-muted">Application No: <strong>{{ $application->application_id }}</strong></p>
        </div>
    </div>

    <h5 class="fw-semibold border-bottom pb-2 mb-4">Company Details</h5>

    <div class="row gy-3">
        <x-app-info label="Billing Country" :value="optional($application->country)->name" />
        <x-app-info label="GST Compliance" :value="$application->gst_compliance ? 'Yes' : 'No'" />
        @if($application->gst_compliance)
            <x-app-info label="GST Number" :value="$application->gst_no" />
        @endif

        <x-app-info label="PAN Number" :value="$application->pan_no" />
        <x-app-info label="TAN Number" :value="$application->tan_no" />
        <x-app-info label="Company Name" :value="$application->company_name" />
        <x-app-info label="Company Address" :value="$application->address" />
        <x-app-info label="Postal Code" :value="$application->postal_code" />
        <x-app-info label="City" :value="$application->city_id" />
        <x-app-info label="State" :value="optional($application->state)->name" />
        <x-app-info label="Company Contact/Landline No" :value="$application->landline" />
        <x-app-info label="Company Email" :value="$application->company_email" />
        <x-app-info label="Website" :value="$application->website" :link="true" />
        <x-app-info label="Main Product Category" :value="optional($productCategories->firstWhere('id', $application->main_product_category))->name" />
        <x-app-info label="Type of Business" :value="$application->type_of_business" />
    </div>

    <h5 class="fw-semibold border-bottom pb-2 mt-5 mb-4">Event Contact Person Details</h5>
    <div class="row gy-3">
        <x-app-info label="Name & Designation" :value="$eventContact->salutation . ' ' . $eventContact->first_name . ' ' . $eventContact->last_name . ', ' . $eventContact->job_title" />
        <x-app-info label="Contact Email" :value="$eventContact->email" />
        <x-app-info label="Mobile Number" :value="$eventContact->contact_number" />
    </div>

    <h5 class="fw-semibold border-bottom pb-2 mt-5 mb-4">Billing Details</h5>
    <div class="row gy-3">
        <x-app-info label="Billing Company" :value="$billingDetails->billing_company" />
        <x-app-info label="Contact Name" :value="$billingDetails->contact_name" />
        <x-app-info label="Email" :value="$billingDetails->email" />
        <x-app-info label="Phone Number" :value="$billingDetails->phone" />
        <x-app-info label="Billing Address" :value="$billingDetails->address" />
        <x-app-info label="Billing City" :value="$billingDetails->city_id" />
        <x-app-info label="Billing Postal Code" :value="$billingDetails->postal_code" />
        <x-app-info label="State" :value="optional($billingDetails->state)->name" />
    </div>
</div>
@endsection
