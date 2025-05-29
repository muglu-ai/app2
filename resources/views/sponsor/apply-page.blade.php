@extends('layouts.sponsor-application')
@section('title', 'Applicant Details')
@section('content')
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
      background-color: #c137f7;
      color: #ffffff;
      font-weight: bold;
      padding: 10px;
      margin-top: 30px;
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
    <tr><th>Company Name</th><td>{{ $application->company_name }}</td></tr>
    <tr><th>Address</th><td>{{ $application->address }}</td></tr>
    <tr><th>City</th><td>{{ $application->city }}</td></tr>
    <tr><th>Website</th><td><a href="{{ $application->website }}">{{ $application->website }}</a></td></tr>
  </table>

  <!-- Contact -->
  <div class="section-title">Primary Contact</div>
  <table class="custom-table">
    <tr><th>Full Name</th><td>{{ $primaryContact->title }} {{ $primaryContact->first_name }} {{ $primaryContact->last_name }}</td></tr>
    <tr><th>Email</th><td>{{ $primaryContact->contact_email }}</td></tr>
    <tr><th>Phone</th><td>{{ $primaryContact->contact_number }}</td></tr>
    <tr><th>Job Title</th><td>{{ $primaryContact->job_title }}</td></tr>
  </table>

  <!-- Application Details -->
  <div class="section-title">Participation Details</div>
  <table class="custom-table">
    <tr><th>Sector</th><td>{{ $application->sector }}</td></tr>
    <tr><th>Sub-Sector</th><td>{{ $application->sub_sector }}</td></tr>
    <tr><th>Region</th><td>{{ $application->region }}</td></tr>
    <tr><th>Previous Participation</th><td>{{ $application->participated_previous ? 'Yes' : 'No' }}</td></tr>
    <tr><th>Stall Category</th><td>{{ $application->stall_category }}</td></tr>
    <tr><th>Applying For</th><td>{{ $application->applying_for }}</td></tr>
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
        {{ $sqm }}
        ({{ $sqmValue }} × ₹{{ number_format($rate) }})
        = ₹{{ number_format($total) }} + 18% GST
      </td>
    </tr>
  </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
