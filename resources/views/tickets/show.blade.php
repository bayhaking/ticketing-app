<x-app-layout>
    @section('title', $event->name . ' - SPECTIX')
    <style>
        .hero-banner { width: 100%; height: 400px; background-size: cover; background-position: center; border-radius: 2rem; margin-bottom: 1.5rem; position: relative; overflow: hidden; border: 1px solid var(--border); cursor: zoom-in; }
        .hero-overlay { position: absolute; inset: 0; background: var(--hero-shadow); pointer-events: none; z-index: 1; }
        
        .show-slider { position: relative; width: 100%; height: 400px; border-radius: 2rem; overflow: hidden; margin-bottom: 1.5rem; border: 1px solid var(--border); background: #000; }
        .show-track { display: flex; height: 100%; transition: transform 0.6s ease-in-out; }
        .show-track img { width: 100%; height: 100%; object-fit: cover; flex-shrink: 0; cursor: zoom-in; opacity: 0.85; transition: 0.3s; }
        .show-track img:hover { opacity: 1; }
        .slider-arrow { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.6); color: white; border: 1px solid var(--accent); width: 45px; height: 45px; border-radius: 50%; cursor: pointer; z-index: 10; font-weight: 900; transition: 0.3s; display: flex; align-items: center; justify-content: center; }
        .slider-arrow:hover { background: var(--accent); color: #000; }
        .arrow-left { left: 15px; }
        .arrow-right { right: 15px; }
        .slider-dots { position: absolute; bottom: 15px; left: 50%; transform: translateX(-50%); display: flex; gap: 8px; z-index: 10; }
        .dot { width: 10px; height: 10px; border-radius: 50%; background: rgba(255,255,255,0.4); cursor: pointer; transition: 0.3s; }
        .dot.active { background: var(--accent); box-shadow: 0 0 10px var(--accent); transform: scale(1.3); }

        .content-grid { display: grid; grid-template-columns: 2fr 1.2fr; gap: 2.5rem; align-items: start; }
        
        .event-meta { color: var(--text-main); font-weight: 800; font-size: 0.9rem; text-transform: uppercase; display: flex; gap: 12px; margin-bottom: 1.5rem; opacity: 0.9; }
        .event-title { color: var(--text-main); font-size: 3.2rem; font-weight: 900; margin: 0 0 10px 0; line-height: 1.1; font-style: italic; letter-spacing: -1px; }
        .event-desc { color: var(--text-sub); line-height: 1.5; font-size: 1rem; margin-bottom: 1.5rem; }

        .btn-lineup { background: transparent; border: 2px solid var(--accent); color: var(--accent); padding: 10px 20px; border-radius: 100px; font-weight: 900; cursor: pointer; font-size: 0.9rem; text-transform: uppercase; display: inline-flex; align-items: center; gap: 8px; transition: 0.3s; }
        .btn-lineup:hover { background: var(--accent); color: #fff; box-shadow: var(--glow); }

        .checkout-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 2rem; padding: 2.5rem; position: sticky; top: 100px; border-top: 5px solid var(--accent); }
        .section-title { color: var(--text-main); font-size: 1.2rem; font-weight: 900; margin-bottom: 1.5rem; text-transform: uppercase; }
        
        .tix-option { background: var(--bg-input); border: 1px solid var(--border); border-radius: 1.2rem; padding: 1.2rem; margin-bottom: 1rem; cursor: pointer; transition: 0.3s; }
        .tix-option.selected { border-color: var(--accent); background: rgba(29, 185, 84, 0.1); box-shadow: var(--glow); }
        .tix-name { color: var(--text-main); font-weight: 800; font-size: 1.1rem; }
        .tix-price { color: var(--accent); font-weight: 900; }
        .tix-stock { color: var(--tix-stock-color); font-size: 0.75rem; margin-top: 5px; font-weight: 600; }
        
        .qty-controls { display: flex; align-items: center; justify-content: space-between; background: var(--bg-input); padding: 10px; border-radius: 100px; margin: 1.5rem 0; border: 1px solid var(--border); }
        .qty-btn { background: var(--bg-card); border: 1px solid var(--border); color: var(--text-main); width: 35px; height: 35px; border-radius: 50%; cursor: pointer; font-weight: 900; }
        
        #total_display { color: var(--accent) !important; font-weight: 900; font-size: 1.5rem; text-shadow: var(--glow); }
        
        .btn-checkout { background: var(--accent); color: #000; width: 100%; padding: 1.2rem; border-radius: 100px; border: none; font-weight: 900; text-transform: uppercase; cursor: pointer; font-style: italic; font-size: 1.1rem; box-shadow: var(--glow); }
        .btn-checkout:disabled { opacity: 0.2; cursor: not-allowed; box-shadow: none; }

        .modal-lineup-box { background: var(--bg-card); border: 1px solid var(--border); border-top: 5px solid var(--accent); border-radius: 1.5rem; padding: 2.5rem; width: 90%; max-width: 500px; animation: zoom 0.3s; position: relative; }
        .artist-card { display: flex; justify-content: space-between; align-items: center; background: var(--bg-input); padding: 1rem 1.5rem; border-radius: 1rem; margin-bottom: 10px; border: 1px solid var(--border); }
        .artist-name { color: var(--text-main); font-weight: 800; font-size: 1.1rem; }
        .social-icons { display: flex; gap: 10px; }
        .icon-btn { display: flex; align-items: center; justify-content: center; width: 35px; height: 35px; border-radius: 50%; background: var(--bg-main); border: 1px solid var(--border); color: var(--text-sub); text-decoration: none; transition: 0.3s; }
        .icon-btn:hover { border-color: var(--accent); color: var(--accent); transform: scale(1.1); }
    </style>

    <div id="lineupModal" class="overlay-modal">
        <div class="modal-lineup-box">
            <span class="close-modal" onclick="closeModal('lineupModal')">&times;</span>
            <h2 style="color: var(--text-main); font-weight: 900; margin-bottom: 1.5rem; text-transform: uppercase; font-style: italic;">SPECTACULAR LINE-UP</h2>
            
            <div id="lineup-list">
                @php $lineups = $event->lineups ?? []; @endphp
                @if(count($lineups) > 0)
                    @foreach($lineups as $artist)
                        <div class="artist-card">
                            <span class="artist-name">{{ $artist->name }}</span>
                            <div class="social-icons">
                                @if($artist->ig)
                                <a href="{{ $artist->ig }}" target="_blank" class="icon-btn" title="Instagram">
                                    <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                                </a>
                                @endif
                                @if($artist->spotify)
                                <a href="{{ $artist->spotify }}" target="_blank" class="icon-btn" title="Spotify">
                                    <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.45 17.34c-.2.325-.635.43-1.005.22-2.75-1.685-6.22-2.065-10.31-1.13-.395.09-.765-.16-.855-.555-.09-.395.16-.765.555-.855 4.47-1.025 8.32-.59 11.405 1.3.365.23.475.67.21 1.02zm1.435-3.185c-.25.405-.78.53-1.19.27-3.16-1.94-8.03-2.53-11.75-1.385-.47.145-.965-.115-1.11-.585-.145-.47.115-.965.585-1.11 4.31-1.33 9.71-.67 13.34 1.56.41.25.53.79.125 1.25zm.135-3.34c-3.79-2.25-10.05-2.455-13.68-1.36-.58.175-1.18-.155-1.355-.735-.175-.58.155-1.18.735-1.355 4.25-1.28 11.23-1.035 15.68 1.61.53.315.7 1.005.385 1.535-.315.53-1.005.7-1.535.385z"/></svg>
                                </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div style="text-align: center; padding: 2rem 0; color: var(--text-sub);">
                        <div style="font-size: 3rem; margin-bottom: 10px;">🥲</div>
                        <p style="font-weight: 800;" data-key="no_lineup">Lineup belum tersedia nih :(</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div style="background-color: var(--bg-main); min-height: 100vh; padding: 2rem 0;">
        <div class="max-w-6xl mx-auto px-6">
            <a href="/" style="color: var(--text-sub); text-decoration: none; font-weight: 800; font-size: 0.8rem; margin-bottom: 1.5rem; display: inline-block;" data-key="back_cat">← KEMBALI KE KATALOG</a>
            
            @php 
                $allPhotos = [];
                if($event->banner) $allPhotos[] = asset('storage/'.$event->banner);
                if(is_array($event->gallery)) {
                    foreach($event->gallery as $g) {
                        $allPhotos[] = asset('storage/'.$g);
                    }
                }
                
                // Cek status berdasarkan Waktu Server
                $isComingSoon = $event->sales_start_date && now()->lessThan($event->sales_start_date);
            @endphp

            @if(count($allPhotos) > 0)
            <div class="show-slider">
                <div class="hero-overlay"></div>
                @if(count($allPhotos) > 1)
                <button class="slider-arrow arrow-left" onclick="moveSlide(-1)">❮</button>
                @endif
                
                <div class="show-track" id="detail-track">
                    @foreach($allPhotos as $index => $photo)
                        <img src="{{ $photo }}" alt="Gallery {{ $index }}" onclick="openModal('imgModal', this.src)">
                    @endforeach
                </div>
                
                @if(count($allPhotos) > 1)
                <button class="slider-arrow arrow-right" onclick="moveSlide(1)">❯</button>
                <div class="slider-dots" id="detail-dots">
                    @foreach($allPhotos as $index => $photo)
                        <div class="dot {{ $index == 0 ? 'active' : '' }}" onclick="goToSlide({{ $index }})"></div>
                    @endforeach
                </div>
                @endif
            </div>
            @else
            <div class="hero-banner" style="background-image: url('https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?auto=format&fit=crop&q=80');">
                <div class="hero-overlay"></div>
            </div>
            @endif

            <div class="content-grid">
                <div>
                    <div class="event-meta">
                        <span class="dyn-trans" data-val="{{ $event->category }}">{{ $event->category }}</span> • 
                        <span class="dyn-trans" data-val="{{ $event->type }}">{{ $event->type }}</span> • 
                        <span>{{ date('d M Y', strtotime($event->date)) }}</span>
                    </div>
                    <h1 class="event-title">{{ $event->name }}</h1>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin: 1.5rem 0;">
                        <div style="height: 4px; width: 50px; background: var(--accent);"></div>
                        
                        <button class="btn-lineup" onclick="openModal('lineupModal')">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path></svg>
                            <span data-key="btn_lineup">LIHAT LINE-UP</span>
                        </button>
                    </div>

                    <h3 class="section-title" data-key="h_detail">Detail Acara</h3>
                    <div class="event-desc">{!! nl2br(e($event->description)) !!}</div>
                </div>

                <div class="checkout-card">
                    
                    @if($event->status !== 'upcoming')
                        <div style="background: rgba(255, 68, 68, 0.1); border: 1px solid #ff4444; color: #ff6b6b; padding: 1.5rem; border-radius: 12px; text-align: center; margin-top: 1rem;">
                            <h3 style="margin: 0 0 10px; font-weight: 900; font-style: italic;">PENJUALAN DITUTUP</h3>
                            <p style="margin: 0; font-size: 0.9rem;">Mohon maaf, penjualan tiket untuk event ini sudah ditutup atau acara telah selesai.</p>
                        </div>
                    @else
                        <div id="countdown-section" style="display: {{ $isComingSoon ? 'block' : 'none' }}; text-align: center; padding: 1rem 0;">
                            <h3 style="color: var(--accent); font-weight: 900; font-style: italic; margin-bottom: 10px; font-size: 1.5rem;" data-key="cd_title">TIKET BELUM DIBUKA!</h3>
                            <p style="color: var(--text-sub); margin-bottom: 25px; font-weight: 600;" data-key="cd_sub">Penjualan tiket untuk event ini akan dimulai dalam:</p>
                            
                            <div style="display: flex; justify-content: center; gap: 10px; font-size: 1.8rem; font-weight: 900; color: var(--text-main);">
                                <div style="background: var(--bg-input); border: 1px solid var(--border); padding: 15px 10px; border-radius: 15px; min-width: 70px; box-shadow: inset 0 4px 10px rgba(0,0,0,0.5);"><span id="cd-days">00</span><div style="font-size: 0.65rem; color: var(--accent); margin-top: 5px;" data-key="l_hari">HARI</div></div>
                                <div style="background: var(--bg-input); border: 1px solid var(--border); padding: 15px 10px; border-radius: 15px; min-width: 70px; box-shadow: inset 0 4px 10px rgba(0,0,0,0.5);"><span id="cd-hours">00</span><div style="font-size: 0.65rem; color: var(--accent); margin-top: 5px;" data-key="l_jam">JAM</div></div>
                                <div style="background: var(--bg-input); border: 1px solid var(--border); padding: 15px 10px; border-radius: 15px; min-width: 70px; box-shadow: inset 0 4px 10px rgba(0,0,0,0.5);"><span id="cd-mins">00</span><div style="font-size: 0.65rem; color: var(--accent); margin-top: 5px;" data-key="l_mnt">MENIT</div></div>
                                <div style="background: var(--bg-input); border: 1px solid var(--border); padding: 15px 10px; border-radius: 15px; min-width: 70px; box-shadow: inset 0 4px 10px rgba(0,0,0,0.5);"><span id="cd-secs">00</span><div style="font-size: 0.65rem; color: var(--accent); margin-top: 5px;" data-key="l_dtk">DETIK</div></div>
                            </div>
                        </div>

                        <div id="checkout-section" style="display: {{ $isComingSoon ? 'none' : 'block' }};">
                            <h3 class="section-title" data-key="h_tix">Pilih Tiket</h3>
                            <form method="POST" action="{{ route('checkout.prepare') }}">
                                @csrf
                                <input type="hidden" name="event_id" value="{{ $event->id }}">
                                
                                <div id="ticket-list">
                                    @foreach($event->ticketTypes as $ticket)
                                        <div class="tix-option" onclick="selectTicket(this, {{ $ticket->id }}, {{ $ticket->price }})">
                                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                                <div class="tix-name">{{ $ticket->name }}</div>
                                                <div class="tix-price">Rp {{ number_format($ticket->price, 0, ',', '.') }}</div>
                                            </div>
                                            <div class="tix-stock"><span data-key="l_stock">Sisa Kuota</span>: {{ $ticket->stock }}</div>
                                        </div>
                                    @endforeach
                                </div>
                                <input type="hidden" id="selected_ticket_id" name="ticket_id">
                                
                                <div class="qty-controls">
                                    <span style="color: var(--text-sub); font-weight: 800; font-size: 0.7rem; margin-left:10px;" data-key="l_qty">JUMLAH:</span>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <button type="button" class="qty-btn" onclick="updateQty(-1)">-</button>
                                        <span class="qty-val" id="qty_display" style="color: var(--text-main);">1</span>
                                        <input type="hidden" id="qty_input" name="quantity" value="1">
                                        <button type="button" class="qty-btn" onclick="updateQty(1)">+</button>
                                    </div>
                                </div>

                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.2rem;">
                                    <span style="color: var(--text-sub); font-weight: 800; font-size: 0.9rem;" data-key="l_total">TOTAL BAYAR</span>
                                    <span id="total_display">Rp 0</span>
                                </div>
                                <button type="submit" class="btn-checkout" id="btn_submit" disabled data-key="btn_buy">CHECKOUT SEKARANG</button>
                            </form>
                        </div>
                    @endif
                    </div>
            </div>
        </div>
    </div>

    @stack('scripts')
    <script>
        // SCRIPT COUNTDOWN (Tanpa Reload, Cukup Tukar CSS Display)
        @if($event->sales_start_date)
            // Pakai Unix Timestamp dari Server biar akurat 100% dan nggak peduli zona waktu browser!
            const countDownDate = {{ $event->sales_start_date->getTimestamp() * 1000 }};

            const cdInterval = setInterval(function() {
                const now = new Date().getTime();
                const distance = countDownDate - now;

                if (distance <= 0) {
                    clearInterval(cdInterval);
                    // HILANGKAN Countdown, MUNCULKAN Form Checkout!
                    const cdSection = document.getElementById("countdown-section");
                    const chkSection = document.getElementById("checkout-section");
                    if(cdSection) cdSection.style.display = "none";
                    if(chkSection) chkSection.style.display = "block";
                } else {
                    const elDays = document.getElementById("cd-days");
                    if(elDays) {
                        elDays.innerText = Math.floor(distance / (1000 * 60 * 60 * 24)).toString().padStart(2, '0');
                        document.getElementById("cd-hours").innerText = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)).toString().padStart(2, '0');
                        document.getElementById("cd-mins").innerText = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60)).toString().padStart(2, '0');
                        document.getElementById("cd-secs").innerText = Math.floor((distance % (1000 * 60)) / 1000).toString().padStart(2, '0');
                    }
                }
            }, 1000);
        @endif

        // SCRIPT SLIDER DETAIL
        let detSlide = 0;
        const totalDetSlides = {{ count($allPhotos) }};
        const detTrack = document.getElementById('detail-track');
        const detDots = document.querySelectorAll('#detail-dots .dot');
        let detInterval;

        function updateDetSlider() {
            if(!detTrack || totalDetSlides <= 1) return;
            detTrack.style.transform = `translateX(-${detSlide * 100}%)`;
            detDots.forEach(dot => dot.classList.remove('active'));
            if(detDots[detSlide]) detDots[detSlide].classList.add('active');
        }

        function moveSlide(dir) {
            detSlide = (detSlide + dir + totalDetSlides) % totalDetSlides;
            updateDetSlider();
            resetDetTimer();
        }

        function goToSlide(index) {
            detSlide = index;
            updateDetSlider();
            resetDetTimer();
        }

        function startDetTimer() {
            if(totalDetSlides > 1) {
                detInterval = setInterval(() => { moveSlide(1); }, 4000);
            }
        }

        function resetDetTimer() {
            clearInterval(detInterval);
            startDetTimer();
        }

        startDetTimer();

        // LOGIKA HARGA TIKET
        let currentPrice = 0; let qty = 1;
        function selectTicket(element, id, price) {
            document.querySelectorAll('.tix-option').forEach(el => el.classList.remove('selected'));
            element.classList.add('selected');
            document.getElementById('selected_ticket_id').value = id;
            currentPrice = price;
            document.getElementById('btn_submit').disabled = false;
            calculateTotal();
        }

        function updateQty(change) {
            let newQty = qty + change;
            if (newQty >= 1 && newQty <= 5) {
                qty = newQty;
                document.getElementById('qty_display').innerText = qty;
                document.getElementById('qty_input').value = qty;
                calculateTotal();
            }
        }

        function calculateTotal() {
            let total = currentPrice * qty;
            document.getElementById('total_display').innerText = 'Rp ' + total.toLocaleString('id-ID');
        }

        // TERJEMAHAN
        const translations = {
            id: { back_cat: "← KEMBALI KE KATALOG", h_detail: "Detail Acara", btn_lineup: "LIHAT LINE-UP", no_lineup: "Lineup belum tersedia nih :(", h_tix: "Pilih Tiket", l_stock: "Sisa Kuota", l_qty: "JUMLAH:", l_total: "TOTAL BAYAR", btn_buy: "CHECKOUT SEKARANG", cd_title: "TIKET BELUM DIBUKA!", cd_sub: "Penjualan tiket untuk event ini akan dimulai dalam:", l_hari: "HARI", l_jam: "JAM", l_mnt: "MENIT", l_dtk: "DETIK" },
            en: { back_cat: "← BACK TO CATALOG", h_detail: "Event Detail", btn_lineup: "VIEW LINE-UP", no_lineup: "Lineup is not available yet :(", h_tix: "Select Ticket", l_stock: "Stock Left", l_qty: "QUANTITY:", l_total: "TOTAL", btn_buy: "CHECKOUT NOW", cd_title: "TICKETS NOT OPEN YET!", cd_sub: "Ticket sales for this event will start in:", l_hari: "DAYS", l_jam: "HOURS", l_mnt: "MINS", l_dtk: "SECS" }
        };

        const dynTranslations = {
            id: { 'Musik': 'Musik', 'Olahraga': 'Olahraga', 'Seminar': 'Seminar', 'Hiburan': 'Hiburan', 'Publik': 'Publik', 'Private': 'Private' },
            en: { 'Musik': 'Music', 'Olahraga': 'Sports', 'Seminar': 'Seminar', 'Hiburan': 'Entertainment', 'Publik': 'Public', 'Private': 'Private' }
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
            document.querySelectorAll('.dyn-trans').forEach(el => {
                const val = el.getAttribute('data-val');
                if (dynTranslations[lang] && dynTranslations[lang][val]) el.innerText = dynTranslations[lang][val];
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const savedLang = localStorage.getItem('lang') || 'id';
            setLang(savedLang);
        });
    </script>
</x-app-layout>