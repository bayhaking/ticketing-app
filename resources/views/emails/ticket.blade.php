<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Tiket SPECTIX</title>
    <style>
        /* Base Reset & Typography */
        body, table, td, div, p, a { font-family: 'Plus Jakarta Sans', Arial, sans-serif; line-height: 1.5; }
        body { background-color: #0a0a0a; margin: 0; padding: 20px 0; color: #ffffff; }
        
        /* Container dengan Glow Hijau */
        .ticket-wrapper { 
            max-width: 650px; 
            margin: 0 auto; 
            background-color: #121212; 
            border-radius: 20px; 
            border: 2px solid #1DB954; 
            overflow: hidden;
            box-shadow: 0 0 25px rgba(29, 185, 84, 0.2);
        }
        
        /* HEADER SECTION */
        .header-table { width: 100%; background-color: #1e1e1e; padding: 25px 30px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .spectix-logo-img { height: 30px; width: auto; display: block; filter: drop-shadow(0 0 8px rgba(29, 185, 84, 0.6)); } 
        .e-tiket-label { color: #1DB954; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; text-align: right; margin: 0; text-shadow: 0 0 10px rgba(29, 185, 84, 0.8); }
        
        /* BANNER - FULL VIEW (NO CROP) */
        .banner-container { width: 100%; background-color: #000000; text-align: center; }
        .banner-img { width: 100%; height: auto; max-height: 400px; object-fit: contain; display: block; }

        /* CONTENT BODY */
        .content-area { padding: 35px; }
        .badge-success { 
            display: inline-block; 
            background-color: rgba(29, 185, 84, 0.1); 
            border: 1px solid #1DB954; 
            color: #1DB954; 
            padding: 8px 18px; 
            border-radius: 50px; 
            font-size: 11px; 
            font-weight: 900; 
            text-transform: uppercase; 
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(29, 185, 84, 0.2);
        }
        
        .event-title { 
            font-size: 32px; 
            font-weight: 900; 
            color: #ffffff; 
            font-style: italic; 
            margin: 0 0 25px 0; 
            line-height: 1.1;
            letter-spacing: -1px;
            text-shadow: 0 0 15px rgba(29, 185, 84, 0.4);
        }

        /* TICKET SPLIT SECTION */
        .split-table { width: 100%; border-top: 1px dashed rgba(255,255,255,0.2); border-bottom: 1px dashed rgba(255,255,255,0.2); padding: 25px 0; margin-bottom: 30px; }
        .info-cell { width: 55%; padding-right: 20px; vertical-align: top; border-right: 1px solid rgba(255,255,255,0.1); }
        .qr-cell { width: 45%; text-align: center; vertical-align: middle; padding-left: 20px; }

        .meta-label { color: #a0a0a0; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 4px 0; }
        .meta-value { color: #ffffff; font-size: 16px; font-weight: 800; margin: 0 0 18px 0; }
        
        /* QR SECTION DENGAN GLOW */
        .qr-wrapper { 
            background-color: #ffffff; 
            padding: 12px; 
            border-radius: 15px; 
            display: inline-block; 
            margin-bottom: 12px; 
            border: 3px solid #1DB954;
            box-shadow: 0 0 15px rgba(29, 185, 84, 0.4);
        }
        .qr-image { width: 140px; height: 140px; display: block; }
        .order-id { 
            font-family: monospace; 
            color: #1DB954; 
            font-size: 18px; 
            font-weight: bold; 
            letter-spacing: 2px; 
            margin: 10px 0 0 0;
            text-shadow: 0 0 10px rgba(29, 185, 84, 0.8);
        }
        .scan-text { color: #1DB954; font-size: 10px; font-weight: bold; text-transform: uppercase; margin-top: 5px; }

        /* RULES */
        .rules-wrapper { background-color: #1e1e1e; border-radius: 15px; padding: 20px; margin-bottom: 20px; border-left: 4px solid #1DB954; }
        .rules-title { color: #ffffff; font-size: 14px; font-weight: 900; text-transform: uppercase; margin: 0 0 15px 0; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px; }
        .rule-list { margin: 0; padding-left: 20px; color: #a0a0a0; font-size: 12px; font-weight: 600; margin-bottom: 15px; }

        .footer { text-align: center; padding: 25px; color: #a0a0a0; font-size: 11px; font-weight: 600; background-color: #0a0a0a; }
    </style>
</head>
<body>
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center" style="padding: 40px 10px;">
                <div class="ticket-wrapper">
                    
                    <table class="header-table" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td align="left">
                                <img src="{{ url('logo0.png') }}" alt="Logo SPECTIX" class="spectix-logo-img">
                            </td>
                            <td align="right">
                                <h2 class="e-tiket-label">E-TIKET</h2>
                            </td>
                        </tr>
                    </table>

                    @php 
                        $imgUrl = $order->event->banner ? url('storage/'.$order->event->banner) : 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?auto=format&fit=crop&q=80'; 
                    @endphp
                    <div class="banner-container">
                        <img src="{{ $imgUrl }}" class="banner-img" alt="Event Banner">
                    </div>

                    <div class="content-area">
                        <div class="badge-success">✔ PEMBAYARAN BERHASIL</div>
                        <h2 class="event-title">{{ $order->event->name }}</h2>

                        <table class="split-table" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td class="info-cell">
                                    <p class="meta-label">Nama Pemesan</p>
                                    <p class="meta-value">{{ $order->customer_name }}</p>

                                    <p class="meta-label">Tanggal Acara</p>
                                    <p class="meta-value">{{ date('d M Y', strtotime($order->event->date)) }}</p>

                                    <p class="meta-label">Kategori Tiket</p>
                                    <p class="meta-value">{{ $order->ticketType->name }}</p>

                                    <p class="meta-label">Jumlah Tiket</p>
                                    <p class="meta-value">1 x</p>

                                    <p class="meta-label" style="color: #1DB954; font-weight: 900;">TOTAL PEMBAYARAN</p>
                                    <p class="meta-value" style="color: #1DB954; font-size: 1.3rem; text-shadow: 0 0 10px rgba(29, 185, 84, 0.4);">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                                </td>

                                <td class="qr-cell">
                                    <div class="qr-wrapper">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $order->order_number }}" class="qr-image" alt="QR Ticket">
                                    </div>
                                    <p class="order-id">{{ $order->order_number }}</p>
                                    <p class="scan-text">SCAN SAAT MASUK VENUE</p>
                                </td>
                            </tr>
                        </table>

                       <div class="rules-wrapper">
                            <h3 class="rules-title">Tata Tertib Acara</h3>
                            
                            <p class="meta-label" style="color: #1DB954; margin-bottom: 5px;">✅ YANG HARUS DILAKUKAN (DO'S)</p>
                            <ul class="rule-list">
                                <li>Siapkan QR Code ini dengan kecerahan layar maksimal saat di gerbang masuk.</li>
                                <li>Bawa Kartu Identitas asli (KTP/SIM/Paspor) yang sesuai dengan nama pemesan.</li>
                                <li>Datang minimal 2 jam sebelum acara dimulai untuk menghindari antrean panjang.</li>
                                <li>Selalu gunakan gelang tiket (wristband) selama berada di dalam area venue.</li>
                                <li>Jaga barang bawaan berharga Anda, panitia tidak bertanggung jawab atas kehilangan.</li>
                            </ul>

                            <p class="meta-label" style="color: #ff6b6b; margin-bottom: 5px; margin-top: 15px;">❌ DILARANG KERAS (DON'TS)</p>
                            <ul class="rule-list" style="margin-bottom: 0;">
                                <li>DILARANG membawa senjata tajam, senjata api, atau benda berbahaya lainnya.</li>
                                <li>DILARANG membawa obat-obatan terlarang dan minuman beralkohol.</li>
                                <li>DILARANG membawa kamera profesional (DSLR/Mirrorless) tanpa izin khusus media.</li>
                                <li>DILARANG membawa makanan dan minuman dari luar area venue.</li>
                                <li>DILARANG merokok di dalam area konser (kecuali di Smoking Area yang disediakan).</li>
                                <li>DILARANG membagikan gambar QR Code ini ke media sosial (Potensi pencurian tiket).</li>
                            </ul>
                        </div>

                    <div class="footer">
                        © 2026 SPECTIX. Harap simpan e-tiket ini dengan baik.<br>
                        Butuh bantuan? Balas email ini atau hubungi support@spectix.com
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>