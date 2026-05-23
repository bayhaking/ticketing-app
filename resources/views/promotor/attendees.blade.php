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

        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; }
        .filter-box { background: var(--bg-card); border: 1px solid var(--border); padding: 1.5rem; border-radius: 1.5rem; margin-bottom: 2rem; display: flex; gap: 15px; align-items: center; flex-wrap: wrap; }
        select.filter-input, input.filter-input { background: var(--bg-input); border: 1px solid var(--border); color: var(--text-main); padding: 10px 15px; border-radius: 10px; font-weight: bold; outline: none; }
        .filter-input { flex-grow: 1; min-width: 200px; }
        .btn-filter { background: var(--accent); color: #000; font-weight: 900; padding: 10px 20px; border-radius: 10px; cursor: pointer; border: none; text-transform: uppercase; transition: 0.3s; }
        .table-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 1.5rem; padding: 2rem; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; color: var(--accent); font-size: 0.75rem; text-transform: uppercase; padding: 1rem; border-bottom: 1px solid var(--border); }
        td { padding: 1rem; color: var(--text-main); font-weight: 600; border-bottom: 1px solid var(--border); font-size: 0.85rem; }
        .badge-status { padding: 4px 10px; border-radius: 8px; font-size: 0.7rem; font-weight: 800; }
        .status-hadir { background: rgba(29, 185, 84, 0.2); color: #1DB954; }
        .status-belum { background: rgba(255, 193, 7, 0.2); color: #ffc107; }
        .modal-form-box { background: var(--bg-card); border: 1px solid var(--border); border-top: 5px solid var(--accent); border-radius: 1.5rem; padding: 2.5rem; width: 90%; max-width: 500px; animation: zoom 0.3s; position: relative; }

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

        /* PASSWORD NOTICE — tampil sekali setelah create staff */
        .password-notice {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(168, 85, 247, 0.15));
            border: 2px solid #818cf8;
            border-radius: 1.5rem; padding: 2rem; margin-bottom: 2rem;
            position: relative;
        }
        .password-notice h3 { color: #818cf8; font-weight: 900; margin: 0 0 1rem; }
        .password-notice .credentials {
            background: var(--bg-input); border-radius: 12px; padding: 1rem;
            font-family: monospace; font-size: 1rem;
            display: flex; flex-direction: column; gap: 8px;
        }
        .password-notice .credentials > div { display: flex; gap: 10px; }
        .password-notice .credentials .label { color: var(--text-sub); min-width: 100px; }
        .password-notice .credentials .value { color: #fff; font-weight: 900; }
        .password-notice .warning {
            color: #ffc107; font-size: 0.85rem; margin-top: 1rem;
            font-weight: 700; line-height: 1.5;
        }
        .copy-btn {
            background: var(--accent); color: #000; border: none;
            padding: 6px 14px; border-radius: 8px; cursor: pointer;
            font-weight: 800; font-size: 0.75rem; margin-left: 10px;
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

            {{-- PASSWORD NOTICE: tampil sekali setelah create staff (TIDAK dari database) --}}
            @if(session('staff_password_notice'))
                @php $notice = session('staff_password_notice'); @endphp
                <div class="password-notice">
                    <h3>🔐 KREDENSIAL STAFF BARU — SIMPAN SEKARANG!</h3>
                    <p style="color: var(--text-sub); font-size: 0.9rem; margin: 0 0 1rem;">
                        Password di bawah ini <strong style="color: #ffc107;">hanya muncul SEKALI</strong>. Setelah halaman di-refresh, password tidak bisa dilihat lagi karena alasan keamanan.
                    </p>
                    <div class="credentials">
                        <div>
                            <span class="label">Nama:</span>
                            <span class="value">{{ $notice['name'] }}</span>
                        </div>
                        <div>
                            <span class="label">Email:</span>
                            <span class="value" id="staff_email">{{ $notice['email'] }}</span>
                            <button type="button" class="copy-btn" onclick="copyToClipboard('{{ $notice['email'] }}', this)">COPY</button>
                        </div>
                        <div>
                            <span class="label">Password:</span>
                            <span class="value" id="staff_password">{{ $notice['password'] }}</span>
                            <button type="button" class="copy-btn" onclick="copyToClipboard('{{ $notice['password'] }}', this)">COPY</button>
                        </div>
                    </div>
                    <div class="warning">
                        ⚠️ Berikan kredensial ini ke staff melalui channel yang aman (WA pribadi / SMS). Jangan kirim lewat email plaintext.
                    </div>
                </div>
            @endif

            <div class="page-header">
                <div>
                    <h1 style="font-size: 2.5rem; font-weight: 900; font-style: italic; color: var(--text-main);">KELOLA PESERTA</h1>
                    <p style="color: var(--text-sub);">Kelola data pembeli tiket, tamu undangan, dan staff lapangan per event.</p>
                </div>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    
                    <a href="{{ route('promotor.attendees.export', request()->only(['event_id'])) }}" style="background: var(--accent); color: #000; padding: 10px 20px; border-radius: 10px; font-weight: 900; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 0.85rem; transition: 0.3s; box-shadow: var(--glow);">
                        ⬇️ DOWNLOAD EXCEL (CSV)
                    </a>
                    
                    <button type="button" class="btn-filter" style="background: #4a5568; color: #fff;" onclick="openModal('staffModal')">+ STAFF SCANNER</button>
                    <button type="button" class="btn-filter" style="background: var(--bg-input); color: var(--text-main); border: 1px solid var(--border);" onclick="openModal('catModal')">+ KATEGORI GUEST</button>
                    <button type="button" class="btn-filter" onclick="openModal('guestModal')">+ GUESTLIST</button>
                </div>
            </div>

            {{-- TAB EVENT --}}
            <div class="event-tabs">
                <a href="{{ route('promotor.attendees') }}"
                   class="event-tab-link {{ !request('event_id') ? 'active' : '' }}">
                    🌐 Semua Event
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

            {{-- QUICK STATS (kalau filter event aktif) --}}
            @if(request('event_id') && isset($currentEvent))
                <div class="quick-stats">
                    <div class="qstat-card">
                        <div class="qstat-label">Total Pembeli</div>
                        <div class="qstat-value">{{ number_format($orders->count()) }}</div>
                    </div>
                    <div class="qstat-card">
                        <div class="qstat-label">Total Tiket</div>
                        <div class="qstat-value">{{ number_format($orders->sum('quantity')) }}</div>
                    </div>
                    <div class="qstat-card">
                        <div class="qstat-label">Sudah Hadir</div>
                        <div class="qstat-value" style="color: #1DB954;">{{ number_format($orders->whereNotNull('scanned_at')->count()) }}</div>
                    </div>
                    <div class="qstat-card">
                        <div class="qstat-label">Belum Hadir</div>
                        <div class="qstat-value" style="color: #ffc107;">{{ number_format($orders->whereNull('scanned_at')->count()) }}</div>
                    </div>
                    <div class="qstat-card">
                        <div class="qstat-label">Guestlist</div>
                        <div class="qstat-value" style="color: #f59e0b;">{{ number_format($guests->count()) }}</div>
                    </div>
                </div>
            @endif

            {{-- FILTER TAMBAHAN --}}
            <form class="filter-box" method="GET" action="{{ route('promotor.attendees') }}">
                @if(request('event_id'))
                    <input type="hidden" name="event_id" value="{{ request('event_id') }}">
                @endif

                <select name="qty_category" class="filter-input">
                    <option value="">📊 Semua Kategori Pembelian</option>
                    <option value="solo" {{ request('qty_category') == 'solo' ? 'selected' : '' }}>🟢 Solo (1 tiket)</option>
                    <option value="pair" {{ request('qty_category') == 'pair' ? 'selected' : '' }}>👥 Pasangan (2 tiket)</option>
                    <option value="small_group" {{ request('qty_category') == 'small_group' ? 'selected' : '' }}>👨‍👩‍👧 Grup Kecil (3-5 tiket)</option>
                    <option value="large_group" {{ request('qty_category') == 'large_group' ? 'selected' : '' }}>👥👥 Grup Besar (6-9 tiket)</option>
                    <option value="bulk" {{ request('qty_category') == 'bulk' ? 'selected' : '' }}>📦 Bulk (10+ tiket)</option>
                </select>

                <input type="text" name="search" class="filter-input" placeholder="🔍 Cari nama / email / order ID..." value="{{ request('search') }}" maxlength="100">

                <button type="submit" class="btn-filter">FILTER</button>

                @if(request()->hasAny(['qty_category', 'search']))
                    <a href="{{ route('promotor.attendees') }}{{ request('event_id') ? '?event_id='.request('event_id') : '' }}"
                       style="color: var(--text-sub); font-size: 0.75rem; text-decoration: underline;">
                        Reset filter
                    </a>
                @endif
            </form>

            {{-- TAB DALAM --}}
            <div class="tabs">
                <div class="tab-link active" onclick="switchTab(event, 'pembeli')">
                    TICKET BUYERS ({{ $orders->count() }})
                </div>
                <div class="tab-link" onclick="switchTab(event, 'guestlist')">
                    GUESTLIST ({{ $guests->count() }})
                </div>
                <div class="tab-link" onclick="switchTab(event, 'staff_list')" style="color: #6366f1;">
                    TEAM SCANNER ({{ $staffs->count() }})
                </div>
            </div>

            {{-- TAB: TICKET BUYERS --}}
            <div id="pembeli" class="tab-content active">
                <div class="table-card">
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Nama</th>
                                @if(!request('event_id'))
                                    <th>Event</th>
                                @endif
                                <th>Tiket</th>
                                <th>Jumlah</th>
                                <th>Status Hadir</th>
                                <th>Aksi</th>
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
                                @if(!request('event_id'))
                                    <td>{{ $order->event->name }}</td>
                                @endif
                                <td><small style="color: var(--accent);">{{ $order->ticketType->name ?? '-' }}</small></td>
                                <td>
                                    @php
                                        $qty = $order->quantity;
                                        if ($qty == 1) { $cat = 'solo'; $label = '1 tiket'; }
                                        elseif ($qty == 2) { $cat = 'solo'; $label = '2 tiket'; }
                                        elseif ($qty >= 3 && $qty <= 5) { $cat = 'group'; $label = $qty . ' tiket'; }
                                        elseif ($qty >= 6 && $qty <= 9) { $cat = 'group'; $label = $qty . ' tiket'; }
                                        else { $cat = 'bulk'; $label = $qty . ' tiket'; }
                                    @endphp
                                    <span class="qty-cat-badge {{ $cat }}">{{ $label }}</span>
                                </td>
                                <td>
                                    @if($order->scanned_at)
                                        <span class="badge-status status-hadir">✅ HADIR</span>
                                        <div style="font-size: 0.65rem; color: var(--text-sub); margin-top: 5px;">
                                            Jam: {{ \Carbon\Carbon::parse($order->scanned_at)->format('H:i') }}<br>
                                            Oleh: <span style="color: var(--accent);">{{ $order->scanner->name ?? 'System' }}</span>
                                        </div>
                                    @else
                                        <span class="badge-status status-belum">⏳ BELUM</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn-filter" style="font-size: 0.6rem; padding: 5px 10px;"
                                            onclick="openResendModal({{ $order->id }}, '{{ e($order->customer_email) }}')">
                                        RESEND
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ request('event_id') ? 6 : 7 }}" align="center" style="padding: 2rem; color: var(--text-sub);">
                                    Belum ada pembeli tiket
                                    @if(request('event_id')) untuk event ini.
                                    @elseif(request()->hasAny(['qty_category', 'search'])) dengan filter yang dipilih.
                                    @else .
                                    @endif
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
                                <th>Guest ID</th>
                                <th>Nama & Kategori</th>
                                @if(!request('event_id'))
                                    <th>Event</th>
                                @endif
                                <th>Kontak</th>
                                <th>Status</th>
                                <th>Aksi</th>
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
                                @if(!request('event_id'))
                                    <td>{{ $guest->event->name ?? '-' }}</td>
                                @endif
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
                                            onclick="openResendModal({{ $guest->id }}, '{{ e($guest->customer_email) }}')">
                                        RESEND
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ request('event_id') ? 5 : 6 }}" align="center" style="padding: 2rem; color: var(--text-sub);">
                                    Belum ada tamu Guestlist.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TAB: STAFF --}}
            <div id="staff_list" class="tab-content">
                <div class="table-card" style="border-top: 4px solid #6366f1;">
                    <p style="color: var(--text-sub); font-size: 0.8rem; margin-bottom: 1rem;">
                        💡 Password staff <strong>tidak ditampilkan</strong> di sini karena alasan keamanan. Password hanya muncul sekali saat dibuat. Jika lupa, hapus staff dan buat ulang.
                    </p>
                    <table>
                        <thead>
                            <tr>
                                <th>Nama Staff</th>
                                <th>Email / Username</th>
                                <th>Dibuat Pada</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($staffs as $staff)
                            <tr>
                                <td style="font-weight: 800; color: #6366f1;">{{ $staff->name }}</td>
                                <td>{{ $staff->email }}</td>
                                <td>{{ $staff->created_at->format('d M Y') }}</td>
                                <td><span style="color: #1DB954; font-weight: 800; font-size: 0.7rem;">🟢 AKTIF</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" align="center" style="padding: 2rem; color: var(--text-sub);">
                                    Belum ada staff lapangan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODALS --}}
    <div id="staffModal" class="overlay-modal">
        <div class="modal-form-box">
            <span class="close-modal" onclick="closeModal('staffModal')">&times;</span>
            <h2 style="color: var(--text-main); font-weight: 900; margin-bottom: 1rem; font-style: italic;">TAMBAH STAFF LAPANGAN</h2>
            <form method="POST" action="{{ route('promotor.staff.store') }}">
                @csrf
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 0.7rem; color: var(--text-sub); font-weight: 800;">NAMA STAFF (GATE/BAGIAN)</label>
                    <input type="text" name="name" class="filter-input" placeholder="Contoh: Budi - Gate A" required style="width:100%;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 0.7rem; color: var(--text-sub); font-weight: 800;">EMAIL LOGIN</label>
                    <input type="email" name="email" class="filter-input" placeholder="staff@spectix.com" required style="width:100%;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 0.7rem; color: var(--text-sub); font-weight: 800;">PASSWORD</label>
                    <input type="password" name="password" class="filter-input" placeholder="Min 8 karakter (huruf besar+kecil+angka)" required minlength="8" style="width:100%;">
                    <div style="font-size: 0.65rem; color: var(--text-sub); margin-top: 5px;">
                        Wajib mengandung huruf besar, huruf kecil, dan angka.
                    </div>
                </div>
                <button type="submit" class="btn-filter" style="width: 100%; margin-top: 20px;">BUAT AKUN STAFF</button>
            </form>
        </div>
    </div>

    <div id="catModal" class="overlay-modal">
        <div class="modal-form-box">
            <span class="close-modal" onclick="closeModal('catModal')">&times;</span>
            <h2 style="color: var(--text-main); font-weight: 900; margin-bottom: 1rem;">BUAT KATEGORI GUEST</h2>
            <form method="POST" action="{{ route('promotor.category.store') }}">
                @csrf
                <label style="font-size: 0.7rem; color: var(--text-sub);">PILIH EVENT</label>
                <select name="event_id" class="filter-input" required style="margin-bottom: 15px; width:100%;">
                    @foreach($events as $ev)
                        <option value="{{ $ev->id }}">{{ $ev->name }}</option>
                    @endforeach
                </select>
                <label style="font-size: 0.7rem; color: var(--text-sub);">NAMA KATEGORI</label>
                <input type="text" name="category_name" class="filter-input" placeholder="Contoh: Media / Artist / Band" required maxlength="100" style="width:100%;">
                <button type="submit" class="btn-filter" style="width: 100%; margin-top: 20px;">SIMPAN KATEGORI</button>
            </form>
        </div>
    </div>

    <div id="guestModal" class="overlay-modal">
        <div class="modal-form-box">
            <span class="close-modal" onclick="closeModal('guestModal')">&times;</span>
            <h2 style="color: var(--text-main); font-weight: 900; margin-bottom: 1rem;">TAMBAH GUESTLIST</h2>
            <form method="POST" action="{{ route('promotor.guest') }}">
                @csrf
                <select name="event_id" id="guest_event" class="filter-input" required onchange="loadCats()" style="margin-bottom:15px; width:100%;">
                    <option value="" disabled selected>-- Pilih Event --</option>
                    @foreach($events as $ev)
                        <option value="{{ $ev->id }}">{{ $ev->name }}</option>
                    @endforeach
                </select>
                <select name="guest_category_id" id="guest_cat" class="filter-input" required disabled style="margin-bottom:15px; width:100%;">
                    <option value="">-- Pilih Kategori --</option>
                </select>
                <input type="text" name="name" class="filter-input" placeholder="Nama Tamu" required style="margin-bottom:15px; width:100%;">
                <input type="email" name="email" class="filter-input" placeholder="Email Tamu" required style="margin-bottom:15px; width:100%;">
                <input type="text" name="phone" class="filter-input" placeholder="Nomor WA" required style="width:100%;">
                <button type="submit" class="btn-filter" style="width: 100%; margin-top: 20px;">KIRIM E-TIKET GRATIS</button>
            </form>
        </div>
    </div>

    <div id="resendModal" class="overlay-modal">
        <div class="modal-form-box">
            <span class="close-modal" onclick="closeModal('resendModal')">&times;</span>
            <h2 style="color: var(--text-main); font-weight: 900; margin-bottom: 1rem;">RESEND TIKET</h2>
            <form id="resendForm" method="POST" action="">
                @csrf
                <input type="email" name="new_email" id="resend_email" class="filter-input" required style="width:100%;">
                <button type="submit" class="btn-filter" style="width: 100%; margin-top: 20px;">KIRIM ULANG</button>
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
    </script>
    @endpush
</x-app-layout>