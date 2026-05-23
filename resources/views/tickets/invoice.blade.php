<x-app-layout>
    <style>
        .invoice-wrapper { padding: 3rem 1rem; display: flex; flex-direction: column; align-items: center; gap: 2.5rem; }
        .ticket-card { width: 100%; max-width: 650px; background-color: var(--bg-card); border-radius: 16px; border: 1px solid var(--border); overflow: hidden; box-shadow: var(--glow); page-break-inside: avoid; }
        .t-header { display: flex; justify-content: space-between; align-items: center; background-color: #1e1e1e; padding: 20px 30px; border-bottom: 1px solid var(--border); }
        .t-label-top { color: var(--text-sub); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; margin: 0; }
        .t-banner { width: 100%; height: 250px; object-fit: cover; display: block; border-bottom: 1px solid var(--border); }
        .t-body { padding: 30px; }
        .badge-success { display: inline-block; background-color: rgba(29, 185, 84, 0.1); border: 1px solid var(--accent); color: var(--accent); padding: 6px 15px; border-radius: 50px; font-size: 0.7rem; font-weight: 900; text-transform: uppercase; margin-bottom: 15px; }
        .event-title { font-size: 2rem; font-weight: 900; font-style: italic; margin: 0 0 25px 0; line-height: 1.1; }
        .t-split { display: flex; border-top: 1px dashed var(--border); border-bottom: 1px dashed var(--border); padding: 20px 0; margin-bottom: 30px; }
        .t-info { flex: 1.5; padding-right: 20px; border-right: 1px solid var(--border); display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .t-qr-section { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding-left: 20px; }
        .m-label { color: var(--text-sub); font-size: 0.7rem; font-weight: 800; text-transform: uppercase; margin-bottom: 3px; }
        .m-value { font-size: 1rem; font-weight: 800; }
        .qr-box { background: #fff; padding: 10px; border-radius: 12px; margin-bottom: 10px; }
        .qr-img { width: 130px; height: 130px; display: block; }
        .order-id { font-family: monospace; color: var(--accent); font-size: 1.1rem; font-weight: bold; margin: 0; }
        .rules-box { background-color: var(--bg-input); border-radius: 12px; padding: 20px; }
        .btn-print { background: var(--accent); color: #fff; padding: 15px 40px; border-radius: 100px; border: none; font-weight: 900; font-size: 1.1rem; text-transform: uppercase; cursor: pointer; box-shadow: var(--glow); margin-bottom: 20px; }

        @media print {
            .btn-print, .badge-success { display: none !important; }
            body, .invoice-wrapper { background: #ffffff !important; padding: 0 !important; }
            .ticket-card { background: #ffffff !important; border: 2px solid #000 !important; box-shadow: none !important; border-radius: 20px !important; margin-bottom: 40px !important; }
            .t-header { background: #f0f0f0 !important; border-bottom: 2px solid #000 !important; }
            .t-body { padding: 40px !important; }
            .event-title, .m-value, .order-id { color: #000 !important; }
            .m-label { color: #555 !important; }
            .t-split { border-top: 2px dashed #000 !important; border-bottom: 2px dashed #000 !important; }
            .t-info { border-right: 2px dashed #000 !important; }
            .rules-box { background: #f9f9f9 !important; border: 1px solid #ddd !important; }
            .qr-box { border: 1px solid #000 !important; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
    </style>

    <div class="invoice-wrapper">
        <button class="btn-print" onclick="window.print()">🖨️ CETAK SEMUA TIKET</button>

        @foreach($orders as $order)
        <div class="ticket-card">
            <div class="t-header">
                <img src="{{ asset('logo.png') }}" alt="Logo SPECTIX" class="spectix-logo-img">
                <h2 class="t-label-top">E-TIKET</h2>
            </div>
            
            @php $imgUrl = $order->event->banner ? asset('storage/'.$order->event->banner) : 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?auto=format&fit=crop&q=80'; @endphp
            <img src="{{ $imgUrl }}" class="t-banner">

            <div class="t-body">
                <div class="badge-success">✔ PEMBAYARAN BERHASIL</div>
                <h2 class="event-title">{{ $order->event->name }}</h2>

                <div class="t-split">
                    <div class="t-info">
                        <div><div class="m-label">Nama Pemesan</div><div class="m-value">{{ $order->customer_name }}</div></div>
                        <div><div class="m-label">Tanggal Acara</div><div class="m-value">{{ date('d M Y', strtotime($order->event->date)) }}</div></div>
                        <div><div class="m-label">Kategori Tiket</div><div class="m-value">{{ $order->ticketType->name }}</div></div>
                        <div><div class="m-label">Jumlah Tiket</div><div class="m-value">1 x</div></div>
                        <div style="grid-column: span 2;"><div class="m-label">Total Pembayaran</div><div class="m-value" style="color: var(--accent); font-size: 1.2rem;">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div></div>
                    </div>

                    <div class="t-qr-section">
                        <div class="qr-box"><img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $order->order_number }}" class="qr-img"></div>
                        <p class="order-id">{{ $order->order_number }}</p>
                        <p style="color: var(--text-sub); font-size: 0.65rem; font-weight: bold; margin-top: 5px;">SCAN SAAT MASUK</p>
                    </div>
                </div>

                <div class="rules-box">
                    <h3 style="font-size: 0.9rem; font-weight: 900; border-bottom: 1px solid var(--border); padding-bottom: 10px; margin-bottom: 15px;">Tata Tertib Acara</h3>
                    <p class="m-label" style="color: var(--accent);">✅ YANG HARUS DILAKUKAN</p>
                    <ul style="color: var(--text-sub); font-size: 0.8rem; padding-left: 20px; margin-bottom: 15px;"><li>Siapkan QR Code ini.</li><li>Bawa KTP/SIM.</li></ul>
                    <p class="m-label" style="color: #ff6b6b;">❌ DILARANG KERAS</p>
                    <ul style="color: var(--text-sub); font-size: 0.8rem; padding-left: 20px;"><li>Membagikan gambar tiket ke sosmed.</li><li>Bawa makan/minum dari luar.</li></ul>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</x-app-layout>