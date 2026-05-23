<nav style="background-color: var(--bg-main); padding: 1rem 3rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 100;">
    <!-- Kiri: Logo & Switchers -->
    <div style="display: flex; align-items: center; gap: 20px;">
        <a href="/" class="logo-link">
            <img src="{{ asset('logo.png') }}" alt="Logo SPECTIX" class="spectix-logo-img" style="height: 40px;">
        </a>
        <button type="button" onclick="document.body.classList.toggle('light-mode')" style="background: var(--bg-input); border: 1px solid var(--accent); color: var(--accent); padding: 5px 12px; border-radius: 8px; font-weight: 900; font-size: 0.6rem; cursor: pointer;">THEME</button>
        <div style="display: flex; gap: 5px;">
            <button class="lang-btn" id="btn-id" onclick="setLang('id')">ID</button>
            <button class="lang-btn" id="btn-en" onclick="setLang('en')">EN</button>
        </div>
    </div>

    <!-- Kanan: User Actions -->
    <div>
        @auth
            <div style="position: relative; display: inline-block;" id="user-dropdown-wrapper">
                <button onclick="toggleDropdown()" style="display: flex; align-items: center; gap: 10px; background: var(--bg-input); border: 1px solid var(--border); padding: 8px 18px; border-radius: 100px; cursor: pointer; transition: 0.3s;">
                    <span style="color: var(--text-main); font-weight: 900; font-size: 0.8rem; letter-spacing: 1px;">{{ strtoupper(Auth::user()->name) }}</span>
                    <svg width="12" height="12" fill="var(--text-sub)" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                </button>

                <!-- Dropdown Menu -->
                <div id="user-dropdown" style="display: none; position: absolute; right: 0; top: 50px; width: 220px; background: var(--bg-card); border: 1px solid var(--border); border-radius: 1.2rem; box-shadow: 0 10px 30px rgba(0,0,0,0.5); overflow: hidden; z-index: 1000;">
                    <a href="{{ route('promotor.dashboard') }}" style="display: block; padding: 12px 20px; color: var(--text-main); font-weight: 800; font-size: 0.75rem; text-decoration: none; transition: 0.3s; border-bottom: 1px solid var(--border);" onmouseover="this.style.background='var(--bg-input)'" onmouseout="this.style.background='none'">📊 DASHBOARD</a>
                    <a href="{{ route('promotor.attendees') }}" style="display: block; padding: 12px 20px; color: var(--text-main); font-weight: 800; font-size: 0.75rem; text-decoration: none; transition: 0.3s; border-bottom: 1px solid var(--border);" onmouseover="this.style.background='var(--bg-input)'" onmouseout="this.style.background='none'">👥 KELOLA PESERTA</a>
                    
                    <div style="padding: 8px 20px; font-size: 0.6rem; color: var(--accent); font-weight: 900; letter-spacing: 1px; background: rgba(29, 185, 84, 0.05);">PENGATURAN AKUN</div>
                    <a href="{{ route('profile.edit') }}" style="display: block; padding: 12px 20px; color: var(--text-main); font-weight: 800; font-size: 0.75rem; text-decoration: none; transition: 0.3s;" onmouseover="this.style.background='var(--bg-input)'" onmouseout="this.style.background='none'">🆔 VERIFIKASI (KTP/NPWP)</a>
                    <a href="{{ route('profile.edit') }}" style="display: block; padding: 12px 20px; color: var(--text-main); font-weight: 800; font-size: 0.75rem; text-decoration: none; transition: 0.3s;" onmouseover="this.style.background='var(--bg-input)'" onmouseout="this.style.background='none'">🏦 DATA PENCAIRAN</a>

                    <form method="POST" action="{{ route('logout') }}" style="border-top: 1px solid var(--border);">
                        @csrf
                        <button type="submit" style="display: block; width: 100%; text-align: left; padding: 12px 20px; color: #ff6b6b; font-weight: 900; font-size: 0.75rem; background: none; border: none; cursor: pointer; transition: 0.3s;" onmouseover="this.style.background='rgba(255,107,107,0.1)'" onmouseout="this.style.background='none'" data-key="drop_logout">LOGOUT</button>
                    </form>
                </div>
            </div>
        @else
            <a href="{{ route('login') }}" style="color: var(--text-main); font-weight: 800; text-decoration: none; font-size: 0.85rem;" data-key="l_staff">LOGIN STAFF</a>
        @endauth
    </div>
</nav>

<script>
    function toggleDropdown() {
        const dd = document.getElementById('user-dropdown');
        dd.style.display = dd.style.display === 'none' ? 'block' : 'none';
    }
    // Close dropdown when clicking outside
    window.onclick = function(event) {
        if (!event.target.closest('#user-dropdown-wrapper')) {
            const dropdown = document.getElementById('user-dropdown');
            if (dropdown) dropdown.style.display = 'none';
        }
    }
</script>