<?php

namespace App\Exports\Sheets;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AllEventsSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, WithColumnWidths
{
    protected int $promotorId;
    protected ?int $filterEventId;

    public function __construct(int $promotorId, ?int $filterEventId = null)
    {
        $this->promotorId = $promotorId;
        $this->filterEventId = $filterEventId;
    }

    public function title(): string
    {
        return '🌐 Semua Event';
    }

    public function collection()
    {
        $query = Order::with(['event', 'ticketType'])
            ->whereHas('event', function ($q) {
                $q->where('user_id', $this->promotorId);
            })
            ->where('status', 'success');

        if ($this->filterEventId) {
            $query->where('event_id', $this->filterEventId);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Order ID',
            'Nama Pembeli',
            'Email',
            'WhatsApp',
            'Event',
            'Tipe Tiket',
            'Jumlah Tiket',
            'Kategori Pembelian',
            'Total Harga (Rp)',
            'Status Bayar',
            'Status Hadir',
            'Waktu Scan',
            'Waktu Pembelian',
        ];
    }

    public function map($order): array
    {
        // Tentukan kategori pembelian
        $qty = $order->quantity;
        if ($qty == 1) $category = '1 tiket (Solo)';
        elseif ($qty == 2) $category = '2 tiket (Pair)';
        elseif ($qty >= 3 && $qty <= 5) $category = $qty . ' tiket (Grup Kecil)';
        elseif ($qty >= 6 && $qty <= 9) $category = $qty . ' tiket (Grup Besar)';
        else $category = $qty . ' tiket (Bulk)';

        return [
            $order->order_number,
            $order->customer_name,
            $order->customer_email,
            $order->customer_phone,
            $order->event->name ?? '-',
            $order->ticketType->name ?? '-',
            $qty,
            $category,
            $order->total_price,
            $order->total_price > 0 ? 'BERBAYAR' : 'GUESTLIST',
            $order->scanned_at ? 'HADIR' : 'BELUM',
            $order->scanned_at ? \Carbon\Carbon::parse($order->scanned_at)->format('d M Y, H:i') : '-',
            $order->created_at->format('d M Y, H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1DB954'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,
            'B' => 25,
            'C' => 30,
            'D' => 18,
            'E' => 25,
            'F' => 15,
            'G' => 10,
            'H' => 22,
            'I' => 15,
            'J' => 12,
            'K' => 12,
            'L' => 18,
            'M' => 18,
        ];
    }
}