<?php

namespace App\Http\Controllers;

use App\Mail\SubmissionMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\GeneralMail;
use App\Mail\InvoiceMail;
use App\Mail\ReminderMail;
use App\Mail\ThankYouMail;
use App\Mail\SponsorInvoiceMail;

class MailController extends Controller
{
    //function mailTest to test the mail return view as mailtest
    public function mailTest()
    {
        return view('emails.mailtest');
    }

    /**
     * Send email
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendEmail(Request $request)
    {

        Log::info('Email request', $request->all());

        $emailType = $request->input('email_type'); // Type of email to send
        $to = (array) $request->input('to'); // Recipient email
        $data = $request->all(); // Additional data for email
        $application_id = $request->input('application_id');
        //$to = 'manish.interlink@gmail.com';


        //dd($emailType, $to, $data, $application_id); // Debugging line

        switch ($emailType) {
            case 'invoice':
                $recipients = is_array($to) ? $to : [$to];
                $recipients[] = 'test.interlinks@gmail.com'; // Add default email
                foreach ($recipients as $recipient) {
                    Mail::to($recipient)->queue(new InvoiceMail($application_id));
                }
                break;
            case 'sponsor_invoice':
                $recipients[] = 'manish.interlink@gmail.com';
                
                $recipients[] = 'manish.sharma@interlinks.in'; // Add default email
                foreach ($recipients as $recipient) {

                    //Mail::to($recipient)->queue(new SponsorInvoiceMail($application_id));
                    $mailInstance = new SponsorInvoiceMail($application_id);
                    $data = $mailInstance->build()->viewData ?? [];
                  //  dd($data); // Debugging line
                    //can we display the sent email view here
                    return view('emails.sponsor_invoice', $data);
                }
                //dd($recipients);
                break;


//            case 'sponsorship_invoice':

//            case 'general':
//                Mail::to($to)->send(new GeneralMail($data));
//                break;
//
//            case 'submission':
//                Mail::to($to)->send(new SubmissionMail($data));
//                break;



            case 'reminder':
                $recipients = is_array($to) ? $to : [$to];
                $recipients[] = 'test.interlinks@gmail.com'; // Add default email
                foreach ($recipients as $recipient) {
                    Mail::to($recipient)->queue(new ReminderMail($application_id));
                }
                break;

//            case 'thank_you':
//                Mail::to($to)->send(new ThankYouMail($data));
//                break;

            default:
                return response()->json(['message' => 'Invalid email type'], 400);
        }

        return response()->json(['message' => 'Email has been queued and will be sent shortly.'], 200);
    }
}
