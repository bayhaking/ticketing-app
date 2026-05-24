<?php

namespace App\Exports;

use App\Models\Event;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\Auth;

/**
 * AttendeesExport — Multi-sheet Excel export
 *
 * Struktur sheet yang di-generate:
 * 1. "Ringkasan" — overview semua event + stats
 * 2. "Semua Event" — daftar pembeli dari semua event
 * 3. "[Nama Event 1]" — pembeli event 1
 * 4. "[Nama Event 2]" — pembeli event 2
 * ... dst per event
 * 5. "Kategori 1 Tiket" — semua pembeli yang beli 1 tiket
 * 6. "Kategori 2 Tiket"
 * ... dst sampai 10+
 *
 * Usage di controller:
 *   return Excel::download(new AttendeesExport($eventId), 'attendees.xlsx');
 */
class AttendeesExport implements WithMultipleSheets
{
    protected ?int $filterEventId;
    protected int $promotorId;

    public function __construct(?int $filterEventId = null)
    {
        $this->filterEventId = $filterEventId;
        $this->promotorId = Auth::id();
    }

    public function sheets(): array
    {
        $sheets = [];

        // ============================================================
        // SHEET 1: Ringkasan / Overview
        // ============================================================
        $sheets[] = new Sheets\SummarySheet($this->promotorId, $this->filterEventId);

        // ============================================================
        // SHEET 2: Semua Event (combined)
        // ============================================================
        $sheets[] = new Sheets\AllEventsSheet($this->promotorId, $this->filterEventId);

        // ============================================================
        // SHEET 3+: Per Event (kalau filter event tidak aktif)
        // ============================================================
        if (!$this->filterEventId) {
            $events = Event::where('user_id', $this->promotorId)
                ->orderBy('date', 'desc')
                ->get();

            foreach ($events as $event) {
                $sheets[] = new Sheets\PerEventSheet($event);
            }
        } else {
            // Kalau filter event aktif, tetap buat sheet khusus event tersebut
            $event = Event::where('id', $this->filterEventId)
                ->where('user_id', $this->promotorId)
                ->first();

            if ($event) {
                $sheets[] = new Sheets\PerEventSheet($event);
            }
        }

        // ============================================================
        // SHEET PER KATEGORI JUMLAH TIKET (1, 2, 3, ..., 10+)
        // ============================================================
        for ($qty = 1; $qty <= 10; $qty++) {
            $sheets[] = new Sheets\PerQuantitySheet($this->promotorId, $qty, $this->filterEventId);
        }

        return $sheets;
    }
}