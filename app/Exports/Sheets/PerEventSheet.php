<?php

namespace App\Exports\Sheets;

use App\Models\Event;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PerEventSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, WithColumnWidths
{
    protected Event $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    public function title(): string
    {
        // Excel sheet name max 31 chars, tidak boleh ada karakter spesial
        $sanitized = preg_replace('/[\\\\\/\?\*\[\]:]/', '', $this->event->name);
        return '🎫 ' . Str::limit($sanitized, 28, '');
    }

    public function collection()
    {
        return Order::with(['ticketType', 'guestlistCategory'])
            ->where('event_id', $this->event->id)
            ->where('status', 'success')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Order ID',
            'Nama Pembeli',
            'Email',
            'WhatsApp',
            'Tipe Tiket',
            'Jumlah',
            'Kategori Pembelian',
            'Total (Rp)',
            'Jenis',
            'Kategori Guest',
            'Status Hadir',
            'Waktu Scan',
            'Waktu Pembelian',
        ];
    }

    public function map($order): array
    {
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
            $order->ticketType->name ?? '-',
            $qty,
            $category,
            $order->total_price,
            $order->total_price > 0 ? 'BERBAYAR' : 'GUESTLIST',
            $order->guestlistCategory->name ?? '-',
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
            'A' => 18, 'B' => 25, 'C' => 30, 'D' => 18,
            'E' => 15, 'F' => 8, 'G' => 22, 'H' => 15,
            'I' => 12, 'J' => 15, 'K' => 12, 'L' => 18, 'M' => 18,
        ];
    }
}