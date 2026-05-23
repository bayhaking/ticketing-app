<x-app-layout>
    <style>
        .stat-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 1.5rem; padding: 2rem; border-top: 4px solid var(--accent); }
        .stat-label { color: var(--text-sub); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; }
        .stat-value { color: var(--text-main); font-size: 1.8rem; font-weight: 900; margin-top: 5px; }
        .chart-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 1.5rem; padding: 2rem; margin-top: 2rem; }
        .table-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 1.5rem; padding: 2rem; margin-top: 2rem; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; color: var(--accent); font-size: 0.75rem; text-transform: uppercase; padding: 1rem; border-bottom: 1px solid var(--border); }
        td { padding: 1rem; color: var(--text-main); font-weight: 600; border-bottom: 1px solid var(--border); font-size: 0.9rem; }
        .btn-action { padding: 6px 12px; border-radius: 8px; font-size: 0.7rem; font-weight: 800; cursor: pointer; transition: 0.3s; border: none; }
    </style>

    <div style="padding: 3rem;">
        <div class="max-w-7xl mx-auto">
            <h1 style="font-size: 2.5rem; font-weight: 900; font-style: italic; margin-bottom: 2rem;" data-key="title">PROMOTOR DASHBOARD</h1>

            @if(session('success'))
                <div style="background: rgba(29, 185, 84, 0.2); border: 1px solid #1DB954; color: #1DB954; padding: 1rem; border-radius: 10px; margin-bottom: 2rem; font-weight: 800;">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                <div class="stat-card">
                    <div class="stat-label" data-key="ev_mendatang">Event Mendatang</div>
                    <div class="stat-value">{{ $upcomingEvents ?? 0 }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label" data-key="tix_jual">Tiket Terjual</div>
                    <div class="stat-value">{{ number_format($totalTicketsSold ?? 0) }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label" data-key="tot_trans">Total Transaksi</div>
                    <div class="stat-value">{{ number_format($totalTransactions ?? 0) }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label" data-key="tot_pendapatan">Total Pendapatan</div>
                    <div class="stat-value" style="color: var(--accent);">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="stat-card" style="border-top-color: #6366f1;">
                    <div class="stat-label" data-key="tot_staff">Total Staff</div>
                    <div class="stat-value">{{ $totalStaff ?? 0 }}</div>
                </div>
                <div class="stat-card" style="border-top-color: #f59e0b;">
                    <div class="stat-label" data-key="tot_guest">Total Guestlist</div>
                    <div class="stat-value">{{ $totalGuestlist ?? 0 }}</div>
                </div>
            </div>

            <div class="chart-card">
                <h3 style="margin-bottom: 1.5rem; font-weight: 800;" data-key="tren">TREN PENDAPATAN (GESER UNTUK MELIHAT HISTORI)</h3>
                <div style="width: 100%; overflow-x: auto; padding-bottom: 10px;">
                    <div style="min-width: 1200px; height: 350px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="table-card">
                <h3 style="margin-bottom: 1.5rem; font-weight: 800;" data-key="tabel_h">KELOLA EVENT</h3>
                <table>
                    <thead>
                        <tr>
                            <th data-key="th_nama">Nama Event</th>
                            <th data-key="th_status">Status</th>
                            <th data-key="th_kuota">Kuota Awal</th>
                            <th data-key="th_jual">Terjual</th>
                            <th data-key="th_uang">Pendapatan</th>
                            <th data-key="th_aksi" style="text-align: center;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($eventDetails ?? [] as $detail)
                        <tr>
                            <td>{{ $detail['name'] }}</td>
                            <td>
                                @if($detail['status'] == 'Aktif')
                                    <span style="background: rgba(29, 185, 84, 0.2); color: #1DB954; padding: 4px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 800;" data-key="stat_aktif">🟢 AKTIF</span>
                                @else
                                    <span style="background: rgba(255, 107, 107, 0.2); color: #ff6b6b; padding: 4px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 800;" data-key="stat_selesai">🔴 SELESAI</span>
                                @endif
                            </td>
                            <td>{{ number_format($detail['initial_quota']) }}</td>
                            <td style="color: var(--accent);">{{ number_format($detail['sold']) }}</td>
                            <td style="font-weight: 900;">Rp {{ number_format($detail['revenue'], 0, ',', '.') }}</td>
                            <td style="display: flex; gap: 10px; justify-content: center;">
                                
                                <a href="{{ route('promotor.event.edit', $detail['id']) }}" class="btn-action" style="background: #3b82f6; color: #fff; text-decoration: none; display: flex; align-items: center;">
                                    ✏️ EDIT
                                </a>

                                <form method="POST" action="{{ route('promotor.event.toggle', $detail['id']) }}">
                                    @csrf
                                    <button type="submit" class="btn-action" style="background: {{ $detail['status'] == 'Aktif' ? '#ffc107' : '#1DB954' }}; color: #000;">
                                        {{ $detail['status'] == 'Aktif' ? '⛔ TUTUP' : '🔓 BUKA' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('promotor.event.delete', $detail['id']) }}" onsubmit="return confirm('Yakin ingin menghapus event ini? Data penjualan akan ikut hilang!')">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="background: none; border: 1px solid #ff4444; color: #ff4444; padding: 4px 10px; border-radius: 8px; font-size: 0.6rem; cursor: pointer; font-weight: 800;">
                                        🗑️ HAPUS
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-sub); padding: 2rem;" data-key="no_event">Belum ada event yang diposting. Coba buat event pertamamu!</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // CHART PENDAPATAN
        @if(isset($chartData))
            const chartLabels = {!! json_encode($chartData->pluck('date')) !!};
            const chartValues = {!! json_encode($chartData->pluck('total')) !!};
            
            if(document.getElementById('revenueChart') && chartLabels.length > 0) {
                const ctx = document.getElementById('revenueChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            label: 'Pendapatan (Rp)',
                            data: chartValues,
                            borderColor: '#1DB954',
                            backgroundColor: 'rgba(29, 185, 84, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 5,
                            pointBackgroundColor: '#1DB954'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#a0a0a0' } },
                            x: { grid: { display: false }, ticks: { color: '#a0a0a0' } }
                        }
                    }
                });
            }
        @endif

        // TRANSLATION ENGINE
        const translations = {
            id: { title: "DASHBOARD PROMOTOR", ev_mendatang: "Event Mendatang", tix_jual: "Tiket Terjual", tot_trans: "Total Transaksi", tot_pendapatan: "Total Pendapatan", tot_staff: "Total Staff", tot_guest: "Total Guestlist", tren: "TREN PENDAPATAN (GESER UNTUK MELIHAT HISTORI)", tabel_h: "KELOLA EVENT", th_nama: "Nama Event", th_status: "Status", th_kuota: "Kuota Awal", th_jual: "Terjual", th_uang: "Pendapatan", th_aksi: "Tindakan", no_event: "Belum ada event yang diposting. Coba buat event pertamamu!", stat_aktif: "🟢 AKTIF", stat_selesai: "🔴 SELESAI" },
            en: { title: "PROMOTER DASHBOARD", ev_mendatang: "Upcoming Events", tix_jual: "Tickets Sold", tot_trans: "Total Transactions", tot_pendapatan: "Total Revenue", tot_staff: "Total Staff", tot_guest: "Total Guestlist", tren: "REVENUE TREND (SCROLL TO VIEW HISTORY)", tabel_h: "MANAGE EVENTS", th_nama: "Event Name", th_status: "Status", th_kuota: "Initial Quota", th_jual: "Sold", th_uang: "Revenue", th_aksi: "Actions", no_event: "No events posted yet. Try creating your first event!", stat_aktif: "🟢 ACTIVE", stat_selesai: "🔴 FINISHED" }
        };

        function setLang(lang) {
            document.querySelectorAll('.lang-btn').forEach(btn => btn.classList.remove('active'));
            const btnActive = document.getElementById('btn-' + lang);
            if(btnActive) btnActive.classList.add('active');
            localStorage.setItem('lang', lang);
            document.querySelectorAll('[data-key]').forEach(el => {
                const key = el.getAttribute('data-key');
                if (translations[lang][key]) el.innerText = translations[lang][key];
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const savedLang = localStorage.getItem('lang') || 'id';
            setLang(savedLang);
        });
    </script>
</x-app-layout>