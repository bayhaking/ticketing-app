<x-app-layout>
    <style>
        /* VARIABEL KHUSUS HALAMAN POSTING TIKET */
        :root { 
            --btn-grad: #1DB954; 
            --promo-bg: #1DB954; 
            --tix-item-bg: #1a1a1a; 
        }
        .light-mode { 
            --btn-grad: #1e3a8a; 
            --promo-bg: #1e3a8a; 
            --tix-item-bg: #f1f5f9; 
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
        .field-label { color: var(--text-sub); font-weight: 800; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 10px; display: block; }
        
        input, select, textarea { background: var(--bg-input); border: 1px solid var(--border); color: var(--text-main); width: 100%; border-radius: 1rem; padding: 1.2rem; font-weight: 600; margin-bottom: 1.5rem; outline: none; transition: 0.3s; }
        input:focus, select:focus, textarea:focus { border-color: var(--accent); }
        
        .input-group { position: relative; display: flex; align-items: center; margin-bottom: 1.5rem; }
        .input-prefix { position: absolute; left: 1.2rem; color: var(--accent); font-weight: 800; font-size: 0.8rem; pointer-events: none; }
        .input-with-prefix { padding-left: 3.2rem !important; margin-bottom: 0 !important; }

        .coupon-wrapper { position: relative; margin-top: 1rem; filter: drop-shadow(0 10px 20px rgba(0,0,0,0.2)); }
        .coupon-container { background: var(--promo-bg); border: 2px dashed rgba(255,255,255,0.4); border-radius: 1.5rem; padding: 1.5rem; position: relative; overflow: hidden; -webkit-mask-image: radial-gradient(circle at 0 50%, transparent 15px, black 16px), radial-gradient(circle at 100% 50%, transparent 15px, black 16px); }
        .punch-hole { position: absolute; width: 30px; height: 30px; background-color: var(--bg-card); border-radius: 50%; top: 50%; transform: translateY(-50%); z-index: 2; }
        .punch-left { left: -16px; }
        .punch-right { right: -16px; }

        .tix-item { display: flex; align-items: center; justify-content: space-between; padding: 1.2rem; background: var(--tix-item-bg); border: 1px solid var(--accent); border-radius: 1rem; margin-bottom: 1rem; color: var(--text-main); }
        .total-stock-badge { background: var(--bg-input); border: 2px solid var(--accent); color: var(--accent); padding: 0.8rem 1.5rem; border-radius: 10px; font-weight: 900; font-size: 0.8rem; margin-bottom: 1.5rem; display: inline-block; }

        .ov-card { background: var(--bg-input); padding: 1.2rem; border-radius: 1rem; border-left: 4px solid var(--accent); margin-bottom: 1rem; color: var(--text-main); display: flex; flex-direction: column; height: 100%; justify-content: center; }
        .ov-label { font-size: 0.65rem; color: var(--accent); font-weight: 900; text-transform: uppercase; margin-bottom: 4px; }
        .ov-val { font-size: 1rem; font-weight: 700; color: var(--text-main); }

        .btn-next { background: var(--btn-grad); color: #fff; font-weight: 900; padding: 1.2rem; border-radius: 100px; border: none; cursor: pointer; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; }
        .btn-next:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(29, 185, 84, 0.4); }
        .btn-back { background: transparent; border: 1px solid var(--border); color: var(--text-sub); padding: 1.2rem; border-radius: 100px; cursor: pointer; font-weight: 800; text-transform: uppercase; transition: 0.3s; }
        .btn-back:hover { border-color: var(--text-main); color: var(--text-main); }

        /* LINEUP CSS */
        .lineup-box { background: var(--bg-main); border: 1px dashed var(--border); padding: 1.5rem; border-radius: 1rem; margin-bottom: 1rem; position: relative; }
        .remove-btn { position: absolute; top: 15px; right: 15px; background: #ef4444; color: white; border: none; border-radius: 50%; width: 25px; height: 25px; cursor: pointer; font-weight: bold; }
        .add-lineup-btn { background: transparent; border: 1px solid var(--accent); color: var(--accent); padding: 10px 20px; border-radius: 100px; font-weight: 800; cursor: pointer; font-size: 0.8rem; width: 100%; margin-top: 10px; transition: 0.3s; }
        .add-lineup-btn:hover { background: rgba(29, 185, 84, 0.1); }
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

                    <div class="form-step" id="step2">
                        <h3 class="section-title" data-key="h2">II. Informasi Acara</h3>
                        <label class="field-label" data-key="l_date">Tanggal Pelaksanaan</label>
                        <input type="date" id="f_date" name="f_date" required>

                        <!-- FITUR COMING SOON -->
                        <div style="margin: 1.5rem 0; background: rgba(29, 185, 84, 0.05); padding: 1.5rem; border-radius: 1rem; border: 1px dashed var(--accent);">
                            <label class="field-label" data-key="l_sales_start">⏱️ Tanggal Buka Penjualan (Opsional)</label>
                            <input type="datetime-local" id="sales_start_date" name="sales_start_date" style="margin-bottom: 0;">
                            <p style="color: var(--text-sub); font-size: 0.8rem; margin-top: 8px;" data-key="l_sales_sub">
                                *Isi jika ingin menggunakan fitur <strong>"Coming Soon" (Countdown)</strong>. Kosongkan jika tiket bisa langsung dibeli sekarang.
                            </p>
                        </div>
                        
                        <label class="field-label" data-key="l_desc">Deskripsi Lengkap</label>
                        <textarea id="f_desc" name="f_desc" rows="5" data-placeholder="p_desc" placeholder="Detail acara..." required></textarea>
                        
                        <div style="margin: 2rem 0; padding-top: 2rem; border-top: 1px solid var(--border);">
                            <h3 style="color: var(--accent); font-weight: 900; margin-bottom: 1rem; text-transform:uppercase; font-size:1.1rem;" data-key="l_lineup_h">DAFTAR LINE-UP (Opsional)</h3>
                            <p style="color: var(--text-sub); font-size: 0.85rem; margin-bottom: 1.5rem;" data-key="l_lineup_sub">Tambahkan artis. Jika dikosongkan, akan muncul "Lineup belum tersedia" di halaman detail.</p>
                            
                            <div id="lineup-container"></div>
                            <button type="button" class="add-lineup-btn" onclick="addLineup()" data-key="btn_add_art">+ TAMBAH ARTIS LAIN</button>
                        </div>

                        <div style="display:flex; gap:10px;">
                            <button type="button" onclick="move(1)" class="btn-back" style="flex:1" data-key="btn_back">BACK</button>
                            <button type="button" onclick="move(3)" class="btn-next" style="flex:2" data-key="btn_next">NEXT</button>
                        </div>
                    </div>

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
                                        <input type="text" id="t_price" class="input-with-prefix" oninput="formatDots(this)" placeholder="0">
                                    </div>
                                </div>
                                <div>
                                    <label class="field-label" data-key="l_t_stock">Jumlah Stok</label>
                                    <div class="input-group">
                                        <span class="input-prefix">Qty</span>
                                        <input type="text" id="t_stock" class="input-with-prefix" oninput="formatDots(this)" placeholder="0">
                                    </div>
                                </div>
                            </div>
                            <button type="button" onclick="addT()" style="width:100%; border:2px solid var(--accent); color:var(--accent); background:none; padding:0.8rem; border-radius:1rem; font-weight:800; cursor:pointer; transition:0.3s;" onmouseover="this.style.background='var(--border)'" onmouseout="this.style.background='none'" data-key="btn_add_tix">+ TAMBAH TIKET</button>
                        </div>

                        <div id="list_t" style="margin-top:1.5rem;"></div>

                        <div class="coupon-wrapper">
                            <div class="punch-hole punch-left"></div>
                            <div class="punch-hole punch-right"></div>
                            <div class="coupon-container">
                                <label class="field-label" style="color:#fff; opacity:0.8;" data-key="l_promo">PROMO AKTIF</label>
                                <select id="f_promo" name="f_promo" style="background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.2); color:#fff; margin:0;">
                                    <option value="0" data-key="p_none">-- Tanpa Promo --</option>
                                    <option value="10">SPECTIVE2026 (10%)</option>
                                </select>
                            </div>
                        </div>

                        <div style="display:flex; gap:10px; margin-top:2rem;">
                            <button type="button" onclick="move(2)" class="btn-back" style="flex:1" data-key="btn_back">BACK</button>
                            <button type="button" onclick="move(4)" class="btn-next" style="flex:2" data-key="btn_next">NEXT</button>
                        </div>
                    </div>

                    <div class="form-step" id="step4">
                        <h3 class="section-title" data-key="h4">IV. Final Review</h3>
                        <div style="display:grid; grid-template-columns: 1.2fr 0.8fr; gap:20px; align-items: start;">
                            
                            <div style="display: flex; flex-direction: column; gap: 15px;">
                                <div class="ov-card" style="padding:0; overflow:hidden; border:none; height: 320px;">
                                    <img id="ov_banner" src="" style="width:100%; height:100%; object-fit:cover;">
                                </div>
                                <div class="ov-card" style="min-height: 150px;">
                                    <p class="ov-label" data-key="l_desc">Deskripsi</p>
                                    <p class="ov-val" id="ov_desc" style="font-size:0.85rem; font-weight:500; line-height:1.4;">-</p>
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
                                <div class="ov-card" style="flex-grow:1;">
                                    <p class="ov-label" data-key="ov_l_list">Daftar Tiket & Stok</p>
                                    <div id="ov_tix" style="margin-top:10px;"></div>
                                </div>
                                <div class="coupon-wrapper">
                                    <div class="punch-hole punch-left" style="background-color: var(--bg-card);"></div>
                                    <div class="punch-hole punch-right" style="background-color: var(--bg-card);"></div>
                                    <div class="coupon-container">
                                        <p style="font-size:0.7rem; color:#fff; opacity:0.7; font-weight: 800; margin-bottom: 5px;" data-key="ov_l_promo">PROMO APPLIED</p>
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
                l_banner: "Banner Utama Event (1 Foto)", l_name: "Nama Event", l_cat: "Kategori", l_type: "Jenis",
                l_date: "Tanggal Pelaksanaan", l_sales_start: "⏱️ Tanggal Buka Penjualan (Opsional)", l_sales_sub: "*Isi jika ingin menggunakan fitur \"Coming Soon\" (Countdown). Kosongkan jika tiket bisa langsung dibeli sekarang.",
                l_desc: "Deskripsi Lengkap",
                l_lineup_h: "DAFTAR LINE-UP (Opsional)", l_lineup_sub: "Tambahkan artis. Jika dikosongkan, akan muncul 'Lineup belum tersedia' di halaman detail.", l_art_name: "Nama Artis / Band", btn_add_art: "+ TAMBAH ARTIS LAIN",
                l_stock: "TOTAL STOK", l_tix_unit: "TIKET", l_t_cat: "Kategori Tiket", l_t_price: "Harga Tiket", l_t_stock: "Jumlah Stok",
                l_promo: "PROMO AKTIF", p_none: "-- Tanpa Promo --",
                opt_cat: "Pilih Kategori", m_music: "Musik", m_sport: "Olahraga", m_semi: "Seminar", m_ent: "Hiburan",
                opt_type: "Pilih Jenis", m_pub: "Publik", m_priv: "Private",
                btn_next: "NEXT", btn_back: "BACK", btn_add_tix: "+ TAMBAH TIKET",
                p_name: "Contoh : Specteve 2026", p_desc: "Detail acara...", p_t_cat: "Contoh: VIP / Festival",
                ov_l_name: "Nama Event & Tanggal", ov_l_list: "Daftar Tiket & Stok", ov_l_promo: "PROMO TERPASANG",
                btn_publish: "Publish Tiket Anda!"
            },
            en: {
                s1: "Identity", s2: "Details", s3: "Tickets", s4: "Review",
                h1: "I. Visual & Identity", h2: "II. Event Information", h3: "III. Tickets & Promo", h4: "IV. Final Review",
                l_banner: "Main Event Banner (1 Photo)", l_name: "Event Name", l_cat: "Category", l_type: "Type",
                l_date: "Execution Date", l_sales_start: "⏱️ Ticket Sales Start Date (Optional)", l_sales_sub: "*Fill this for 'Coming Soon' (Countdown) feature. Leave empty if tickets are available immediately.",
                l_desc: "Full Description",
                l_lineup_h: "LINE-UP LIST (Optional)", l_lineup_sub: "Add artists. If empty, 'Lineup unavailable' will show on details page.", l_art_name: "Artist / Band Name", btn_add_art: "+ ADD ANOTHER ARTIST",
                l_stock: "TOTAL STOCK", l_tix_unit: "TICKETS", l_t_cat: "Ticket Category", l_t_price: "Ticket Price", l_t_stock: "Stock Quantity",
                l_promo: "ACTIVE PROMO", p_none: "-- No Promo --",
                opt_cat: "Select Category", m_music: "Music", m_sport: "Sports", m_semi: "Seminar", m_ent: "Entertainment",
                opt_type: "Select Type", m_pub: "Public", m_priv: "Private",
                btn_next: "NEXT", btn_back: "BACK", btn_add_tix: "+ ADD TICKET",
                p_name: "Example : Specteve 2026", p_desc: "Event details...", p_t_cat: "Example: VIP / Festival",
                ov_l_name: "Event Name & Date", ov_l_list: "Ticket List & Stock", ov_l_promo: "PROMO APPLIED",
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
            if(btnActive) btnActive.classList.add('active');
            localStorage.setItem('lang', lang);
        }

        function addLineup() {
            const container = document.getElementById('lineup-container');
            const l_art_name = translations[currentLang].l_art_name;
            const html = `
                <div class="lineup-box">
                    <button type="button" class="remove-btn" onclick="this.parentElement.remove()">X</button>
                    <label class="field-label" data-key="l_art_name">${l_art_name}</label>
                    <input type="text" name="lineup_name[]" placeholder="Contoh: FSTVLST">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div><label class="field-label">Link Instagram</label><input type="url" name="lineup_ig[]" placeholder="https://instagram.com/..."></div>
                        <div><label class="field-label">Link Spotify</label><input type="url" name="lineup_spotify[]" placeholder="https://spotify.com/..."></div>
                    </div>
                </div>`;
            container.insertAdjacentHTML('beforeend', html);
        }

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
        
        function formatDots(input) { let val = input.value.replace(/\D/g, ""); input.value = val.replace(/\B(?=(\d{3})+(?!\d))/g, "."); }
        
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

            if(t === 4) {
                document.getElementById('ov_name').innerText = (document.getElementById('f_name').value || "-") + " | " + (document.getElementById('f_date').value || "TBA");
                document.getElementById('ov_cat').innerText = document.getElementById('f_cat').value || "-";
                document.getElementById('ov_type').innerText = document.getElementById('f_type').value || "-";
                
                const firstImg = document.querySelector('#banner-preview img'); 
                if(firstImg) document.getElementById('ov_banner').src = firstImg.src;
                
                document.getElementById('ov_desc').innerText = document.getElementById('f_desc').value || "-";
                
                let listHtml = ""; 
                document.querySelectorAll('#list_t .tix-item').forEach(item => { 
                    let text = item.querySelector('div').innerHTML.replace(/<button.*<\/button>/, ""); 
                    listHtml += `<div class="tix-item" style="padding: 0.8rem; margin-bottom: 5px; border-style: dashed;"><div>${text}</div></div>`; 
                });
                document.getElementById('ov_tix').innerHTML = listHtml || "-";
                
                const promoSelect = document.getElementById('f_promo'); let promoText = promoSelect.options[promoSelect.selectedIndex].text;
                if(promoSelect.value == "0") promoText = translations[currentLang].p_none; document.getElementById('ov_promo_val').innerText = promoText;
            }
        }
        
        function addT() {
            const n = document.getElementById('t_name'), p = document.getElementById('t_price'), s = document.getElementById('t_stock'), list = document.getElementById('list_t');
            if(!n.value || !s.value) {
                alert("Isi nama kategori dan jumlah stok tiket dulu!");
                return;
            }
            const d = document.createElement('div'); d.className = 'tix-item';
            d.innerHTML = `<input type="hidden" name="t_names[]" value="${n.value}"><input type="hidden" name="t_prices[]" value="${p.value || 0}"><input type="hidden" name="t_stocks[]" value="${s.value}"><div><b>${n.value}</b><br><small>Rp ${p.value || 0} | Stok: ${s.value}</small></div><button type="button" onclick="this.parentElement.remove(); upStok();" style="background:none; border:none; color:#ff4444; font-weight:900; cursor:pointer;">X</button>`;
            list.appendChild(d); upStok(); n.value = ""; p.value = ""; s.value = "";
        }
        
        function upStok() { let t = 0; document.querySelectorAll('#list_t .tix-item small').forEach(el => { let parts = el.innerText.split('Stok: '); if(parts[1]) t += parseInt(parts[1].replace(/\./g, "")); }); document.getElementById('stok_val').innerText = t.toLocaleString('id-ID'); }
    </script>
</x-app-layout>