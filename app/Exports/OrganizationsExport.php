<?php
namespace App\Exports;

use App\Models\DelOrgDetail;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrganizationsExport implements FromCollection, WithHeadings, WithMapping
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'TIN No',
            'Org Name',
            'Sector',
            'Pay Status',
            'Reg Date',
            'Total Amount',
            'Delegate Count',
            'Delegate Emails',
        ];
    }

    public function map($org): array
    {
        return [
            $org->tin_no,
            $org->org_name,
            $org->sector,
            $org->pay_status,
            $org->reg_date,
            $org->total,
            $org->delegates->count(),
            $org->delegates->pluck('email')->join('; '),
        ];
    }
}
