<?php

namespace App\Exports;

use Maatwebsite\Excel\Excel;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Carbon\Carbon;

class SponsorExport implements Responsable, WithHeadings, WithStyles, FromArray, WithColumnFormatting
{
    use Exportable;

    private $fileName = 'sponsor_master.xlsx';
    private $writerType = Excel::XLSX;

    public function headings(): array
    {
        return [
            'ID',
            'Nama',
            'Tanggal Lahir',
            'Tempat Lahir',
            'Alamat Kabupaten',
            'Alamat Lengkap',
            'Nomor Hp',
            'Gereja',
            'Email'
        ];
    }

    public function array(): array
    {
        return [
            [
                '32',
                'Sunadi Purnomo',
                Date::dateTimeToExcel(Carbon::parse('19-08-1970')),
                'Kabupaten Semarang',
                'Kabupaten Semarang',
                'PT. Anugerah Steel, Jl. Raya Pangeran Tubagus Angke Komplek Ruko Taman Duta mas Blok E1 No.10',
                '081333122213',
                'GBI ROSYPINNA',
                'sumadipurnomo@gmail.com',
            ]
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_DATE_DDMMYYYY,
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
