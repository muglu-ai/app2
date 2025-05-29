<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Event Registration Confirmation</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f5f5f5; color: #333333;">
  <table cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width: 600px; margin: 0 auto; background-color: #ffffff;">
    <tr>
      <td style="padding: 40px 30px;">
        <!-- Header with Logo -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td align="center" style="padding-bottom: 30px;">
                <img src="{{ config('constants.HOSTED_URL') }}/asset/img/logos/logo.png?height=80&width=320" alt="Event Logo" style="max-height: 80px; max-width: 320px;">
            </td>
          </tr>
        </table>

        <!-- Confirmation Message -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td align="center" style="padding-bottom: 30px;">
              <h1 style="font-size: 24px; font-weight: bold; color: #333333; margin: 0 0 10px 0;">Registration Confirmed</h1>
                <p style="font-size: 16px; color: #666666; margin: 0;">Thank you for registering for {{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}. Your ticket is ready!</p>
            </td>
          </tr>
        </table>

        <!-- Event Details Card -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f9f9f9; border-radius: 8px; border: 1px solid #e0e0e0; margin-bottom: 30px;">
          <tr>
            <td style="padding: 25px;">
                <h2 style="font-size: 20px; font-weight: bold; color: #333333; margin: 0 0 20px 0;">{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</h2>
              
              <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td valign="top" style="padding-bottom: 15px;">
                    <table cellpadding="0" cellspacing="0" border="0">
                      <tr>
                        <td valign="top" style="padding-right: 15px;">
                          <img src="{{ config('constants.HOSTED_URL') }}/mails/calendar-icon.png" alt="Calendar" width="20" height="20" style="vertical-align: middle;">
                        </td>
                        <td style="font-size: 16px; color: #555555;">{{ \Carbon\Carbon::parse(config('constants.EVENT_DATE_START'))->format('M j') }}-{{ \Carbon\Carbon::parse(config('constants.EVENT_DATE_END'))->format('j, Y') }}</td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td valign="top" style="padding-bottom: 15px;">
                    <table cellpadding="0" cellspacing="0" border="0">
                      <tr>
                        <td valign="top" style="padding-right: 15px;">
                          <img src="{{ config('constants.HOSTED_URL') }}/mails/clock-icon.png" alt="Clock" width="20" height="20" style="vertical-align: middle;">
                        </td>
                        <td style="font-size: 16px; color: #555555;">Doors open at 10:00 AM</td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td valign="top" style="padding-bottom: 15px;">
                    <table cellpadding="0" cellspacing="0" border="0">
                      <tr>
                        <td valign="top" style="padding-right: 15px;">
                          <img src="{{ config('constants.HOSTED_URL') }}/mails/location-icon.png" alt="Location" width="20" height="20" style="vertical-align: middle;">
                        </td>
                        <td style="font-size: 16px; color: #555555;">{{ config('constants.EVENT_VENUE') }}</td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td valign="top">
                    <table cellpadding="0" cellspacing="0" border="0">
                      <tr>
                        <td valign="top" style="padding-right: 15px;">
                            <img src="{{ config('constants.HOSTED_URL') }}/mails/globe-icon.png" alt="Website" width="20" height="20" style="vertical-align: middle;">
                        </td>
                        <td>
                            <a href="{{ config('constants.EVENT_WEBSITE') }}" style="font-size: 16px; color: #0066cc; text-decoration: none;">{{ parse_url(config('constants.EVENT_WEBSITE'), PHP_URL_HOST) }}</a>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>

        <!-- Ticket Information with QR Code -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 30px; overflow: hidden;">
          <tr>
            <td style="background-color: #333333; color: #ffffff; padding: 12px 20px;">
              <h3 style="font-size: 18px; font-weight: bold; margin: 0;">Your Ticket</h3>
            </td>
          </tr>
          <tr>
            <td style="padding: 25px;">
              <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <!-- Ticket Details -->
                  <td width="60%" style="padding-right: 20px; vertical-align: top;">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                      <tr>
                        <td style="padding-bottom: 15px;">
                          <span style="display: block; font-size: 14px; color: #888888; margin-bottom: 3px;">Name</span>
                          <span style="font-size: 16px; font-weight: 500;">{{$data['name']}}</span>
                        </td>
                      </tr>
                      <tr>
                        <td style="padding-bottom: 15px;">
                          <span style="display: block; font-size: 14px; color: #888888; margin-bottom: 3px;">Email</span>
                          <span style="font-size: 16px; font-weight: 500;">{{$data['email']}}</span>
                        </td>
                      </tr>
                      <tr>
                        <td style="padding-bottom: 15px;">
                          <span style="display: block; font-size: 14px; color: #888888; margin-bottom: 3px;">Ticket Type</span>
                          <span style="font-size: 16px; font-weight: 500;">{{$data['ticket_type']}}</span>
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <span style="display: block; font-size: 14px; color: #888888; margin-bottom: 3px;">Ticket ID</span>
                          <span style="font-size: 16px; font-weight: 500;">{{$data['unique_id']}}</span>
                        </td>
                      </tr>
                    </table>
                  </td>
                  
                  <!-- QR Code -->
                  <td width="40%" align="center" style="vertical-align: top;">
                    <div style="background-color: #ffffff; padding: 12px; border: 1px solid #e0e0e0; border-radius: 6px; display: inline-block;">
                      <img src="{{$data['qr_code_path']}}" alt="QR Code" width="150" height="150" style="display: block;">
                      <p style="font-size: 12px; color: #888888; text-align: center; margin: 8px 0 0 0;">Scan for check-in</p>
                    </div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>

        <!-- Additional Information -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 30px;">
          <tr>
            <td style="font-size: 14px; color: #666666; line-height: 1.5;">
              <p style="margin: 0 0 15px 0;">Please bring a copy of this ticket (digital or printed) and a valid ID to the event. The QR code will be scanned at the entrance for quick check-in.</p>
                <p style="margin: 0;">If you have any questions, please contact our support team at <a href="mailto:{{ config('constants.organizer.email') }}" style="color: #0066cc; text-decoration: none;">{{ config('constants.organizer.email') }}</a></p>
            </td>
          </tr>
        </table>

        <!-- Footer -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-top: 1px solid #e0e0e0; padding-top: 20px;">
          <tr>
            <td align="center" style="font-size: 12px; color: #888888;">
                <p style="margin: 0 0 5px 0;">©  {{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}. All rights reserved.</p>
              <p style="margin: 0;">This email was sent to {{$data['email']}} </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>