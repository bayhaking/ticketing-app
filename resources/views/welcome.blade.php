<x-app-layout>
    @php
        // 1. DATA PREPARATION
        $activeEvents = $events->where('status', '!=', 'finished');
        
        $heroEventsData = $activeEvents->sortByDesc('views')->take(5)->map(function($e) {
            return [
                'id' => $e->id,
                'name' => $e->name,
                'category' => $e->category,
                'type' => $e->type,
                'isFinished' => false,
                'banner' => $e->banner ? asset('storage/'.$e->banner) : 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?q=80'
            ];
        })->values();

        $upcomingSpectacles = $activeEvents->filter(function($e) {
            $days = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($e->date), false);
            return $days >= 0 && $days <= 14;
        })->sortBy('date')->values();

        // 2. MATT FIX: BUILD JSON-LD SEO MURNI PAKAI PHP (ANTI BLADE ERROR)
        $schemaEvents = [];
        foreach($events->take(10) as $index => $event) {
            $schemaEvents[] = [
                "@type" => "ListItem",
                "position" => $index + 1,
                "item" => [
                    "@type" => "Event",
                    "name" => $event->name,
                    "startDate" => \Carbon\Carbon::parse($event->date)->toIso8601String(),
                    "eventStatus" => "https://schema.org/EventScheduled",
                    "image" => $event->banner ? asset('storage/'.$event->banner) : 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?q=80'
                ]
            ];
        }
        $schemaData = [
            "@context" => "https://schema.org",
            "@type" => "ItemList",
            "itemListElement" => $schemaEvents
        ];
    @endphp

    <script type="application/ld+json">
        {!! json_encode($schemaData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>

    <style>
        /* MATT FIX: Skeleton Loading Animation */
        .skeleton-bg { background: linear-gradient(90deg, #111 25%, #222 50%, #111 75%); background-size: 400% 100%; animation: shimmer 1.5s infinite; }
        @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

        .hero-section { width: 100%; height: 350px; border-radius: 2rem; margin: 2rem 0; position: relative; overflow: hidden; border: 1px solid var(--border); display: flex; align-items: center; padding: 4rem; cursor: pointer; transition: 0.3s; background-color: #0a0a0a; }
        .hero-section:hover { box-shadow: 0 0 30px var(--accent); border-color: var(--accent); }
        .hero-bg-layer { position: absolute; inset: 0; width: 100%; height: 100%; background-size: cover; background-position: center; transition: opacity 1s ease-in-out; opacity: 0; filter: grayscale(40%); }
        .hero-bg-layer.active { opacity: 0.4; } 
        .hero-content { position: relative; z-index: 10; transition: opacity 0.5s, transform 0.5s; opacity: 0; transform: translateY(20px); }
        .hero-content.active { opacity: 1; transform: translateY(0); }
        
        .filter-container { display: flex; gap: 12px; margin-bottom: 2.5rem; overflow-x: auto; padding-bottom: 10px; }
        .filter-btn { background: var(--bg-card); border: 1px solid var(--border); color: var(--text-main); padding: 10px 22px; border-radius: 100px; font-size: 0.85rem; font-weight: 800; cursor: pointer; white-space: nowrap; transition: 0.3s; }
        .filter-btn:hover { border-color: var(--accent); }
        .filter-btn.active { background: var(--accent); color: #fff; border-color: var(--accent); box-shadow: var(--glow); }

        .event-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px; }
        .event-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 1.8rem; overflow: hidden; cursor: pointer; transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); position: relative; }
        .event-card:hover { transform: translateY(-10px); border-color: var(--accent); box-shadow: var(--glow); }
        
        .card-img-wrapper { position: relative; width: 100%; height: 220px; overflow: hidden; background-color: #111; }
        .card-img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
        .event-card:hover .card-img { transform: scale(1.1); }
        
        .finished-badge { position: absolute; top: 15px; left: 15px; background: #ff4444; color: #fff; padding: 4px 12px; border-radius: 5px; font-weight: 900; font-size: 0.7rem; z-index: 10; box-shadow: 0 0 10px rgba(255, 68, 68, 0.5); }
        
        .card-price-badge { position: absolute; bottom: 15px; right: 15px; background: rgba(0,0,0,0.85); color: var(--accent); padding: 8px 18px; border-radius: 100px; font-weight: 900; font-size: 0.85rem; border: 1px solid var(--accent); }
        .card-body { padding: 1.8rem; }
        .card-cat { color: var(--accent); font-size: 0.75rem; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; }
        .card-title { color: var(--text-main); font-size: 1.3rem; font-weight: 800; margin: 8px 0; line-height: 1.2; }
        .card-date { color: var(--text-sub); font-size: 0.85rem; font-weight: 600; display: flex; align-items: center; gap: 5px; }

        .section-title { font-size: 1.8rem; font-weight: 900; font-style: italic; color: var(--text-main); margin: 3rem 0 1.5rem 0; border-left: 5px solid var(--accent); padding-left: 15px; line-height: 1; }

        /* Style Aman untuk Countdown XSS Free */
        .cd-safe-wrapper { font-size: 0.7rem; background: var(--bg-input); padding: 2px 6px; border-radius: 5px; margin-left: 5px; display: inline-block; font-weight: 900; }
        .cd-normal { color: var(--text-sub); }
        .cd-warning { color: #ffc107; }
        .cd-success { color: #1DB954; }
    </style>

    <div style="background-color: var(--bg-main); min-height: 100vh; padding: 0 3rem 4rem 3rem;">
        <div class="max-w-7xl mx-auto">
            
            <div class="hero-section skeleton-bg" id="hero-wrapper" onclick="goToHeroEvent()">
                <div id="hero-bg-container"></div>
                <div class="hero-content" id="hero-content">
                    <h1 style="color:#fff; font-size:4rem; font-weight:900; font-style:italic; line-height:1; text-shadow: 0 5px 15px rgba(0,0,0,0.8);" id="hero_title">TOP SPECTACLES</h1>
                    <p style="color:var(--accent); font-weight:800; margin-top:10px; letter-spacing:2px; text-transform:uppercase;" id="hero_sub">MEMUAT EVENT...</p>
                </div>
            </div>

            @if(count($upcomingSpectacles) > 0)
                <h2 class="section-title" data-key="sec_upcoming">UPCOMING SPECTACLES 🔥</h2>
                <div class="event-grid" style="margin-bottom: 3rem;">
                    @foreach($upcomingSpectacles as $event)
                        @php 
                            $minPrice = $event->ticketTypes->min('price'); 
                            $minPrice = $minPrice ? $minPrice : 0;
                        @endphp
                        <div class="event-card" onclick="window.location.href='/event/{{ $event->id }}'" style="border-color: var(--accent); box-shadow: 0 0 15px rgba(29, 185, 84, 0.1);">
                            <div class="card-img-wrapper skeleton-bg">
                                <div style="position: absolute; top: 10px; right: 10px; background: var(--accent); color: #000; font-weight: 900; font-size: 0.7rem; padding: 5px 10px; border-radius: 5px; z-index: 10;" data-key="badge_segera">SEGERA HADIR!</div>
                                <img src="{{ $event->banner ? asset('storage/'.$event->banner) : 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?q=80' }}" class="card-img" loading="lazy" onload="this.parentElement.classList.remove('skeleton-bg')">
                                <div class="card-price-badge"><span data-key="lbl_mulai">Mulai dari Rp</span> {{ number_format($minPrice, 0, ',', '.') }}</div>
                            </div>
                            <div class="card-body">
                                <div class="card-cat dyn-trans" data-val="{{ $event->category }}">{{ $event->category }}</div>
                                <h4 class="card-title">{{ $event->name }}</h4>
                                <div class="card-date" style="color: var(--accent); font-weight: 800;">
                                    📅 {{ date('d M Y', strtotime($event->date)) }} 
                                    <span class="live-countdown cd-safe-wrapper cd-normal" 
                                          data-start="{{ $event->sales_start_date ? \Carbon\Carbon::parse($event->sales_start_date)->getTimestamp() * 1000 : 'null' }}" 
                                          data-event="{{ \Carbon\Carbon::parse($event->date)->startOfDay()->getTimestamp() * 1000 }}">
                                        TBA
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <h2 class="section-title" data-key="sec_jelajah">JELAJAH SEMUA EVENT</h2>
            <div class="filter-container">
                <button class="filter-btn active" onclick="filterEvent('all', this)" data-key="f_all">SEMUA</button>
                <button class="filter-btn" onclick="filterEvent('Musik', this)" data-key="f_music">MUSIK</button>
                <button class="filter-btn" onclick="filterEvent('Olahraga', this)" data-key="f_sport">OLAHRAGA</button>
                <button class="filter-btn" onclick="filterEvent('Seminar', this)" data-key="f_semi">SEMINAR</button>
                <button class="filter-btn" onclick="filterEvent('Hiburan', this)" data-key="f_ent">HIBURAN</button>
            </div>

            <div class="event-grid" id="event-grid">
                @if(count($events) > 0)
                    @foreach($events as $event)
                        @php 
                            $isFinished = ($event->status === 'finished');
                            $minPrice = $event->ticketTypes->min('price');
                            $minPrice = $minPrice ? $minPrice : 0;
                        @endphp
                        <div class="event-card" data-category="{{ $event->category }}" onclick="window.location.href='/event/{{ $event->id }}'" style="{{ $isFinished ? 'opacity: 0.6;' : '' }}">
                            <div class="card-img-wrapper skeleton-bg">
                                @if($isFinished)
                                    <div class="finished-badge" data-key="badge_selesai">SELESAI</div>
                                @endif
                                
                                <img src="{{ $event->banner ? asset('storage/'.$event->banner) : 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?q=80' }}" class="card-img" loading="lazy" onload="this.parentElement.classList.remove('skeleton-bg')" style="{{ $isFinished ? 'filter: grayscale(1);' : '' }}">
                                
                                <div class="card-price-badge">
                                    @if($isFinished)
                                        <span data-key="lbl_tutup">Event Selesai</span>
                                    @else
                                        <span data-key="lbl_mulai">Mulai dari Rp</span> {{ number_format($minPrice, 0, ',', '.') }}
                                    @endif
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="card-cat dyn-trans" data-val="{{ $event->category }}">{{ $event->category }}</div>
                                <h4 class="card-title">{{ $event->name }}</h4>
                                <div class="card-date">
                                    📅 {{ date('d M Y', strtotime($event->date)) }}
                                    @if(!$isFinished)
                                        <span class="live-countdown cd-safe-wrapper cd-normal" 
                                              data-start="{{ $event->sales_start_date ? \Carbon\Carbon::parse($event->sales_start_date)->getTimestamp() * 1000 : 'null' }}" 
                                              data-event="{{ \Carbon\Carbon::parse($event->date)->startOfDay()->getTimestamp() * 1000 }}">
                                            TBA
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p style="color: var(--text-sub);" data-key="no_event">Belum ada event.</p>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // INTERSECTION OBSERVER
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                } else {
                    entry.target.classList.remove('is-visible');
                }
            });
        }, { rootMargin: "50px" });

        document.querySelectorAll('.live-countdown').forEach(el => observer.observe(el));

        // SCRIPT REALTIME COUNTDOWN
        setInterval(function() {
            const now = new Date().getTime();
            
            document.querySelectorAll('.live-countdown.is-visible').forEach(el => {
                const startAttr = el.getAttribute('data-start');
                const eventMs = parseInt(el.getAttribute('data-event'));
                const oneHour = 60 * 60 * 1000;
                const oneDay = 24 * oneHour;
                
                if(startAttr !== 'null' && startAttr !== '') {
                    const startMs = parseInt(startAttr);
                    const distance = startMs - now;

                    if (distance > 0 && distance <= oneHour) {
                        const m = Math.floor((distance % oneHour) / (1000 * 60)).toString().padStart(2, '0');
                        const s = Math.floor((distance % (1000 * 60)) / 1000).toString().padStart(2, '0');
                        el.textContent = `Buka dlm ${m}:${s}`;
                        el.className = 'live-countdown cd-safe-wrapper cd-warning is-visible';
                    } else if (distance > oneHour && distance <= oneDay) {
                        const h = Math.floor(distance / oneHour);
                        const m = Math.floor((distance % oneHour) / (1000 * 60)).toString().padStart(2, '0');
                        el.textContent = `Buka dlm ${h}J ${m}M`;
                        el.className = 'live-countdown cd-safe-wrapper cd-warning is-visible';
                    } else if (distance > oneDay) {
                        const d = Math.floor(distance / oneDay);
                        el.textContent = `H-${d} Hari`;
                        el.className = 'live-countdown cd-safe-wrapper cd-normal is-visible';
                    } else if (distance <= 0 && distance > -oneHour) {
                        el.textContent = '🔥 SUDAH DIBUKA!';
                        el.className = 'live-countdown cd-safe-wrapper cd-success is-visible';
                    } else {
                        const evDist = eventMs - now;
                        const d = Math.floor(evDist / oneDay);
                        el.textContent = evDist > 0 ? `H-${d} Hari` : 'HARI H 🔥';
                        el.className = 'live-countdown cd-safe-wrapper cd-normal is-visible';
                    }
                } else if (!isNaN(eventMs)) {
                    const evDist = eventMs - now;
                    const d = Math.floor(evDist / oneDay);
                    el.textContent = evDist > 0 ? `H-${d} Hari` : 'HARI H 🔥';
                    el.className = 'live-countdown cd-safe-wrapper cd-normal is-visible';
                }
            });
        }, 1000);

        const translations = {
            id: { f_all: "SEMUA", f_music: "MUSIK", f_sport: "OLAHRAGA", f_semi: "SEMINAR", f_ent: "HIBURAN", no_event: "Belum ada event.", badge_selesai: "SELESAI", lbl_mulai: "Mulai dari Rp", lbl_tutup: "Event Selesai", sec_upcoming: "UPCOMING SPECTACLES 🔥", sec_jelajah: "JELAJAH SEMUA EVENT", badge_segera: "SEGERA HADIR!" },
            en: { f_all: "ALL", f_music: "MUSIC", f_sport: "SPORTS", f_semi: "SEMINAR", f_ent: "ENTERTAINMENT", no_event: "No events found.", badge_selesai: "CLOSED", lbl_mulai: "Starts from Rp", lbl_tutup: "Event Closed", sec_upcoming: "UPCOMING SPECTACLES 🔥", sec_jelajah: "EXPLORE ALL EVENTS", badge_segera: "COMING SOON!" }
        };

        const dynTranslations = {
            id: { 'Musik': 'MUSIK', 'Olahraga': 'OLAHRAGA', 'Seminar': 'SEMINAR', 'Hiburan': 'HIBURAN' },
            en: { 'Musik': 'MUSIC', 'Olahraga': 'SPORTS', 'Seminar': 'SEMINAR', 'Hiburan': 'ENTERTAINMENT' }
        };

        const heroEvents = @json($heroEventsData);
        let currentHero = 0;
        const bgContainer = document.getElementById('hero-bg-container');
        const contentBox = document.getElementById('hero-content');
        const titleEl = document.getElementById('hero_title');
        const subEl = document.getElementById('hero_sub');
        const heroWrapper = document.getElementById('hero-wrapper');

        if(heroEvents.length > 0) {
            heroEvents.forEach((ev, i) => {
                let div = document.createElement('div');
                div.className = 'hero-bg-layer';
                div.id = 'bg-layer-' + i;
                div.style.backgroundImage = `url('${encodeURI(ev.banner)}')`;
                bgContainer.appendChild(div);
            });
            
            heroWrapper.classList.remove('skeleton-bg');
            
            updateHero(true);
            setInterval(() => {
                currentHero = (currentHero + 1) % heroEvents.length;
                updateHero(false);
            }, 5000); 
        } else {
            heroWrapper.classList.remove('skeleton-bg');
            titleEl.textContent = 'SPECTIX';
            subEl.textContent = 'FIND YOUR SPECTACLES';
            contentBox.classList.add('active');
        }

        function updateHero(instant = false) {
            if(!instant) contentBox.classList.remove('active');
            document.querySelectorAll('.hero-bg-layer').forEach(el => el.classList.remove('active'));
            
            setTimeout(() => {
                const layer = document.getElementById('bg-layer-' + currentHero);
                if(layer) layer.classList.add('active');
                
                let ev = heroEvents[currentHero];
                let lang = localStorage.getItem('lang') || 'id';

                let catText = (dynTranslations[lang] && dynTranslations[lang][ev.category]) ? dynTranslations[lang][ev.category] : ev.category.toUpperCase();
                let typeText = ev.type ? ev.type.toUpperCase() : '';

                titleEl.textContent = ev.name;
                subEl.textContent = catText + (typeText ? ' • ' + typeText : '') + ' 🔥 TOP INSIGHT';
                
                contentBox.classList.add('active');
            }, instant ? 0 : 400); 
        }

        function setLang(lang) {
            document.querySelectorAll('.lang-btn').forEach(btn => btn.classList.remove('active'));
            const btnActive = document.getElementById('btn-' + lang);
            if(btnActive) btnActive.classList.add('active');
            localStorage.setItem('lang', lang);

            document.querySelectorAll('[data-key]').forEach(el => {
                const key = el.getAttribute('data-key');
                if (translations[lang][key]) el.textContent = translations[lang][key];
            });

            document.querySelectorAll('.dyn-trans').forEach(el => {
                const val = el.getAttribute('data-val');
                if (dynTranslations[lang] && dynTranslations[lang][val]) {
                    el.textContent = dynTranslations[lang][val];
                }
            });

            if(heroEvents.length > 0) updateHero(true);
        }

        function filterEvent(category, btn) {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            document.querySelectorAll('#event-grid .event-card').forEach(card => {
                card.style.display = (category === 'all' || card.getAttribute('data-category') === category) ? 'block' : 'none';
            });
        }

        function goToHeroEvent() { if(heroEvents.length > 0) window.location.href = '/event/' + heroEvents[currentHero].id; }

        document.addEventListener('DOMContentLoaded', () => {
            const savedLang = localStorage.getItem('lang') || 'id';
            setLang(savedLang);
        });
    </script>
    @endpush
</x-app-layout>