<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{config('constants.APP_NAME')}}  Invitation</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 5px;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
    <tr>
        <td align="center" style="padding: 20px 0;">
            <table role="presentation" style="max-width: 600px; width: 100%; border-collapse: collapse; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                <!-- Header -->
                <tr>
                    <td style="background-color:rgb(255, 255, 255); padding: 20px; text-align: center;">
                        <img src="{{config('constants.event_logo')}}" alt="{{config('constants.APP_NAME')}} Logo" style="height: 60px;">
                        <span style="display: block; margin-top: 10px; font-size: 14px; font-weight: bold;">{{config('constants.APP_NAME')}}</span>
                    </td>
                </tr>

                <!-- Content -->
                <tr>
                    <td style="background-color: #ffffff; padding: 30px 20px;">
                        <h2 style="color: #1e293b; margin-bottom: 20px; font-size: 24px; text-align: center;">You're Invited to {{config('constants.APP_NAME')}}!</h2>

                        <p style="color: #475569; line-height: 1.6; margin-bottom: 15px; font-size: 16px;">
                            Dear Representative,
                        </p>

                        <p style="color: #475569; line-height: 1.6; margin-bottom: 15px; font-size: 16px;">
                            <strong>{{ $companyName }}</strong> has invited you to participate in {{config('constants.APP_NAME')}} as a <strong>{{ $delegateType }}</strong>.
                        </p>

                        <div style="text-align: center; margin: 30px 0;">
                            <a href="{{ route('exhibition.invited', ['token' => $token]) }}" style="background-color:rgb(0, 0, 0); color: white; padding: 12px 25px; border-radius: 4px; text-decoration: none; font-weight: bold; display: inline-block; font-size: 16px;">
                                Confirm Your Participation
                            </a>

                            <p>Or copy and paste the following URL into your browser:</p>
                            <p>{{ route('exhibition.invited', ['token' => $token]) }}</p>
                        </div>


                        <p style="color: #475569; line-height: 1.6; font-size: 16px; border-top: 1px solid #e2e8f0; padding-top: 20px;">
                            If you have any questions, please contact us at <a href="mailto:{{ config('constants.organizer.email') }}" style="color:rgb(15, 15, 14); text-decoration: none;">semiconindia@semi.org</a>.
                        </p>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background-color: #1e293b; color: white; padding: 20px; text-align: center;">
                        <p style="margin: 0 0 10px 0; font-size: 14px;">
                            {{config('constants.APP_NAME')}}. All rights reserved.
                        </p>
                        <p style="margin: 0; font-size: 14px;">
                            <a href="{{config('constants.EVENT_WEBSITE')}}" style="color: white; text-decoration: underline;">Visit our website</a>
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
