<x-app-layout>
    @push('styles')
    <style>
        .event-tabs {
            display: flex; gap: 8px; overflow-x: auto;
            border-bottom: 1px solid var(--border); margin-bottom: 2rem;
            padding-bottom: 2px;
        }
        .event-tab-link {
            background: var(--bg-card); color: var(--text-sub);
            padding: 12px 20px; cursor: pointer; font-weight: 800;
            border: 1px solid var(--border); border-bottom: none;
            border-radius: 12px 12px 0 0; white-space: nowrap;
            transition: 0.2s; font-size: 0.85rem;
            text-decoration: none;
            display: flex; align-items: center; gap: 6px;
        }
        .event-tab-link:hover { color: var(--accent); border-color: var(--accent); }
        .event-tab-link.active {
            color: var(--accent); border-color: var(--accent);
            background: rgba(29, 185, 84, 0.1);
            border-bottom: 2px solid var(--accent);
        }
        .event-tab-count {
            background: var(--bg-input); color: var(--accent);
            padding: 2px 8px; border-radius: 100px;
            font-size: 0.65rem; font-weight: 900;
        }

        .tabs { display: flex; gap: 20px; border-bottom: 1px solid var(--border); margin-bottom: 2rem; }
        .tab-link { color: var(--text-sub); padding: 10px 20px; cursor: pointer; font-weight: 800; border-bottom: 3px solid transparent; transition: 0.3s; }
        .tab-link.active { color: var(--accent); border-bottom-color: var(--accent); }
        .tab-content { display: none; animation: fadeIn 0.4s ease-in-out; }
        .tab-content.active { display: block; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .guest-cat-badge { background: var(--bg-input); border: 1px solid var(--accent); color: var(--accent); font-size: 0.6rem; padding: 2px 8px; border-radius: 5px; text-transform: uppercase; }
        .qty-cat-badge {
            background: rgba(255, 193, 7, 0.15); border: 1px solid rgba(255, 193, 7, 0.4);
            color: #ffc107; font-size: 0.6rem; padding: 2px 8px; border-radius: 5px;
            text-transform: uppercase; font-weight: 800; white-space: nowrap;
        }
        .qty-cat-badge.solo { background: rgba(29, 185, 84, 0.15); border-color: rgba(29, 185, 84, 0.4); color: #1DB954; }
        .qty-cat-badge.group { background: rgba(99, 102, 241, 0.15); border-color: rgba(99, 102, 241, 0.4); color: #818cf8; }
        .qty-cat-badge.bulk { background: rgba(244, 63, 94, 0.15); border-color: rgba(244, 63, 94, 0.4); color: #fb7185; }

        .page-header { margin-bottom: 2rem; }
        .page-header h1 {
            font-size: 2.5rem; font-weight: 900; font-style: italic;
            color: var(--text-main); margin: 0 0 0.5rem;
        }
        .page-header p { color: var(--text-sub); margin: 0; }

        .action-bar {
            display: flex; justify-content: space-between; align-items: center;
            gap: 1rem; flex-wrap: wrap;
            margin-bottom: 2rem;
            padding: 1.25rem 1.5rem;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
        }
        .action-group { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .action-group-label {
            font-size: 0.65rem; font-weight: 900; color: var(--text-sub);
            text-transform: uppercase; letter-spacing: 1px;
            padding-right: 12px;
            border-right: 1px solid var(--border);
            margin-right: 4px;
        }

        .btn-base {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 10px 16px; border-radius: 10px;
            font-weight: 800; font-size: 0.75rem;
            text-transform: uppercase; letter-spacing: 0.5px;
            border: none; cursor: pointer; text-decoration: none;
            transition: 0.2s;
        }
        .btn-base:hover { transform: translateY(-1px); }

        .btn-add-staff { background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.4); color: #818cf8; }
        .btn-add-staff:hover { background: rgba(99, 102, 241, 0.25); }
        .btn-add-cat { background: rgba(255, 193, 7, 0.15); border: 1px solid rgba(255, 193, 7, 0.4); color: #ffc107; }
        .btn-add-cat:hover { background: rgba(255, 193, 7, 0.25); }
        .btn-add-guest { background: rgba(29, 185, 84, 0.15); border: 1px solid rgba(29, 185, 84, 0.4); color: var(--accent); }
        .btn-add-guest:hover { background: rgba(29, 185, 84, 0.25); }

        .btn-export {
            background: linear-gradient(135deg, #1DB954, #16a34a);
            color: #fff;
            box-shadow: 0 4px 12px rgba(29, 185, 84, 0.3);
            padding: 12px 22px; font-size: 0.8rem;
        }
        .btn-export:hover {
            box-shadow: 0 6px 20px rgba(29, 185, 84, 0.5);
            transform: translateY(-2px);
        }
        .btn-export-info {
            font-size: 0.65rem; color: var(--text-sub);
            margin-top: 6px; font-weight: 600; text-align: right;
        }

        .filter-box {
            background: var(--bg-card); border: 1px solid var(--border);
            padding: 1.5rem; border-radius: 1.5rem; margin-bottom: 2rem;
            display: flex; gap: 15px; align-items: center; flex-wrap: wrap;
        }
        select.filter-input, input.filter-input {
            background: var(--bg-input); border: 1px solid var(--border);
            color: var(--text-main); padding: 10px 15px; border-radius: 10px;
            font-weight: bold; outline: none;
        }
        .filter-input { flex-grow: 1; min-width: 200px; }
        .btn-filter {
            background: var(--accent); color: #000; font-weight: 900;
            padding: 10px 20px; border-radius: 10px; cursor: pointer;
            border: none; text-transform: uppercase; transition: 0.3s;
        }

        .table-card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 1.5rem; padding: 2rem; overflow-x: auto;
        }
        table { width: 100%; border-collapse: collapse; }
        th {
            text-align: left; color: var(--accent); font-size: 0.75rem;
            text-transform: uppercase; padding: 1rem;
            border-bottom: 1px solid var(--border);
        }
        td {
            padding: 1rem; color: var(--text-main); font-weight: 600;
            border-bottom: 1px solid var(--border); font-size: 0.85rem;
        }
        .badge-status { padding: 4px 10px; border-radius: 8px; font-size: 0.7rem; font-weight: 800; }
        .status-hadir { background: rgba(29, 185, 84, 0.2); color: #1DB954; }
        .status-belum { background: rgba(255, 193, 7, 0.2); color: #ffc107; }

        .modal-form-box {
            background: var(--bg-card); border: 1px solid var(--border);
            border-top: 5px solid var(--accent); border-radius: 1.5rem;
            padding: 2.5rem; width: 90%; max-width: 500px;
            animation: zoom 0.3s; position: relative;
        }

        .quick-stats {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 12px; margin-bottom: 1.5rem;
        }
        .qstat-card {
            background: var(--bg-input); border: 1px solid var(--border);
            border-radius: 12px; padding: 12px; text-align: center;
        }
        .qstat-label { font-size: 0.65rem; color: var(--text-sub); font-weight: 800; text-transform: uppercase; }
        .qstat-value { font-size: 1.4rem; font-weight: 900; color: var(--accent); margin-top: 4px; }

        .password-notice {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(168, 85, 247, 0.15));
            border: 2px solid #818cf8;
            border-radius: 1.5rem; padding: 2rem; margin-bottom: 2rem;
        }
        .password-notice h3 { color: #818cf8; font-weight: 900; margin: 0 0 1rem; }
        .password-notice .credentials {
            background: var(--bg-input); border-radius: 12px; padding: 1rem;
            font-family: monospace; font-size: 1rem;
            display: flex; flex-direction: column; gap: 8px;
        }
        .password-notice .credentials > div { display: flex; gap: 10px; align-items: center; }
        .password-notice .credentials .label { color: var(--text-sub); min-width: 100px; }
        .password-notice .credentials .value { color: #fff; font-weight: 900; }
        .password-notice .warning {
            color: #ffc107; font-size: 0.85rem; margin-top: 1rem;
            font-weight: 700; line-height: 1.5;
        }
        .copy-btn {
            background: var(--accent); color: #000; border: none;
            padding: 6px 14px; border-radius: 8px; cursor: pointer;
            font-weight: 800; font-size: 0.75rem;
        }

        /* DELETE STAFF BUTTON */
        .btn-delete-staff {
            background: rgba(255, 68, 68, 0.15);
            border: 1px solid rgba(255, 68, 68, 0.4);
            color: #ff6b6b;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.7rem;
            font-weight: 800;
            cursor: pointer;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .btn-delete-staff:hover {
            background: rgba(255, 68, 68, 0.3);
            transform: translateY(-1px);
        }

        /* DELETE MODAL — warning style */
        .delete-modal-box {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-top: 5px solid #ff4444;
            border-radius: 1.5rem;
            padding: 2.5rem;
            width: 90%; max-width: 480px;
            animation: zoom 0.3s;
            position: relative;
        }
        .delete-warning {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #ff6b6b;
            padding: 12px 16px;
            border-radius: 10px;
            margin: 1rem 0;
            font-size: 0.85rem;
            line-height: 1.5;
        }
        .delete-staff-name {
            font-size: 1.1rem;
            color: #fff;
            font-weight: 900;
            margin: 0.5rem 0;
            padding: 10px 14px;
            background: var(--bg-input);
            border-radius: 8px;
            text-align: center;
        }
        .btn-confirm-delete {
            background: #ff4444;
            color: #fff;
            font-weight: 900;
            padding: 12px 20px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            text-transform: uppercase;
            transition: 0.3s;
        }
        .btn-confirm-delete:hover { background: #ff1a1a; }
        .btn-cancel {
            background: var(--bg-input);
            color: var(--text-main);
            font-weight: 700;
            padding: 12px 20px;
            border-radius: 10px;
            border: 1px solid var(--border);
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .action-bar { flex-direction: column; align-items: stretch; }
            .action-group { justify-content: center; }
            .action-group-label { display: none; }
        }
    </style>
    @endpush

    <div style="padding: 3rem;">
        <div class="max-w-7xl mx-auto">

            @if(session('success'))
                <div style="background: rgba(29, 185, 84, 0.2); border: 1px solid #1DB954; color: #1DB954; padding: 1rem; border-radius: 10px; font-weight: 800; margin-bottom: 2rem;">✅ {{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div style="background: rgba(255, 68, 68, 0.2); border: 1px solid #ff4444; color: #ff6b6b; padding: 1rem; border-radius: 10px; font-weight: 800; margin-bottom: 2rem;">❌ {{ session('error') }}</div>
            @endif

            {{-- Password notice setelah create staff --}}
            @if(session('staff_password_notice'))
                @php $notice = session('staff_password_notice'); @endphp
                <div class="password-notice">
                    <h3 data-key="pwd_notice_title">🔐 KREDENSIAL STAFF BARU — SIMPAN SEKARANG!</h3>
                    <p style="color: var(--text-sub); font-size: 0.9rem; margin: 0 0 1rem;" data-key="pwd_notice_desc">
                        Password di bawah ini hanya muncul SEKALI. Setelah halaman di-refresh, password tidak bisa dilihat lagi.
                    </p>
                    <div class="credentials">
                        <div>
                            <span class="label" data-key="lbl_name">Nama:</span>
                            <span class="value">{{ $notice['name'] }}</span>
                        </div>
                        <div>
                            <span class="label" data-key="lbl_email">Email:</span>
                            <span class="value">{{ $notice['email'] }}</span>
                            <button type="button" class="copy-btn" onclick="copyToClipboard('{{ $notice['email'] }}', this)" data-key="btn_copy">COPY</button>
                        </div>
                        <div>
                            <span class="label" data-key="lbl_pwd">Password:</span>
                            <span class="value">{{ $notice['password'] }}</span>
                            <button type="button" class="copy-btn" onclick="copyToClipboard('{{ $notice['password'] }}', this)" data-key="btn_copy2">COPY</button>
                        </div>
                    </div>
                    <div class="warning" data-key="pwd_warning">
                        ⚠️ Berikan kredensial ini ke staff melalui channel yang aman (WA pribadi). Jangan kirim lewat email plaintext.
                    </div>
                </div>
            @endif

            <div class="page-header">
                <h1 data-key="page_title">KELOLA PESERTA</h1>
                <p data-key="page_subtitle">Kelola data pembeli tiket, tamu undangan, dan staff lapangan per event.</p>
            </div>

            {{-- ACTION BAR --}}
            <div class="action-bar">
                <div class="action-group">
                    <span class="action-group-label" data-key="lbl_tambah">➕ Tambah</span>
                    <button type="button" class="btn-base btn-add-staff" onclick="openModal('staffModal')">
                        <span data-key="btn_staff_scanner">👷 Staff Scanner</span>
                    </button>
                    <button type="button" class="btn-base btn-add-cat" onclick="openModal('catModal')">
                        <span data-key="btn_kat_guest">🏷️ Kategori Guest</span>
                    </button>
                    <button type="button" class="btn-base btn-add-guest" onclick="openModal('guestModal')">
                        <span data-key="btn_guestlist">🎟️ Guestlist</span>
                    </button>
                </div>

                <div>
                    <div class="action-group" style="justify-content: flex-end;">
                        <a href="{{ route('promotor.attendees.export') }}{{ request('event_id') ? '?event_id='.request('event_id') : '' }}"
                           class="btn-base btn-export">
                            <span style="font-size: 1.2rem;">📊</span>
                            <span>
                                <span data-key="btn_download">Download Excel</span>
                                {{ request('event_id') ? '(Event Ini)' : '(Semua)' }}
                            </span>
                        </a>
                    </div>
                    <div class="btn-export-info" data-key="export_info">
                        Multi-sheet · per event · per kategori
                    </div>
                </div>
            </div>

            {{-- TAB EVENT --}}
            <div class="event-tabs">
                <a href="{{ route('promotor.attendees') }}"
                   class="event-tab-link {{ !request('event_id') ? 'active' : '' }}">
                    <span data-key="tab_semua">🌐 Semua Event</span>
                    <span class="event-tab-count">{{ $totalAllAttendees ?? 0 }}</span>
                </a>
                @foreach($events as $ev)
                    <a href="{{ route('promotor.attendees') }}?event_id={{ $ev->id }}"
                       class="event-tab-link {{ request('event_id') == $ev->id ? 'active' : '' }}">
                        🎫 {{ Str::limit($ev->name, 25) }}
                        <span class="event-tab-count">{{ $ev->attendees_count ?? 0 }}</span>
                    </a>
                @endforeach
            </div>

            {{-- QUICK STATS --}}
            @if(request('event_id') && isset($currentEvent))
                <div class="quick-stats">
                    <div class="qstat-card">
                        <div class="qstat-label" data-key="stat_pembeli">Total Pembeli</div>
                        <div class="qstat-value">{{ number_format($orders->count()) }}</div>
                    </div>
                    <div class="qstat-card">
                        <div class="qstat-label" data-key="stat_tiket">Total Tiket</div>
                        <div class="qstat-value">{{ number_format($orders->sum('quantity')) }}</div>
                    </div>
                    <div class="qstat-card">
                        <div class="qstat-label" data-key="stat_hadir">Sudah Hadir</div>
                        <div class="qstat-value" style="color: #1DB954;">{{ number_format($orders->whereNotNull('scanned_at')->count()) }}</div>
                    </div>
                    <div class="qstat-card">
                        <div class="qstat-label" data-key="stat_belum">Belum Hadir</div>
                        <div class="qstat-value" style="color: #ffc107;">{{ number_format($orders->whereNull('scanned_at')->count()) }}</div>
                    </div>
                    <div class="qstat-card">
                        <div class="qstat-label" data-key="stat_guestlist">Guestlist</div>
                        <div class="qstat-value" style="color: #f59e0b;">{{ number_format($guests->count()) }}</div>
                    </div>
                </div>
            @endif

            {{-- FILTER --}}
            <form class="filter-box" method="GET" action="{{ route('promotor.attendees') }}">
                @if(request('event_id'))
                    <input type="hidden" name="event_id" value="{{ request('event_id') }}">
                @endif

                <select name="qty_category" class="filter-input">
                    <option value="" data-key="opt_all_kat">📊 Semua Kategori Pembelian</option>
                    <option value="solo" {{ request('qty_category') == 'solo' ? 'selected' : '' }} data-key="opt_solo">🟢 Solo (1 tiket)</option>
                    <option value="pair" {{ request('qty_category') == 'pair' ? 'selected' : '' }} data-key="opt_pair">👥 Pasangan (2 tiket)</option>
                    <option value="small_group" {{ request('qty_category') == 'small_group' ? 'selected' : '' }} data-key="opt_small">👨‍👩‍👧 Grup Kecil (3-5 tiket)</option>
                    <option value="large_group" {{ request('qty_category') == 'large_group' ? 'selected' : '' }} data-key="opt_large">👥👥 Grup Besar (6-9 tiket)</option>
                    <option value="bulk" {{ request('qty_category') == 'bulk' ? 'selected' : '' }} data-key="opt_bulk">📦 Bulk (10+ tiket)</option>
                </select>

                <input type="text" name="search" class="filter-input" placeholder="🔍 Cari nama / email / order ID..." value="{{ request('search') }}" maxlength="100" id="search_input">

                <button type="submit" class="btn-filter" data-key="btn_filter">FILTER</button>

                @if(request()->hasAny(['qty_category', 'search']))
                    <a href="{{ route('promotor.attendees') }}{{ request('event_id') ? '?event_id='.request('event_id') : '' }}"
                       style="color: var(--text-sub); font-size: 0.75rem; text-decoration: underline;" data-key="reset_filter">
                        Reset filter
                    </a>
                @endif
            </form>

            {{-- TAB DALAM --}}
            <div class="tabs">
                <div class="tab-link active" onclick="switchTab(event, 'pembeli')">
                    <span data-key="tab_buyers">TICKET BUYERS</span> ({{ $orders->count() }})
                </div>
                <div class="tab-link" onclick="switchTab(event, 'guestlist')">
                    <span data-key="tab_guestlist">GUESTLIST</span> ({{ $guests->count() }})
                </div>
                <div class="tab-link" onclick="switchTab(event, 'staff_list')" style="color: #6366f1;">
                    <span data-key="tab_scanner">TEAM SCANNER</span> ({{ $staffs->count() }})
                </div>
            </div>

            {{-- TAB: TICKET BUYERS --}}
            <div id="pembeli" class="tab-content active">
                <div class="table-card">
                    <table>
                        <thead>
                            <tr>
                                <th data-key="th_order_id">Order ID</th>
                                <th data-key="th_nama">Nama</th>
                                @if(!request('event_id')) <th data-key="th_event">Event</th> @endif
                                <th data-key="th_tiket">Tiket</th>
                                <th data-key="th_jumlah">Jumlah</th>
                                <th data-key="th_status_hadir">Status Hadir</th>
                                <th data-key="th_aksi">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                            <tr>
                                <td style="font-family: monospace; font-size: 0.75rem;">{{ $order->order_number }}</td>
                                <td>
                                    <b>{{ $order->customer_name }}</b><br>
                                    <small style="color: var(--text-sub);">{{ $order->customer_email }}</small>
                                </td>
                                @if(!request('event_id')) <td>{{ $order->event->name }}</td> @endif
                                <td><small style="color: var(--accent);">{{ $order->ticketType->name ?? '-' }}</small></td>
                                <td>
                                    @php
                                        $qty = $order->quantity;
                                        if ($qty <= 2) { $cat = 'solo'; }
                                        elseif ($qty >= 3 && $qty <= 9) { $cat = 'group'; }
                                        else { $cat = 'bulk'; }
                                    @endphp
                                    <span class="qty-cat-badge {{ $cat }}">{{ $qty }} tiket</span>
                                </td>
                                <td>
                                    @if($order->scanned_at)
                                        <span class="badge-status status-hadir" data-key="badge_hadir">✅ HADIR</span>
                                        <div style="font-size: 0.65rem; color: var(--text-sub); margin-top: 5px;">
                                            <span data-key="lbl_jam">Jam:</span> {{ \Carbon\Carbon::parse($order->scanned_at)->format('H:i') }}<br>
                                            <span data-key="lbl_oleh">Oleh:</span> <span style="color: var(--accent);">{{ $order->scanner->name ?? 'System' }}</span>
                                        </div>
                                    @else
                                        <span class="badge-status status-belum" data-key="badge_belum">⏳ BELUM</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn-filter" style="font-size: 0.6rem; padding: 5px 10px;"
                                            onclick="openResendModal({{ $order->id }}, '{{ e($order->customer_email) }}')" data-key="btn_resend">
                                        RESEND
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ request('event_id') ? 6 : 7 }}" align="center" style="padding: 2rem; color: var(--text-sub);" data-key="empty_buyers">
                                    Belum ada pembeli tiket.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TAB: GUESTLIST --}}
            <div id="guestlist" class="tab-content">
                <div class="table-card">
                    <table>
                        <thead>
                            <tr>
                                <th data-key="th_guest_id">Guest ID</th>
                                <th data-key="th_nama_kat">Nama & Kategori</th>
                                @if(!request('event_id')) <th data-key="th_event2">Event</th> @endif
                                <th data-key="th_kontak">Kontak</th>
                                <th data-key="th_status2">Status</th>
                                <th data-key="th_aksi2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($guests as $guest)
                            <tr>
                                <td style="font-family: monospace; font-size: 0.75rem;">{{ $guest->order_number }}</td>
                                <td>
                                    <b>{{ $guest->customer_name }}</b><br>
                                    <span class="guest-cat-badge">{{ $guest->guestlistCategory->name ?? 'Undangan' }}</span>
                                </td>
                                @if(!request('event_id')) <td>{{ $guest->event->name ?? '-' }}</td> @endif
                                <td>{{ $guest->customer_email }}</td>
                                <td>
                                    @if($guest->scanned_at)
                                        <span class="badge-status status-hadir">✅ VOID</span>
                                    @else
                                        <span class="badge-status status-belum">⏳ READY</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn-filter" style="font-size: 0.6rem; padding: 5px 10px;"
                                            onclick="openResendModal({{ $guest->id }}, '{{ e($guest->customer_email) }}')" data-key="btn_resend2">
                                        RESEND
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="{{ request('event_id') ? 5 : 6 }}" align="center" style="padding: 2rem; color: var(--text-sub);" data-key="empty_guest">Belum ada tamu Guestlist.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TAB: STAFF --}}
            <div id="staff_list" class="tab-content">
                <div class="table-card" style="border-top: 4px solid #6366f1;">
                    <p style="color: var(--text-sub); font-size: 0.8rem; margin-bottom: 1rem;" data-key="staff_info">
                        💡 Password staff tidak ditampilkan di sini. Password hanya muncul sekali saat dibuat. Jika staff lupa password, hapus dan buat ulang akun.
                    </p>
                    <table>
                        <thead>
                            <tr>
                                <th data-key="th_nama_staff">Nama Staff</th>
                                <th data-key="th_email">Email / Username</th>
                                <th data-key="th_dibuat">Dibuat Pada</th>
                                <th data-key="th_status_staff">Status</th>
                                <th data-key="th_aksi_staff">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($staffs as $staff)
                            <tr>
                                <td style="font-weight: 800; color: #6366f1;">{{ $staff->name }}</td>
                                <td>{{ $staff->email }}</td>
                                <td>{{ $staff->created_at->format('d M Y') }}</td>
                                <td><span style="color: #1DB954; font-weight: 800; font-size: 0.7rem;" data-key="staff_active">🟢 AKTIF</span></td>
                                <td>
                                    <button type="button" class="btn-delete-staff"
                                            onclick="openDeleteStaffModal({{ $staff->id }}, '{{ e($staff->name) }}')">
                                        🗑️ <span data-key="btn_hapus">HAPUS</span>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" align="center" style="padding: 2rem; color: var(--text-sub);" data-key="empty_staff">Belum ada staff lapangan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL: TAMBAH STAFF --}}
    <div id="staffModal" class="overlay-modal">
        <div class="modal-form-box">
            <span class="close-modal" onclick="closeModal('staffModal')">&times;</span>
            <h2 style="color: var(--text-main); font-weight: 900; margin-bottom: 1rem; font-style: italic;" data-key="modal_staff_title">TAMBAH STAFF SCANNER</h2>
            <form method="POST" action="{{ route('promotor.staff.store') }}">
                @csrf
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 0.7rem; color: var(--text-sub); font-weight: 800;" data-key="modal_staff_nama">NAMA STAFF (GATE/BAGIAN)</label>
                    <input type="text" name="name" class="filter-input" placeholder="Contoh: Budi - Gate A" required style="width:100%;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 0.7rem; color: var(--text-sub); font-weight: 800;" data-key="modal_staff_email">EMAIL LOGIN</label>
                    <input type="email" name="email" class="filter-input" placeholder="staff@spectix.com" required style="width:100%;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 0.7rem; color: var(--text-sub); font-weight: 800;" data-key="modal_staff_pwd">PASSWORD</label>
                    <input type="password" name="password" class="filter-input" placeholder="Min 8 karakter" required minlength="8" style="width:100%;">
                    <div style="font-size: 0.65rem; color: var(--text-sub); margin-top: 5px;" data-key="modal_staff_pwd_hint">Wajib huruf besar, kecil, dan angka.</div>
                </div>
                <button type="submit" class="btn-filter" style="width: 100%; margin-top: 20px;" data-key="modal_staff_submit">BUAT AKUN STAFF</button>
            </form>
        </div>
    </div>

    {{-- MODAL: KATEGORI GUEST --}}
    <div id="catModal" class="overlay-modal">
        <div class="modal-form-box">
            <span class="close-modal" onclick="closeModal('catModal')">&times;</span>
            <h2 style="color: var(--text-main); font-weight: 900; margin-bottom: 1rem;" data-key="modal_cat_title">BUAT KATEGORI GUEST</h2>
            <form method="POST" action="{{ route('promotor.category.store') }}">
                @csrf
                <label style="font-size: 0.7rem; color: var(--text-sub);" data-key="modal_cat_event">PILIH EVENT</label>
                <select name="event_id" class="filter-input" required style="margin-bottom: 15px; width:100%;">
                    @foreach($events as $ev)
                        <option value="{{ $ev->id }}">{{ $ev->name }}</option>
                    @endforeach
                </select>
                <label style="font-size: 0.7rem; color: var(--text-sub);" data-key="modal_cat_nama">NAMA KATEGORI</label>
                <input type="text" name="category_name" class="filter-input" placeholder="Contoh: Media / Artist / Band" required maxlength="100" style="width:100%;">
                <button type="submit" class="btn-filter" style="width: 100%; margin-top: 20px;" data-key="modal_cat_submit">SIMPAN KATEGORI</button>
            </form>
        </div>
    </div>

    {{-- MODAL: GUESTLIST --}}
    <div id="guestModal" class="overlay-modal">
        <div class="modal-form-box">
            <span class="close-modal" onclick="closeModal('guestModal')">&times;</span>
            <h2 style="color: var(--text-main); font-weight: 900; margin-bottom: 1rem;" data-key="modal_guest_title">TAMBAH GUESTLIST</h2>
            <form method="POST" action="{{ route('promotor.guest') }}">
                @csrf
                <select name="event_id" id="guest_event" class="filter-input" required onchange="loadCats()" style="margin-bottom:15px; width:100%;">
                    <option value="" disabled selected data-key="opt_pick_event">-- Pilih Event --</option>
                    @foreach($events as $ev) <option value="{{ $ev->id }}">{{ $ev->name }}</option> @endforeach
                </select>
                <select name="guest_category_id" id="guest_cat" class="filter-input" required disabled style="margin-bottom:15px; width:100%;">
                    <option value="" data-key="opt_pick_cat">-- Pilih Kategori --</option>
                </select>
                <input type="text" name="name" class="filter-input" placeholder="Nama Tamu" required style="margin-bottom:15px; width:100%;" id="guest_name_input">
                <input type="email" name="email" class="filter-input" placeholder="Email Tamu" required style="margin-bottom:15px; width:100%;" id="guest_email_input">
                <input type="text" name="phone" class="filter-input" placeholder="Nomor WA" required style="width:100%;" id="guest_phone_input">
                <button type="submit" class="btn-filter" style="width: 100%; margin-top: 20px;" data-key="modal_guest_submit">KIRIM E-TIKET GRATIS</button>
            </form>
        </div>
    </div>

    {{-- MODAL: RESEND --}}
    <div id="resendModal" class="overlay-modal">
        <div class="modal-form-box">
            <span class="close-modal" onclick="closeModal('resendModal')">&times;</span>
            <h2 style="color: var(--text-main); font-weight: 900; margin-bottom: 1rem;" data-key="modal_resend_title">RESEND TIKET</h2>
            <form id="resendForm" method="POST" action="">
                @csrf
                <input type="email" name="new_email" id="resend_email" class="filter-input" required style="width:100%;">
                <button type="submit" class="btn-filter" style="width: 100%; margin-top: 20px;" data-key="modal_resend_submit">KIRIM ULANG</button>
            </form>
        </div>
    </div>

    {{-- MODAL: HAPUS STAFF (KONFIRMASI) --}}
    <div id="deleteStaffModal" class="overlay-modal">
        <div class="delete-modal-box">
            <span class="close-modal" onclick="closeModal('deleteStaffModal')">&times;</span>
            <h2 style="color: #ff6b6b; font-weight: 900; margin-bottom: 0; text-align: center;" data-key="del_title">⚠️ HAPUS STAFF?</h2>
            <p style="color: var(--text-sub); font-size: 0.85rem; text-align: center; margin: 1rem 0 0;" data-key="del_desc">
                Anda akan menghapus akun staff scanner berikut:
            </p>

            <div class="delete-staff-name" id="delete_staff_name">-</div>

            <div class="delete-warning">
                <strong data-key="del_warn_title">⚠️ Yang akan terjadi:</strong>
                <ul style="margin: 6px 0 0; padding-left: 18px; font-size: 0.8rem;">
                    <li data-key="del_warn1">Akun staff akan dihapus permanen.</li>
                    <li data-key="del_warn2">Staff tidak bisa login lagi.</li>
                    <li data-key="del_warn3">Riwayat scan tetap tersimpan (untuk audit).</li>
                    <li data-key="del_warn4">Untuk kasih akses lagi, buat akun staff baru.</li>
                </ul>
            </div>

            <form id="deleteStaffForm" method="POST" action="" style="margin-top: 1.5rem;">
                @csrf
                @method('DELETE')
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" class="btn-cancel" onclick="closeModal('deleteStaffModal')" data-key="del_cancel">
                        BATAL
                    </button>
                    <button type="submit" class="btn-confirm-delete" data-key="del_confirm">
                        🗑️ YA, HAPUS
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        const eventData = @json($events->map(fn($e) => ['id' => $e->id, 'cats' => $e->guestlistCategories]));

        function switchTab(evt, tabName) {
            const contents = document.getElementsByClassName("tab-content");
            for (let i = 0; i < contents.length; i++) contents[i].classList.remove("active");
            const links = document.getElementsByClassName("tab-link");
            for (let i = 0; i < links.length; i++) links[i].classList.remove("active");
            document.getElementById(tabName).classList.add("active");
            evt.currentTarget.classList.add("active");
        }

        function loadCats() {
            const evId = document.getElementById('guest_event').value;
            const catSelect = document.getElementById('guest_cat');
            catSelect.innerHTML = '<option value="">-- Pilih Kategori --</option>';
            const selected = eventData.find(e => e.id == evId);
            if (selected && selected.cats.length > 0) {
                selected.cats.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.id;
                    opt.textContent = c.name;
                    catSelect.appendChild(opt);
                });
                catSelect.disabled = false;
            } else {
                catSelect.innerHTML = '<option value="">(Buat kategori dulu!)</option>';
                catSelect.disabled = true;
            }
        }

        function openResendModal(id, email) {
            document.getElementById('resendForm').action = `/promotor/attendees/resend/${id}`;
            document.getElementById('resend_email').value = email;
            openModal('resendModal');
        }

        function openDeleteStaffModal(staffId, staffName) {
            document.getElementById('delete_staff_name').textContent = staffName;
            document.getElementById('deleteStaffForm').action = `/promotor/attendees/staff/${staffId}`;
            openModal('deleteStaffModal');
        }

        function copyToClipboard(text, btn) {
            navigator.clipboard.writeText(text).then(() => {
                const original = btn.textContent;
                btn.textContent = 'COPIED!';
                btn.style.background = '#1DB954';
                setTimeout(() => {
                    btn.textContent = original;
                    btn.style.background = '';
                }, 1500);
            });
        }

        // ============================================================
        // TRANSLATIONS untuk halaman Kelola Peserta
        // Pakai window.attendeesTranslations supaya bisa di-extend dari layout
        // ============================================================
        const attendeesTranslations = {
            id: {
                page_title: "KELOLA PESERTA",
                page_subtitle: "Kelola data pembeli tiket, tamu undangan, dan staff lapangan per event.",

                // Action bar
                lbl_tambah: "➕ Tambah",
                btn_staff_scanner: "👷 Staff Scanner",
                btn_kat_guest: "🏷️ Kategori Guest",
                btn_guestlist: "🎟️ Guestlist",
                btn_download: "Download Excel",
                export_info: "Multi-sheet · per event · per kategori",

                // Tabs
                tab_semua: "🌐 Semua Event",
                tab_buyers: "PEMBELI TIKET",
                tab_guestlist: "GUESTLIST",
                tab_scanner: "TIM SCANNER",

                // Stats
                stat_pembeli: "Total Pembeli",
                stat_tiket: "Total Tiket",
                stat_hadir: "Sudah Hadir",
                stat_belum: "Belum Hadir",
                stat_guestlist: "Guestlist",

                // Filter
                opt_all_kat: "📊 Semua Kategori Pembelian",
                opt_solo: "🟢 Solo (1 tiket)",
                opt_pair: "👥 Pasangan (2 tiket)",
                opt_small: "👨‍👩‍👧 Grup Kecil (3-5 tiket)",
                opt_large: "👥👥 Grup Besar (6-9 tiket)",
                opt_bulk: "📦 Bulk (10+ tiket)",
                btn_filter: "FILTER",
                reset_filter: "Reset filter",

                // Table headers
                th_order_id: "Order ID",
                th_nama: "Nama",
                th_event: "Event",
                th_tiket: "Tiket",
                th_jumlah: "Jumlah",
                th_status_hadir: "Status Hadir",
                th_aksi: "Aksi",
                th_guest_id: "Guest ID",
                th_nama_kat: "Nama & Kategori",
                th_event2: "Event",
                th_kontak: "Kontak",
                th_status2: "Status",
                th_aksi2: "Aksi",
                th_nama_staff: "Nama Staff",
                th_email: "Email / Username",
                th_dibuat: "Dibuat Pada",
                th_status_staff: "Status",
                th_aksi_staff: "Aksi",

                // Status badges
                badge_hadir: "✅ HADIR",
                badge_belum: "⏳ BELUM",
                lbl_jam: "Jam:",
                lbl_oleh: "Oleh:",
                btn_resend: "RESEND",
                btn_resend2: "RESEND",
                btn_hapus: "HAPUS",
                staff_active: "🟢 AKTIF",
                staff_info: "💡 Password staff tidak ditampilkan di sini. Password hanya muncul sekali saat dibuat. Jika staff lupa password, hapus dan buat ulang akun.",

                // Empty states
                empty_buyers: "Belum ada pembeli tiket.",
                empty_guest: "Belum ada tamu Guestlist.",
                empty_staff: "Belum ada staff lapangan.",

                // Modal — Staff
                modal_staff_title: "TAMBAH STAFF SCANNER",
                modal_staff_nama: "NAMA STAFF (GATE/BAGIAN)",
                modal_staff_email: "EMAIL LOGIN",
                modal_staff_pwd: "PASSWORD",
                modal_staff_pwd_hint: "Wajib huruf besar, kecil, dan angka.",
                modal_staff_submit: "BUAT AKUN STAFF",

                // Modal — Kategori
                modal_cat_title: "BUAT KATEGORI GUEST",
                modal_cat_event: "PILIH EVENT",
                modal_cat_nama: "NAMA KATEGORI",
                modal_cat_submit: "SIMPAN KATEGORI",

                // Modal — Guestlist
                modal_guest_title: "TAMBAH GUESTLIST",
                opt_pick_event: "-- Pilih Event --",
                opt_pick_cat: "-- Pilih Kategori --",
                modal_guest_submit: "KIRIM E-TIKET GRATIS",

                // Modal — Resend
                modal_resend_title: "RESEND TIKET",
                modal_resend_submit: "KIRIM ULANG",

                // Modal — Delete Staff
                del_title: "⚠️ HAPUS STAFF?",
                del_desc: "Anda akan menghapus akun staff scanner berikut:",
                del_warn_title: "⚠️ Yang akan terjadi:",
                del_warn1: "Akun staff akan dihapus permanen.",
                del_warn2: "Staff tidak bisa login lagi.",
                del_warn3: "Riwayat scan tetap tersimpan (untuk audit).",
                del_warn4: "Untuk kasih akses lagi, buat akun staff baru.",
                del_cancel: "BATAL",
                del_confirm: "🗑️ YA, HAPUS",

                // Password notice
                pwd_notice_title: "🔐 KREDENSIAL STAFF BARU — SIMPAN SEKARANG!",
                pwd_notice_desc: "Password di bawah ini hanya muncul SEKALI. Setelah halaman di-refresh, password tidak bisa dilihat lagi.",
                pwd_warning: "⚠️ Berikan kredensial ini ke staff melalui channel yang aman (WA pribadi). Jangan kirim lewat email plaintext.",
                lbl_name: "Nama:",
                lbl_email: "Email:",
                lbl_pwd: "Password:",
                btn_copy: "COPY",
                btn_copy2: "COPY",
            },
            en: {
                page_title: "MANAGE ATTENDEES",
                page_subtitle: "Manage ticket buyers, guestlist, and field staff per event.",

                lbl_tambah: "➕ Add",
                btn_staff_scanner: "👷 Scanner Staff",
                btn_kat_guest: "🏷️ Guest Category",
                btn_guestlist: "🎟️ Guestlist",
                btn_download: "Download Excel",
                export_info: "Multi-sheet · per event · per category",

                tab_semua: "🌐 All Events",
                tab_buyers: "TICKET BUYERS",
                tab_guestlist: "GUESTLIST",
                tab_scanner: "SCANNER TEAM",

                stat_pembeli: "Total Buyers",
                stat_tiket: "Total Tickets",
                stat_hadir: "Attended",
                stat_belum: "Not Yet",
                stat_guestlist: "Guestlist",

                opt_all_kat: "📊 All Purchase Categories",
                opt_solo: "🟢 Solo (1 ticket)",
                opt_pair: "👥 Pair (2 tickets)",
                opt_small: "👨‍👩‍👧 Small Group (3-5 tickets)",
                opt_large: "👥👥 Large Group (6-9 tickets)",
                opt_bulk: "📦 Bulk (10+ tickets)",
                btn_filter: "FILTER",
                reset_filter: "Reset filter",

                th_order_id: "Order ID",
                th_nama: "Name",
                th_event: "Event",
                th_tiket: "Ticket",
                th_jumlah: "Qty",
                th_status_hadir: "Attendance",
                th_aksi: "Action",
                th_guest_id: "Guest ID",
                th_nama_kat: "Name & Category",
                th_event2: "Event",
                th_kontak: "Contact",
                th_status2: "Status",
                th_aksi2: "Action",
                th_nama_staff: "Staff Name",
                th_email: "Email / Username",
                th_dibuat: "Created At",
                th_status_staff: "Status",
                th_aksi_staff: "Action",

                badge_hadir: "✅ PRESENT",
                badge_belum: "⏳ PENDING",
                lbl_jam: "Time:",
                lbl_oleh: "By:",
                btn_resend: "RESEND",
                btn_resend2: "RESEND",
                btn_hapus: "DELETE",
                staff_active: "🟢 ACTIVE",
                staff_info: "💡 Staff passwords are not shown here. Passwords only appear once when created. If staff forgets, delete and create new account.",

                empty_buyers: "No ticket buyers yet.",
                empty_guest: "No guestlist guests yet.",
                empty_staff: "No field staff yet.",

                modal_staff_title: "ADD SCANNER STAFF",
                modal_staff_nama: "STAFF NAME (GATE/SECTION)",
                modal_staff_email: "LOGIN EMAIL",
                modal_staff_pwd: "PASSWORD",
                modal_staff_pwd_hint: "Must contain uppercase, lowercase, and digit.",
                modal_staff_submit: "CREATE STAFF ACCOUNT",

                modal_cat_title: "CREATE GUEST CATEGORY",
                modal_cat_event: "SELECT EVENT",
                modal_cat_nama: "CATEGORY NAME",
                modal_cat_submit: "SAVE CATEGORY",

                modal_guest_title: "ADD GUESTLIST",
                opt_pick_event: "-- Select Event --",
                opt_pick_cat: "-- Select Category --",
                modal_guest_submit: "SEND FREE E-TICKET",

                modal_resend_title: "RESEND TICKET",
                modal_resend_submit: "SEND AGAIN",

                del_title: "⚠️ DELETE STAFF?",
                del_desc: "You are about to delete this scanner staff account:",
                del_warn_title: "⚠️ What will happen:",
                del_warn1: "Staff account will be permanently deleted.",
                del_warn2: "Staff can no longer log in.",
                del_warn3: "Scan history is preserved (for audit).",
                del_warn4: "To grant access again, create a new staff account.",
                del_cancel: "CANCEL",
                del_confirm: "🗑️ YES, DELETE",

                pwd_notice_title: "🔐 NEW STAFF CREDENTIALS — SAVE NOW!",
                pwd_notice_desc: "The password below is only shown ONCE. After refresh, the password cannot be seen again.",
                pwd_warning: "⚠️ Share these credentials via secure channel (personal WhatsApp). Do not send via plaintext email.",
                lbl_name: "Name:",
                lbl_email: "Email:",
                lbl_pwd: "Password:",
                btn_copy: "COPY",
                btn_copy2: "COPY",
            }
        };

        /**
         * Apply translations untuk halaman ini.
         * Dipanggil otomatis saat user klik tombol ID/EN di navbar (function setLang dari app.blade.php).
         *
         * Cara kerja: layout app.blade.php punya function setLang yang loop ke semua [data-key].
         * Kita override translations dictionary-nya supaya merge dengan attendeesTranslations.
         */
        function applyAttendeesTranslations(lang) {
            if (lang !== 'id' && lang !== 'en') lang = 'id';
            const dict = attendeesTranslations[lang];
            if (!dict) return;

            document.querySelectorAll('[data-key]').forEach(function(el) {
                const key = el.getAttribute('data-key');
                if (dict[key]) {
                    el.textContent = dict[key];
                }
            });

            // Update placeholder untuk input search & form
            const search = document.getElementById('search_input');
            if (search) {
                search.placeholder = lang === 'id'
                    ? '🔍 Cari nama / email / order ID...'
                    : '🔍 Search name / email / order ID...';
            }

            const guestName = document.getElementById('guest_name_input');
            const guestEmail = document.getElementById('guest_email_input');
            const guestPhone = document.getElementById('guest_phone_input');
            if (guestName) guestName.placeholder = lang === 'id' ? 'Nama Tamu' : 'Guest Name';
            if (guestEmail) guestEmail.placeholder = lang === 'id' ? 'Email Tamu' : 'Guest Email';
            if (guestPhone) guestPhone.placeholder = lang === 'id' ? 'Nomor WA' : 'WhatsApp Number';
        }

        // Hook ke fungsi setLang global (dari app.blade.php) — listen perubahan bahasa
        document.addEventListener('DOMContentLoaded', function() {
            // Initial apply berdasarkan saved lang
            let savedLang = 'id';
            try {
                const stored = localStorage.getItem('lang');
                if (stored === 'id' || stored === 'en') savedLang = stored;
            } catch (e) {}
            applyAttendeesTranslations(savedLang);

            // Override setLang dari app.blade.php supaya juga apply translation kita
            if (typeof window.setLang === 'function') {
                const originalSetLang = window.setLang;
                window.setLang = function(lang) {
                    originalSetLang(lang);
                    applyAttendeesTranslations(lang);
                };
            }
        });
    </script>
    @endpush
</x-app-layout>