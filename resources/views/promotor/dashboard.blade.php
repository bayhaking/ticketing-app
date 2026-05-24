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
        .btn-action {
            padding: 6px 12px; border-radius: 8px; font-size: 0.7rem; font-weight: 800;
            cursor: pointer; transition: 0.3s; border: none;
            display: inline-flex; align-items: center; gap: 4px; white-space: nowrap;
        }

        .chart-filter-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; gap: 1rem; flex-wrap: wrap; }
        .chart-filter-bar h3 { margin: 0; font-weight: 800; }
        .filter-select { background: var(--bg-input); border: 1px solid var(--border); color: var(--text-main); padding: 8px 14px; border-radius: 8px; font-weight: 700; outline: none; min-width: 250px; cursor: pointer; }
        .filter-select:focus { border-color: var(--accent); }

        .distribution-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; margin-top: 1.5rem; }
        .dist-card { background: var(--bg-input); border: 1px solid var(--border); border-radius: 12px; padding: 1rem; text-align: center; transition: 0.3s; }
        .dist-card:hover { border-color: var(--accent); transform: translateY(-2px); }
        .dist-card.empty { opacity: 0.4; }
        .dist-label { font-size: 0.65rem; color: var(--text-sub); font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
        .dist-value { font-size: 1.6rem; font-weight: 900; color: var(--accent); margin: 6px 0; line-height: 1; }
        .dist-meta { font-size: 0.65rem; color: var(--text-sub); border-top: 1px solid var(--border); padding-top: 6px; margin-top: 6px; }

        .btn-quick-attendees { background: rgba(99, 102, 241, 0.15); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.3); padding: 6px 12px; border-radius: 8px; font-size: 0.7rem; font-weight: 800; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; transition: 0.2s; }
        .btn-quick-attendees:hover { background: rgba(99, 102, 241, 0.3); transform: translateX(2px); }

        /* STATUS BADGES — 3-tier */
        .status-badge {
            padding: 4px 10px; border-radius: 8px;
            font-size: 0.7rem; font-weight: 900;
            display: inline-flex; align-items: center; gap: 4px;
        }
        .status-upcoming { background: rgba(29, 185, 84, 0.2); color: #1DB954; }
        .status-live { background: rgba(255, 68, 68, 0.2); color: #ff6b6b; animation: blink 1.5s infinite; }
        .status-finished { background: rgba(148, 163, 184, 0.2); color: #94a3b8; }
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }

        /* ACTION BUTTONS */
        .btn-insight { background: rgba(168, 85, 247, 0.15); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.4); }
        .btn-insight:hover { background: rgba(168, 85, 247, 0.3); }

        .btn-edit { background: #3b82f6; color: #fff; text-decoration: none; }
        .btn-edit:hover { background: #2563eb; }

        .btn-close-sales { background: #ffc107; color: #000; }
        .btn-close-sales:hover { background: #f59e0b; }

        .btn-reopen { background: #10b981; color: #fff; }
        .btn-reopen:hover { background: #059669; }

        .btn-finish { background: #6b7280; color: #fff; }
        .btn-finish:hover { background: #4b5563; }

        .btn-delete {
            background: none; border: 1px solid #ff4444; color: #ff4444;
            padding: 4px 10px; border-radius: 8px; font-size: 0.6rem;
            cursor: pointer; font-weight: 800;
        }
        .btn-delete:hover { background: rgba(255, 68, 68, 0.15); }

        .action-cell {
            display: flex; gap: 6px; justify-content: center;
            flex-wrap: wrap; max-width: 360px;
        }

        /* INSIGHT MODAL */
        .insight-modal {
            display: none; position: fixed; z-index: 1000;
            left: 0; top: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.85); backdrop-filter: blur(10px);
            overflow-y: auto;
            padding: 2rem;
        }
        .insight-modal.active { display: block; }
        .insight-modal-content {
            background: var(--bg-card);
            border: 2px solid var(--accent);
            border-radius: 1.5rem;
            max-width: 1200px;
            margin: 0 auto;
            padding: 2.5rem;
            position: relative;
            animation: zoom 0.3s;
        }
        .insight-close {
            position: absolute; top: 20px; right: 20px;
            color: var(--text-sub); font-size: 32px; cursor: pointer;
            background: var(--bg-input); border: 1px solid var(--border);
            width: 40px; height: 40px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            transition: 0.2s;
        }
        .insight-close:hover { color: var(--accent); transform: rotate(90deg); }

        .insight-header {
            display: flex; align-items: center; gap: 1rem;
            margin-bottom: 1.5rem; padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
        }
        .insight-title { font-size: 1.5rem; font-weight: 900; margin: 0; }
        .insight-loading {
            text-align: center; padding: 4rem;
            color: var(--text-sub); font-size: 0.9rem;
        }

        /* Big Numbers Row */
        .big-numbers {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem; margin-bottom: 2rem;
        }
        .big-num-card {
            background: var(--bg-input); border: 1px solid var(--border);
            border-radius: 12px; padding: 1.25rem;
        }
        .big-num-icon { font-size: 1.8rem; margin-bottom: 0.5rem; }
        .big-num-value { font-size: 1.8rem; font-weight: 900; color: var(--text-main); line-height: 1; }
        .big-num-label { font-size: 0.7rem; color: var(--text-sub); margin-top: 6px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; }
        .big-num-sub { font-size: 0.65rem; color: var(--accent); margin-top: 4px; font-weight: 700; }

        /* Funnel */
        .funnel-section { margin: 2rem 0; }
        .funnel-row {
            display: flex; align-items: center; gap: 1rem;
            padding: 12px 0;
        }
        .funnel-stage {
            font-size: 0.85rem; font-weight: 700; color: var(--text-sub);
            min-width: 160px;
        }
        .funnel-bar-wrap { flex: 1; background: var(--bg-input); border-radius: 8px; overflow: hidden; height: 32px; position: relative; }
        .funnel-bar {
            background: linear-gradient(90deg, #1DB954, #16a34a);
            height: 100%; display: flex; align-items: center; padding: 0 12px;
            color: #fff; font-weight: 900; font-size: 0.8rem;
            transition: width 0.6s ease;
        }
        .funnel-percent { min-width: 60px; text-align: right; font-weight: 900; color: var(--accent); }

        /* Time to Convert */
        .ttc-section { background: var(--bg-input); border-radius: 12px; padding: 1.5rem; margin: 2rem 0; }
        .ttc-avg { font-size: 1.2rem; font-weight: 900; color: var(--accent); margin-bottom: 1rem; }
        .ttc-bucket {
            display: flex; align-items: center; gap: 1rem;
            padding: 8px 0; border-bottom: 1px solid var(--border);
        }
        .ttc-bucket:last-child { border-bottom: none; }
        .ttc-label { font-size: 0.85rem; min-width: 120px; }
        .ttc-bar-wrap { flex: 1; background: rgba(168, 85, 247, 0.15); border-radius: 6px; overflow: hidden; height: 20px; }
        .ttc-bar { background: #c084fc; height: 100%; }
        .ttc-count { min-width: 60px; text-align: right; font-weight: 700; }

        /* Hourly Heatmap */
        .heatmap-section { margin: 2rem 0; }
        .heatmap-grid {
            display: grid;
            grid-template-columns: repeat(24, 1fr);
            gap: 3px;
            margin-top: 1rem;
        }
        .heatmap-cell {
            aspect-ratio: 1;
            border-radius: 4px;
            background: var(--bg-input);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.55rem; color: rgba(255,255,255,0.7); font-weight: 700;
            cursor: pointer; transition: 0.2s;
        }
        .heatmap-cell:hover { transform: scale(1.2); z-index: 10; position: relative; }
        .heatmap-labels {
            display: grid;
            grid-template-columns: repeat(24, 1fr);
            gap: 3px;
            font-size: 0.55rem;
            color: var(--text-sub);
            text-align: center;
            margin-top: 4px;
        }

        /* Sources & Device */
        .insight-row-grid {
            display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;
            margin: 2rem 0;
        }
        @media (max-width: 768px) {
            .insight-row-grid { grid-template-columns: 1fr; }
        }
        .insight-block {
            background: var(--bg-input);
            border-radius: 12px; padding: 1.5rem;
        }
        .insight-block h4 { margin: 0 0 1rem; font-weight: 900; font-size: 0.9rem; }
        .source-item, .device-item {
            display: flex; justify-content: space-between; align-items: center;
            padding: 8px 0; border-bottom: 1px solid var(--border);
            font-size: 0.85rem;
        }
        .source-item:last-child, .device-item:last-child { border-bottom: none; }
        .source-name { font-weight: 700; }
        .source-stats { font-size: 0.7rem; color: var(--text-sub); }
        .source-conv { color: var(--accent); font-weight: 900; }

        @keyframes zoom {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
    </style>
    @endpush

    <div style="padding: 3rem;">
        <div class="max-w-7xl mx-auto">
            <h1 style="font-size: 2.5rem; font-weight: 900; font-style: italic; margin-bottom: 2rem;" data-key="title">DASHBOARD PROMOTOR</h1>

            @if(session('success'))
                <div style="background: rgba(29, 185, 84, 0.2); border: 1px solid #1DB954; color: #1DB954; padding: 1rem; border-radius: 10px; margin-bottom: 2rem; font-weight: 800;">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div style="background: rgba(255, 68, 68, 0.2); border: 1px solid #ff4444; color: #ff6b6b; padding: 1rem; border-radius: 10px; margin-bottom: 2rem; font-weight: 800;">
                    ❌ {{ session('error') }}
                </div>
            @endif

            {{-- STATS CARDS --}}
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                <div class="stat-card">
                    <div class="stat-label" data-key="ev_mendatang">Event Aktif</div>
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

            {{-- CHART PENDAPATAN --}}
            <div class="chart-card">
                <div class="chart-filter-bar">
                    <h3 data-key="tren">📊 TREN PENDAPATAN</h3>
                    <select id="event_filter" class="filter-select">
                        <option value="" {{ !$filterEventId ? 'selected' : '' }}>🌐 Semua Event</option>
                        @foreach($eventsForFilter as $ev)
                            <option value="{{ $ev['id'] }}" {{ $filterEventId == $ev['id'] ? 'selected' : '' }}>
                                {{ $ev['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="width: 100%; overflow-x: auto; padding-bottom: 10px;">
                    <div style="min-width: 1200px; height: 350px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- DISTRIBUSI --}}
            <div class="chart-card">
                <h3 style="margin: 0 0 0.5rem; font-weight: 800;" data-key="dist_title">🎫 DISTRIBUSI PEMBELIAN TIKET PER TRANSAKSI</h3>
                <p style="color: var(--text-sub); font-size: 0.8rem; margin: 0 0 1rem;" data-key="dist_desc">
                    Pola pembelian berdasarkan jumlah tiket dalam satu transaksi
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
                                <span class="status-badge status-{{ $detail['status'] }}">
                                    {{ $detail['status_label']['label'] }}
                                </span>
                                <div style="font-size: 0.6rem; color: var(--text-sub); margin-top: 4px;">
                                    {{ $detail['status_label']['desc'] }}
                                </div>
                            </td>
                            <td>{{ number_format($detail['initial_quota']) }}</td>
                            <td style="color: var(--accent);">{{ number_format($detail['sold']) }}</td>
                            <td style="font-weight: 900;">Rp {{ number_format($detail['revenue'], 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('promotor.attendees') }}?event_id={{ $detail['id'] }}" class="btn-quick-attendees">
                                    👥 {{ number_format($detail['attendees']) }} →
                                </a>
                            </td>
                            <td>
                                <div class="action-cell">
                                    {{-- INSIGHT --}}
                                    <button type="button" class="btn-action btn-insight"
                                            onclick="openInsight({{ $detail['id'] }}, '{{ e($detail['name']) }}')">
                                        📊 INSIGHT
                                    </button>

                                    {{-- EDIT --}}
                                    <a href="{{ route('promotor.event.edit', $detail['id']) }}" class="btn-action btn-edit">
                                        ✏️ EDIT
                                    </a>

                                    {{-- CLOSE / REOPEN / FINISH (logic conditional) --}}
                                    @if($detail['status'] === 'upcoming')
                                        <form method="POST" action="{{ route('promotor.event.closeSales', $detail['id']) }}" style="margin:0;">
                                            @csrf
                                            <button type="submit" class="btn-action btn-close-sales"
                                                    title="Tutup penjualan tiket (scanner tetap aktif)">
                                                ⛔ CLOSE
                                            </button>
                                        </form>
                                    @elseif($detail['status'] === 'live')
                                        <form method="POST" action="{{ route('promotor.event.reopenSales', $detail['id']) }}" style="margin:0;">
                                            @csrf
                                            <button type="submit" class="btn-action btn-reopen"
                                                    title="Buka kembali penjualan">
                                                🔓 REOPEN
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('promotor.event.finishEvent', $detail['id']) }}" style="margin:0;"
                                              onsubmit="return confirm('Tandai event ini sebagai SELESAI? Aksi ini tidak bisa di-undo dari UI.')">
                                            @csrf
                                            <button type="submit" class="btn-action btn-finish">
                                                🏁 SELESAI
                                            </button>
                                        </form>
                                    @endif

                                    {{-- DELETE --}}
                                    <form method="POST" action="{{ route('promotor.event.delete', $detail['id']) }}" style="margin:0;"
                                          onsubmit="return confirm('Yakin ingin menghapus event ini? Data penjualan akan ikut hilang!')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-delete">
                                            🗑️ HAPUS
                                        </button>
                                    </form>
                                </div>
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

    {{-- INSIGHT MODAL --}}
    <div id="insightModal" class="insight-modal">
        <div class="insight-modal-content">
            <button class="insight-close" onclick="closeInsight()">&times;</button>

            <div class="insight-header">
                <div>
                    <h2 class="insight-title" id="insight_event_name">Loading...</h2>
                    <div style="color: var(--text-sub); font-size: 0.8rem;" id="insight_event_meta">-</div>
                </div>
            </div>

            <div id="insight_body">
                <div class="insight-loading">
                    <div style="font-size: 2rem; margin-bottom: 1rem;">📊</div>
                    Memuat data insight...
                </div>
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
                        borderWidth: 3, fill: true, tension: 0.4,
                        pointRadius: 5, pointBackgroundColor: '#1DB954'
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const idx = context.dataIndex;
                                    return [
                                        'Pendapatan: Rp ' + context.parsed.y.toLocaleString('id-ID'),
                                        'Transaksi: ' + (chartTrans[idx] || 0),
                                        'Tiket: ' + (chartTickets[idx] || 0)
                                    ];
                                }
                            }
                        }
                    },
                    scales: {
                        y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#a0a0a0', callback: v => v >= 1000000 ? 'Rp ' + (v/1000000).toFixed(1) + 'jt' : v >= 1000 ? 'Rp ' + (v/1000).toFixed(0) + 'rb' : 'Rp ' + v } },
                        x: { grid: { display: false }, ticks: { color: '#a0a0a0' } }
                    }
                }
            });
        @endif

        document.getElementById('event_filter').addEventListener('change', function(e) {
            const url = new URL(window.location.href);
            if (e.target.value) url.searchParams.set('event_id', e.target.value);
            else url.searchParams.delete('event_id');
            window.location.href = url.toString();
        });

        // ============================================================
        // INSIGHT MODAL
        // ============================================================
        window.openInsight = function(eventId, eventName) {
            const modal = document.getElementById('insightModal');
            document.getElementById('insight_event_name').textContent = eventName;
            document.getElementById('insight_event_meta').textContent = 'ID: ' + eventId;
            document.getElementById('insight_body').innerHTML = '<div class="insight-loading"><div style="font-size: 2rem; margin-bottom: 1rem;">📊</div>Memuat data insight...</div>';
            modal.classList.add('active');

            // Fetch insight via AJAX
            fetch(`/promotor/event/${eventId}/insight`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                }
            })
            .then(r => r.json())
            .then(data => renderInsight(data))
            .catch(err => {
                document.getElementById('insight_body').innerHTML = '<div class="insight-loading" style="color: #ff6b6b;">❌ Gagal memuat data insight</div>';
                console.error(err);
            });
        };

        window.closeInsight = function() {
            document.getElementById('insightModal').classList.remove('active');
        };

        // Close on backdrop click
        document.getElementById('insightModal').addEventListener('click', function(e) {
            if (e.target === this) closeInsight();
        });

        function renderInsight(data) {
            const bn = data.insight.big_numbers;
            const funnel = data.insight.funnel;
            const ttc = data.insight.time_to_convert;
            const hourly = data.insight.hourly_traffic;
            const sources = data.insight.top_sources;
            const devices = data.insight.device_breakdown;

            // Update header
            document.getElementById('insight_event_meta').textContent =
                `${data.event.status_label.label} · Tanggal: ${data.event.date || '-'}`;

            // Build HTML
            let html = '';

            // ===== BIG NUMBERS =====
            html += '<div class="big-numbers">';
            html += bigNumCard('👁️', bn.total_views, 'Total Views', `${bn.unique_sessions} unique`);
            html += bigNumCard('🖱️', bn.click_buy, 'Klik Beli Tiket', `${bn.ctr}% CTR`);
            html += bigNumCard('💰', bn.conversions, 'Konversi', `${bn.conversion_rate}% rate`);
            html += bigNumCard('🎫', bn.tickets_sold, 'Tiket Terjual', `Rp ${bn.revenue.toLocaleString('id-ID')}`);
            html += '</div>';

            // ===== FUNNEL =====
            html += '<div class="funnel-section">';
            html += '<h4 style="margin: 0 0 1rem; font-weight: 900;">🎯 FUNNEL CONVERSION</h4>';
            const maxFunnel = Math.max(...funnel.map(f => f.count));
            funnel.forEach(stage => {
                const widthPct = maxFunnel > 0 ? (stage.count / maxFunnel) * 100 : 0;
                html += `
                    <div class="funnel-row">
                        <div class="funnel-stage">${stage.stage}</div>
                        <div class="funnel-bar-wrap">
                            <div class="funnel-bar" style="width: ${widthPct}%;">${stage.count}</div>
                        </div>
                        <div class="funnel-percent">${stage.percentage}%</div>
                    </div>
                `;
            });
            html += '</div>';

            // ===== TIME TO CONVERT =====
            if (ttc.total_converted > 0) {
                html += '<div class="ttc-section">';
                html += '<h4 style="margin: 0 0 0.5rem; font-weight: 900;">⏱️ WAKTU KONVERSI</h4>';
                html += `<div class="ttc-avg">Rata-rata: ${ttc.average_label} (dari ${ttc.total_converted} konversi)</div>`;
                const maxTtc = Math.max(...Object.values(ttc.distribution));
                for (const [label, count] of Object.entries(ttc.distribution)) {
                    const pct = maxTtc > 0 ? (count / maxTtc) * 100 : 0;
                    html += `
                        <div class="ttc-bucket">
                            <div class="ttc-label">${label}</div>
                            <div class="ttc-bar-wrap">
                                <div class="ttc-bar" style="width: ${pct}%;"></div>
                            </div>
                            <div class="ttc-count">${count}</div>
                        </div>
                    `;
                }
                html += '</div>';
            }

            // ===== HOURLY HEATMAP =====
            const maxHourly = Math.max(...hourly.map(h => h.views));
            html += '<div class="heatmap-section">';
            html += '<h4 style="margin: 0 0 0.5rem; font-weight: 900;">🕐 TRAFFIC HEATMAP (24 JAM)</h4>';
            html += '<p style="color: var(--text-sub); font-size: 0.75rem; margin: 0 0 1rem;">Warna lebih terang = traffic lebih banyak di jam itu</p>';
            html += '<div class="heatmap-grid">';
            hourly.forEach(h => {
                const intensity = maxHourly > 0 ? (h.views / maxHourly) : 0;
                const opacity = 0.1 + intensity * 0.9;
                const bg = `rgba(29, 185, 84, ${opacity})`;
                html += `
                    <div class="heatmap-cell" style="background: ${bg};"
                         title="Jam ${h.label}: ${h.views} views, ${h.conversions} konversi">
                        ${h.views > 0 ? h.views : ''}
                    </div>
                `;
            });
            html += '</div>';
            html += '<div class="heatmap-labels">';
            for (let i = 0; i < 24; i++) {
                html += `<div>${i}</div>`;
            }
            html += '</div>';
            html += '</div>';

            // ===== TWO COLUMN: SOURCES & DEVICES =====
            html += '<div class="insight-row-grid">';

            // Sources
            html += '<div class="insight-block">';
            html += '<h4>🌐 TOP SOURCES</h4>';
            if (sources.length === 0) {
                html += '<div style="color: var(--text-sub); font-size: 0.85rem; padding: 1rem; text-align: center;">Belum ada data source.</div>';
            } else {
                sources.slice(0, 8).forEach(s => {
                    html += `
                        <div class="source-item">
                            <div>
                                <div class="source-name">${s.source}</div>
                                <div class="source-stats">${s.views} views · ${s.clicks} klik</div>
                            </div>
                            <div class="source-conv">${s.conversions} sale<br><span style="font-size: 0.65rem; color: var(--text-sub);">${s.conversion_rate}%</span></div>
                        </div>
                    `;
                });
            }
            html += '</div>';

            // Devices
            html += '<div class="insight-block">';
            html += '<h4>📱 DEVICE BREAKDOWN</h4>';
            if (devices.length === 0) {
                html += '<div style="color: var(--text-sub); font-size: 0.85rem; padding: 1rem; text-align: center;">Belum ada data device.</div>';
            } else {
                const totalDeviceViews = devices.reduce((sum, d) => sum + d.views, 0);
                devices.forEach(d => {
                    const pct = totalDeviceViews > 0 ? Math.round((d.views / totalDeviceViews) * 100) : 0;
                    const icon = d.device === 'Mobile' ? '📱' : d.device === 'Tablet' ? '📲' : d.device === 'Desktop' ? '💻' : '🖥️';
                    html += `
                        <div class="device-item">
                            <div>
                                <div class="source-name">${icon} ${d.device}</div>
                                <div class="source-stats">${d.views} views (${pct}%)</div>
                            </div>
                            <div class="source-conv">${d.conversions} sale</div>
                        </div>
                    `;
                });
            }
            html += '</div>';
            html += '</div>';

            document.getElementById('insight_body').innerHTML = html;
        }

        function bigNumCard(icon, value, label, sub) {
            const valStr = typeof value === 'number' ? value.toLocaleString('id-ID') : value;
            return `
                <div class="big-num-card">
                    <div class="big-num-icon">${icon}</div>
                    <div class="big-num-value">${valStr}</div>
                    <div class="big-num-label">${label}</div>
                    <div class="big-num-sub">${sub}</div>
                </div>
            `;
        }

        // Translations (sama seperti sebelumnya, simpler now)
        const translations = {
            id: {
                title: "DASHBOARD PROMOTOR",
                ev_mendatang: "Event Aktif", tix_jual: "Tiket Terjual",
                tot_trans: "Total Transaksi", tot_pendapatan: "Total Pendapatan",
                tot_staff: "Total Staff", tot_guest: "Total Guestlist",
                tren: "📊 TREN PENDAPATAN", tabel_h: "KELOLA EVENT",
                th_nama: "Nama Event", th_status: "Status", th_kuota: "Kuota",
                th_jual: "Terjual", th_uang: "Pendapatan", th_peserta: "Peserta",
                th_aksi: "Tindakan", no_event: "Belum ada event yang diposting.",
                dist_title: "🎫 DISTRIBUSI PEMBELIAN TIKET PER TRANSAKSI",
                dist_desc: "Pola pembelian berdasarkan jumlah tiket dalam satu transaksi"
            },
            en: {
                title: "PROMOTER DASHBOARD",
                ev_mendatang: "Active Events", tix_jual: "Tickets Sold",
                tot_trans: "Total Transactions", tot_pendapatan: "Total Revenue",
                tot_staff: "Total Staff", tot_guest: "Total Guestlist",
                tren: "📊 REVENUE TREND", tabel_h: "MANAGE EVENTS",
                th_nama: "Event Name", th_status: "Status", th_kuota: "Quota",
                th_jual: "Sold", th_uang: "Revenue", th_peserta: "Attendees",
                th_aksi: "Actions", no_event: "No events posted yet.",
                dist_title: "🎫 PURCHASE DISTRIBUTION PER TRANSACTION",
                dist_desc: "Purchase pattern based on ticket count per transaction"
            }
        };

        window.setLang = function(lang) {
            if (lang !== 'id' && lang !== 'en') lang = 'id';
            document.querySelectorAll('.lang-btn').forEach(btn => btn.classList.remove('active'));
            const btnActive = document.getElementById('btn-' + lang);
            if (btnActive) btnActive.classList.add('active');
            try { localStorage.setItem('lang', lang); } catch (e) {}
            document.querySelectorAll('[data-key]').forEach(el => {
                const key = el.getAttribute('data-key');
                if (translations[lang] && translations[lang][key]) el.textContent = translations[lang][key];
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