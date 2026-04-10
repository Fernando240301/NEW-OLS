<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class RpumExport implements 
    FromCollection, 
    WithHeadings, 
    WithStyles, 
    ShouldAutoSize, 
    WithColumnFormatting,
    WithEvents,
    WithMapping
{
    public function collection()
    {
        // ===============================
        // SUBTOTAL PPJB (qty * harga)
        // ===============================
        $detail = DB::table('ppjb_detailnews')
            ->select(
                'ppjb_id',
                DB::raw('SUM(qty * harga) as total_ppjb')
            )
            ->groupBy('ppjb_id');

        // ===============================
        // TOTAL RPUM (realisasi pembayaran)
        // ===============================
        $rpum = DB::table('rpums')
            ->select(
                'ppjb_id',
                DB::raw('SUM(jumlah) as total_rpum'),
                DB::raw('COUNT(id) as jumlah_transaksi')
            )
            ->groupBy('ppjb_id');

        // ===============================
        // JOIN KE PPJB
        // ===============================
        return DB::table('ppjbnews as p')
            ->leftJoinSub($detail, 'd', function ($join) {
                $join->on('d.ppjb_id', '=', 'p.id');
            })
            ->leftJoinSub($rpum, 'r', function ($join) {
                $join->on('r.ppjb_id', '=', 'p.id');
            })
            ->select(
                'p.no_ppjb',
                'p.tanggal_permohonan',
                'p.refer_project',
                'p.pekerjaan',
                'p.pic',

                DB::raw('COALESCE(d.total_ppjb, 0) as total_ppjb'),
                DB::raw('COALESCE(r.total_rpum, 0) as total_rpum'),

                DB::raw('(COALESCE(d.total_ppjb,0) - COALESCE(r.total_rpum,0)) as sisa'),

                DB::raw('COALESCE(r.jumlah_transaksi, 0) as jumlah_rpum')
            )
            ->orderBy('p.tanggal_permohonan', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No PPJB',
            'Tanggal Permohonan',
            'Project No',
            'Description',
            'PIC',
            'Total PPJB',
            'Total RPUM',
            'Sisa',
            'Jumlah Transaksi RPUM'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [ // baris header
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center'
                ]
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function($event) {

                $sheet = $event->sheet->getDelegate();

                // Border semua tabel
                $sheet->getStyle('A1:I1000')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => 'thin',
                        ],
                    ],
                ]);

                // Header background
                $sheet->getStyle('A1:I1')->applyFromArray([
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => 'D9E1F2'],
                    ],
                ]);
            },
        ];
    }

    public function map($row): array
    {
        return [
            $row->no_ppjb,

            // FORMAT TANGGAL INDONESIA
            $row->tanggal_permohonan
                ? Carbon::parse($row->tanggal_permohonan)->translatedFormat('d F Y')
                : '-',

            $row->refer_project,
            $row->pekerjaan,
            $row->pic,
            $row->total_ppjb,
            $row->total_rpum,
            $row->sisa,
            $row->jumlah_rpum,
        ];
    }
}