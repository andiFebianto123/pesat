<?php

namespace App\Exports;

use App\Models\ChildMaster;
use Maatwebsite\Excel\Excel;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ChildMasterExport implements Responsable, WithHeadings, WithStyles
{
    use Exportable;

    private $fileName = 'child_master.xlsx';
    private $writerType = Excel::XLSX;

    public function headings(): array
    {
        return [
            'ID',
            'No. Induk',
            'N a m a',
            'Panggilan',
            'S',
            'Status',
            'Tpt Lahir',
            'FC',
            'Agama',
            'Alamat',
            'Kecamatan',
            'Kabupaten',
            'Propinsi',
            'Ayah',
            'Ibu',
            'Pekerjaan',
            'Eko',
            'Kelas',
            'an',
            'Sekolah',
            'Masuk FC',
            'Keluar FC',
            'Alasan Keluar',
            "keterangan"
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:X1')->getFont()->setBold(true);
        foreach (range('A', 'X') as $columnID) {
            $sheet->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        $sheet->getStyle('A1:X1')->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
    }
}
