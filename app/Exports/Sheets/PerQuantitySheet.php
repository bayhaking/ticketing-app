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

/**
 * PerQuantitySheet — Sheet per kategori jumlah tiket per transaksi.
 * Quantity 10 = bucket "10+" yang include semua transaksi >=10 tiket.
 */
class PerQuantitySheet implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, WithColumnWidths
{
    protected int $promotorId;
    protected int $quantity;
    protected ?int $filterEventId;

    public function __construct(int $promotorId, int $quantity, ?int $filterEventId = null)
    {
        $this->promotorId = $promotorId;
        $this->quantity = $quantity;
        $this->filterEventId = $filterEventId;
    }

    public function title(): string
    {
        if ($this->quantity >= 10) {
            return '📦 10+ Tiket';
        }
        return '🎫 ' . $this->quantity . ' Tiket';
    }

    public function collection()
    {
        $query = Order::with(['event', 'ticketType'])
            ->whereHas('event', function ($q) {
                $q->where('user_id', $this->promotorId);
            })
            ->where('status', 'success')
            ->where('total_price', '>', 0); // exclude guestlist

        // Filter by quantity
        if ($this->quantity >= 10) {
            // Bucket 10+: semua transaksi dengan quantity >= 10
            $query->where('quantity', '>=', 10);
        } else {
            $query->where('quantity', $this->quantity);
        }

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
            'Harga Per Tiket (Rp)',
            'Total Harga (Rp)',
            'Status Hadir',
            'Waktu Pembelian',
        ];
    }

    public function map($order): array
    {
        $pricePerTicket = $order->quantity > 0 ? $order->total_price / $order->quantity : 0;

        return [
            $order->order_number,
            $order->customer_name,
            $order->customer_email,
            $order->customer_phone,
            $order->event->name ?? '-',
            $order->ticketType->name ?? '-',
            $order->quantity,
            number_format($pricePerTicket, 0, ',', '.'),
            number_format($order->total_price, 0, ',', '.'),
            $order->scanned_at ? 'HADIR' : 'BELUM',
            $order->created_at->format('d M Y, H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Color berbeda per kategori (gradient dari hijau ke merah untuk bulk)
        $colorMap = [
            1 => '1DB954',  // solo - hijau
            2 => '10B981',  // pair - hijau muda
            3 => '34D399',
            4 => '6EE7B7',
            5 => 'FCD34D',  // small group - kuning
            6 => 'F59E0B',
            7 => 'FB923C',  // group besar - oranye
            8 => 'F97316',
            9 => 'EF4444',
            10 => 'DC2626', // bulk - merah
        ];

        $color = $colorMap[$this->quantity] ?? '1DB954';

        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $color],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18, 'B' => 25, 'C' => 30, 'D' => 18,
            'E' => 25, 'F' => 15, 'G' => 10, 'H' => 18,
            'I' => 18, 'J' => 12, 'K' => 18,
        ];
    }
}