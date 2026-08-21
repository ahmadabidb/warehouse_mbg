<?php

namespace App\Http\Controllers;

use App\Models\{BahanBaku, StokMasuk, StokKeluar};
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ReportController extends Controller
{
    private function transactionData(Request $r, string $type)
    {
        $model = $type === 'incoming' ? StokMasuk::class : StokKeluar::class;
        $date = $type === 'incoming' ? 'tanggal_masuk' : 'tanggal_keluar';

        return $model::with('bahanBaku', 'user')
            ->when($r->start_date, fn ($q, $v) => $q->whereDate($date, '>=', $v))
            ->when($r->end_date, fn ($q, $v) => $q->whereDate($date, '<=', $v))
            ->orderBy($date)
            ->get();
    }

    private function opnameData(Request $r)
    {
        return BahanBaku::with('category')->get()->map(fn ($i) => [
            'item' => $i,
            'opening' => 0,
            'incoming' => $i->stokMasuks()
                ->when($r->start_date, fn ($q, $v) => $q->whereDate('tanggal_masuk', '>=', $v))
                ->when($r->end_date, fn ($q, $v) => $q->whereDate('tanggal_masuk', '<=', $v))
                ->sum('jumlah'),
            'outgoing' => $i->stokKeluars()
                ->when($r->start_date, fn ($q, $v) => $q->whereDate('tanggal_keluar', '>=', $v))
                ->when($r->end_date, fn ($q, $v) => $q->whereDate('tanggal_keluar', '<=', $v))
                ->sum('jumlah'),
            'closing' => $i->stok,
        ]);
    }

    private function formatPeriod(Request $r): string
    {
        $start = $r->start_date ? \Carbon\Carbon::parse($r->start_date)->format('d/m/Y') : '—';
        $end = $r->end_date ? \Carbon\Carbon::parse($r->end_date)->format('d/m/Y') : '—';

        return ($start === '—' && $end === '—') ? 'Seluruh data' : "$start s.d. $end";
    }

    public function incoming(Request $r)
    {
        return view('reports.transaction', [
            'type' => 'incoming',
            'records' => $this->transactionData($r, 'incoming'),
        ]);
    }

    public function outgoing(Request $r)
    {
        return view('reports.transaction', [
            'type' => 'outgoing',
            'records' => $this->transactionData($r, 'outgoing'),
        ]);
    }

    public function opname(Request $r)
    {
        return view('reports.opname', [
            'items' => $this->opnameData($r),
        ]);
    }

    public function export(Request $r, string $type, string $format)
    {
        if ($format === 'pdf') {
            $pdfData = [
                'title' => $type === 'opname' ? 'Laporan Stock Opname' : 'Laporan Stok ' . ($type === 'incoming' ? 'Masuk' : 'Keluar'),
                'type' => $type,
                'records' => $type === 'opname' ? [] : $this->transactionData($r, $type),
                'items' => $type === 'opname' ? $this->opnameData($r) : [],
                'period' => $this->formatPeriod($r),
                'generatedAt' => now()->format('d/m/Y H:i'),
            ];

            $filename = 'laporan-' . $type . '-' . now()->format('Ymd_His') . '.pdf';

            return Pdf::loadView($type === 'opname' ? 'reports.pdf.opname' : 'reports.pdf.transaction', $pdfData)
                ->setPaper('a4', 'portrait')
                ->download($filename);
        }

        $rows = [];
        $head = [];

        if ($type === 'opname') {
            $items = $this->opnameData($r);
            $rows = collect($items)->map(fn ($x) => [
                $x['item']->nama_bahan,
                $x['opening'],
                $x['incoming'],
                $x['outgoing'],
                $x['closing'],
            ]);
            $head = ['Bahan', 'Stok Awal', 'Masuk', 'Keluar', 'Stok Akhir'];
        } else {
            $records = $this->transactionData($r, $type);
            $rows = collect($records)->map(fn ($x) => [
                $x->nomor_transaksi,
                $x->bahanBaku->nama_bahan,
                $x->jumlah,
                $x->user->name,
            ]);
            $head = ['Nomor', 'Bahan', 'Jumlah', 'Petugas'];
        }

        $export = new class($rows, $head, $type) implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
        {
            public function __construct(private $rows, private $head, private $type)
            {
            }

            public function collection()
            {
                return $this->rows;
            }

            public function headings(): array
            {
                return $this->head;
            }

            public function styles(Worksheet $sheet)
            {
                $sheet->setShowGridlines(false);
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                $sheet->mergeCells('A1:' . $highestColumn . '1');
                $sheet->setCellValue('A1', $this->type === 'opname' ? 'LAPORAN STOCK OPNAME' : 'LAPORAN STOK');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => Color::COLOR_WHITE]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0F766E']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF0F766E']]],
                ]);

                $sheet->getStyle('A2:' . $highestColumn . $highestRow)->applyFromArray([
                    'font' => ['size' => 10],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sheet->getStyle('A2:' . $highestColumn . '2')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => Color::COLOR_WHITE]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0F766E']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                return [
                    1 => ['font' => ['bold' => true, 'size' => 12]],
                ];
            }
        };

        $filename = 'laporan-' . $type . '-' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download($export, $filename);
    }
}

