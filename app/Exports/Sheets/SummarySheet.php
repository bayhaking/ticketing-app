<?php

namespace App\Exports\Sheets;

use App\Models\Event;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SummarySheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
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
        return '📊 Ringkasan';
    }

    public function array(): array
    {
        $eventsQuery = Event::where('user_id', $this->promotorId);
        if ($this->filterEventId) {
            $eventsQuery->where('id', $this->filterEventId);
        }
        $events = $eventsQuery->orderBy('date', 'desc')->get();

        $totalRevenue = 0;
        $totalTransactions = 0;
        $totalTickets = 0;
        $totalAttended = 0;
        $totalGuestlist = 0;

        $eventRows = [];

        foreach ($events as $event) {
            $paidOrders = Order::where('event_id', $event->id)
                ->where('status', 'success')
                ->where('total_price', '>', 0);

            $revenue = (clone $paidOrders)->sum('total_price');
            $transactions = (clone $paidOrders)->count();
            $tickets = (clone $paidOrders)->sum('quantity');
            $attended = (clone $paidOrders)->whereNotNull('scanned_at')->count();

            $guests = Order::where('event_id', $event->id)
                ->where('status', 'success')
                ->where('total_price', 0)
                ->count();

            $totalRevenue += $revenue;
            $totalTransactions += $transactions;
            $totalTickets += $tickets;
            $totalAttended += $attended;
            $totalGuestlist += $guests;

            $eventRows[] = [
                $event->name,
                $event->date ? \Carbon\Carbon::parse($event->date)->format('d M Y') : '-',
                $event->status == 'upcoming' ? 'Aktif' : 'Selesai',
                $transactions,
                $tickets,
                $attended,
                $guests,
                'Rp ' . number_format($revenue, 0, ',', '.'),
            ];
        }

        // Bangun struktur array untuk Excel
        $data = [];

        // Header report
        $data[] = ['LAPORAN PESERTA SPECTIX'];
        $data[] = ['Dibuat pada: ' . now()->format('d M Y, H:i')];
        $data[] = ['Promotor: ' . \App\Models\User::find($this->promotorId)->name];
        $data[] = ['']; // blank row

        // Stats overview
        $data[] = ['📊 RINGKASAN KESELURUHAN'];
        $data[] = ['Total Event', count($events)];
        $data[] = ['Total Transaksi', number_format($totalTransactions)];
        $data[] = ['Total Tiket Terjual', number_format($totalTickets)];
        $data[] = ['Total Pendapatan', 'Rp ' . number_format($totalRevenue, 0, ',', '.')];
        $data[] = ['Total Sudah Hadir', number_format($totalAttended)];
        $data[] = ['Total Guestlist', number_format($totalGuestlist)];
        $data[] = ['']; // blank row

        // Per event breakdown
        $data[] = ['📋 BREAKDOWN PER EVENT'];
        $data[] = [
            'Nama Event',
            'Tanggal',
            'Status',
            'Transaksi',
            'Tiket Terjual',
            'Sudah Hadir',
            'Guestlist',
            'Pendapatan',
        ];

        foreach ($eventRows as $row) {
            $data[] = $row;
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Title rows
            1 => [
                'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1DB954']],
            ],
            // Section header
            5 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1DB954']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F0FDF4'],
                ],
            ],
            // Per event header
            13 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1DB954']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F0FDF4'],
                ],
            ],
            // Table header
            14 => [
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
            'A' => 30,
            'B' => 15,
            'C' => 12,
            'D' => 12,
            'E' => 15,
            'F' => 15,
            'G' => 12,
            'H' => 20,
        ];
    }
}