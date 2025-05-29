<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{config('constants.EVENT_NAME')}} - Proforma Invoice</title>
    <link rel="icon" type="image/png" href="{{ config('constants.FAVICON_16') }}" sizes="16x16">
        <link rel="icon" type="image/png" href="{{ config('constants.FAVICON') }}">
        <link rel="apple-touch-icon" href="{{ config('constants.FAVICON_APPLE') }}">
    <style type="text/css">
        /* CSS Reset (simplified for email) */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; background-color: #f4f4f4; }
        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4;">
    <!-- Main Table -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="background-color: #f4f4f4;">
                <!--[if (gte mso 9)|(IE)]>
                <table align="center" border="0" cellspacing="0" cellpadding="0" width="600">
                <tr>
                <td align="center" valign="top" width="600">
                <![endif]-->
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border: 1px solid #dddddd;">
                    <!-- LOGO SECTION -->
                    <tr>
                        <td align="center" style="padding: 20px 0 10px 0;">
                            <img src="{{config('constants.event_logo')}}" alt="{{config('constants.EVENT_NAME')}}" width="200" style="display: block; font-family: Arial, sans-serif; color: #333333; font-size: 16px;" border="0">
                        </td>
                    </tr>

                    <!-- BOOKING ID SECTION -->
                    <tr>
                        <td colspan="2" style="padding: 5px 30px 10px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="font-family: Arial, sans-serif; font-size: 16px; color: #555555;">
                                <tr>
                                    <td align="left" width="70%" style="padding: 0;">
                                        <strong>Booking ID:</strong> {{$application->application_id}}
                                    </td>
                                    <td align="right" width="30%" style="padding: 0;">
                                        <strong>Date:</strong> {{$application->submission_date}}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- HERO SECTION / TITLE -->
                    <tr>
                        <td style="padding: 15px 30px 15px 30px; background-color: #4A0072; color: #ffffff;">
                            <h1 style="margin: 0; font-family: Arial, sans-serif; font-size: 24px; font-weight: bold; text-align: center;">
                                Provisional Receipt
                            </h1>
                        </td>
                    </tr>

                    <!-- CONTENT SECTION: GREETING -->
                    <tr>
                        <td style="padding: 20px 30px 10px 30px; font-family: Arial, sans-serif; font-size: 16px; line-height: 1.6; color: #333333;">
                            <p style="margin: 0;">Dear {{$application->eventContact->salutation}} {{$application->eventContact->first_name}} {{$application->eventContact->last_name}} and the {{$application->company_name}} Team,</p>
                            <p style="margin-top: 15px;">Thank you for your interest in exhibiting at the upcoming <strong>{{config('constants.EVENT_NAME')}} {{config('constants.EVENT_YEAR')}}</strong>. Please find your Proforma Invoice for the stall booking below.</p>
                        </td>
                    </tr>

                    <!-- BOOKING SUMMARY TABLE -->
                    <tr>
                        <td style="padding: 10px 30px;">
                            <h2 style="font-family: Arial, sans-serif; color: #4A0072; font-size: 18px; margin-top: 0; margin-bottom: 10px;">Exhibitor & Stall Details</h2>
                            <table border="0" cellpadding="5" cellspacing="0" width="100%" style="border-collapse: collapse; font-family: Arial, sans-serif; font-size: 14px; color: #555555;">
                                <tr>
                                    <td width="40%" style="border: 1px solid #dddddd; padding: 8px; background-color: #f9f9f9;"><strong>Company Name</strong></td>
                                    <td width="60%" style="border: 1px solid #dddddd; padding: 8px;">{{$application->company_name}}</td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #dddddd; padding: 8px; background-color: #f9f9f9;"><strong>Contact Person</strong></td>
                                    <td style="border: 1px solid #dddddd; padding: 8px;">{{$application->eventContact->salutation}} {{$application->eventContact->first_name}} {{$application->eventContact->last_name}}</td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #dddddd; padding: 8px; background-color: #f9f9f9;"><strong>Billing Email</strong></td>
                                    <td style="border: 1px solid #dddddd; padding: 8px;">{{$application->eventContact->email}} </td>
                                </tr>
                                {{-- <tr>
                                    <td style="border: 1px solid #dddddd; padding: 8px; background-color: #f9f9f9;"><strong>Application For</strong></td>
                                    <td style="border: 1px solid #dddddd; padding: 8px;">Exhibition</td>
                                </tr> --}}
                                <tr>
                                    <td style="border: 1px solid #dddddd; padding: 8px; background-color: #f9f9f9;"><strong>Sector</strong></td>
                                    <td style="border: 1px solid #dddddd; padding: 8px;">{{$application->sector->name}}</td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #dddddd; padding: 8px; background-color: #f9f9f9;"><strong>Sub-Sector</strong></td>
                                    <td style="border: 1px solid #dddddd; padding: 8px;">{{$application->sub_sector}}</td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #dddddd; padding: 8px; background-color: #f9f9f9;"><strong>Stall Category</strong></td>
                                    <td style="border: 1px solid #dddddd; padding: 8px;">{{$application->stall_category}}</td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #dddddd; padding: 8px; background-color: #f9f9f9;"><strong>Stall Size</strong></td>
                                    <td style="border: 1px solid #dddddd; padding: 8px;">{{ (int) $application->interested_sqm }} SQM</td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #dddddd; padding: 8px; background-color: #f9f9f9;"><strong>Billing Address</strong></td>
                                    <td style="border: 1px solid #dddddd; padding: 8px;">{{ $application->address }} {{ $application->city_id }} {{ $application->state->name }}
                                    - {{ $application->postal_code }} , {{ $application->country->name }}</td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #dddddd; padding: 8px; background-color: #f9f9f9;"><strong>GST Compliance</strong></td>
                                    <td style="border: 1px solid #dddddd; padding: 8px;">{{ $application->gst_compliance == 1 ? 'Yes' : 'No' }}
                                    @if ($application->gst_compliance == 1 && $application->gst_no)
                                        &nbsp;|&nbsp; <strong>GST No:</strong> {{ $application->gst_no }}
                                    @endif</td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #dddddd; padding: 8px; background-color: #f9f9f9;"><strong>Company PAN No.</strong></td>
                                    <td style="border: 1px solid #dddddd; padding: 8px;">{{$application->pan_no}}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- PAYMENT DETAILS TABLE -->
                    <tr>
                        <td style="padding: 20px 30px 10px 30px;">
                            <h2 style="font-family: Arial, sans-serif; color: #4A0072; font-size: 18px; margin-top: 0; margin-bottom: 10px;">Payment Information</h2>
                            <table border="0" cellpadding="5" cellspacing="0" width="100%" style="border-collapse: collapse; font-family: Arial, sans-serif; font-size: 14px; color: #555555;">
                                <tr>
                                    <td width="70%" style="border: 1px solid #dddddd; padding: 8px; background-color: #f9f9f9;">Stall Price ({{ (int) $application->interested_sqm }} SQM * INR {{ $application->stall_category == 'Shell Scheme' ? config('constants.SHELL_SCHEME_RATE') : config('constants.RAW_SPACE_RATE') }})</td>
                                    <td width="30%" style="border: 1px solid #dddddd; padding: 8px; text-align: right;">{{$application->payment_currency}} {{$invoice->price}}</td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #dddddd; padding: 8px; background-color: #f9f9f9;">GST {{config('constants.GST_RATE')}}%</td>
                                    <td style="border: 1px solid #dddddd; padding: 8px; text-align: right;">{{$application->payment_currency}} {{$invoice->gst}}</td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #dddddd; padding: 8px; background-color: #f9f9f9;">Processing Charge</td>
                                    <td style="border: 1px solid #dddddd; padding: 8px; text-align: right;">{{$application->payment_currency}} {{$invoice->processing_charges}}</td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #dddddd; padding: 8px; background-color: #f0e6f6; font-weight: bold;"><strong>Total Amount Payable</strong></td>
                                    <td style="border: 1px solid #dddddd; padding: 8px; text-align: right; background-color: #f0e6f6; font-weight: bold;"><strong>{{$application->payment_currency}} {{$invoice->amount}}</strong></td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- PAYMENT INFORMATION & BUTTON -->
                    <tr>
                        <td style="padding: 20px 30px 20px 30px; font-family: Arial, sans-serif; font-size: 16px; line-height: 1.6; color: #333333;">
                            <form action="{{ route('exhibitor.ccavenue.payment', ['id' => $application->application_id]) }}" method="POST" style="margin:0;">
                                @csrf

                                <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                    <tr>
                                        <td align="center">
                                            <table border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td style="padding-bottom: 10px; display: none;">
                                                        <label for="payer_name" style="font-family: Arial, sans-serif; font-size: 14px; color: #333333;">Payer Name:</label>
                                                        <input type="hidden" id="payer_name" name="payer_name" required style="padding: 6px; border: 1px solid #ccc; border-radius: 3px; width: 200px;">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="padding-bottom: 10px; display: none;">
                                                        <label for="payer_email" style="font-family: Arial, sans-serif; font-size: 14px; color: #333333;">Payer Email:</label>
                                                        <input type="hidden" id="payer_email" name="payer_email" required style="padding: 6px; border: 1px solid #ccc; border-radius: 3px; width: 200px;">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td align="center" style="border-radius: 5px; background-color: #007bff;">
                                                        <button type="submit" style="font-size: 16px; font-family: Arial, sans-serif; color: #ffffff; text-decoration: none; border-radius: 5px; padding: 12px 25px; border: 1px solid #007bff; background: #007bff; display: inline-block; font-weight: bold; cursor: pointer;">
                                                            CLICK HERE TO PAY NOW
                                                        </button>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </form>
                        </td>
                    </tr>

                    <!-- TERMS & CONDITIONS (Optional for Proforma, but good to include a link) -->
                    <tr>
                        <td style="padding: 10px 30px 10px 30px; font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6; color: #333333;">
                            <p style="margin: 0;">For details on cancellation, booth allocation, and other exhibitor terms, please refer to our <a href="{{config('constants.TERMS_URL')}}" target="_blank" style="color: #007bff; text-decoration: underline;">Exhibitor Terms & Conditions</a>.</p>
                        </td>
                    </tr>


                    <!-- SOCIAL MEDIA HANDLES -->
                    <tr>
                        <td style="padding: 20px 30px 10px 30px; background-color: #f0e6f6;">
                            <h2 style="font-family: Arial, sans-serif; color: #4A0072; font-size: 18px; margin-top: 0; margin-bottom: 15px; text-align: center;">Stay Connected</h2>
                            <p style="font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6; color: #333333; text-align: center; margin-bottom: 15px;">
                                Follow us on our social media channels for the latest event updates.
                            </p>
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center" style="font-family: Arial, sans-serif; font-size: 14px;">
                                        <a href="{{config('constants.SOCIAL_LINKS.linkedin')}}" target="_blank" style="color: #0077b5; text-decoration: none; margin: 0 10px;">LinkedIn</a> |
                                        <a href="{{config('constants.SOCIAL_LINKS.twitter')}}" target="_blank" style="color: #1da1f2; text-decoration: none; margin: 0 10px;">Twitter</a> |
                                        <a href="{{config('constants.SOCIAL_LINKS.facebook')}}" target="_blank" style="color: #3b5998; text-decoration: none; margin: 0 10px;">Facebook</a> |
                                        <a href="{{config('constants.SOCIAL_LINKS.instagram')}}" target="_blank" style="color: #e4405f; text-decoration: none; margin: 0 10px;">Instagram</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                     <!-- CLOSING -->
{{--                    <tr>--}}
{{--                        <td style="padding: 20px 30px 10px 30px; font-family: Arial, sans-serif; font-size: 16px; line-height: 1.6; color: #333333;">--}}
{{--                            <p style="margin: 0;">We look forward to your participation at the Bengaluru Tech Summit! Should you have any questions regarding this Proforma Invoice or the payment process, please do not hesitate to contact us.</p>--}}
{{--                            <p style="margin-top: 15px;">Sincerely,</p>--}}
{{--                            <p style="margin:0;"><strong>The Bengaluru Tech Summit Team</strong></p>--}}
{{--                        </td>--}}
{{--                    </tr>--}}

                    <!-- ORGANISER DETAILS -->
                    <tr>
                        <td style="padding: 20px 30px; background-color: #333333; color: #cccccc;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="border: none;">
                                <tr>
                                    <td width="120" align="left" valign="top" style="padding-right: 20px;">
                                        <img src="{{config('constants.organizer_logo')}}" alt="Organizer Logo" width="100" style="display: block; border-radius: 5px; background: #fff; padding: 5px;">
                                    </td>
                                    <td align="left" valign="top">
                                        <h3 style="font-family: Arial, sans-serif; color: #ffffff; font-size: 16px; margin-top: 0; margin-bottom: 10px; text-align: left;">Organiser Details</h3>
                                        <p style="font-family: Arial, sans-serif; font-size: 13px; line-height: 1.5; margin: 5px 0; text-align: left;">
                                            <strong>{{config('constants.organizer.name')}}</strong><br>
                                            {{config('constants.organizer.address')}}<br>
                                            GSTIN: {{config('constants.GSTIN')}}<br>
                                            Website: <a href="{{config('constants.organizer.website')}}" target="_blank" style="color: #87cefa; text-decoration: underline;">{{config('constants.organizer.website')}}</a><br>
                                            Email: <a href="mailto:{{config('constants.organizer.email')}}" style="color: #87cefa; text-decoration: underline;">{{config('constants.organizer.email')}}</a><br>
                                            Phone: {{config('constants.organizer.phone')}}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td style="padding: 15px 30px; text-align: center; font-family: Arial, sans-serif; font-size: 12px; color: #888888;">
                            © {{ date('Y') }}  Bengaluru Tech Summit. All rights reserved.
                        </td>
                    </tr>

                </table>
                <!--[if (gte mso 9)|(IE)]>
                </td>
                </tr>
                </table>
                <![endif]-->
            </td>
        </tr>
    </table>
    <!-- End Main Table -->
</body>
</html>
