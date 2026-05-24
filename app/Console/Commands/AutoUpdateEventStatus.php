<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;
use Carbon\Carbon;

/**
 * Auto-update status event berdasarkan tanggal:
 * - upcoming → live: di hari H (jam 00:00 tanggal event)
 * - live → finished: 24 jam setelah tanggal event
 *
 * Schedule: tiap jam (set di routes/console.php atau app/Console/Kernel.php)
 *
 * Usage manual: php artisan events:auto-status
 */
class AutoUpdateEventStatus extends Command
{
    protected $signature = 'events:auto-status {--dry-run : Tampilkan apa yang akan diubah tanpa update}';
    protected $description = 'Auto-update status event berdasarkan tanggal';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $now = now();

        $this->info("=== Auto-update Status Event ===");
        $this->info("Waktu sekarang: " . $now->format('d M Y H:i'));
        $this->info("Mode: " . ($dryRun ? 'DRY-RUN (tidak ada yang diubah)' : 'LIVE'));
        $this->newLine();

        // ============================================================
        // 1. upcoming → live: event yang tanggalnya hari ini atau sudah lewat
        // ============================================================
        $toLiveQuery = Event::where('status', 'upcoming')
            ->where('sales_closed_manually', false) // jangan otomatis kalau sudah ditutup manual
            ->whereDate('date', '<=', $now->toDateString());

        $toLive = $toLiveQuery->get();

        if ($toLive->count() > 0) {
            $this->info("📍 {$toLive->count()} event akan jadi LIVE:");
            foreach ($toLive as $event) {
                $this->line("   - {$event->name} (tanggal: {$event->date})");
            }

            if (!$dryRun) {
                $toLiveQuery->update([
                    'status' => 'live',
                    'last_status_changed_at' => $now,
                ]);
                $this->info("   ✅ Updated to LIVE");
            }
            $this->newLine();
        }

        // ============================================================
        // 2. live → finished: event yang sudah lewat 24 jam dari tanggal event
        // ============================================================
        $cutoff = $now->copy()->subDay()->toDateString();

        $toFinishedQuery = Event::where('status', 'live')
            ->whereDate('date', '<=', $cutoff);

        $toFinished = $toFinishedQuery->get();

        if ($toFinished->count() > 0) {
            $this->info("🏁 {$toFinished->count()} event akan jadi FINISHED:");
            foreach ($toFinished as $event) {
                $this->line("   - {$event->name} (tanggal: {$event->date})");
            }

            if (!$dryRun) {
                $toFinishedQuery->update([
                    'status' => 'finished',
                    'last_status_changed_at' => $now,
                ]);
                $this->info("   ✅ Updated to FINISHED");
            }
            $this->newLine();
        }

        // ============================================================
        // Summary
        // ============================================================
        $total = $toLive->count() + $toFinished->count();

        if ($total === 0) {
            $this->info("✅ Tidak ada event yang perlu di-update statusnya.");
        } else {
            $this->info("✅ Total event ter-update: {$total}");
        }

        return Command::SUCCESS;
    }
}