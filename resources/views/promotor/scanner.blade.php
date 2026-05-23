<x-app-layout>
    @push('styles')
    <style>
        /* ===== SCANNER WRAPPER ===== */
        .scanner-wrapper {
            min-height: calc(100vh - 80px);
            padding: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }
        .scanner-container {
            width: 100%;
            max-width: 600px;
        }

        /* ===== HEADER ===== */
        .scanner-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .scanner-badge {
            display: inline-block;
            background: rgba(29, 185, 84, 0.15);
            border: 1px solid var(--accent);
            color: var(--accent);
            padding: 6px 16px;
            border-radius: 100px;
            font-size: 0.7rem;
            font-weight: 900;
            letter-spacing: 2px;
            margin-bottom: 1rem;
            text-transform: uppercase;
        }
        .scanner-title {
            font-size: 2rem;
            font-weight: 900;
            font-style: italic;
            color: var(--text-main);
            margin: 0 0 0.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .scanner-subtitle {
            color: var(--text-sub);
            font-size: 0.9rem;
            margin: 0;
        }

        /* ===== STAFF INFO BAR ===== */
        .staff-info-bar {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-left: 4px solid var(--accent);
            border-radius: 12px;
            padding: 12px 18px;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
        }
        .staff-info-bar .label {
            color: var(--text-sub);
            font-weight: 700;
        }
        .staff-info-bar .value {
            color: var(--text-main);
            font-weight: 800;
        }
        .live-indicator {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--accent);
            font-weight: 900;
            font-size: 0.7rem;
        }
        .live-indicator::before {
            content: '';
            width: 8px; height: 8px;
            background: var(--accent);
            border-radius: 50%;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(1.2); }
        }

        /* ===== SCANNER FRAME ===== */
        .scanner-frame {
            background: #000;
            border-radius: 20px;
            overflow: hidden;
            position: relative;
            border: 2px solid var(--border);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
            transition: 0.3s;
        }
        .scanner-frame.active {
            border-color: var(--accent);
            box-shadow: 0 8px 40px rgba(29, 185, 84, 0.3);
        }
        .scanner-frame.processing {
            border-color: #ffc107;
            box-shadow: 0 8px 40px rgba(255, 193, 7, 0.3);
        }

        #reader {
            width: 100%;
            min-height: 400px;
        }

        /* Override styling html5-qrcode */
        #reader__dashboard_section_csr button {
            background: var(--accent) !important;
            color: #000 !important;
            border: none !important;
            padding: 10px 20px !important;
            border-radius: 8px !important;
            font-weight: 800 !important;
            cursor: pointer !important;
            margin: 5px !important;
        }
        #reader__scan_region {
            background: #000 !important;
        }
        #reader__camera_selection {
            background: var(--bg-input) !important;
            color: var(--text-main) !important;
            border: 1px solid var(--border) !important;
            border-radius: 8px !important;
            padding: 8px !important;
            margin: 10px !important;
        }

        /* ===== RESULT BOX (Toast Style) ===== */
        .result-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            animation: fadeIn 0.2s;
        }
        .result-overlay.active { display: flex; }

        .result-box {
            background: var(--bg-card);
            border: 2px solid var(--border);
            border-radius: 24px;
            padding: 3rem 2rem;
            text-align: center;
            max-width: 400px;
            width: 90%;
            animation: zoomIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
        }
        .result-box.success { border-color: var(--accent); }
        .result-box.error { border-color: #ff4444; }
        .result-box.warning { border-color: #ffc107; }

        .result-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            animation: bounce 0.5s;
        }
        .result-message {
            font-size: 1.4rem;
            font-weight: 900;
            margin: 0 0 0.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .result-detail {
            color: var(--text-sub);
            font-size: 0.9rem;
            margin: 0 0 1.5rem;
            line-height: 1.5;
        }
        .result-detail b { color: var(--text-main); }

        .result-box.success .result-message { color: var(--accent); }
        .result-box.error .result-message { color: #ff6b6b; }
        .result-box.warning .result-message { color: #ffc107; }

        .result-countdown {
            font-size: 0.75rem;
            color: var(--text-sub);
            margin-top: 1rem;
            font-weight: 700;
        }
        .result-countdown span {
            color: var(--accent);
            font-weight: 900;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes zoomIn {
            from { transform: scale(0.7); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        @keyframes bounce {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }

        /* ===== STATS BAR ===== */
        .stats-bar {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 1.5rem;
        }
        .stat-mini {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px;
            text-align: center;
        }
        .stat-mini .num {
            font-size: 1.4rem;
            font-weight: 900;
            line-height: 1;
        }
        .stat-mini .lbl {
            font-size: 0.65rem;
            color: var(--text-sub);
            font-weight: 700;
            text-transform: uppercase;
            margin-top: 4px;
            letter-spacing: 0.5px;
        }
        .stat-mini.success .num { color: var(--accent); }
        .stat-mini.warning .num { color: #ffc107; }
        .stat-mini.error .num { color: #ff6b6b; }

        /* ===== MANUAL INPUT ===== */
        .manual-input-toggle {
            margin-top: 1.5rem;
            text-align: center;
        }
        .manual-input-toggle button {
            background: transparent;
            border: 1px dashed var(--border);
            color: var(--text-sub);
            padding: 10px 20px;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 800;
            cursor: pointer;
            transition: 0.3s;
        }
        .manual-input-toggle button:hover {
            border-color: var(--accent);
            color: var(--accent);
        }
        .manual-input-box {
            display: none;
            margin-top: 1rem;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
        }
        .manual-input-box.active { display: block; }
        .manual-input-box input {
            width: 100%;
            background: var(--bg-input);
            border: 1px solid var(--border);
            color: var(--text-main);
            padding: 12px 16px;
            border-radius: 8px;
            font-family: monospace;
            font-weight: 700;
            outline: none;
            margin-bottom: 10px;
        }
        .manual-input-box input:focus { border-color: var(--accent); }
        .manual-input-box button {
            width: 100%;
            background: var(--accent);
            color: #000;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 900;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ===== RECENT SCANS ===== */
        .recent-scans {
            margin-top: 2rem;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
        }
        .recent-scans h3 {
            font-size: 0.85rem;
            font-weight: 900;
            color: var(--text-sub);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 1rem;
        }
        .recent-scan-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid var(--border);
            font-size: 0.85rem;
        }
        .recent-scan-item:last-child { border-bottom: none; }
        .recent-scan-item .info {
            display: flex; align-items: center; gap: 10px;
        }
        .recent-scan-item .icon {
            font-size: 1.2rem;
        }
        .recent-scan-item .time {
            color: var(--text-sub);
            font-size: 0.7rem;
            font-family: monospace;
        }
        .empty-scans {
            color: var(--text-sub);
            font-size: 0.8rem;
            text-align: center;
            padding: 1rem;
            font-style: italic;
        }
    </style>
    @endpush

    <div class="scanner-wrapper">
        <div class="scanner-container">

            {{-- HEADER --}}
            <div class="scanner-header">
                <div class="scanner-badge">🎫 Check-In System</div>
                <h1 class="scanner-title">SCANNER TIKET</h1>
                <p class="scanner-subtitle">Arahkan kamera ke QR code tiket peserta</p>
            </div>

            {{-- STAFF INFO BAR --}}
            <div class="staff-info-bar">
                <div>
                    <span class="label">Staff:</span>
                    <span class="value">{{ Auth::user()->name }}</span>
                </div>
                <div class="live-indicator">LIVE</div>
            </div>

            {{-- SCANNER FRAME --}}
            <div class="scanner-frame active" id="scanner-frame">
                <div id="reader"></div>
            </div>

            {{-- STATS BAR --}}
            <div class="stats-bar">
                <div class="stat-mini success">
                    <div class="num" id="stat_success">0</div>
                    <div class="lbl">✅ Berhasil</div>
                </div>
                <div class="stat-mini warning">
                    <div class="num" id="stat_duplicate">0</div>
                    <div class="lbl">⚠️ Sudah Discan</div>
                </div>
                <div class="stat-mini error">
                    <div class="num" id="stat_invalid">0</div>
                    <div class="lbl">❌ Invalid</div>
                </div>
            </div>

            {{-- MANUAL INPUT (fallback kalau QR rusak / kamera bermasalah) --}}
            <div class="manual-input-toggle">
                <button type="button" id="toggle_manual">⌨️ Input Manual (kode tiket)</button>
            </div>
            <div class="manual-input-box" id="manual_box">
                <input type="text" id="manual_code" placeholder="Contoh: TIX-ABC12345" autocomplete="off">
                <button type="button" id="manual_submit">VALIDASI MANUAL</button>
            </div>

            {{-- RECENT SCANS --}}
            <div class="recent-scans">
                <h3>📜 Riwayat Scan Sesi Ini</h3>
                <div id="recent_list">
                    <div class="empty-scans">Belum ada scan dalam sesi ini.</div>
                </div>
            </div>
        </div>
    </div>

    {{-- RESULT OVERLAY MODAL --}}
    <div class="result-overlay" id="result_overlay">
        <div class="result-box" id="result_box">
            <div class="result-icon" id="result_icon">✅</div>
            <h2 class="result-message" id="result_message">VALID!</h2>
            <p class="result-detail" id="result_detail"></p>
            <p class="result-countdown">Lanjut scan dalam <span id="countdown">3</span> detik...</p>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
    (function() {
        'use strict';

        // ============================================================
        // STATE
        // ============================================================
        let isProcessing = false;
        let stats = { success: 0, duplicate: 0, invalid: 0 };
        let recentScans = [];
        const MAX_RECENT = 5;

        const csrfToken = '{{ csrf_token() }}';
        const validateUrl = '{{ route("checkin.validate") }}';

        // ============================================================
        // SCANNER SETUP
        // ============================================================
        const html5QrcodeScanner = new Html5QrcodeScanner(
            "reader",
            {
                fps: 10,
                qrbox: { width: 280, height: 280 },
                aspectRatio: 1.0,
                showTorchButtonIfSupported: true,
                showZoomSliderIfSupported: true,
            },
            /* verbose= */ false
        );

        html5QrcodeScanner.render(onScanSuccess, onScanError);

        // ============================================================
        // SCAN HANDLER
        // ============================================================
        function onScanSuccess(decodedText) {
            if (isProcessing) return; // cegah double-scan
            isProcessing = true;

            document.getElementById('scanner-frame').classList.remove('active');
            document.getElementById('scanner-frame').classList.add('processing');

            // Pause scanner sementara
            html5QrcodeScanner.pause(true);

            validateTicket(decodedText);
        }

        function onScanError(errorMessage) {
            // Silent — error muncul terus saat tidak ada QR code di frame
        }

        // ============================================================
        // VALIDATE TICKET (via fetch)
        // ============================================================
        function validateTicket(orderNumber) {
            fetch(validateUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: JSON.stringify({ order_number: orderNumber })
            })
            .then(res => res.json())
            .then(data => {
                handleResult(data, orderNumber);
            })
            .catch(err => {
                console.error('Validation error', err);
                handleResult({
                    success: false,
                    message: 'Koneksi gagal',
                    detail: 'Periksa koneksi internet dan coba lagi.',
                }, orderNumber);
            });
        }

        // ============================================================
        // HANDLE RESULT
        // ============================================================
        function handleResult(data, orderNumber) {
            const overlay = document.getElementById('result_overlay');
            const box = document.getElementById('result_box');
            const icon = document.getElementById('result_icon');
            const message = document.getElementById('result_message');
            const detail = document.getElementById('result_detail');

            // Reset class
            box.className = 'result-box';

            let category = 'success';

            if (data.success) {
                category = 'success';
                icon.textContent = '✅';
                stats.success++;
            } else if (data.message && data.message.toLowerCase().includes('sudah')) {
                // Sudah pernah scan
                category = 'warning';
                icon.textContent = '⚠️';
                stats.duplicate++;
            } else {
                category = 'error';
                icon.textContent = '❌';
                stats.invalid++;
            }

            box.classList.add(category);
            message.textContent = data.message || 'Tidak ada response';

            // Detail: nama, event, ticket type, dll (kalau backend kirim)
            let detailHtml = '';
            if (data.customer_name) {
                detailHtml += `<b>${escapeHtml(data.customer_name)}</b><br>`;
            }
            if (data.event_name) {
                detailHtml += `${escapeHtml(data.event_name)}<br>`;
            }
            if (data.ticket_type) {
                detailHtml += `<small style="color: var(--accent);">${escapeHtml(data.ticket_type)}</small><br>`;
            }
            if (data.scanned_at) {
                detailHtml += `<small>Discan: ${escapeHtml(data.scanned_at)}</small>`;
            }
            detail.innerHTML = detailHtml || `<small>Kode: ${escapeHtml(orderNumber)}</small>`;

            // Tampilkan overlay
            overlay.classList.add('active');

            // Update stats UI
            updateStats();

            // Tambah ke recent scans
            addRecentScan({
                category: category,
                message: data.message,
                name: data.customer_name || orderNumber,
                time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }),
            });

            // Countdown 3 detik lalu lanjut scan
            startCountdown(3);
        }

        function startCountdown(seconds) {
            const countdownEl = document.getElementById('countdown');
            let remaining = seconds;
            countdownEl.textContent = remaining;

            const interval = setInterval(() => {
                remaining--;
                countdownEl.textContent = remaining;
                if (remaining <= 0) {
                    clearInterval(interval);
                    closeResultAndResume();
                }
            }, 1000);
        }

        function closeResultAndResume() {
            document.getElementById('result_overlay').classList.remove('active');
            document.getElementById('scanner-frame').classList.remove('processing');
            document.getElementById('scanner-frame').classList.add('active');

            try {
                html5QrcodeScanner.resume();
            } catch (e) {
                // Kalau resume gagal, fallback ke reload
                console.warn('Resume failed, reloading...');
                location.reload();
                return;
            }

            isProcessing = false;
        }

        // ============================================================
        // STATS UI
        // ============================================================
        function updateStats() {
            document.getElementById('stat_success').textContent = stats.success;
            document.getElementById('stat_duplicate').textContent = stats.duplicate;
            document.getElementById('stat_invalid').textContent = stats.invalid;
        }

        // ============================================================
        // RECENT SCANS
        // ============================================================
        function addRecentScan(scan) {
            recentScans.unshift(scan);
            if (recentScans.length > MAX_RECENT) {
                recentScans = recentScans.slice(0, MAX_RECENT);
            }
            renderRecentScans();
        }

        function renderRecentScans() {
            const container = document.getElementById('recent_list');
            if (recentScans.length === 0) {
                container.innerHTML = '<div class="empty-scans">Belum ada scan dalam sesi ini.</div>';
                return;
            }

            container.innerHTML = recentScans.map(s => {
                const iconMap = { success: '✅', warning: '⚠️', error: '❌' };
                const colorMap = { success: '#1DB954', warning: '#ffc107', error: '#ff6b6b' };
                return `
                    <div class="recent-scan-item">
                        <div class="info">
                            <span class="icon">${iconMap[s.category]}</span>
                            <span style="color: ${colorMap[s.category]}; font-weight: 800;">${escapeHtml(s.name)}</span>
                        </div>
                        <span class="time">${escapeHtml(s.time)}</span>
                    </div>
                `;
            }).join('');
        }

        // ============================================================
        // MANUAL INPUT
        // ============================================================
        document.getElementById('toggle_manual').addEventListener('click', () => {
            const box = document.getElementById('manual_box');
            box.classList.toggle('active');
            if (box.classList.contains('active')) {
                document.getElementById('manual_code').focus();
            }
        });

        document.getElementById('manual_submit').addEventListener('click', () => {
            const code = document.getElementById('manual_code').value.trim();
            if (!code) return;
            if (isProcessing) return;

            isProcessing = true;
            document.getElementById('scanner-frame').classList.remove('active');
            document.getElementById('scanner-frame').classList.add('processing');

            try { html5QrcodeScanner.pause(true); } catch (e) {}

            validateTicket(code);
            document.getElementById('manual_code').value = '';
        });

        document.getElementById('manual_code').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('manual_submit').click();
            }
        });

        // ============================================================
        // HELPERS
        // ============================================================
        function escapeHtml(text) {
            if (text == null) return '';
            const div = document.createElement('div');
            div.textContent = String(text);
            return div.innerHTML;
        }
    })();
    </script>
    @endpush
</x-app-layout>