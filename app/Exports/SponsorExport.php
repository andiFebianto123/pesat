<?php

namespace App\Exports;

use App\Models\Sponsor;
use Maatwebsite\Excel\Excel;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SponsorExport implements Responsable, WithHeadings, WithStyles
{
    use Exportable;

    private $fileName = 'sponsor_master.xlsx';
    private $writerType = Excel::XLSX;

    public function headings(): array
    {
        return [
            'Kode',
            'N a m a',
            'Status',
            'Tpt Lahir',
            'Tgl Lahir',
            'Alamat',
            'Email',
            'Handphone',
            'Gereja'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);
        foreach (range('A', 'I') as $columnID) {
            $sheet->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        $sheet->getStyle('A1:I1')->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
    }
}
