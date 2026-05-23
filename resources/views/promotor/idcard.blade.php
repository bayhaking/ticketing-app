<x-app-layout>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <style>
        .id-studio-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: start; margin-top: 1rem; }
        .id-input-group { margin-bottom: 1rem; }
        .id-input-group label {
            display: flex; justify-content: space-between; align-items: center;
            font-size: 0.8rem; font-weight: 800; color: var(--text-sub);
            margin-bottom: 5px; text-transform: uppercase;
        }
        .id-input-group label .zoom-value {
            color: #1DB954; font-weight: 900; text-transform: none;
        }
        .id-input-group input[type="text"] {
            width: 100%; background: var(--bg-input); border: 1px solid var(--border); color: var(--text-main);
            padding: 10px; border-radius: 8px; font-weight: 600; outline: none;
        }
        .id-input-group input[type="text"]:focus { border-color: var(--accent); }
        .id-input-group input[type="range"] {
            width: 100%;
            accent-color: #1DB954;
        }

        /* ===== FILE UPLOAD WRAPPER (dengan tombol hapus) ===== */
        .file-upload-wrapper {
            display: flex; gap: 8px; align-items: stretch;
        }
        .file-upload-wrapper input[type="file"] {
            flex: 1; background: var(--bg-input); border: 1px solid var(--border); color: var(--text-main);
            padding: 10px; border-radius: 8px; font-weight: 600; outline: none;
            min-width: 0;
        }
        .btn-clear-upload {
            flex-shrink: 0;
            width: 42px; height: auto;
            background: #2a2a2a;
            border: 1px solid #444;
            color: #ff6b6b;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 900;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .btn-clear-upload:hover {
            background: #ff4444;
            color: #fff;
            border-color: #ff4444;
        }
        .btn-clear-upload:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }

        /* ===== KARTU ID ===== */
        .id-card-wrapper {
            width: 280px; height: 420px;
            background-color: #111111 !important;
            border-radius: 15px;
            position: relative; overflow: hidden;
            border: 2px solid #1DB954 !important;
            color: #ffffff !important; margin: 0 auto;
            -webkit-font-smoothing: antialiased; box-sizing: border-box;
        }

        .id-bg {
            position: absolute; inset: 0; width: 100%; height: 100%;
            background-size: cover; background-repeat: no-repeat;
            background-position: center;
            opacity: 0.55; z-index: 1; cursor: grab;
        }

        .id-top {
            position: absolute; top: 18px; left: 16px; right: 16px;
            display: flex; justify-content: space-between; align-items: center;
            z-index: 4; width: calc(100% - 32px); pointer-events: none;
        }

        .id-logo-spectix {
            height: 24px; width: auto; object-fit: contain;
            display: block;
        }

        .id-logo-event-slot {
            height: 32px; min-width: 60px; max-width: 90px;
            display: flex; align-items: center; justify-content: center;
        }
        .id-logo-event-placeholder {
            font-size: 9px; font-weight: 800; color: #555;
            border: 1px dashed #444; padding: 6px 12px;
            border-radius: 4px; letter-spacing: 1px;
        }
        .id-logo-event {
            height: 32px; max-width: 90px; object-fit: contain;
            display: none;
        }
        .id-logo-event.is-loaded { display: block; }

        .id-body {
            position: absolute; top: 90px; left: 0; width: 100%;
            display: flex; flex-direction: column; align-items: center;
            z-index: 3; padding: 0 12px; box-sizing: border-box;
        }

        .id-staff-photo-container {
            width: 140px; height: 140px; border-radius: 50%;
            border: 4px solid #1DB954 !important;
            background: #222222 !important;
            margin-bottom: 18px; overflow: hidden; position: relative;
            cursor: grab; z-index: 5; box-sizing: border-box;
        }
        .id-staff-photo-dragger {
            width: 100%; height: 100%;
            background-size: cover; background-position: center;
            background-repeat: no-repeat; display: block;
        }

        .id-name {
            color: #ffffff !important; font-size: 1.35rem; font-weight: 900;
            text-transform: uppercase; margin: 0; text-align: center;
            line-height: 1.15; width: 100%; word-wrap: break-word;
            letter-spacing: 0.5px;
        }
        .id-role {
            color: #1DB954 !important; font-size: 0.8rem; font-weight: 800;
            text-transform: uppercase; margin-top: 10px;
            background: rgba(0,0,0,0.85) !important;
            padding: 5px 18px; border-radius: 100px;
            border: 1px solid #1DB954 !important;
            display: inline-block; white-space: nowrap;
            letter-spacing: 0.5px;
        }

        .dragging { cursor: grabbing !important; }

        @media (max-width: 768px) {
            .id-studio-grid { grid-template-columns: 1fr; }
        }
    </style>

    <div style="padding: 3rem;">
        <div class="max-w-6xl mx-auto">

            <h1 style="font-size: 2.5rem; font-weight: 900; font-style: italic; margin-bottom: 1rem; color: #ffffff;">
                🪪 STUDIO ID CARD
            </h1>
            <p style="color: var(--text-sub); font-size: 1rem; margin-bottom: 2rem;">
                Sesuaikan desain ID card sesuai kebutuhan event Anda, atur posisi foto dan background pada area preview, lalu simpan ID card dalam format PNG resolusi tinggi (HQ).
            </p>

            <div class="id-studio-grid">
                <!-- ===== PANEL KONTROL ===== -->
                <div style="background: var(--bg-card); padding: 2rem; border-radius: 1.5rem; border: 1px solid var(--border);">

                    <!-- Upload Foto Staff -->
                    <div class="id-input-group">
                        <label>Foto Staff (Bisa digeser di preview)</label>
                        <div class="file-upload-wrapper">
                            <input type="file" accept="image/*" id="inp_photo">
                            <button type="button" class="btn-clear-upload" id="clear_photo" disabled title="Hapus foto">✕</button>
                        </div>
                    </div>

                    <!-- Zoom Foto Staff -->
                    <div class="id-input-group">
                        <label>
                            <span>🔍 Zoom Foto Staff</span>
                            <span class="zoom-value" id="zoom_photo_value">100%</span>
                        </label>
                        <input type="range" min="30" max="500" value="100" id="zoom_photo">
                    </div>

                    <!-- Upload Background -->
                    <div class="id-input-group">
                        <label>Background Card (Bisa digeser di preview)</label>
                        <div class="file-upload-wrapper">
                            <input type="file" accept="image/*" id="inp_bg">
                            <button type="button" class="btn-clear-upload" id="clear_bg" disabled title="Hapus background">✕</button>
                        </div>
                    </div>

                    <!-- Zoom Background -->
                    <div class="id-input-group">
                        <label>
                            <span>🔍 Zoom Background</span>
                            <span class="zoom-value" id="zoom_bg_value">100%</span>
                        </label>
                        <input type="range" min="30" max="500" value="100" id="zoom_bg">
                    </div>

                    <!-- Logo Event -->
                    <div class="id-input-group">
                        <label>Logo Event (Pojok Kanan Atas)</label>
                        <div class="file-upload-wrapper">
                            <input type="file" accept="image/*" id="inp_logo">
                            <button type="button" class="btn-clear-upload" id="clear_logo" disabled title="Hapus logo">✕</button>
                        </div>
                    </div>

                    <!-- Brightness Background -->
                    <div class="id-input-group">
                        <label>
                            <span>💡 Brightness Background</span>
                            <span class="zoom-value" id="bright_value">55%</span>
                        </label>
                        <input type="range" min="20" max="100" value="55" id="inp_bright">
                    </div>

                    <!-- Nama (kosong default) -->
                    <div class="id-input-group">
                        <label>Nama Lengkap Staff</label>
                        <input type="text" id="inp_name" placeholder="Masukkan nama lengkap staff" value="">
                    </div>

                    <!-- Role (kosong default) -->
                    <div class="id-input-group">
                        <label>Posisi / Role</label>
                        <input type="text" id="inp_role" placeholder="Masukkan posisi atau jabatan" value="">
                    </div>

                    <button id="btn_download" style="width: 100%; background: #1DB954; color: #fff; padding: 15px; border-radius: 10px; border: none; font-weight: 900; cursor: pointer; margin-top: 10px; transition: 0.3s; font-size: 14px;">
                        💾 DOWNLOAD HQ PNG
                    </button>

                    <p style="margin: 12px 0 0; font-size: 11px; color: var(--text-sub); line-height: 1.5;">
                        Petunjuk penggunaan: klik dan geser pada area foto atau background di preview untuk menyesuaikan posisi gambar. Gunakan slider zoom atau scroll-wheel untuk memperbesar tampilan, lalu klik tombol Download HQ PNG untuk menyimpan ID card.
                    </p>
                </div>

                <!-- ===== PREVIEW KARTU ===== -->
                <div style="display: flex; justify-content: center; align-items: center; background: #000; padding: 3rem; border-radius: 1.5rem; border: 1px dashed var(--border);">
                    <div style="width: 280px; height: 420px; position: relative;">
                        <div class="id-card-wrapper" id="card-to-save">

                            <div id="preview_bg" class="id-bg" style="background-image: none;"></div>

                            <div class="id-top">
                                <img src="{{ asset('logo0.png') }}"
                                     alt="SPECTIX"
                                     class="id-logo-spectix"
                                     id="logo_spectix"
                                     crossorigin="anonymous">

                                <div class="id-logo-event-slot">
                                    <img src="" id="preview_event_logo" class="id-logo-event" alt="">
                                    <div class="id-logo-event-placeholder" id="logo_event_placeholder">LOGO</div>
                                </div>
                            </div>

                            <div class="id-body">
                                <div class="id-staff-photo-container">
                                    <div id="preview_photo_dragger" class="id-staff-photo-dragger"></div>
                                </div>
                                <div class="id-name" id="display_name">NAMA STAFF</div>
                                <div>
                                    <span class="id-role" id="display_role">POSISI</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ============================================================
        // STATE
        // ============================================================
        const state = {
            bg:    { img: null, scale: 1, x: 0.5, y: 0.5 },
            photo: { img: null, scale: 1, x: 0.5, y: 0.5 }
        };

        // ============================================================
        // RENDER PREVIEW
        // ============================================================
        function renderBg() {
            const el = document.getElementById('preview_bg');
            if (!state.bg.img) {
                el.style.backgroundImage = 'none';
                return;
            }
            const s = state.bg;
            el.style.backgroundImage = `url('${s.img.src}')`;
            el.style.backgroundSize = `${s.scale * 100}% auto`;
            el.style.backgroundPosition = `${s.x * 100}% ${s.y * 100}%`;
        }

        function renderPhoto() {
            const el = document.getElementById('preview_photo_dragger');
            if (!state.photo.img) {
                el.style.backgroundImage = 'none';
                el.style.background = '#333';
                return;
            }
            const s = state.photo;
            el.style.backgroundImage = `url('${s.img.src}')`;
            el.style.backgroundSize = `${s.scale * 100}% auto`;
            el.style.backgroundPosition = `${s.x * 100}% ${s.y * 100}%`;
        }

        function syncZoomSlider(key) {
            const sliderId = key === 'bg' ? 'zoom_bg' : 'zoom_photo';
            const valueId  = key === 'bg' ? 'zoom_bg_value' : 'zoom_photo_value';
            const pct = Math.round(state[key].scale * 100);
            document.getElementById(sliderId).value = pct;
            document.getElementById(valueId).textContent = pct + '%';
        }

        // ============================================================
        // LOAD FILE → IMAGE OBJECT
        // ============================================================
        function loadImageToState(file, key, afterCallback) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.crossOrigin = 'anonymous';
                img.onload = function() {
                    state[key].img = img;
                    state[key].scale = 1;
                    state[key].x = 0.5;
                    state[key].y = 0.5;
                    afterCallback();
                    syncZoomSlider(key);
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }

        // ============================================================
        // CLEAR / RESET FILE UPLOAD
        // ============================================================
        function clearUpload(type) {
            if (type === 'photo') {
                state.photo.img = null;
                state.photo.scale = 1;
                state.photo.x = 0.5;
                state.photo.y = 0.5;
                document.getElementById('inp_photo').value = '';
                renderPhoto();
                syncZoomSlider('photo');
                document.getElementById('clear_photo').disabled = true;
            } else if (type === 'bg') {
                state.bg.img = null;
                state.bg.scale = 1;
                state.bg.x = 0.5;
                state.bg.y = 0.5;
                document.getElementById('inp_bg').value = '';
                renderBg();
                syncZoomSlider('bg');
                document.getElementById('clear_bg').disabled = true;
            } else if (type === 'logo') {
                const imgEl = document.getElementById('preview_event_logo');
                imgEl.src = '';
                imgEl.classList.remove('is-loaded');
                document.getElementById('logo_event_placeholder').style.display = 'block';
                document.getElementById('inp_logo').value = '';
                document.getElementById('clear_logo').disabled = true;
            }
        }

        document.getElementById('clear_photo').addEventListener('click', () => clearUpload('photo'));
        document.getElementById('clear_bg').addEventListener('click', () => clearUpload('bg'));
        document.getElementById('clear_logo').addEventListener('click', () => clearUpload('logo'));

        // ============================================================
        // INPUT HANDLERS
        // ============================================================
        document.getElementById('inp_photo').addEventListener('change', function(e) {
            if (e.target.files[0]) {
                loadImageToState(e.target.files[0], 'photo', renderPhoto);
                document.getElementById('clear_photo').disabled = false;
            }
        });

        document.getElementById('inp_bg').addEventListener('change', function(e) {
            if (e.target.files[0]) {
                loadImageToState(e.target.files[0], 'bg', renderBg);
                document.getElementById('clear_bg').disabled = false;
            }
        });

        document.getElementById('inp_logo').addEventListener('change', function(e) {
            if (!e.target.files[0]) return;
            const reader = new FileReader();
            reader.onload = function(ev) {
                const imgEl = document.getElementById('preview_event_logo');
                imgEl.src = ev.target.result;
                imgEl.classList.add('is-loaded');
                document.getElementById('logo_event_placeholder').style.display = 'none';
                document.getElementById('clear_logo').disabled = false;
            };
            reader.readAsDataURL(e.target.files[0]);
        });

        document.getElementById('inp_bright').addEventListener('input', function(e) {
            const val = e.target.value;
            document.getElementById('preview_bg').style.opacity = (val / 100).toFixed(2);
            document.getElementById('bright_value').textContent = val + '%';
        });

        document.getElementById('inp_name').addEventListener('input', function(e) {
            document.getElementById('display_name').textContent = (e.target.value || 'NAMA STAFF').toUpperCase();
        });

        document.getElementById('inp_role').addEventListener('input', function(e) {
            document.getElementById('display_role').textContent = (e.target.value || 'POSISI').toUpperCase();
        });

        // ============================================================
        // SLIDER ZOOM HANDLERS
        // ============================================================
        document.getElementById('zoom_photo').addEventListener('input', function(e) {
            const pct = parseInt(e.target.value, 10);
            state.photo.scale = pct / 100;
            document.getElementById('zoom_photo_value').textContent = pct + '%';
            renderPhoto();
        });

        document.getElementById('zoom_bg').addEventListener('input', function(e) {
            const pct = parseInt(e.target.value, 10);
            state.bg.scale = pct / 100;
            document.getElementById('zoom_bg_value').textContent = pct + '%';
            renderBg();
        });

        // ============================================================
        // DRAG & WHEEL HANDLER
        // ============================================================
        function makeDraggable(el, key) {
            let dragging = false, lastX = 0, lastY = 0;

            const start = (e) => {
                if (!state[key].img) return;
                dragging = true;
                el.classList.add('dragging');
                const p = e.touches ? e.touches[0] : e;
                lastX = p.pageX;
                lastY = p.pageY;
                e.preventDefault();
            };

            const move = (e) => {
                if (!dragging) return;
                const p = e.touches ? e.touches[0] : e;
                const dx = p.pageX - lastX;
                const dy = p.pageY - lastY;
                lastX = p.pageX;
                lastY = p.pageY;
                const w = el.offsetWidth, h = el.offsetHeight;
                state[key].x = Math.max(0, Math.min(1, state[key].x - dx / w));
                state[key].y = Math.max(0, Math.min(1, state[key].y - dy / h));
                if (key === 'bg') renderBg(); else renderPhoto();
            };

            const end = () => {
                dragging = false;
                el.classList.remove('dragging');
            };

            el.addEventListener('mousedown', start);
            window.addEventListener('mousemove', move);
            window.addEventListener('mouseup', end);
            el.addEventListener('touchstart', start, { passive: false });
            window.addEventListener('touchmove', move, { passive: false });
            window.addEventListener('touchend', end);

            el.addEventListener('wheel', (e) => {
                if (!state[key].img) return;
                e.preventDefault();
                const delta = e.deltaY < 0 ? 1.08 : 0.92;
                state[key].scale = Math.max(0.3, Math.min(5, state[key].scale * delta));
                if (key === 'bg') renderBg(); else renderPhoto();
                syncZoomSlider(key);
            }, { passive: false });
        }

        document.addEventListener('DOMContentLoaded', () => {
            makeDraggable(document.getElementById('preview_bg'), 'bg');
            makeDraggable(document.getElementById('preview_photo_dragger'), 'photo');
        });

        // ============================================================
        // HQ EXPORT
        // ============================================================
        document.getElementById('btn_download').addEventListener('click', async function() {
            const btn = this;
            const originalText = btn.innerText;
            btn.innerText = '⏳ MEMPROSES HQ...';
            btn.style.opacity = '0.5';
            btn.disabled = true;

            try {
                const SCALE = 4;
                const W = 280 * SCALE;
                const H = 420 * SCALE;
                const canvas = document.createElement('canvas');
                canvas.width = W;
                canvas.height = H;
                const ctx = canvas.getContext('2d');
                ctx.imageSmoothingEnabled = true;
                ctx.imageSmoothingQuality = 'high';

                const radius = 15 * SCALE;
                ctx.fillStyle = '#111111';
                roundRect(ctx, 0, 0, W, H, radius);
                ctx.fill();

                if (state.bg.img) {
                    ctx.save();
                    roundRect(ctx, 0, 0, W, H, radius);
                    ctx.clip();
                    const opacity = parseFloat(document.getElementById('preview_bg').style.opacity || 0.55);
                    ctx.globalAlpha = opacity;
                    drawScaledImage(ctx, state.bg.img, state.bg.scale, state.bg.x, state.bg.y, 0, 0, W, H);
                    ctx.globalAlpha = 1;
                    ctx.restore();
                }

                ctx.strokeStyle = '#1DB954';
                ctx.lineWidth = 2 * SCALE;
                roundRect(ctx, SCALE, SCALE, W - 2 * SCALE, H - 2 * SCALE, radius - SCALE);
                ctx.stroke();

                // Logo SPECTIX
                const logoSpectix = document.getElementById('logo_spectix');
                if (logoSpectix.complete && logoSpectix.naturalWidth > 0) {
                    const logoH = 24 * SCALE;
                    const ratio = logoSpectix.naturalWidth / logoSpectix.naturalHeight;
                    const logoW = logoH * ratio;
                    const topOffset = 18 * SCALE + (32 * SCALE - logoH) / 2;
                    ctx.drawImage(logoSpectix, 16 * SCALE, topOffset, logoW, logoH);
                }

                // Logo Event
                const logoEvent = document.getElementById('preview_event_logo');
                if (logoEvent.classList.contains('is-loaded') && logoEvent.complete && logoEvent.naturalWidth > 0) {
                    const logoH = 32 * SCALE;
                    const maxW = 90 * SCALE;
                    const ratio = logoEvent.naturalWidth / logoEvent.naturalHeight;
                    let logoW = logoH * ratio;
                    let finalH = logoH;
                    if (logoW > maxW) {
                        logoW = maxW;
                        finalH = maxW / ratio;
                    }
                    const rightX = W - 16 * SCALE - logoW;
                    const topY = 18 * SCALE + (logoH - finalH) / 2;
                    ctx.drawImage(logoEvent, rightX, topY, logoW, finalH);
                }

                // Photo circle
                const cx = W / 2;
                const cy = 90 * SCALE + 70 * SCALE;
                const photoRadius = 70 * SCALE;
                const innerRadius = photoRadius - 4 * SCALE;

                ctx.save();
                ctx.beginPath();
                ctx.arc(cx, cy, innerRadius, 0, Math.PI * 2);
                ctx.clip();
                if (state.photo.img) {
                    const size = innerRadius * 2;
                    drawScaledImage(ctx, state.photo.img, state.photo.scale, state.photo.x, state.photo.y,
                        cx - innerRadius, cy - innerRadius, size, size);
                } else {
                    ctx.fillStyle = '#333';
                    ctx.fillRect(cx - photoRadius, cy - photoRadius, photoRadius * 2, photoRadius * 2);
                }
                ctx.restore();

                ctx.strokeStyle = '#1DB954';
                ctx.lineWidth = 4 * SCALE;
                ctx.beginPath();
                ctx.arc(cx, cy, innerRadius + 2 * SCALE, 0, Math.PI * 2);
                ctx.stroke();

                // Name
                const name = (document.getElementById('inp_name').value || 'NAMA STAFF').toUpperCase();
                ctx.fillStyle = '#ffffff';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'top';
                const fontFamily = getComputedStyle(document.body).fontFamily || 'sans-serif';
                ctx.font = `900 ${21 * SCALE}px ${fontFamily}`;
                const nameY = cy + photoRadius + 14 * SCALE;
                const lineHeight = 25 * SCALE;
                const lines = wrapText(ctx, name, W - 40 * SCALE);
                lines.forEach((line, i) => {
                    ctx.fillText(line, cx, nameY + i * lineHeight);
                });

                // Role pill
                const role = (document.getElementById('inp_role').value || 'POSISI').toUpperCase();
                ctx.font = `800 ${12 * SCALE}px ${fontFamily}`;
                const roleMetrics = ctx.measureText(role);
                const pillPadX = 18 * SCALE;
                const pillH = 28 * SCALE;
                const pillW = roleMetrics.width + pillPadX * 2;
                const pillY = nameY + lines.length * lineHeight + 10 * SCALE;
                const pillX = cx - pillW / 2;

                ctx.fillStyle = 'rgba(0,0,0,0.85)';
                roundRect(ctx, pillX, pillY, pillW, pillH, pillH / 2);
                ctx.fill();
                ctx.strokeStyle = '#1DB954';
                ctx.lineWidth = 1 * SCALE;
                roundRect(ctx, pillX, pillY, pillW, pillH, pillH / 2);
                ctx.stroke();

                ctx.fillStyle = '#1DB954';
                ctx.textBaseline = 'middle';
                ctx.fillText(role, cx, pillY + pillH / 2);

                // Download
                const safeName = (document.getElementById('inp_name').value || 'Staff').replace(/[^a-zA-Z0-9]/g, '_');
                const safeRole = (document.getElementById('inp_role').value || 'Role').replace(/[^a-zA-Z0-9]/g, '_');
                const link = document.createElement('a');
                link.download = `IDCARD_${safeName}_${safeRole}.png`;
                link.href = canvas.toDataURL('image/png', 1.0);
                link.click();

                btn.innerText = '✅ TERSIMPAN!';
                setTimeout(() => {
                    btn.innerText = originalText;
                    btn.style.opacity = '1';
                    btn.disabled = false;
                }, 1500);

            } catch (err) {
                console.error(err);
                btn.innerText = '❌ GAGAL! COBA LAGI';
                btn.style.background = '#ff4444';
                setTimeout(() => {
                    btn.innerText = originalText;
                    btn.style.background = '#1DB954';
                    btn.style.opacity = '1';
                    btn.disabled = false;
                }, 2000);
            }
        });

        // ============================================================
        // CANVAS HELPERS
        // ============================================================
        function roundRect(ctx, x, y, w, h, r) {
            ctx.beginPath();
            ctx.moveTo(x + r, y);
            ctx.arcTo(x + w, y, x + w, y + h, r);
            ctx.arcTo(x + w, y + h, x, y + h, r);
            ctx.arcTo(x, y + h, x, y, r);
            ctx.arcTo(x, y, x + w, y, r);
            ctx.closePath();
        }

        function drawScaledImage(ctx, img, scale, ax, ay, dx, dy, dw, dh) {
            const iw = img.naturalWidth, ih = img.naturalHeight;
            const boxRatio = dw / dh;
            const imgRatio = iw / ih;
            let baseW, baseH;
            if (imgRatio > boxRatio) {
                baseH = dh; baseW = dh * imgRatio;
            } else {
                baseW = dw; baseH = dw / imgRatio;
            }
            const drawW = baseW * scale;
            const drawH = baseH * scale;
            const drawX = dx + (dw - drawW) * ax;
            const drawY = dy + (dh - drawH) * ay;
            ctx.drawImage(img, drawX, drawY, drawW, drawH);
        }

        function wrapText(ctx, text, maxWidth) {
            const words = text.split(' ');
            const lines = [];
            let line = '';
            for (let i = 0; i < words.length; i++) {
                const test = line + words[i] + ' ';
                if (ctx.measureText(test).width > maxWidth && line) {
                    lines.push(line.trim());
                    line = words[i] + ' ';
                } else {
                    line = test;
                }
            }
            lines.push(line.trim());
            return lines;
        }
    </script>
</x-app-layout>