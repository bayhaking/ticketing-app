<x-app-layout>
    <style>
        :root {
            --btn-grad: #1DB954;
            --promo-bg: #1DB954;
            --tix-item-bg: #1a1a1a;
            --fee-card-bg: rgba(168, 85, 247, 0.08);
            --fee-card-border: rgba(168, 85, 247, 0.4);
        }
        .light-mode {
            --btn-grad: #1e3a8a;
            --promo-bg: #1e3a8a;
            --tix-item-bg: #f1f5f9;
            --fee-card-bg: rgba(99, 102, 241, 0.08);
            --fee-card-border: rgba(99, 102, 241, 0.4);
        }

        /* STEPPER & FORM STYLES */
        .stepper-wrapper { display: flex; align-items: center; justify-content: center; margin-bottom: 3.5rem; }
        .step-unit { display: flex; align-items: center; opacity: 0.4; transition: 0.5s; }
        .step-unit.active { opacity: 1; }
        .step-circle { width: 45px; height: 45px; border-radius: 50%; border: 2px solid var(--border); background: var(--bg-card); display: flex; align-items: center; justify-content: center; font-weight: 800; color: var(--text-sub); transition: 0.4s; }
        .step-label { margin-left: 12px; color: var(--text-sub); font-weight: 800; font-size: 0.9rem; text-transform: uppercase; display: none; }
        .step-line { width: 40px; height: 2px; background: var(--border); margin: 0 10px; }
        .step-unit.active .step-circle { border-color: var(--accent); color: var(--accent); box-shadow: 0 0 15px var(--accent); }
        .step-unit.active .step-label { display: block; color: var(--text-main); }

        .form-step { display: none; }
        .form-step.active { display: block; animation: fadeIn 0.4s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .section-box { background-color: var(--bg-card); border: 1px solid var(--border); border-radius: 2.5rem; padding: 3rem; border-top: 8px solid var(--accent); color: var(--text-main); }
        .section-title { color: var(--accent); font-weight: 900; text-transform: uppercase; font-size: 1.4rem; margin-bottom: 2rem; font-style: italic; }
        .sub-section-title { color: var(--accent); font-weight: 900; text-transform: uppercase; font-size: 1rem; margin: 2rem 0 1rem; padding-top: 1.5rem; border-top: 1px solid var(--border); }
        .sub-section-title:first-of-type { padding-top: 0; border-top: none; margin-top: 0; }
        .field-label { color: var(--text-sub); font-weight: 800; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 10px; display: block; }
        .field-hint { color: var(--text-sub); font-size: 0.75rem; margin-top: -10px; margin-bottom: 15px; line-height: 1.4; }

        input, select, textarea { background: var(--bg-input); border: 1px solid var(--border); color: var(--text-main); width: 100%; border-radius: 1rem; padding: 1.2rem; font-weight: 600; margin-bottom: 1.5rem; outline: none; transition: 0.3s; }
        input:focus, select:focus, textarea:focus { border-color: var(--accent); }

        .input-group { position: relative; display: flex; align-items: center; margin-bottom: 1.5rem; }
        .input-prefix { position: absolute; left: 1.2rem; color: var(--accent); font-weight: 800; font-size: 0.8rem; pointer-events: none; z-index: 2; }
        .input-with-prefix { padding-left: 3.2rem !important; margin-bottom: 0 !important; }

        .coupon-wrapper { position: relative; margin-top: 1rem; filter: drop-shadow(0 10px 20px rgba(0,0,0,0.2)); }
        .coupon-container { background: var(--promo-bg); border: 2px dashed rgba(255,255,255,0.4); border-radius: 1.5rem; padding: 1.5rem; position: relative; overflow: hidden; -webkit-mask-image: radial-gradient(circle at 0 50%, transparent 15px, black 16px), radial-gradient(circle at 100% 50%, transparent 15px, black 16px); }
        .punch-hole { position: absolute; width: 30px; height: 30px; background-color: var(--bg-card); border-radius: 50%; top: 50%; transform: translateY(-50%); z-index: 2; }
        .punch-left { left: -16px; }
        .punch-right { right: -16px; }

        .switch { position: relative; display: inline-block; width: 44px; height: 22px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.3); transition: .4s; border-radius: 34px; border: 1px solid rgba(255,255,255,0.5); }
        .slider:before { position: absolute; content: ""; height: 14px; width: 14px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
        input:checked + .slider { background-color: #fff; border-color: #fff; box-shadow: var(--glow); }
        input:checked + .slider:before { transform: translateX(22px); background-color: var(--accent); }

        .tix-item { display: flex; align-items: center; justify-content: space-between; padding: 1.2rem; background: var(--tix-item-bg); border: 1px solid var(--accent); border-radius: 1rem; margin-bottom: 1rem; color: var(--text-main); }
        .total-stock-badge { background: var(--bg-input); border: 2px solid var(--accent); color: var(--accent); padding: 0.8rem 1.5rem; border-radius: 10px; font-weight: 900; font-size: 0.8rem; margin-bottom: 1.5rem; display: inline-block; }

        .ov-card { background: var(--bg-input); padding: 1.2rem; border-radius: 1rem; border-left: 4px solid var(--accent); margin-bottom: 1rem; color: var(--text-main); display: flex; flex-direction: column; height: 100%; justify-content: center; }
        .ov-label { font-size: 0.65rem; color: var(--accent); font-weight: 900; text-transform: uppercase; margin-bottom: 4px; }
        .ov-val { font-size: 1rem; font-weight: 700; color: var(--text-main); }

        .btn-next { background: var(--btn-grad); color: #fff; font-weight: 900; padding: 1.2rem; border-radius: 100px; border: none; cursor: pointer; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; }
        .btn-next:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(29, 185, 84, 0.4); }
        .btn-back { background: transparent; border: 1px solid var(--border); color: var(--text-sub); padding: 1.2rem; border-radius: 100px; cursor: pointer; font-weight: 800; text-transform: uppercase; transition: 0.3s; }
        .btn-back:hover { border-color: var(--text-main); color: var(--text-main); }

        .lineup-box, .sponsor-box {
            background: var(--bg-main); border: 1px dashed var(--border);
            padding: 1.5rem; border-radius: 1rem; margin-bottom: 1rem; position: relative;
        }
        .remove-btn {
            position: absolute; top: 15px; right: 15px;
            background: #ef4444; color: white; border: none;
            border-radius: 50%; width: 25px; height: 25px;
            cursor: pointer; font-weight: bold;
        }
        .add-btn {
            background: transparent; border: 1px solid var(--accent); color: var(--accent);
            padding: 10px 20px; border-radius: 100px;
            font-weight: 800; cursor: pointer; font-size: 0.8rem;
            width: 100%; margin-top: 10px; transition: 0.3s;
        }
        .add-btn:hover { background: rgba(29, 185, 84, 0.1); }

        /* FEE CALCULATOR MINI CARD */
        .fee-calc-card {
            background: var(--fee-card-bg);
            border: 1px solid var(--fee-card-border);
            border-radius: 14px;
            padding: 14px 18px;
            margin-top: 12px;
            margin-bottom: 0;
            font-size: 0.85rem;
            display: none;
        }
        .fee-calc-card.show { display: block; animation: fadeIn 0.3s ease; }
        .fee-calc-card .fee-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 4px 0;
            color: var(--text-sub);
        }
        .fee-calc-card .fee-row.net {
            border-top: 1px dashed var(--fee-card-border);
            margin-top: 8px;
            padding-top: 10px;
            font-weight: 900;
            color: var(--accent);
            font-size: 1rem;
        }
        .fee-calc-card .fee-label { font-weight: 700; }
        .fee-calc-card .fee-value { font-family: monospace; font-weight: 800; }
        .fee-calc-card .fee-info {
            font-size: 0.7rem; color: var(--text-sub);
            margin-top: 8px; padding-top: 8px;
            border-top: 1px dashed var(--fee-card-border);
            line-height: 1.4;
        }

        /* Venue + Time Grid */
        .venue-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .time-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        @media (max-width: 640px) {
            .venue-grid, .time-grid { grid-template-columns: 1fr; }
        }

        /* IG input prefix */
        .ig-input-wrap { position: relative; }
        .ig-input-wrap .input-prefix { color: var(--text-sub); }
        .ig-input-wrap input { padding-left: 4.5rem !important; }
    </style>

    <div style="background-color: var(--bg-main); min-height: 100vh; padding: 3rem 0;">
        <div class="max-w-4xl mx-auto px-6">

            <div class="stepper-wrapper">
                <div class="step-unit active" id="s1"><div class="step-circle">1</div><div class="step-label" id="l1" data-key="s1">Identitas</div></div>
                <div class="step-line"></div>
                <div class="step-unit" id="s2"><div class="step-circle">2</div><div class="step-label" id="l2" data-key="s2">Detail</div></div>
                <div class="step-line"></div>
                <div class="step-unit" id="s3"><div class="step-circle">3</div><div class="step-label" id="l3" data-key="s3">Tiket</div></div>
                <div class="step-line"></div>
                <div class="step-unit" id="s4"><div class="step-circle">4</div><div class="step-label" id="l4" data-key="s4">Review</div></div>
            </div>

            <div class="section-box">
                <form id="spectixForm" method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- ====================================================== --}}
                    {{-- STEP 1: VISUAL & IDENTITAS                              --}}
                    {{-- ====================================================== --}}
                    <div class="form-step active" id="step1">
                        <h3 class="section-title" data-key="h1">I. Visual & Identitas</h3>

                        <label class="field-label" data-key="l_banner">Banner Utama Event (1 Foto)</label>
                        <input type="file" id="f_banner" name="f_banner" accept="image/*" onchange="previewSingle(this)" required>
                        <div id="banner-preview" style="margin-bottom: 1.5rem; display: none;"></div>

                        <label class="field-label" style="margin-top: 10px;">Foto Tambahan / Line-Up (Bisa lebih dari 1 foto)</label>
                        <input type="file" id="f_gallery" name="f_gallery[]" accept="image/*" multiple onchange="previewMultiple(this)" style="border: 1px dashed var(--accent);">
                        <small style="color: var(--text-sub); display: block; margin-top: -10px; margin-bottom: 10px;">*Tahan CTRL (Win) / CMD (Mac) untuk memilih banyak foto.</small>
                        <div id="gallery-preview" style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 1.5rem;"></div>

                        <label class="field-label" data-key="l_name">Nama Event</label>
                        <input type="text" id="f_name" name="f_name" data-placeholder="p_name" placeholder="Contoh : Specteve 2026" required>

                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                            <div>
                                <label class="field-label" data-key="l_cat">Kategori</label>
                                <select id="f_cat" name="f_cat" required>
                                    <option disabled selected value="" data-key="opt_cat">Pilih Kategori</option>
                                    <option value="Musik" data-key="m_music">Musik</option>
                                    <option value="Olahraga" data-key="m_sport">Olahraga</option>
                                    <option value="Seminar" data-key="m_semi">Seminar</option>
                                    <option value="Hiburan" data-key="m_ent">Hiburan</option>
                                </select>
                            </div>
                            <div>
                                <label class="field-label" data-key="l_type">Jenis</label>
                                <select id="f_type" name="f_type" required>
                                    <option disabled selected value="" data-key="opt_type">Pilih Jenis</option>
                                    <option value="Publik" data-key="m_pub">Publik</option>
                                    <option value="Private" data-key="m_priv">Private</option>
                                </select>
                            </div>
                        </div>
                        <button type="button" onclick="move(2)" class="btn-next" style="width:100%" data-key="btn_next">NEXT</button>
                    </div>

                    {{-- ====================================================== --}}
                    {{-- STEP 2: DETAIL ACARA (Tanggal + Venue + Jam + Sponsor + Lineup) --}}
                    {{-- ====================================================== --}}
                    <div class="form-step" id="step2">
                        <h3 class="section-title" data-key="h2">II. Informasi Acara</h3>

                        {{-- TANGGAL & SALES START --}}
                        <h4 class="sub-section-title" data-key="sec_jadwal">📅 Jadwal Acara</h4>

                        <label class="field-label" data-key="l_date">Tanggal Pelaksanaan</label>
                        <input type="date" id="f_date" name="f_date" required>

                        <div class="time-grid">
                            <div>
                                <label class="field-label" data-key="l_start_time">⏰ Jam Mulai</label>
                                <input type="time" id="f_start_time" name="f_start_time" required>
                            </div>
                            <div>
                                <label class="field-label" data-key="l_end_time">⏰ Jam Selesai (Opsional)</label>
                                <input type="time" id="f_end_time" name="f_end_time">
                            </div>
                        </div>

                        <div style="margin: 1.5rem 0; background: rgba(29, 185, 84, 0.05); padding: 1.5rem; border-radius: 1rem; border: 1px dashed var(--accent);">
                            <label class="field-label" data-key="l_sales_start">⏱️ Tanggal Buka Penjualan (Opsional)</label>
                            <input type="datetime-local" id="sales_start_date" name="sales_start_date" style="margin-bottom: 0;">
                            <p style="color: var(--text-sub); font-size: 0.8rem; margin-top: 8px;" data-key="l_sales_sub">
                                *Isi jika ingin menggunakan fitur <strong>"Coming Soon" (Countdown)</strong>. Kosongkan jika tiket bisa langsung dibeli sekarang.
                            </p>
                        </div>

                        {{-- VENUE / LOKASI --}}
                        <h4 class="sub-section-title" data-key="sec_venue">📍 Lokasi / Venue</h4>

                        <label class="field-label" data-key="l_venue">Nama Venue</label>
                        <input type="text" id="f_venue" name="f_venue" data-placeholder="p_venue" placeholder="Contoh: Istora Senayan, Jakarta" required maxlength="255">

                        <label class="field-label" data-key="l_venue_url">Google Maps Link (Opsional)</label>
                        <input type="url" id="f_venue_url" name="f_venue_url" placeholder="https://maps.google.com/..." maxlength="500">
                        <p class="field-hint" data-key="l_venue_url_hint">*Customer bisa klik buat buka di Google Maps. Sangat membantu untuk navigasi.</p>

                        {{-- DESKRIPSI --}}
                        <h4 class="sub-section-title" data-key="sec_desc">📝 Deskripsi Acara</h4>

                        <label class="field-label" data-key="l_desc">Deskripsi Lengkap</label>
                        <textarea id="f_desc" name="f_desc" rows="5" data-placeholder="p_desc" placeholder="Detail acara..." required></textarea>

                        {{-- CREATOR INFO --}}
                        <h4 class="sub-section-title" data-key="sec_creator">🎨 Info Creator / Penyelenggara</h4>

                        <label class="field-label" data-key="l_creator_ig">Instagram Penyelenggara (Opsional)</label>
                        <div class="ig-input-wrap">
                            <span class="input-prefix" style="color: var(--text-sub);">📷 @</span>
                            <input type="text" id="f_creator_ig" name="f_creator_ig" placeholder="usernameanda" maxlength="100" style="padding-left: 3.5rem;">
                        </div>
                        <p class="field-hint" data-key="l_creator_ig_hint">*Customer bisa cek IG kamu untuk konfirmasi keaslian event.</p>

                        {{-- LINEUP --}}
                        <h4 class="sub-section-title" data-key="sec_lineup">🎤 Daftar Line-Up (Opsional)</h4>
                        <p style="color: var(--text-sub); font-size: 0.85rem; margin-bottom: 1.5rem;" data-key="l_lineup_sub">Tambahkan artis. Jika dikosongkan, akan muncul "Lineup belum tersedia" di halaman detail.</p>

                        <div id="lineup-container"></div>
                        <button type="button" class="add-btn" onclick="addLineup()" data-key="btn_add_art">+ TAMBAH ARTIS LAIN</button>

                        {{-- SPONSOR --}}
                        <h4 class="sub-section-title" data-key="sec_sponsor">🤝 Sponsor / Partner (Opsional)</h4>
                        <p style="color: var(--text-sub); font-size: 0.85rem; margin-bottom: 1.5rem;" data-key="l_sponsor_sub">Tambahkan sponsor / partner yang mendukung event ini. Akan tampil di halaman event.</p>

                        <div id="sponsor-container"></div>
                        <button type="button" class="add-btn" onclick="addSponsor()" data-key="btn_add_sponsor">+ TAMBAH SPONSOR</button>

                        <div style="display:flex; gap:10px; margin-top: 2rem;">
                            <button type="button" onclick="move(1)" class="btn-back" style="flex:1" data-key="btn_back">BACK</button>
                            <button type="button" onclick="move(3)" class="btn-next" style="flex:2" data-key="btn_next">NEXT</button>
                        </div>
                    </div>

                    {{-- ====================================================== --}}
                    {{-- STEP 3: TIKET & PROMO (DENGAN KALKULATOR FEE)            --}}
                    {{-- ====================================================== --}}
                    <div class="form-step" id="step3">
                        <h3 class="section-title" data-key="h3">III. Tiket & Promo</h3>

                        <div class="total-stock-badge"><span data-key="l_stock">TOTAL STOK</span>: <span id="stok_val">0</span> <span data-key="l_tix_unit">TIKET</span></div>

                        <div style="background:var(--bg-input); padding:1.5rem; border-radius:1.5rem; border:1px solid var(--border);">
                            <label class="field-label" data-key="l_t_cat">Kategori Tiket</label>
                            <input type="text" id="t_name" data-placeholder="p_t_cat" placeholder="Contoh: VIP / Festival">

                            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                                <div>
                                    <label class="field-label" data-key="l_t_price">Harga Tiket</label>
                                    <div class="input-group">
                                        <span class="input-prefix">Rp</span>
                                        <input type="text" id="t_price" class="input-with-prefix" oninput="formatDots(this); updateFeeCalc();" placeholder="0">
                                    </div>
                                </div>
                                <div>
                                    <label class="field-label" data-key="l_t_stock">Jumlah Stok</label>
                                    <div class="input-group">
                                        <span class="input-prefix">Qty</span>
                                        <input type="text" id="t_stock" class="input-with-prefix" oninput="formatDots(this); updateFeeCalc();" placeholder="0">
                                    </div>
                                </div>
                            </div>

                            {{-- ===== FEE CALCULATOR MINI CARD (real-time per tiket) ===== --}}
                            <div id="fee_calc_card" class="fee-calc-card">
                                <div class="fee-row">
                                    <span class="fee-label" data-key="fee_price">Harga jual ke customer</span>
                                    <span class="fee-value" id="fee_price_val">Rp 0</span>
                                </div>
                                <div class="fee-row">
                                    <span class="fee-label" data-key="fee_service">Biaya layanan SPECTIX (5%)</span>
                                    <span class="fee-value" id="fee_service_val" style="color: #ef4444;">- Rp 0</span>
                                </div>
                                <div class="fee-row net">
                                    <span class="fee-label" data-key="fee_net">Kamu terima per tiket</span>
                                    <span class="fee-value" id="fee_net_val">Rp 0</span>
                                </div>
                                <div class="fee-info" id="fee_info_estimasi">
                                    <span data-key="fee_estim">💰 Estimasi kalau semua tiket terjual:</span><br>
                                    <span data-key="fee_total_revenue">Total pendapatan kotor:</span> <span id="fee_gross_total" style="font-weight: 900;">Rp 0</span><br>
                                    <span data-key="fee_total_fee">Total biaya SPECTIX:</span> <span id="fee_fee_total" style="font-weight: 900; color: #ef4444;">Rp 0</span><br>
                                    <span data-key="fee_total_net">Net yang kamu terima:</span> <span id="fee_net_total" style="font-weight: 900; color: var(--accent);">Rp 0</span>
                                </div>
                            </div>

                            <button type="button" onclick="addT()" style="width:100%; border:2px solid var(--accent); color:var(--accent); background:none; padding:0.8rem; border-radius:1rem; font-weight:800; cursor:pointer; transition:0.3s; margin-top: 12px;" onmouseover="this.style.background='var(--border)'" onmouseout="this.style.background='none'" data-key="btn_add_tix">+ TAMBAH TIKET</button>
                        </div>

                        <div id="list_t" style="margin-top:1.5rem;"></div>

                        <div class="coupon-wrapper">
                            <div class="punch-hole punch-left"></div>
                            <div class="punch-hole punch-right"></div>
                            <div class="coupon-container">
                                <label class="field-label" style="color:#fff; opacity:0.8;" data-key="l_promo">VOUCHER PROMOSI (Khusus Event Anda)</label>

                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px; border-bottom: 1px dashed rgba(255,255,255,0.2); padding-bottom: 10px;">
                                    <span style="font-size: 0.8rem; color: #fff; font-weight: 700;" data-key="l_voucher_toggle">Izinkan penggunaan kode voucher:</span>
                                    <label class="switch">
                                        <input type="checkbox" name="is_voucher_active" value="1" id="f_promo_toggle">
                                        <span class="slider"></span>
                                    </label>
                                </div>

                                @if(isset($activeVouchers) && $activeVouchers->count() > 0)
                                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                        @foreach($activeVouchers as $v)
                                            <div style="background: rgba(255,255,255,0.1); padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; border: 1px solid var(--accent);">
                                                <strong style="color: var(--accent); font-family: monospace;">{{ $v->code }}</strong>
                                                <span style="color: #ccc; margin-left: 5px;">
                                                    (Diskon {{ $v->type == 'nominal' ? 'Rp '.number_format($v->amount, 0, ',', '.') : $v->amount.'%' }})
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                    <small style="color: rgba(255,255,255,0.6); display: block; margin-top: 10px; font-size: 0.7rem;">*Pembeli dapat menggunakan kode di atas saat checkout jika tombol diaktifkan.</small>
                                @else
                                    <div style="color: #000; font-size: 0.8rem; font-weight: 800; background: #ffc107; padding: 10px; border-radius: 8px;">
                                        ⚠️ Belum ada voucher yang di-ACC. Ajukan di menu Kelola Voucher.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div style="display:flex; gap:10px; margin-top:2rem;">
                            <button type="button" onclick="move(2)" class="btn-back" style="flex:1" data-key="btn_back">BACK</button>
                            <button type="button" onclick="move(4)" class="btn-next" style="flex:2" data-key="btn_next">NEXT</button>
                        </div>
                    </div>

                    {{-- ====================================================== --}}
                    {{-- STEP 4: FINAL REVIEW                                    --}}
                    {{-- ====================================================== --}}
                    <div class="form-step" id="step4">
                        <h3 class="section-title" data-key="h4">IV. Final Review</h3>
                        <div style="display:grid; grid-template-columns: 1.2fr 0.8fr; gap:20px; align-items: start;">

                            <div style="display: flex; flex-direction: column; gap: 15px;">
                                <div class="ov-card" style="padding:0; overflow:hidden; border:none; height: 320px;">
                                    <img id="ov_banner" src="" style="width:100%; height:100%; object-fit:cover;">
                                </div>
                                <div class="ov-card" style="min-height: 120px;">
                                    <p class="ov-label" data-key="l_desc">Deskripsi</p>
                                    <p class="ov-val" id="ov_desc" style="font-size:0.85rem; font-weight:500; line-height:1.4;">-</p>
                                </div>
                                <div class="ov-card">
                                    <p class="ov-label" data-key="ov_l_venue">📍 Venue</p>
                                    <p class="ov-val" id="ov_venue" style="font-size:0.9rem;">-</p>
                                </div>
                                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                                    <div class="ov-card"><p class="ov-label" data-key="l_cat">Kategori</p><p class="ov-val" id="ov_cat">-</p></div>
                                    <div class="ov-card"><p class="ov-label" data-key="l_type">Jenis</p><p class="ov-val" id="ov_type">-</p></div>
                                </div>
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 15px;">
                                <div class="ov-card">
                                    <p class="ov-label" data-key="ov_l_name">Nama Event & Tanggal</p>
                                    <p class="ov-val" id="ov_name">-</p>
                                </div>
                                <div class="ov-card">
                                    <p class="ov-label" data-key="ov_l_time">⏰ Waktu</p>
                                    <p class="ov-val" id="ov_time" style="font-size:0.9rem;">-</p>
                                </div>
                                <div class="ov-card" style="flex-grow:1;">
                                    <p class="ov-label" data-key="ov_l_list">Daftar Tiket & Stok</p>
                                    <div id="ov_tix" style="margin-top:10px;"></div>
                                </div>
                                <div class="coupon-wrapper">
                                    <div class="punch-hole punch-left" style="background-color: var(--bg-card);"></div>
                                    <div class="punch-hole punch-right" style="background-color: var(--bg-card);"></div>
                                    <div class="coupon-container">
                                        <p style="font-size:0.7rem; color:#fff; opacity:0.7; font-weight: 800; margin-bottom: 5px;" data-key="ov_l_promo">STATUS VOUCHER</p>
                                        <p id="ov_promo_val" style="color:#fff; font-weight:900; font-size: 1.3rem; letter-spacing: 1px;">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="display:flex; gap:10px; margin-top:3rem;">
                            <button type="button" onclick="move(3)" class="btn-back" style="flex:1" data-key="btn_back">BACK</button>
                            <button type="submit" class="btn-next" style="flex:2; font-style:italic; font-size:1.3rem;" data-key="btn_publish">Publish Tiket Anda!</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @stack('scripts')
    <script>
        let currentLang = 'id';
        const translations = {
            id: {
                s1: "Identitas", s2: "Detail", s3: "Tiket", s4: "Review",
                h1: "I. Visual & Identitas", h2: "II. Informasi Acara", h3: "III. Tiket & Promo", h4: "IV. Final Review",
                sec_jadwal: "📅 Jadwal Acara", sec_venue: "📍 Lokasi / Venue", sec_desc: "📝 Deskripsi Acara",
                sec_creator: "🎨 Info Creator / Penyelenggara", sec_lineup: "🎤 Daftar Line-Up (Opsional)", sec_sponsor: "🤝 Sponsor / Partner (Opsional)",
                l_banner: "Banner Utama Event (1 Foto)", l_name: "Nama Event", l_cat: "Kategori", l_type: "Jenis",
                l_date: "Tanggal Pelaksanaan",
                l_start_time: "⏰ Jam Mulai", l_end_time: "⏰ Jam Selesai (Opsional)",
                l_sales_start: "⏱️ Tanggal Buka Penjualan (Opsional)", l_sales_sub: "*Isi jika ingin menggunakan fitur \"Coming Soon\" (Countdown). Kosongkan jika tiket bisa langsung dibeli sekarang.",
                l_venue: "Nama Venue", p_venue: "Contoh: Istora Senayan, Jakarta",
                l_venue_url: "Google Maps Link (Opsional)",
                l_venue_url_hint: "*Customer bisa klik buat buka di Google Maps. Sangat membantu untuk navigasi.",
                l_desc: "Deskripsi Lengkap",
                l_creator_ig: "Instagram Penyelenggara (Opsional)",
                l_creator_ig_hint: "*Customer bisa cek IG kamu untuk konfirmasi keaslian event.",
                l_lineup_sub: "Tambahkan artis. Jika dikosongkan, akan muncul 'Lineup belum tersedia' di halaman detail.",
                l_sponsor_sub: "Tambahkan sponsor / partner yang mendukung event ini. Akan tampil di halaman event.",
                l_art_name: "Nama Artis / Band",
                l_sponsor_name: "Nama Sponsor", l_sponsor_ig: "Instagram Sponsor (Opsional)",
                btn_add_art: "+ TAMBAH ARTIS LAIN", btn_add_sponsor: "+ TAMBAH SPONSOR",
                l_stock: "TOTAL STOK", l_tix_unit: "TIKET", l_t_cat: "Kategori Tiket", l_t_price: "Harga Tiket", l_t_stock: "Jumlah Stok",
                l_promo: "VOUCHER PROMOSI (Khusus Event Anda)", l_voucher_toggle: "Izinkan penggunaan kode voucher:", p_none: "-- Tidak Aktif --",
                opt_cat: "Pilih Kategori", m_music: "Musik", m_sport: "Olahraga", m_semi: "Seminar", m_ent: "Hiburan",
                opt_type: "Pilih Jenis", m_pub: "Publik", m_priv: "Private",
                btn_next: "NEXT", btn_back: "BACK", btn_add_tix: "+ TAMBAH TIKET",
                p_name: "Contoh : Specteve 2026", p_desc: "Detail acara...", p_t_cat: "Contoh: VIP / Festival",
                ov_l_name: "Nama Event & Tanggal", ov_l_list: "Daftar Tiket & Stok", ov_l_promo: "STATUS VOUCHER",
                ov_l_venue: "📍 Venue", ov_l_time: "⏰ Waktu",
                fee_price: "Harga jual ke customer", fee_service: "Biaya layanan SPECTIX (5%)", fee_net: "Kamu terima per tiket",
                fee_estim: "💰 Estimasi kalau semua tiket terjual:",
                fee_total_revenue: "Total pendapatan kotor:", fee_total_fee: "Total biaya SPECTIX:", fee_total_net: "Net yang kamu terima:",
                btn_publish: "Publish Tiket Anda!"
            },
            en: {
                s1: "Identity", s2: "Details", s3: "Tickets", s4: "Review",
                h1: "I. Visual & Identity", h2: "II. Event Information", h3: "III. Tickets & Promo", h4: "IV. Final Review",
                sec_jadwal: "📅 Event Schedule", sec_venue: "📍 Location / Venue", sec_desc: "📝 Event Description",
                sec_creator: "🎨 Creator / Organizer Info", sec_lineup: "🎤 Line-Up List (Optional)", sec_sponsor: "🤝 Sponsor / Partner (Optional)",
                l_banner: "Main Event Banner (1 Photo)", l_name: "Event Name", l_cat: "Category", l_type: "Type",
                l_date: "Execution Date",
                l_start_time: "⏰ Start Time", l_end_time: "⏰ End Time (Optional)",
                l_sales_start: "⏱️ Ticket Sales Start Date (Optional)", l_sales_sub: "*Fill this for 'Coming Soon' (Countdown) feature. Leave empty if tickets are available immediately.",
                l_venue: "Venue Name", p_venue: "Example: Istora Senayan, Jakarta",
                l_venue_url: "Google Maps Link (Optional)",
                l_venue_url_hint: "*Customers can tap to open in Google Maps. Very helpful for navigation.",
                l_desc: "Full Description",
                l_creator_ig: "Organizer Instagram (Optional)",
                l_creator_ig_hint: "*Customers can check your IG to verify event authenticity.",
                l_lineup_sub: "Add artists. If empty, 'Lineup unavailable' will show on details page.",
                l_sponsor_sub: "Add sponsors / partners supporting this event. Will appear on the event page.",
                l_art_name: "Artist / Band Name",
                l_sponsor_name: "Sponsor Name", l_sponsor_ig: "Sponsor Instagram (Optional)",
                btn_add_art: "+ ADD ANOTHER ARTIST", btn_add_sponsor: "+ ADD SPONSOR",
                l_stock: "TOTAL STOCK", l_tix_unit: "TICKETS", l_t_cat: "Ticket Category", l_t_price: "Ticket Price", l_t_stock: "Stock Quantity",
                l_promo: "PROMO VOUCHER (For Your Event)", l_voucher_toggle: "Allow voucher code usage:", p_none: "-- Inactive --",
                opt_cat: "Select Category", m_music: "Music", m_sport: "Sports", m_semi: "Seminar", m_ent: "Entertainment",
                opt_type: "Select Type", m_pub: "Public", m_priv: "Private",
                btn_next: "NEXT", btn_back: "BACK", btn_add_tix: "+ ADD TICKET",
                p_name: "Example : Specteve 2026", p_desc: "Event details...", p_t_cat: "Example: VIP / Festival",
                ov_l_name: "Event Name & Date", ov_l_list: "Ticket List & Stock", ov_l_promo: "VOUCHER STATUS",
                ov_l_venue: "📍 Venue", ov_l_time: "⏰ Time",
                fee_price: "Price to customer", fee_service: "SPECTIX service fee (5%)", fee_net: "You receive per ticket",
                fee_estim: "💰 Estimate if all tickets sold:",
                fee_total_revenue: "Total gross revenue:", fee_total_fee: "Total SPECTIX fees:", fee_total_net: "Net you receive:",
                btn_publish: "Publish Your Tix!"
            }
        };

        function setLang(lang) {
            currentLang = lang;
            document.querySelectorAll('[data-key]').forEach(el => {
                const key = el.getAttribute('data-key');
                if (translations[lang][key]) el.innerText = translations[lang][key];
            });
            document.querySelectorAll('[data-placeholder]').forEach(el => {
                const key = el.getAttribute('data-placeholder');
                if (translations[lang][key]) el.placeholder = translations[lang][key];
            });
            document.querySelectorAll('.lang-btn').forEach(btn => btn.classList.remove('active'));
            const btnActive = document.getElementById('btn-' + lang);
            if (btnActive) btnActive.classList.add('active');
            localStorage.setItem('lang', lang);
        }

        // ============================================================
        // LINEUP
        // ============================================================
        function addLineup() {
            const container = document.getElementById('lineup-container');
            const l_art_name = translations[currentLang].l_art_name;
            const html = `
                <div class="lineup-box">
                    <button type="button" class="remove-btn" onclick="this.parentElement.remove()">X</button>
                    <label class="field-label">${l_art_name}</label>
                    <input type="text" name="lineup_name[]" placeholder="Contoh: FSTVLST">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div><label class="field-label">Link Instagram</label><input type="url" name="lineup_ig[]" placeholder="https://instagram.com/..."></div>
                        <div><label class="field-label">Link Spotify</label><input type="url" name="lineup_spotify[]" placeholder="https://spotify.com/..."></div>
                    </div>
                </div>`;
            container.insertAdjacentHTML('beforeend', html);
        }

        // ============================================================
        // SPONSOR
        // ============================================================
        function addSponsor() {
            const container = document.getElementById('sponsor-container');
            const l_sponsor_name = translations[currentLang].l_sponsor_name;
            const l_sponsor_ig = translations[currentLang].l_sponsor_ig;
            const html = `
                <div class="sponsor-box">
                    <button type="button" class="remove-btn" onclick="this.parentElement.remove()">X</button>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label class="field-label">${l_sponsor_name}</label>
                            <input type="text" name="sponsor_name[]" placeholder="Contoh: Coca-Cola" maxlength="100">
                        </div>
                        <div>
                            <label class="field-label">${l_sponsor_ig}</label>
                            <div style="position: relative;">
                                <span style="position: absolute; left: 1.2rem; top: 50%; transform: translateY(-50%); color: var(--text-sub); font-weight: 800; font-size: 0.8rem; pointer-events: none; z-index: 2;">@</span>
                                <input type="text" name="sponsor_ig[]" placeholder="cocacolaindonesia" maxlength="100" style="padding-left: 2.5rem;">
                            </div>
                        </div>
                    </div>
                </div>`;
            container.insertAdjacentHTML('beforeend', html);
        }

        // ============================================================
        // PREVIEW UPLOAD
        // ============================================================
        function previewSingle(input) {
            const preview = document.getElementById('banner-preview');
            preview.innerHTML = "";
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" style="width: 100%; max-height: 250px; object-fit: cover; border-radius: 1rem; border: 2px solid var(--accent); box-shadow: var(--glow);">`;
                    preview.style.display = "block";
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = "none";
            }
        }

        function previewMultiple(input) {
            const preview = document.getElementById('gallery-preview');
            preview.innerHTML = "";
            if (input.files) {
                Array.from(input.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML += `<img src="${e.target.result}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 10px; border: 1px solid var(--border);">`;
                    }
                    reader.readAsDataURL(file);
                });
            }
        }

        function formatDots(input) {
            let val = input.value.replace(/\D/g, "");
            input.value = val.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // ============================================================
        // FEE CALCULATOR REAL-TIME
        // ============================================================
        const SERVICE_FEE_PCT = 0.05; // 5%

        function updateFeeCalc() {
            const priceStr = document.getElementById('t_price').value.replace(/\./g, '');
            const stockStr = document.getElementById('t_stock').value.replace(/\./g, '');
            const price = parseInt(priceStr) || 0;
            const stock = parseInt(stockStr) || 0;

            const card = document.getElementById('fee_calc_card');

            if (price <= 0) {
                card.classList.remove('show');
                return;
            }

            card.classList.add('show');

            const fee = Math.round(price * SERVICE_FEE_PCT);
            const net = price - fee;

            document.getElementById('fee_price_val').textContent = 'Rp ' + price.toLocaleString('id-ID');
            document.getElementById('fee_service_val').textContent = '- Rp ' + fee.toLocaleString('id-ID');
            document.getElementById('fee_net_val').textContent = 'Rp ' + net.toLocaleString('id-ID');

            // Estimasi total kalau semua terjual
            const grossTotal = price * stock;
            const feeTotal = fee * stock;
            const netTotal = net * stock;

            document.getElementById('fee_gross_total').textContent = 'Rp ' + grossTotal.toLocaleString('id-ID');
            document.getElementById('fee_fee_total').textContent = 'Rp ' + feeTotal.toLocaleString('id-ID');
            document.getElementById('fee_net_total').textContent = 'Rp ' + netTotal.toLocaleString('id-ID');
        }

        // ============================================================
        // STEPPER NAVIGATION
        // ============================================================
        let currentStepNum = 1;

        function move(t) {
            if (t > currentStepNum) {
                const stepDiv = document.getElementById('step' + currentStepNum);
                const reqInputs = stepDiv.querySelectorAll('[required]');

                for (let i = 0; i < reqInputs.length; i++) {
                    if (!reqInputs[i].value) {
                        alert("Tolong isi semua kolom wajib sebelum lanjut ya!");
                        reqInputs[i].focus();
                        return;
                    }
                }

                // Validasi end_time > start_time (kalau end_time diisi)
                if (currentStepNum === 2) {
                    const startTime = document.getElementById('f_start_time').value;
                    const endTime = document.getElementById('f_end_time').value;
                    if (startTime && endTime && endTime <= startTime) {
                        alert("Jam selesai harus lebih besar dari jam mulai!");
                        document.getElementById('f_end_time').focus();
                        return;
                    }
                }

                if (currentStepNum === 3 && t === 4) {
                    const listTix = document.getElementById('list_t');
                    if (listTix.children.length === 0) {
                        alert("Kamu belum menambahkan tiket satupun! Buat minimal 1 kategori tiket dulu.");
                        return;
                    }
                }
            }

            document.querySelectorAll('.form-step, .step-unit').forEach(el => el.classList.remove('active'));
            document.getElementById('step' + t).classList.add('active');
            document.getElementById('s' + t).classList.add('active');

            currentStepNum = t;

            if (t === 4) {
                document.getElementById('ov_name').innerText = (document.getElementById('f_name').value || "-") + " | " + (document.getElementById('f_date').value || "TBA");
                document.getElementById('ov_cat').innerText = document.getElementById('f_cat').value || "-";
                document.getElementById('ov_type').innerText = document.getElementById('f_type').value || "-";

                // Venue & time
                const venue = document.getElementById('f_venue').value || "-";
                const venueUrl = document.getElementById('f_venue_url').value;
                document.getElementById('ov_venue').innerHTML = venue + (venueUrl ? `<br><a href="${venueUrl}" target="_blank" style="color: var(--accent); font-size: 0.75rem;">📍 Buka di Maps</a>` : '');

                const startTime = document.getElementById('f_start_time').value;
                const endTime = document.getElementById('f_end_time').value;
                let timeStr = startTime || "-";
                if (startTime && endTime) timeStr = `${startTime} - ${endTime} WIB`;
                else if (startTime) timeStr = `Mulai ${startTime} WIB`;
                document.getElementById('ov_time').innerText = timeStr;

                const firstImg = document.querySelector('#banner-preview img');
                if (firstImg) document.getElementById('ov_banner').src = firstImg.src;

                document.getElementById('ov_desc').innerText = document.getElementById('f_desc').value || "-";

                let listHtml = "";
                document.querySelectorAll('#list_t .tix-item').forEach(item => {
                    let text = item.querySelector('div').innerHTML.replace(/<button.*<\/button>/, "");
                    listHtml += `<div class="tix-item" style="padding: 0.8rem; margin-bottom: 5px; border-style: dashed;"><div>${text}</div></div>`;
                });
                document.getElementById('ov_tix').innerHTML = listHtml || "-";

                const promoToggle = document.getElementById('f_promo_toggle');
                let promoText = (promoToggle && promoToggle.checked) ? "AKTIF" : translations[currentLang].p_none;
                document.getElementById('ov_promo_val').innerText = promoText;
            }
        }

        // ============================================================
        // TAMBAH TIKET
        // ============================================================
        function addT() {
            const n = document.getElementById('t_name'), p = document.getElementById('t_price'), s = document.getElementById('t_stock'), list = document.getElementById('list_t');
            if (!n.value || !s.value) {
                alert("Isi nama kategori dan jumlah stok tiket dulu!");
                return;
            }
            const priceNum = parseInt(p.value.replace(/\./g, '')) || 0;
            const stockNum = parseInt(s.value.replace(/\./g, '')) || 0;
            const feePerTicket = Math.round(priceNum * SERVICE_FEE_PCT);
            const netPerTicket = priceNum - feePerTicket;

            const d = document.createElement('div');
            d.className = 'tix-item';
            d.innerHTML = `
                <input type="hidden" name="t_names[]" value="${n.value}">
                <input type="hidden" name="t_prices[]" value="${p.value || 0}">
                <input type="hidden" name="t_stocks[]" value="${s.value}">
                <div>
                    <b>${n.value}</b><br>
                    <small>Rp ${p.value || 0} | Stok: ${s.value}</small>
                    <br>
                    <small style="color: var(--accent);">💰 Net per tiket: Rp ${netPerTicket.toLocaleString('id-ID')}</small>
                </div>
                <button type="button" onclick="this.parentElement.remove(); upStok();" style="background:none; border:none; color:#ff4444; font-weight:900; cursor:pointer;">X</button>
            `;
            list.appendChild(d);
            upStok();
            n.value = "";
            p.value = "";
            s.value = "";

            // Reset fee calculator
            document.getElementById('fee_calc_card').classList.remove('show');
        }

        function upStok() {
            let t = 0;
            document.querySelectorAll('#list_t .tix-item small').forEach(el => {
                let parts = el.innerText.split('Stok: ');
                if (parts[1]) t += parseInt(parts[1].replace(/\./g, ""));
            });
            document.getElementById('stok_val').innerText = t.toLocaleString('id-ID');
        }

        // ============================================================
        // INIT
        // ============================================================
        document.addEventListener('DOMContentLoaded', () => {
            const savedLang = localStorage.getItem('lang') || 'id';
            setLang(savedLang);
        });
    </script>
</x-app-layout>