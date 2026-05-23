<x-app-layout>
    <style>
        /* TAB STYLING DENGAN EFEK HALUS */
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
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .filter-box { background: var(--bg-card); border: 1px solid var(--border); padding: 1.5rem; border-radius: 1.5rem; margin-bottom: 2rem; display: flex; gap: 15px; align-items: center; }
        select.filter-input, input.filter-input { background: var(--bg-input); border: 1px solid var(--border); color: var(--text-main); padding: 10px 15px; border-radius: 10px; font-weight: bold; flex-grow: 1; outline: none; width: 100%; }
        .btn-filter { background: var(--accent); color: #000; font-weight: 900; padding: 10px 20px; border-radius: 10px; cursor: pointer; border: none; text-transform: uppercase; transition: 0.3s; }
        .table-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 1.5rem; padding: 2rem; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; color: var(--accent); font-size: 0.75rem; text-transform: uppercase; padding: 1rem; border-bottom: 1px solid var(--border); }
        td { padding: 1rem; color: var(--text-main); font-weight: 600; border-bottom: 1px solid var(--border); font-size: 0.85rem; }
        .badge-status { padding: 4px 10px; border-radius: 8px; font-size: 0.7rem; font-weight: 800; }
        .status-hadir { background: rgba(29, 185, 84, 0.2); color: #1DB954; }
        .status-belum { background: rgba(255, 193, 7, 0.2); color: #ffc107; }
        .modal-form-box { background: var(--bg-card); border: 1px solid var(--border); border-top: 5px solid var(--accent); border-radius: 1.5rem; padding: 2.5rem; width: 90%; max-width: 500px; animation: zoom 0.3s; position: relative; }
    </style>

    <div style="padding: 3rem;">
        <div class="max-w-7xl mx-auto">
            
            @if(session('success'))
                <div style="background: rgba(29, 185, 84, 0.2); border: 1px solid #1DB954; color: #1DB954; padding: 1rem; border-radius: 10px; font-weight: 800; margin-bottom: 2rem;">✅ {{ session('success') }}</div>
            @endif

            <div class="page-header">
                <div>
                    <h1 style="font-size: 2.5rem; font-weight: 900; font-style: italic; color: var(--text-main);">MANAGE ATTENDEES</h1>
                    <p style="color: var(--text-sub);">Kelola data pembeli tiket, tamu undangan, dan staff lapangan.</p>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button class="btn-filter" style="background: #4a5568; color: #fff;" onclick="openModal('staffModal')">+ TAMBAH STAFF SCANNER</button>
                    <button class="btn-filter" style="background: var(--bg-input); color: var(--text-main); border: 1px solid var(--border);" onclick="openModal('catModal')">+ BUAT KATEGORI GUEST</button>
                    <button class="btn-filter" onclick="openModal('guestModal')">+ GUESTLIST BARU</button>
                </div>
            </div>

            <form class="filter-box" method="GET" action="{{ route('promotor.attendees') }}">
                <select name="event_id" class="filter-input">
                    <option value="">-- Tampilkan Semua Event --</option>
                    @foreach($events as $ev)
                        <option value="{{ $ev->id }}" {{ request('event_id') == $ev->id ? 'selected' : '' }}>{{ $ev->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-filter">FILTER</button>
            </form>

            <div class="tabs">
                <div class="tab-link active" onclick="switchTab(event, 'pembeli')">TICKET BUYERS ({{ $orders->count() }})</div>
                <div class="tab-link" onclick="switchTab(event, 'guestlist')">GUESTLIST ({{ $guests->count() }})</div>
                <div class="tab-link" onclick="switchTab(event, 'staff_list')" style="color: #6366f1;">TEAM SCANNER ({{ $staffs->count() }})</div>
            </div>

            <div id="pembeli" class="tab-content active">
                <div class="table-card">
                    <table>
                        <thead>
                            <tr><th>Order ID</th><th>Nama</th><th>Event & Tiket</th><th>Status Hadir</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                            <tr>
                                <td style="font-family: monospace;">{{ $order->order_number }}</td>
                                <td>{{ $order->customer_name }}</td>
                                <td>{{ $order->event->name }} <br> <small style="color: var(--accent);">{{ $order->ticketType->name }}</small></td>
                                <td>
                                    @if($order->scanned_at)
                                        <span class="badge-status status-hadir">✅ HADIR</span>
                                        <div style="font-size: 0.65rem; color: var(--text-sub); margin-top: 5px;">
                                            Jam: {{ \Carbon\Carbon::parse($order->scanned_at)->format('H:i') }} <br>
                                            Oleh: <span style="color: var(--accent);">{{ $order->scanner->name ?? 'System' }}</span>
                                        </div>
                                    @else
                                        <span class="badge-status status-belum">⏳ BELUM</span>
                                    @endif
                                </td>
                                <td><button class="btn-filter" style="font-size: 0.6rem; padding: 5px 10px;" onclick="openResendModal({{ $order->id }}, '{{ $order->customer_email }}')">RESEND</button></td>
                            </tr>
                            @empty
                            <tr><td colspan="5" align="center">Belum ada pembeli tiket.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="guestlist" class="tab-content">
                <div class="table-card">
                    <table>
                        <thead>
                            <tr><th>Guest ID</th><th>Nama & Kategori</th><th>Kontak</th><th>Status</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                            @forelse($guests as $guest)
                            <tr>
                                <td style="font-family: monospace;">{{ $guest->order_number }}</td>
                                <td>
                                    <b>{{ $guest->customer_name }}</b> <br>
                                    <span class="guest-cat-badge">{{ $guest->guestlistCategory->name ?? 'Undangan' }}</span>
                                </td>
                                <td>{{ $guest->customer_email }}</td>
                                <td>
                                    @if($guest->scanned_at)
                                        <span class="badge-status status-hadir">✅ VOID</span>
                                    @else
                                        <span class="badge-status status-belum">⏳ READY</span>
                                    @endif
                                </td>
                                <td><button class="btn-filter" style="font-size: 0.6rem; padding: 5px 10px;" onclick="openResendModal({{ $guest->id }}, '{{ $guest->customer_email }}')">RESEND</button></td>
                            </tr>
                            @empty
                            <tr><td colspan="5" align="center">Belum ada tamu Guestlist.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="staff_list" class="tab-content">
                <div class="table-card" style="border-top-color: #6366f1;">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama Staff</th>
                                <th>Email / Username</th>
                                <th>Password Login</th>
                                <th>Dibuat Pada</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($staffs as $staff)
                            <tr>
                                <td style="font-weight: 800; color: #6366f1;">{{ $staff->name }}</td>
                                <td>{{ $staff->email }}</td>
                                <td style="font-family: monospace; background: var(--bg-input); padding: 5px 10px; border-radius: 5px; font-size: 0.75rem;">
                                    {{ $staff->plain_password ?? '******' }}
                                </td>
                                <td>{{ $staff->created_at->format('d M Y') }}</td>
                                <td><span style="color: #1DB954; font-weight: 800; font-size: 0.7rem;">🟢 AKTIF</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="5" align="center">Belum ada staff lapangan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="staffModal" class="overlay-modal">
        <div class="modal-form-box">
            <span class="close-modal" onclick="closeModal('staffModal')">&times;</span>
            <h2 style="color: var(--text-main); font-weight: 900; margin-bottom: 1rem; font-style: italic;">TAMBAH STAFF LAPANGAN</h2>
            <form method="POST" action="{{ route('promotor.staff.store') }}">
                @csrf
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 0.7rem; color: var(--text-sub); font-weight: 800;">NAMA STAFF (GATE/BAGIAN)</label>
                    <input type="text" name="name" class="filter-input" placeholder="Contoh: Budi - Gate A" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 0.7rem; color: var(--text-sub); font-weight: 800;">EMAIL LOGIN</label>
                    <input type="email" name="email" class="filter-input" placeholder="staff@spectix.com" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 0.7rem; color: var(--text-sub); font-weight: 800;">PASSWORD</label>
                    <input type="password" name="password" class="filter-input" placeholder="Min 6 Karakter" required>
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
                <select name="event_id" class="filter-input" required style="margin-bottom: 15px;">
                    @foreach($events as $ev) <option value="{{ $ev->id }}">{{ $ev->name }}</option> @endforeach
                </select>
                <label style="font-size: 0.7rem; color: var(--text-sub);">NAMA KATEGORI</label>
                <input type="text" name="category_name" class="filter-input" placeholder="Contoh: Media / Artist / Band" required>
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
                <select name="event_id" id="guest_event" class="filter-input" required onchange="loadCats()" style="margin-bottom:15px;">
                    <option value="" disabled selected>-- Pilih Event --</option>
                    @foreach($events as $ev) <option value="{{ $ev->id }}">{{ $ev->name }}</option> @endforeach
                </select>
                <select name="guest_category_id" id="guest_cat" class="filter-input" required disabled style="margin-bottom:15px;">
                    <option value="">-- Pilih Kategori --</option>
                </select>
                <input type="text" name="name" class="filter-input" placeholder="Nama Tamu" required style="margin-bottom:15px;">
                <input type="email" name="email" class="filter-input" placeholder="Email Tamu" required style="margin-bottom:15px;">
                <input type="text" name="phone" class="filter-input" placeholder="Nomor WA" required>
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
                <input type="email" name="new_email" id="resend_email" class="filter-input" required>
                <button type="submit" class="btn-filter" style="width: 100%; margin-top: 20px;">KIRIM ULANG</button>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // DATA EVENT UNTUK DROPDOWN DINAMIS
        const eventData = @json($events->map(fn($e) => ['id' => $e->id, 'cats' => $e->guestlistCategories]));

        // FUNGSI PINDAH TAB DENGAN EFEK HALUS
        function switchTab(evt, tabName) {
            // Sembunyikan semua tab content
            const contents = document.getElementsByClassName("tab-content");
            for (let i = 0; i < contents.length; i++) {
                contents[i].classList.remove("active");
            }

            // Matikan semua tab link active
            const links = document.getElementsByClassName("tab-link");
            for (let i = 0; i < links.length; i++) {
                links[i].classList.remove("active");
            }

            // Tampilkan tab yang dipilih
            document.getElementById(tabName).classList.add("active");
            evt.currentTarget.classList.add("active");
        }

        function loadCats() {
            const evId = document.getElementById('guest_event').value;
            const catSelect = document.getElementById('guest_cat');
            catSelect.innerHTML = '<option value="">-- Pilih Kategori --</option>';
            const selected = eventData.find(e => e.id == evId);
            if(selected && selected.cats.length > 0) {
                selected.cats.forEach(c => catSelect.innerHTML += `<option value="${c.id}">${c.name}</option>`);
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
    </script>
    @endpush
</x-app-layout>