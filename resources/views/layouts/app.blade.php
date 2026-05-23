<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Security meta tags --}}
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta name="robots" content="index, follow">

    <title>@yield('title', 'SPECTIX - Find Your Spectacles')</title>

    {{-- SEO meta tags --}}
    <meta name="description" content="@yield('description', 'SPECTIX - Platform tiket event terbaik di Indonesia. Beli tiket konser, seminar, olahraga, dan hiburan dengan mudah dan aman.')">
    <meta property="og:title" content="@yield('og_title', 'SPECTIX - Find Your Spectacles')">
    <meta property="og:description" content="@yield('og_description', 'Platform tiket event terbaik di Indonesia')">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ asset('logo.png') }}">

    {{-- Favicon dengan multiple fallbacks --}}
    @php $faviconVersion = config('app.favicon_version', '1'); @endphp
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=1.0">
    <link rel="shortcut icon" type="image/png" href="{{ asset('favicon.png') }}?v={{ $faviconVersion }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}?v={{ $faviconVersion }}">

    <link rel="preload" href="{{ asset('logo.png') }}" as="image">

    {{-- Google Fonts: pakai preconnect untuk performa --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

    <style>
        /* =========================================
           GLOBAL CSS VARIABLES & THEME
           ========================================= */
        :root {
            --bg-main: #0a0a0a; --bg-card: #121212; --bg-input: #1e1e1e;
            --text-main: #ffffff; --text-sub: #a0a0a0; --accent: #1DB954; --border: rgba(255,255,255,0.1);
            --hero-shadow: linear-gradient(to top, #0a0a0a 0%, transparent 100%);
            --tix-stock-color: #a0a0a0;
            --glow: 0 0 20px rgba(29, 185, 84, 0.4);
            --logo-color: #1DB954;
            --logo-filter: drop-shadow(0 0 15px rgba(29, 185, 84, 0.9));
        }

        .light-mode {
            --bg-main: #f3f4f6; --bg-card: #ffffff; --bg-input: #f9fafb;
            --text-main: #1e293b; --text-sub: #64748b; --accent: #1e3a8a; --border: #e2e8f0;
            --hero-shadow: none; --tix-stock-color: #64748b; --glow: 0 0 20px rgba(30, 58, 138, 0.3);
            --logo-color: #1e3a8a;
            --logo-filter: none;
        }

        body, * { transition: 0.3s ease; font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; }
        body { background-color: var(--bg-main); color: var(--text-main); margin: 0; }

        /* =========================================
           GLOBAL NAVBAR
           ========================================= */
        .nav-container { background-color: var(--bg-main); padding: 1rem 3rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 100; backdrop-filter: blur(10px); background-color: rgba(10, 10, 10, 0.8); }
        .light-mode .nav-container { background-color: rgba(243, 244, 246, 0.9); }
        .nav-left { display: flex; align-items: center; gap: 20px; }
        .nav-controls { display: flex; align-items: center; gap: 10px; }

        .logo-wrapper { position: relative; display: inline-flex; align-items: center; filter: var(--logo-filter); transition: filter 0.3s ease; transform: translateZ(0); will-change: filter; }
        .spectix-logo-img { height: 28px; width: auto; visibility: hidden; }
        .logo-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: var(--logo-color); -webkit-mask: url("{{ asset('logo.png') }}") no-repeat left center / contain; mask: url("{{ asset('logo.png') }}") no-repeat left center / contain; transition: background-color 0.3s ease; }

        .lang-btn { background: var(--bg-input); border: 1px solid var(--border); color: var(--text-main); padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 5px; opacity: 0.6; }
        .lang-btn.active { opacity: 1; border-color: var(--accent); box-shadow: var(--glow); color: var(--accent); transform: scale(1.05); }
        .user-btn { background: none; border: none; color: var(--text-main); font-weight: 800; cursor: pointer; text-transform: uppercase; font-size: 0.85rem; }

        /* User Dropdown SPECTIX PRO */
        .user-dropdown { position: relative; display: inline-block; }
        .dropdown-content { display: none; position: absolute; right: 0; top: 30px; background-color: var(--bg-card); min-width: 220px; box-shadow: 0px 10px 30px rgba(0,0,0,0.5); z-index: 1000; border-radius: 12px; border: 1px solid var(--border); overflow: hidden; }
        .dropdown-content a, .dropdown-content button { color: var(--text-main); padding: 12px 20px; text-decoration: none; display: block; font-size: 0.75rem; font-weight: 800; border-bottom: 1px solid var(--border); width: 100%; text-align: left; background: none; cursor: pointer; transition: 0.3s; }
        .dropdown-content a:hover, .dropdown-content button:hover { background-color: var(--bg-input); color: var(--accent); }
        .user-dropdown:hover .dropdown-content, .user-dropdown:focus-within .dropdown-content { display: block; }

        .overlay-modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.95); backdrop-filter: blur(10px); align-items: center; justify-content: center; }
        .modal-content-img { max-width: 90%; max-height: 85vh; border-radius: 1.5rem; border: 2px solid var(--accent); animation: zoom 0.3s; box-shadow: var(--glow); }
        .close-modal { position: absolute; top: 20px; right: 30px; color: var(--text-sub); font-size: 40px; font-weight: 300; cursor: pointer; line-height: 1; transition: 0.2s; z-index: 1100; }
        .close-modal:hover { color: var(--accent); transform: rotate(90deg); }
        @keyframes zoom { from {transform:scale(0.8); opacity: 0;} to {transform:scale(1); opacity: 1;} }

        /* Skip link untuk accessibility */
        .skip-link { position: absolute; left: -9999px; top: 0; z-index: 999; padding: 1rem; background: var(--accent); color: #fff; text-decoration: none; }
        .skip-link:focus { left: 0; }
    </style>

    @stack('styles')
</head>
<body class="antialiased">

    {{-- Skip to main content untuk accessibility --}}
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <div class="nav-container">
        <div class="nav-left">
            <a href="/" style="text-decoration: none;" aria-label="SPECTIX Home">
                <div class="logo-wrapper">
                    <img src="{{ asset('logo.png') }}" alt="Logo SPECTIX" class="spectix-logo-img">
                    <div class="logo-overlay"></div>
                </div>
            </a>
            <div class="nav-controls">
                <button type="button" id="btn-theme" style="background: var(--bg-input); border: 1px solid var(--accent); cursor: pointer; color: var(--accent); padding: 5px 12px; border-radius: 8px; font-weight: 900; font-size: 0.6rem; text-transform: uppercase;">SWITCH THEME</button>
                <button type="button" class="lang-btn active" id="btn-id" data-lang="id">🇮🇩 ID</button>
                <button type="button" class="lang-btn" id="btn-en" data-lang="en">🇺🇸 EN</button>
            </div>
        </div>
        <div class="user-dropdown">
            @auth
                <button type="button" class="user-btn">{{ strtoupper(Auth::user()->name) }} ▾</button>
                <div class="dropdown-content">
                    <a href="/promotor/dashboard" data-key="drop_dash">📊 DASHBOARD</a>
                    <a href="{{ route('promotor.idcard') }}">🪪 STUDIO ID CARD</a>
                    <a href="/promotor/attendees" data-key="drop_attend">👥 KELOLA PESERTA</a>
                    <a href="/tickets/create" data-key="drop_post">🎫 POSTING TIKET</a>

                    <div style="padding: 8px 20px; font-size: 0.6rem; color: var(--accent); font-weight: 900; letter-spacing: 1px; background: rgba(29, 185, 84, 0.05); border-bottom: 1px solid var(--border);">PENGATURAN AKUN</div>
                    <a href="{{ route('profile.edit') }}">🆔 VERIFIKASI (KTP/NPWP)</a>
                    <a href="{{ route('profile.edit') }}">🏦 DATA PENCAIRAN</a>

                    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                        @csrf
                        <button type="submit" style="color: #ff6b6b;">LOGOUT</button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="user-btn" style="text-decoration: none; border: 1px solid var(--border); padding: 8px 15px; border-radius: 8px;">LOGIN / DAFTAR</a>
            @endauth
        </div>
    </div>

    <div id="imgModal" class="overlay-modal" role="dialog" aria-modal="true" aria-hidden="true">
        <span class="close-modal" style="color:white; right: 40px; top: 30px;" id="closeImgModal" role="button" tabindex="0" aria-label="Close modal">&times;</span>
        <img class="modal-content-img" id="imgFull" alt="">
    </div>

    <main id="main-content">
        {{ $slot }}
    </main>

    <script>
        // =========================================
        // GLOBAL CSRF TOKEN SETUP (untuk AJAX request)
        // =========================================
        window.SPECTIX = window.SPECTIX || {};
        window.SPECTIX.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Helper untuk AJAX dengan CSRF auto-attached
        window.SPECTIX.fetch = function(url, options = {}) {
            const defaults = {
                headers: {
                    'X-CSRF-TOKEN': window.SPECTIX.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            };
            options.headers = Object.assign({}, defaults.headers, options.headers || {});
            // Same-origin only — block accidental cross-origin requests
            options.credentials = options.credentials || 'same-origin';
            return fetch(url, options);
        };

        // =========================================
        // THEME TOGGLE
        // =========================================
        function toggleTheme() {
            document.body.classList.toggle('light-mode');
            const isLight = document.body.classList.contains('light-mode');
            try { localStorage.setItem('theme', isLight ? 'light' : 'dark'); } catch(e) {}
        }

        // =========================================
        // LANG TOGGLE (whitelist validation)
        // =========================================
        const ALLOWED_LANGS = ['id', 'en'];

        function safeSetLang(lang) {
            // Whitelist check — jangan terima sembarang string dari localStorage
            if (!ALLOWED_LANGS.includes(lang)) lang = 'id';
            if (typeof setLang === 'function') {
                setLang(lang);
            }
        }

        // =========================================
        // DOM READY
        // =========================================
        document.addEventListener('DOMContentLoaded', () => {
            // Theme restore (whitelist check)
            try {
                const savedTheme = localStorage.getItem('theme');
                if (savedTheme === 'light') {
                    document.body.classList.add('light-mode');
                }
            } catch(e) {}

            // Theme button
            const btnTheme = document.getElementById('btn-theme');
            if (btnTheme) btnTheme.addEventListener('click', toggleTheme);

            // Lang buttons (pakai addEventListener, bukan inline onclick → CSP-friendly)
            document.querySelectorAll('.lang-btn[data-lang]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const lang = btn.getAttribute('data-lang');
                    safeSetLang(lang);
                });
            });

            // Lang restore (whitelist check)
            let savedLang = 'id';
            try {
                const stored = localStorage.getItem('lang');
                if (ALLOWED_LANGS.includes(stored)) savedLang = stored;
            } catch(e) {}
            safeSetLang(savedLang);

            // Modal close
            const closeBtn = document.getElementById('closeImgModal');
            if (closeBtn) {
                closeBtn.addEventListener('click', () => closeModal('imgModal'));
                closeBtn.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        closeModal('imgModal');
                    }
                });
            }

            // ESC to close modal
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.overlay-modal').forEach(m => {
                        if (m.style.display === 'flex') m.style.display = 'none';
                    });
                }
            });
        });

        // =========================================
        // MODAL HELPERS
        // =========================================
        function openModal(modalId, imgUrl = null) {
            const modal = document.getElementById(modalId);
            if (!modal) return;
            modal.style.display = "flex";
            modal.setAttribute('aria-hidden', 'false');
            if (imgUrl && modalId === 'imgModal') {
                // Validate URL — hanya allow same-origin atau https
                try {
                    const url = new URL(imgUrl, window.location.origin);
                    if (url.protocol === 'https:' || url.origin === window.location.origin) {
                        document.getElementById('imgFull').src = url.href;
                    }
                } catch(e) {
                    console.warn('Invalid image URL blocked');
                }
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = "none";
                modal.setAttribute('aria-hidden', 'true');
            }
        }
    </script>

    @stack('scripts')
</body>
</html>