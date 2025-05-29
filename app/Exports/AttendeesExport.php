<?php 
namespace App\Exports;

use App\Models\Attendee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class AttendeesExport implements FromCollection, WithHeadings
{
    public function collection(): Collection
    {
        return Attendee::all()->map(function ($attendee) {
            $purpose = $attendee->purpose;
            $products = $attendee->products;
        
            // Normalize purpose to comma-separated string
            if (is_string($purpose)) {
                $decoded = json_decode($purpose, true);
                $purpose = is_array($decoded) ? implode(', ', $decoded) : $purpose;
            } elseif (is_array($purpose)) {
                $purpose = implode(', ', $purpose);
            }
        
            // Normalize products to comma-separated string
            if (is_string($products)) {
                $decoded = json_decode($products, true);
                $products = is_array($decoded) ? implode(', ', $decoded) : $products;
            } elseif (is_array($products)) {
                $products = implode(', ', $products);
            }
        
            return [
                'id' => $attendee->id,
                'unique_id' => $attendee->unique_id,
                'status' => $attendee->status,
                'badge_category' => $attendee->badge_category,
                'title' => $attendee->title,
                'first_name' => $attendee->first_name,
                'last_name' => $attendee->last_name,
                'designation' => $attendee->designation,
                'company' => $attendee->company,
                'address' => $attendee->address,
                'country' => $attendee->country,
                'state' => $attendee->state,
                'city' => $attendee->city,
                'postal_code' => $attendee->postal_code,
                'mobile' => $attendee->mobile,
                'email' => $attendee->email,
                'purpose' => $purpose,
                'products' => $products,
                'business_nature' => $attendee->business_nature,
                'job_function' => $attendee->job_function,
                'consent' => $attendee->consent ? 'Yes' : 'No',
                'created_at' => $attendee->created_at,
                'updated_at' => $attendee->updated_at,
                'qr_code_path' => $attendee->qr_code_path,
                'source' => $attendee->source,
            ];
        });
        
    }

    public function headings(): array
    {
        return [
            'ID',
            'Unique ID',
            'Status',
            'Badge Category',
            'Title',
            'First Name',
            'Last Name',
            'Designation',
            'Company',
            'Address',
            'Country',
            'State',
            'City',
            'Postal Code',
            'Mobile',
            'Email',
            'Purpose',
            'Products',
            'Business Nature',
            'Job Function',
            'Consent',
            'Created At',
            'Updated At',
            'QR Code Path',
            'Source',
        ];
    }
}
