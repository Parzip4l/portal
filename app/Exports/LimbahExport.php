<?php

namespace App\Exports;

// app/Exports/LimbahExport.php

use App\B3\LimbahModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LimbahExport implements FromCollection, WithHeadings
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return LimbahModel::whereBetween('tanggal', [$this->startDate, $this->endDate])
        ->select('id','tanggal','jenis','asal','jumlah','uom')
        ->get();

        $totalKg = $limbahData->where('uom', 'kg')->sum('jumlah');
        $totalLiter = $limbahData->where('uom', 'liter')->sum('jumlah');

        $limbahData->push([
            'Total',
            '',
            '',
            '',
            $totalKg,
            'kg',
        ]);

        $limbahData->push([
            'Total',
            '',
            '',
            '',
            $totalLiter,
            'liter',
        ]);

        return $limbahData;
    }

    public function headings(): array
    {
        return [
            '#',
            'Tanggal',
            'Jenis',
            'Asal',
            'Jumlah',
            'UOM',
        ];
    }
}
