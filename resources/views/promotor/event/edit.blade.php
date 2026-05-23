<x-app-layout>
    <style>
        .form-container { background: var(--bg-card); border: 1px solid var(--border); border-radius: 1.5rem; padding: 2.5rem; margin-top: 2rem; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 800; color: var(--text-sub); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-control { width: 100%; background: var(--bg-input); border: 1px solid var(--border); color: var(--text-main); padding: 12px 16px; border-radius: 10px; font-weight: 600; outline: none; transition: 0.3s; }
        .form-control:focus { border-color: var(--accent); box-shadow: var(--glow); }
        .btn-save { background: var(--accent); color: #fff; border: none; padding: 14px 28px; border-radius: 10px; font-weight: 900; font-style: italic; cursor: pointer; transition: 0.3s; width: 100%; font-size: 1rem; box-shadow: var(--glow); margin-top: 2rem; }
        .btn-save:hover { transform: translateY(-2px); opacity: 0.9; }
        .preview-banner-box { width: 100%; height: 200px; border-radius: 12px; overflow: hidden; border: 1px dashed var(--border); margin-top: 10px; background-size: cover; background-position: center; display: flex; align-items: center; justify-content: center; color: var(--text-sub); font-weight: 700; font-size: 0.8rem; }
        
        /* Dynamic Section */
        .dynamic-section { border: 1px solid var(--border); padding: 1.5rem; border-radius: 1rem; margin-bottom: 1.5rem; background: rgba(255,255,255,0.02); }
        .dynamic-row { display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 15px; margin-bottom: 15px; align-items: end; }
        .btn-add { background: transparent; border: 1px dashed var(--accent); color: var(--accent); padding: 10px 20px; border-radius: 8px; font-weight: 800; cursor: pointer; width: 100%; transition: 0.3s; }
        .btn-add:hover { background: rgba(29, 185, 84, 0.1); }
        .btn-remove { background: rgba(255, 68, 68, 0.1); border: 1px solid #ff4444; color: #ff4444; padding: 12px; border-radius: 10px; cursor: pointer; font-weight: 900; transition: 0.3s; }
        .btn-remove:hover { background: #ff4444; color: #fff; }
        
        /* Toggle Switch iOS Style */
        .switch { position: relative; display: inline-block; width: 50px; height: 26px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #333; transition: .4s; border-radius: 34px; border: 1px solid var(--border); }
        .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 4px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
        input:checked + .slider { background-color: var(--accent); border-color: var(--accent); box-shadow: var(--glow); }
        input:checked + .slider:before { transform: translateX(22px); }

        @media(max-width: 768px) {
            .dynamic-row { grid-template-columns: 1fr; }
        }
    </style>

    <div style="padding: 3rem; background-color: var(--bg-main); min-height: 100vh;">
        <div class="max-w-4xl mx-auto">
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <div>
                    <h1 style="font-size: 2.5rem; font-weight: 900; font-style: italic; color: #ffffff; margin: 0;" data-key="page_title">🛠️ EDIT SPECTACLE</h1>
                    <p style="color: var(--text-sub); margin-top: 5px;" data-key="page_desc">Perbarui data, tambah tiket, atau ubah lineup acaramu.</p>
                </div>
                <a href="/promotor/dashboard" style="color: var(--text-sub); text-decoration: none; font-weight: 800; font-size: 0.85rem; border: 1px solid var(--border); padding: 10px 20px; border-radius: 100px;" data-key="btn_back">KEMBALI</a>
            </div>

            @if ($errors->any())
                <div style="background: rgba(255, 68, 68, 0.1); border: 1px solid #ff4444; color: #ff4444; padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem;">
                    <ul style="margin: 0; padding-left: 20px; font-weight: 700; font-size: 0.85rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-container">
                <form action="{{ route('promotor.event.update', $event->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <h3 style="color: var(--accent); font-weight: 900; margin-bottom: 1rem; border-bottom: 1px solid var(--border); padding-bottom: 10px;" data-key="sec_info">1. INFORMASI DASAR</h3>
                    <div class="form-group">
                        <label data-key="lbl_name">Nama Event</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $event->name) }}" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label data-key="lbl_category">Kategori</label>
                            <select name="category" class="form-control" style="cursor: pointer;" required>
                                <option value="Musik" {{ old('category', $event->category) == 'Musik' ? 'selected' : '' }} data-key="opt_music">MUSIK</option>
                                <option value="Olahraga" {{ old('category', $event->category) == 'Olahraga' ? 'selected' : '' }} data-key="opt_sport">OLAHRAGA</option>
                                <option value="Seminar" {{ old('category', $event->category) == 'Seminar' ? 'selected' : '' }} data-key="opt_seminar">SEMINAR</option>
                                <option value="Hiburan" {{ old('category', $event->category) == 'Hiburan' ? 'selected' : '' }} data-key="opt_entertainment">HIBURAN</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label data-key="lbl_type">Tipe Event</label>
                            <select name="type" class="form-control" style="cursor: pointer;" required>
                                <option value="Publik" {{ old('type', $event->type) == 'Publik' ? 'selected' : '' }} data-key="opt_public">PUBLIK (Bisa dibeli siapa saja)</option>
                                <option value="Privat" {{ old('type', $event->type) == 'Privat' ? 'selected' : '' }} data-key="opt_private">PRIVAT (Hanya undangan/link khusus)</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label data-key="lbl_date">Tanggal Pelaksanaan</label>
                            <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d', strtotime($event->date))) }}" required>
                        </div>
                        <div class="form-group">
                            <label data-key="lbl_sales">Tanggal Tiket Dijual (Opsional / Kosongkan untuk Reset)</label>
                            <input type="datetime-local" name="sales_start_date" class="form-control" value="{{ old('sales_start_date', $event->sales_start_date ? date('Y-m-d\TH:i', strtotime($event->sales_start_date)) : '') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label data-key="lbl_banner">Banner Event (Kosongkan jika tidak ingin diubah)</label>
                        <input type="file" name="banner" id="banner-input" class="form-control" accept="image/*">
                        <div class="preview-banner-box" id="preview-box" style="background-image: url('{{ $event->banner ? asset('storage/'.$event->banner) : 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?q=80' }}')"></div>
                    </div>

                    <div class="form-group">
                        <label data-key="lbl_desc">Deskripsi Acara</label>
                        <textarea name="description" class="form-control" rows="4">{{ old('description', $event->description) }}</textarea>
                    </div>

                    <h3 style="color: var(--accent); font-weight: 900; margin: 2rem 0 1rem 0; border-bottom: 1px solid var(--border); padding-bottom: 10px;" data-key="sec_ticket">2. KATEGORI TIKET</h3>
                    <div class="dynamic-section" id="ticket-section">
                        @foreach($event->ticketTypes as $ticket)
                        <div class="dynamic-row">
                            <input type="hidden" name="t_ids[]" value="{{ $ticket->id }}">
                            <div>
                                <label style="font-size: 0.7rem; font-weight: 800; color: var(--text-sub); margin-bottom: 5px; display: block;" data-key="lbl_t_name">Nama Tiket</label>
                                <input type="text" name="t_names[]" class="form-control" value="{{ $ticket->name }}" required>
                            </div>
                            <div>
                                <label style="font-size: 0.7rem; font-weight: 800; color: var(--text-sub); margin-bottom: 5px; display: block;" data-key="lbl_t_price">Harga (Rp)</label>
                                <input type="text" name="t_prices[]" class="form-control rupiah-input" value="{{ number_format($ticket->price, 0, '', '.') }}" required>
                            </div>
                            <div>
                                <label style="font-size: 0.7rem; font-weight: 800; color: var(--text-sub); margin-bottom: 5px; display: block;" data-key="lbl_t_stock">Stok</label>
                                <input type="number" name="t_stocks[]" class="form-control" value="{{ $ticket->stock }}" required>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn-add" onclick="addTicketRow()" data-key="btn_add_ticket">+ TAMBAH KATEGORI TIKET BARU</button>

                    <h3 style="color: var(--accent); font-weight: 900; margin: 2rem 0 1rem 0; border-bottom: 1px solid var(--border); padding-bottom: 10px;" data-key="sec_lineup">3. LINEUP / GUEST STAR</h3>
                    <div class="dynamic-section" id="lineup-section">
                        @forelse($event->lineups as $lineup)
                        <div class="dynamic-row">
                            <div>
                                <label style="font-size: 0.7rem; font-weight: 800; color: var(--text-sub); margin-bottom: 5px; display: block;" data-key="lbl_l_name">Nama Lineup</label>
                                <input type="text" name="lineup_name[]" class="form-control" value="{{ $lineup->name }}">
                            </div>
                            <div>
                                <label style="font-size: 0.7rem; font-weight: 800; color: var(--text-sub); margin-bottom: 5px; display: block;" data-key="lbl_l_ig">Link Instagram</label>
                                <input type="text" name="lineup_ig[]" class="form-control" value="{{ $lineup->ig }}">
                            </div>
                            <div>
                                <label style="font-size: 0.7rem; font-weight: 800; color: var(--text-sub); margin-bottom: 5px; display: block;" data-key="lbl_l_spotify">Link Spotify</label>
                                <input type="text" name="lineup_spotify[]" class="form-control" value="{{ $lineup->spotify }}">
                            </div>
                            <button type="button" class="btn-remove" onclick="this.parentElement.remove()">X</button>
                        </div>
                        @empty
                        @endforelse
                    </div>
                    <button type="button" class="btn-add" onclick="addLineupRow()" data-key="btn_add_lineup">+ TAMBAH LINEUP BARU</button>

                    <h3 style="color: var(--accent); font-weight: 900; margin: 2rem 0 1rem 0; border-bottom: 1px solid var(--border); padding-bottom: 10px;" data-key="sec_voucher">4. PENGATURAN VOUCHER</h3>
                    <div class="form-group" style="background: rgba(29, 185, 84, 0.05); padding: 15px; border-radius: 10px; border: 1px solid var(--accent); display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <label style="margin-bottom: 2px;" data-key="lbl_voucher_toggle">Aktivasi Voucher Promosi</label>
                            <span style="font-size: 0.7rem; color: var(--text-sub);" data-key="desc_voucher_toggle">Izinkan pembeli menggunakan kode voucher diskon pada event ini.</span>
                        </div>
                        <label class="switch">
                            <input type="checkbox" name="is_voucher_active" value="1" {{ old('is_voucher_active', $event->is_voucher_active ?? false) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>

                    <button type="submit" class="btn-save" data-key="btn_submit">💾 PERBARUI SELURUH DATA</button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Preview Image
        document.getElementById('banner-input').onchange = function (evt) {
            const [file] = this.files;
            if (file) {
                document.getElementById('preview-box').style.backgroundImage = `url('${URL.createObjectURL(file)}')`;
            }
        }

        // Add Ticket Row
        function addTicketRow() {
            const wrapper = document.getElementById('ticket-section');
            const row = document.createElement('div');
            row.className = 'dynamic-row';
            row.innerHTML = `
                <input type="hidden" name="t_ids[]" value="">
                <div><input type="text" name="t_names[]" class="form-control" placeholder="VIP / Reguler" required></div>
                <div><input type="text" name="t_prices[]" class="form-control rupiah-input" placeholder="Harga" required></div>
                <div><input type="number" name="t_stocks[]" class="form-control" placeholder="Jumlah" required></div>
                <button type="button" class="btn-remove" onclick="this.parentElement.remove()">X</button>
            `;
            wrapper.appendChild(row);
            attachRupiahListener(row.querySelector('.rupiah-input'));
        }

        // Add Lineup Row
        function addLineupRow() {
            const wrapper = document.getElementById('lineup-section');
            const row = document.createElement('div');
            row.className = 'dynamic-row';
            row.innerHTML = `
                <div><input type="text" name="lineup_name[]" class="form-control" placeholder="Nama Artis"></div>
                <div><input type="text" name="lineup_ig[]" class="form-control" placeholder="Link IG"></div>
                <div><input type="text" name="lineup_spotify[]" class="form-control" placeholder="Link Spotify"></div>
                <button type="button" class="btn-remove" onclick="this.parentElement.remove()">X</button>
            `;
            wrapper.appendChild(row);
        }

        // Format Rupiah
        function formatRupiah(angka) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split   = number_string.split(','),
            sisa    = split[0].length % 3,
            rupiah  = split[0].substr(0, sisa),
            ribuan  = split[0].substr(sisa).match(/\d{3}/gi);
            if(ribuan){
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            return rupiah;
        }

        function attachRupiahListener(el) {
            el.addEventListener('keyup', function(e){
                this.value = formatRupiah(this.value);
            });
        }
        document.querySelectorAll('.rupiah-input').forEach(el => attachRupiahListener(el));

        // TRANSLATION ENGINE (Realtime)
        const translationsEdit = {
            id: { 
                page_title: "🛠️ EDIT SPECTACLE", page_desc: "Perbarui data, tambah tiket, atau ubah lineup acaramu.", btn_back: "KEMBALI",
                sec_info: "1. INFORMASI DASAR", lbl_name: "Nama Event", lbl_category: "Kategori", lbl_type: "Tipe Event",
                opt_music: "MUSIK", opt_sport: "OLAHRAGA", opt_seminar: "SEMINAR", opt_entertainment: "HIBURAN",
                opt_public: "PUBLIK (Bisa dibeli siapa saja)", opt_private: "PRIVAT (Hanya undangan/link khusus)",
                lbl_date: "Tanggal Pelaksanaan", lbl_sales: "Tanggal Tiket Dijual (Opsional / Kosongkan untuk Reset)",
                lbl_banner: "Banner Event (Kosongkan jika tidak ingin diubah)", lbl_desc: "Deskripsi Acara",
                sec_ticket: "2. KATEGORI TIKET", lbl_t_name: "Nama Tiket", lbl_t_price: "Harga (Rp)", lbl_t_stock: "Stok", btn_add_ticket: "+ TAMBAH KATEGORI TIKET BARU",
                sec_lineup: "3. LINEUP / GUEST STAR", lbl_l_name: "Nama Lineup", lbl_l_ig: "Link Instagram", lbl_l_spotify: "Link Spotify", btn_add_lineup: "+ TAMBAH LINEUP BARU",
                sec_voucher: "4. PENGATURAN VOUCHER", lbl_voucher_toggle: "Aktivasi Voucher Promosi", desc_voucher_toggle: "Izinkan pembeli menggunakan kode voucher diskon pada event ini.",
                btn_submit: "💾 PERBARUI SELURUH DATA"
            },
            en: { 
                page_title: "🛠️ EDIT SPECTACLE", page_desc: "Update details, add tickets, or modify your lineup.", btn_back: "GO BACK",
                sec_info: "1. BASIC INFORMATION", lbl_name: "Event Name", lbl_category: "Category", lbl_type: "Event Type",
                opt_music: "MUSIC", opt_sport: "SPORTS", opt_seminar: "SEMINAR", opt_entertainment: "ENTERTAINMENT",
                opt_public: "PUBLIC (Open for everyone)", opt_private: "PRIVATE (Invite/Special link only)",
                lbl_date: "Event Date", lbl_sales: "Ticket Sales Start (Optional / Clear to reset)",
                lbl_banner: "Event Banner (Leave blank if unchanged)", lbl_desc: "Event Description",
                sec_ticket: "2. TICKET CATEGORIES", lbl_t_name: "Ticket Name", lbl_t_price: "Price (Rp)", lbl_t_stock: "Stock", btn_add_ticket: "+ ADD NEW TICKET CATEGORY",
                sec_lineup: "3. LINEUP / GUEST STAR", lbl_l_name: "Lineup Name", lbl_l_ig: "Instagram Link", lbl_l_spotify: "Spotify Link", btn_add_lineup: "+ ADD NEW LINEUP",
                sec_voucher: "4. VOUCHER SETTINGS", lbl_voucher_toggle: "Enable Promo Vouchers", desc_voucher_toggle: "Allow buyers to use discount voucher codes for this event.",
                btn_submit: "💾 UPDATE ALL DATA"
            }
        };

        function setLang(lang) {
            document.querySelectorAll('[data-key]').forEach(el => {
                const key = el.getAttribute('data-key');
                if (translationsEdit[lang] && translationsEdit[lang][key]) {
                    if(el.tagName === 'INPUT') { el.placeholder = translationsEdit[lang][key]; }
                    else { el.innerText = translationsEdit[lang][key]; }
                }
            });
        }

        // Listen for language change from Navbar
        document.addEventListener('DOMContentLoaded', () => {
            const savedLang = localStorage.getItem('lang') || 'id';
            setLang(savedLang);

            // Mutation observer to catch language changes if triggered from layout
            const observer = new MutationObserver(() => {
                const currentLang = localStorage.getItem('lang') || 'id';
                setLang(currentLang);
            });
            observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });
            
            // Re-apply when user clicks language button in navbar
            document.querySelectorAll('.lang-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    setTimeout(() => setLang(localStorage.getItem('lang')), 100);
                });
            });
        });
    </script>
    @endpush
</x-app-layout>