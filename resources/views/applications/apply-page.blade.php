@extends('layouts.sponsor-application')
@section('title', 'Applicant Details')
@section('content')

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            font-size: 14px;
            background-color: #ffffff;
            color: #333;
        }

        .bts-header {
            background-color: #3a006a;
            color: #ffffff;
            padding: 20px;
            border-bottom: 5px solid #ff0090;
        }

        .bts-header h4 {
            color: #ff2dd2;
            font-weight: bold;
        }

        .section-title {
            background-color: #3a006a;
            color: #ffffff;
            font-weight: bold;
            padding: 10px;
            /* margin-top: 30px; */
            border-left: 5px solid #ff0090;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .custom-table th,
        .custom-table td {
            border: 1px solid #dee2e6;
            padding: 8px 12px;
            vertical-align: top;
        }

        .custom-table th {
            width: 30%;
            background-color: #f6f6f6;
            font-weight: 600;
        }

        .footer {
            font-size: 12px;
            color: #999;
            text-align: right;
            margin-top: 30px;
        }
    </style>
    <style>
        @media (min-width: 500px) {
            .progress-bar2 {
                display: none !important;
            }
        }

        .form-check-input.is-filled {
            color: black;
        }

        .form-label,
        label {
            color: #000;

        }

        .red-label {
            color: red;
            font-weight: bold;
        }

        .textB {
            color: #000 !important;
        }

        .custom-hr {
            border: none;
            height: 3px;
            background: #bfb8b8;
            width: 100%;
            margin: 20px auto;
        }

        .dropdown-item:hover {
            background-color: transparent !important;
        }

        .form-check-label {
            word-wrap: break-word;
            display: inline-block;
            /* Makes sure the label behaves properly inside the flex container */
            max-width: 100%;
            /* Ensures it doesn't overflow the container */
        }
    </style>
    <div class="container py-2">
        <div class="row min-vh-220 mt-5">
            <div class="col-lg-12 col-md-10 col-12 m-auto">
                <div class="card">
                    <div class="card-header p-0 position-relative mt-n5 mx-3 z-index-2">
                        <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                            <div class="multisteps-form__progress">
                                <button class="multisteps-form__progress-btn js-active" disabled>
                                    <span>1. Show Profile</span>
                                </button>
                                <button class="multisteps-form__progress-btn js-active" disabled>2. Application
                                    Form
                                </button>
                                <button class="multisteps-form__progress-btn" disabled>3. Terms and
                                    Conditions
                                </button>
                                <button class="multisteps-form__progress-btn" disabled>4. Review
                                </button>
                            </div>
                            <small class="progress-bar2 d-block text-center text-white">2. Product Info</small>

                        </div>
                    </div>
                    <div class="card-body" id="card-body">
                        <div class="section-title">Company Information</div>
                        <table class="custom-table">
                            <tr>
                                <th>Company Name</th>
                                <td>{{ $application->company_name }}</td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td>{{ $application->address }} {{ $application->city_id }} {{ $application->state->name }}
                                    - {{ $application->postal_code }} , {{ $application->country->name }} </td>
                            </tr>
                            <tr>
                                <th>Website</th>
                                <td><a href="{{ $application->website }}">{{ $application->website }}</a></td>
                            </tr>
                        </table>

                        <!-- Application Details -->
                        <div class="section-title">Participation Details</div>
                        <table class="custom-table">
                            <tr>
                                <th>Sector</th>
                                <td>{{ $application->sector->name }}</td>
                            </tr>
                            <tr>
                                <th>Sub-Sector</th>
                                <td>{{ $application->sub_sector }}</td>
                            </tr>
                            <tr>
                                <th>Region</th>
                                <td>{{ $application->region }}</td>
                            </tr>
                            <tr>
                                <th>Previous Participation</th>
                                <td>{{ $application->previous_participation ? 'Yes' : 'No' }}</td>
                            </tr>
                            <tr>
                                <th>Stall Category</th>
                                <td>{{ $application->stall_category }}</td>
                            </tr>
                            <tr>
                                <th>Applying For</th>
                                <td>{{ $application->sponsor_only == 0 ? 'Exhibition' : 'Sponsorship' }}</td>
                            </tr>
                            <tr>
                                <th>Interested SQM</th>

                                    <td>
        @php
            $sqm = $application->interested_sqm;
            $isShell = Str::contains(strtolower($sqm), 's');
            $sqmValue = (int) filter_var($sqm, FILTER_SANITIZE_NUMBER_INT);
            if ($isShell) {
                $rate = config('constants.SHELL_SCHEME_RATE');
            } else {
                $rate = config('constants.RAW_SPACE_RATE');
            }
            $total = $sqmValue * $rate;
        @endphp
        {{ $sqmValue }} sqm
        ({{ $sqmValue }} × ₹{{ number_format($rate) }})
        = ₹{{ number_format($total) }} + 18% GST
      </td>

                            </tr>
                        </table>

                        <!-- Contact -->
                        <div class="section-title">Primary Contact Person Details</div>
                        <table class="custom-table">
                            <tr>
                                <th>Full Name</th>
                                <td>{{ $application->eventContact->salutation }}
                                    {{ $application->eventContact->first_name }}
                                    {{ $application->eventContact->last_name }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $application->eventContact->email }}</td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td>{{ $application->eventContact->contact_number }}</td>
                            </tr>
                            <tr>
                                <th>Job Title</th>
                                <td>{{ $application->eventContact->job_title }}</td>
                            </tr>
                        </table>
                        <div class="section-title">Billing Details</div>
                        <table class="custom-table">
                            <tr>
                                <th>Billing Company</th>
                                <td>{{ $application->billingDetail->billing_company }}</td>
                                    </td>
                            </tr>
                            <tr>
                                <th>Contact Name</th>
                                <td>{{ $application->billingDetail->contact_name }}</td>
                            </tr>
                            <tr>
                                <th>Billing Email</th>
                                <td>{{ $application->billingDetail->email }}</td>
                            </tr>
                            <tr>
                                <th>Billing Mobile</th>
                                <td>{{ $application->billingDetail->phone }}</td>
                            </tr>
                            <tr>
                                <th>Billing Address</th>
                                <td>{{ $application->billingDetail->address }}, {{ $application->billingDetail->city_id }}, {{ $application->billingDetail->state->name }} - {{$application->billingDetail->postal_code }} {{$application->billingDetail->country->name }}</td>
                            </tr>
                            <tr>
                                <th>GST Compliance </th>
                                <td>
                                    {{ $application->gst_compliance == 1 ? 'Yes' : 'No' }}
                                    @if ($application->gst_compliance == 1 && $application->gst_no)
                                        &nbsp;|&nbsp; <strong>GST No:</strong> {{ $application->gst_no }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>PAN Number</th>
                                <td>{{ $application->pan_no }}</td>
                            </tr>
                        </table>


                        <div class="row justify-content-end">
                            <div class="col-md-4">
                                <div class="card shadow">
                                <div class="section-title card-header py-2 px-3">
                                    <strong >Pricing Details</strong>
                                </div>
                                <div class="card-body p-0">
                                    <table class="custom-table mb-0">
                                        @php
                                            $stallPrice = $total;
                                            $gst = round($stallPrice * 0.18);
                                            $processingCharge = config('constants.IND_PROCESSING_CHARGE');
                                            $processingBase = $stallPrice + $gst;
                                            if ($application->payment_currency == 'USD') {
                                                $processingChargeRate = config('constants.INT_PROCESSING_CHARGE', 0);
                                            } else {
                                                $processingChargeRate = config('constants.IND_PROCESSING_CHARGE', 0);
                                            }
                                            $processingCharge = round($processingBase * ($processingChargeRate / 100));
                                            $grandTotal = $stallPrice + $gst + $processingCharge;
                                        @endphp
                                        <tr>
                                            <th>Stall Price</th>
                                            <td>{{$application->payment_currency}} {{ number_format($stallPrice) }}</td>
                                        </tr>
                                        <tr>
                                            <th>GST ({{ config('constants.GST_RATE') }}%)</th>
                                            <td>{{$application->payment_currency}} {{ number_format($gst) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Processing Charge</th>
                                            <td>{{$application->payment_currency}} {{ number_format($processingCharge) }}</td>
                                        </tr>
                                        <tr>
                                            <th><strong>Total</strong></th>
                                            <td><strong>{{$application->payment_currency}} {{ number_format($grandTotal) }}</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            </div>
                        </div>

                        <div class="mt-4 justify-center">
                            <div class="alert alert-warning mb-3" role="alert">
                                <strong>Note:</strong> Once the application is submitted, it cannot be edited.
                            </div>
                            <div class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-2">
                                <a href="{{ route('new_form', ['event' => $application->event->slug]) }}" class="btn btn-secondary">
                                    &larr; Go Back
                                </a>
                                <form action="{{route('application.submit.store')}}" method="POST" class="d-inline m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        Submit &amp; Pay Now
                                    </button>
                                </form>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
