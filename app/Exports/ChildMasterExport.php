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

class ChildMasterExport implements Responsable, WithHeadings, WithStyles, FromArray, WithColumnFormatting
{
    use Exportable;

    private $fileName = 'child_master.xlsx';
    private $writerType = Excel::XLSX;

    public function headings(): array
    {
        return [
            'ID',
            'No Induk',
            'Nama',
            'Panggilan',
            'Jenis Kelamin',
            'Tanggal Lahir',
            'Tempat Lahir',
            'Kabupaten',
            'Kecamatan',
            'Propinsi',
            'FC',
            'Nominal Sponsor',
            'Agama',
            'Ayah',
            'Ibu',
            'Pekerjaan',
            'Ekonomi',
            'Kelas',
            'Tahun Ajaran',
            'Sekolah',
            'Masuk FC',
            'Keluar FC',
            'Alasan Keluar',
            'Keterangan',
            'Foto Profile Url'
        ];
    }

    public function array(): array
    {
        return [
            [
                '',
                'JW/CLN/00001',
                'Dhiana Sari',
                'Diana',
                'P',
                Date::dateTimeToExcel(Carbon::parse('03-09-2002')),
                'Kabupaten Semarang',
                'Kabupaten Semarang',
                'Pedurungan',
                'Jawa Tengah',
                'Celengan',
                150000,
                'Islam',
                'Mohamad Yasin',
                'Sartini',
                'Buruh - Irt',
                'Miskin',
                'XII',
                '2020-2021',
                'SMA',
                Date::dateTimeToExcel(Carbon::parse('01-01-2006')),
                Date::dateTimeToExcel(Carbon::parse('30-07-2020')),
                'LULUS SMA/SMK',
                'DLP MAR 2011 OK... LULUS SMA/SMK TP 2019-2020.',
                'https://pesat.org/wp-content/uploads/2019/07/Dhiana-Sari-XI-SMA.jpg'
            ]
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'L' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'U' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'V' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }


    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:Y1')->getFont()->setBold(true);
        foreach (range('A', 'Y') as $columnID) {
            $sheet->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        $sheet->getStyle('A1:Y1')->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
    }
}
