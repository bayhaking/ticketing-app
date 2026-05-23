<x-app-layout>
    @push('styles')
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

        .chart-filter-bar {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 1.5rem; gap: 1rem; flex-wrap: wrap;
        }
        .chart-filter-bar h3 { margin: 0; font-weight: 800; }
        .filter-select {
            background: var(--bg-input); border: 1px solid var(--border); color: var(--text-main);
            padding: 8px 14px; border-radius: 8px; font-weight: 700; outline: none;
            min-width: 250px; cursor: pointer;
        }
        .filter-select:focus { border-color: var(--accent); }

        .distribution-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 12px; margin-top: 1.5rem;
        }
        .dist-card {
            background: var(--bg-input); border: 1px solid var(--border);
            border-radius: 12px; padding: 1rem; text-align: center;
            transition: 0.3s;
        }
        .dist-card:hover { border-color: var(--accent); transform: translateY(-2px); }
        .dist-card.empty { opacity: 0.4; }
        .dist-label {
            font-size: 0.65rem; color: var(--text-sub); font-weight: 800;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .dist-value {
            font-size: 1.6rem; font-weight: 900; color: var(--accent);
            margin: 6px 0; line-height: 1;
        }
        .dist-meta {
            font-size: 0.65rem; color: var(--text-sub);
            border-top: 1px solid var(--border); padding-top: 6px; margin-top: 6px;
        }

        .btn-quick-attendees {
            background: rgba(99, 102, 241, 0.15);
            color: #818cf8;
            border: 1px solid rgba(99, 102, 241, 0.3);
            padding: 6px 12px; border-radius: 8px;
            font-size: 0.7rem; font-weight: 800;
            text-decoration: none;
            display: inline-flex; align-items: center; gap: 4px;
            transition: 0.2s;
        }
        .btn-quick-attendees:hover {
            background: rgba(99, 102, 241, 0.3);
            transform: translateX(2px);
        }
    </style>
    @endpush

    <div style="padding: 3rem;">
        <div class="max-w-7xl mx-auto">
            <h1 style="font-size: 2.5rem; font-weight: 900; font-style: italic; margin-bottom: 2rem;" data-key="title">PROMOTOR DASHBOARD</h1>

            @if(session('success'))
                <div style="background: rgba(29, 185, 84, 0.2); border: 1px solid #1DB954; color: #1DB954; padding: 1rem; border-radius: 10px; margin-bottom: 2rem; font-weight: 800;">
                    ✅ {{ session('success') }}
                </div>
            @endif

            {{-- STATS CARDS --}}
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

            {{-- CHART PENDAPATAN DENGAN FILTER --}}
            <div class="chart-card">
                <div class="chart-filter-bar">
                    <h3 data-key="tren">📊 TREN PENDAPATAN</h3>
                    <select id="event_filter" class="filter-select">
                        <option value="" {{ !$filterEventId ? 'selected' : '' }}>
                            🌐 Semua Event
                        </option>
                        @foreach($eventsForFilter as $ev)
                            <option value="{{ $ev['id'] }}" {{ $filterEventId == $ev['id'] ? 'selected' : '' }}>
                                {{ $ev['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <p style="color: var(--text-sub); font-size: 0.8rem; margin-bottom: 1rem;">
                    @if($filterEventId)
                        Menampilkan data event terpilih, 90 hari terakhir.
                    @else
                        Menampilkan data <strong style="color: var(--accent);">semua event</strong>, 90 hari terakhir.
                    @endif
                </p>
                <div style="width: 100%; overflow-x: auto; padding-bottom: 10px;">
                    <div style="min-width: 1200px; height: 350px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- DISTRIBUSI JUMLAH TIKET PER TRANSAKSI --}}
            <div class="chart-card">
                <h3 style="margin: 0 0 0.5rem; font-weight: 800;">🎫 DISTRIBUSI PEMBELIAN TIKET PER TRANSAKSI</h3>
                <p style="color: var(--text-sub); font-size: 0.8rem; margin: 0 0 1rem;">
                    Pola pembelian berdasarkan jumlah tiket dalam satu transaksi
                    @if($filterEventId)
                        — <strong style="color: var(--accent);">filter event aktif</strong>
                    @else
                        (semua event)
                    @endif
                </p>

                <div class="distribution-grid">
                    @foreach($distribution as $dist)
                        <div class="dist-card {{ $dist['transactions'] == 0 ? 'empty' : '' }}">
                            <div class="dist-label">{{ $dist['label'] }}</div>
                            <div class="dist-value">{{ number_format($dist['transactions']) }}</div>
                            <div class="dist-meta">
                                <div>{{ number_format($dist['tickets']) }} tiket</div>
                                <div style="margin-top: 2px; color: var(--accent);">
                                    Rp {{ number_format($dist['revenue'], 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <p style="font-size: 0.7rem; color: var(--text-sub); margin-top: 1rem; line-height: 1.5;">
                    💡 <strong>Insight:</strong> Pembelian 1-2 tiket = pelanggan individual.
                    3-5 tiket = grup keluarga/teman. 6+ tiket = potensi reseller atau corporate buyer
                    yang perlu di-follow up.
                </p>
            </div>

            {{-- TABEL KELOLA EVENT --}}
            <div class="table-card">
                <h3 style="margin-bottom: 1.5rem; font-weight: 800;" data-key="tabel_h">KELOLA EVENT</h3>
                <table>
                    <thead>
                        <tr>
                            <th data-key="th_nama">Nama Event</th>
                            <th data-key="th_status">Status</th>
                            <th data-key="th_kuota">Kuota</th>
                            <th data-key="th_jual">Terjual</th>
                            <th data-key="th_uang">Pendapatan</th>
                            <th data-key="th_peserta">Peserta</th>
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
                            <td>
                                <a href="{{ route('promotor.attendees') }}?event_id={{ $detail['id'] }}" class="btn-quick-attendees">
                                    👥 {{ number_format($detail['attendees']) }} →
                                </a>
                            </td>
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
                            <td colspan="7" style="text-align: center; color: var(--text-sub); padding: 2rem;" data-key="no_event">Belum ada event yang diposting. Coba buat event pertamamu!</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    (function() {
        'use strict';

        @if(isset($chartData) && $chartData->count() > 0)
            const chartLabels = {!! json_encode($chartData->pluck('date')) !!};
            const chartValues = {!! json_encode($chartData->pluck('total')) !!};
            const chartTrans  = {!! json_encode($chartData->pluck('transactions')) !!};
            const chartTickets = {!! json_encode($chartData->pluck('tickets')) !!};

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
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const idx = context.dataIndex;
                                    const revenue = context.parsed.y;
                                    const trans = chartTrans[idx] || 0;
                                    const tickets = chartTickets[idx] || 0;
                                    return [
                                        'Pendapatan: Rp ' + revenue.toLocaleString('id-ID'),
                                        'Transaksi: ' + trans,
                                        'Tiket: ' + tickets
                                    ];
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            grid: { color: 'rgba(255,255,255,0.05)' },
                            ticks: {
                                color: '#a0a0a0',
                                callback: function(value) {
                                    if (value >= 1000000) return 'Rp ' + (value/1000000).toFixed(1) + 'jt';
                                    if (value >= 1000) return 'Rp ' + (value/1000).toFixed(0) + 'rb';
                                    return 'Rp ' + value;
                                }
                            }
                        },
                        x: { grid: { display: false }, ticks: { color: '#a0a0a0' } }
                    }
                }
            });
        @else
            const canvas = document.getElementById('revenueChart');
            if (canvas) {
                const ctx = canvas.getContext('2d');
                ctx.fillStyle = '#a0a0a0';
                ctx.font = '14px sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('Belum ada data penjualan untuk periode ini', canvas.width / 2, canvas.height / 2);
            }
        @endif

        document.getElementById('event_filter').addEventListener('change', function(e) {
            const eventId = e.target.value;
            const url = new URL(window.location.href);
            if (eventId) {
                url.searchParams.set('event_id', eventId);
            } else {
                url.searchParams.delete('event_id');
            }
            window.location.href = url.toString();
        });

        const translations = {
            id: {
                title: "DASHBOARD PROMOTOR",
                ev_mendatang: "Event Mendatang", tix_jual: "Tiket Terjual",
                tot_trans: "Total Transaksi", tot_pendapatan: "Total Pendapatan",
                tot_staff: "Total Staff", tot_guest: "Total Guestlist",
                tren: "📊 TREN PENDAPATAN", tabel_h: "KELOLA EVENT",
                th_nama: "Nama Event", th_status: "Status", th_kuota: "Kuota",
                th_jual: "Terjual", th_uang: "Pendapatan", th_peserta: "Peserta",
                th_aksi: "Tindakan",
                no_event: "Belum ada event yang diposting. Coba buat event pertamamu!",
                stat_aktif: "🟢 AKTIF", stat_selesai: "🔴 SELESAI"
            },
            en: {
                title: "PROMOTER DASHBOARD",
                ev_mendatang: "Upcoming Events", tix_jual: "Tickets Sold",
                tot_trans: "Total Transactions", tot_pendapatan: "Total Revenue",
                tot_staff: "Total Staff", tot_guest: "Total Guestlist",
                tren: "📊 REVENUE TREND", tabel_h: "MANAGE EVENTS",
                th_nama: "Event Name", th_status: "Status", th_kuota: "Quota",
                th_jual: "Sold", th_uang: "Revenue", th_peserta: "Attendees",
                th_aksi: "Actions",
                no_event: "No events posted yet. Try creating your first event!",
                stat_aktif: "🟢 ACTIVE", stat_selesai: "🔴 FINISHED"
            }
        };

        window.setLang = function(lang) {
            if (lang !== 'id' && lang !== 'en') lang = 'id';
            document.querySelectorAll('.lang-btn').forEach(function(btn) { btn.classList.remove('active'); });
            const btnActive = document.getElementById('btn-' + lang);
            if (btnActive) btnActive.classList.add('active');
            try { localStorage.setItem('lang', lang); } catch (e) {}
            document.querySelectorAll('[data-key]').forEach(function(el) {
                const key = el.getAttribute('data-key');
                if (translations[lang] && translations[lang][key]) {
                    el.textContent = translations[lang][key];
                }
            });
        };

        document.addEventListener('DOMContentLoaded', function() {
            let savedLang = 'id';
            try {
                const stored = localStorage.getItem('lang');
                if (stored === 'id' || stored === 'en') savedLang = stored;
            } catch (e) {}
            window.setLang(savedLang);
        });
    })();
    </script>
    @endpush
</x-app-layout>