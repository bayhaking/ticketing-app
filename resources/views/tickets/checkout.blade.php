<x-app-layout>
    <style>
        .checkout-container { max-width: 1100px; margin: 3rem auto; padding: 0 2rem; display: grid; grid-template-columns: 1.5fr 1fr; gap: 3rem; align-items: start; }
        .c-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 1.5rem; padding: 2.5rem; border-top: 5px solid var(--accent); box-shadow: 0 15px 40px rgba(0,0,0,0.5); }
        .c-title { font-weight: 900; font-size: 1.3rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border); padding-bottom: 1rem; text-transform: uppercase; }
        .input-group { margin-bottom: 1.5rem; }
        .input-label { display: block; color: var(--text-sub); font-size: 0.85rem; font-weight: 800; margin-bottom: 8px; text-transform: uppercase; }
        .input-box { width: 100%; background: var(--bg-input); border: 1px solid var(--border); color: var(--text-main); padding: 15px; border-radius: 12px; font-size: 1rem; outline: none; transition: 0.3s; }
        .input-box:focus { border-color: var(--accent); box-shadow: var(--glow); }
        
        .summary-item { display: flex; justify-content: space-between; margin-bottom: 10px; color: var(--text-sub); font-weight: 600; }
        .summary-total { display: flex; justify-content: space-between; margin-top: 15px; padding-top: 15px; border-top: 1px dashed var(--border); color: var(--accent); font-weight: 900; font-size: 1.5rem; text-shadow: var(--glow); }
        
        .payment-method { display: flex; align-items: center; gap: 15px; background: var(--bg-input); padding: 15px; border-radius: 12px; border: 1px solid var(--border); cursor: pointer; margin-bottom: 10px; transition: 0.3s; }
        .payment-method:hover { border-color: var(--accent); }
        
        .btn-pay { width: 100%; background: var(--accent); color: #fff; padding: 1.2rem; border-radius: 100px; border: none; font-weight: 900; font-size: 1.1rem; cursor: pointer; margin-top: 2rem; box-shadow: var(--glow); font-style: italic; text-transform: uppercase; transition: 0.2s; }
        .btn-pay:hover:not(:disabled) { transform: translateY(-3px); }
        .btn-pay:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
        
        /* CSS VOUCHER */
        .btn-voucher { background: var(--bg-input); border: 1px solid var(--accent); color: var(--accent); padding: 0 20px; border-radius: 12px; font-weight: 800; cursor: pointer; transition: 0.3s; white-space: nowrap; }
        .btn-voucher:hover { background: var(--accent); color: #fff; box-shadow: var(--glow); }

        @media (max-width: 768px) { .checkout-container { grid-template-columns: 1fr; } }
    </style>

    <div style="padding-bottom: 4rem;">
        <div class="checkout-container">
            <div class="c-card">
                <h2 class="c-title">Informasi Pemegang Tiket</h2>
                <form id="checkoutForm">
                    
                    @for($i = 1; $i <= $quantity; $i++)
                    <div style="background: var(--bg-input); padding: 20px; border-radius: 12px; margin-bottom: 20px; border: 1px solid var(--border);">
                        <h3 style="color: var(--accent); margin-top:0; font-size: 1rem; margin-bottom: 15px; font-weight: 900; font-style: italic;">🎫 TIKET {{ $i }}</h3>
                        
                        <div class="input-group">
                            <label class="input-label">Nama Lengkap</label>
                            <input type="text" name="names[]" class="input-box ticket-name" value="" placeholder="Masukkan nama sesuai identitas" required>
                        </div>
                        <div class="input-group" style="margin-bottom: 0;">
                            <label class="input-label">Email (Untuk e-Ticket)</label>
                            <input type="email" name="emails[]" class="input-box ticket-email" value="" placeholder="email@contoh.com" required>
                        </div>
                    </div>
                    @endfor

                    <h2 class="c-title" style="margin-top: 3rem;">Verifikasi WhatsApp Utama</h2>
                    <div class="input-group">
                        <label class="input-label">Nomor WhatsApp Pemesan</label>
                        <input type="number" name="whatsapp" id="wa_number" class="input-box" placeholder="0812..." required>
                        <small style="color: var(--accent); font-size: 0.75rem; margin-top:5px; display:block;">*Hanya 1 OTP yang dikirim ke nomor ini untuk memvalidasi semua pesanan.</small>
                    </div>

                    <h2 class="c-title" style="margin-top: 3rem;">Metode Pembayaran</h2>
                    <label class="payment-method"><input type="radio" name="payment" value="qris" checked><span style="font-weight: 800;">QRIS (Gopay, OVO, Dana)</span></label>
                    <label class="payment-method"><input type="radio" name="payment" value="va_bca"><span style="font-weight: 800;">BCA Virtual Account</span></label>
                </form>
            </div>

            <div class="c-card" style="position: sticky; top: 100px;">
                <h2 class="c-title">Ringkasan Pesanan</h2>
                
                <div class="summary-item">
                    <span>{{ $ticket->name }} (x{{ $quantity }})</span>
                    <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                
                <div class="summary-item" id="discount_row" style="display: none; color: var(--accent); font-weight: 800;">
                    <span>Diskon Voucher</span>
                    <span id="discount_amount">- Rp 0</span>
                </div>

                <div class="summary-total">
                    <span>TOTAL</span>
                    <span id="final_total">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>

                <div style="margin-top: 2.5rem; margin-bottom: 1.5rem; border-top: 1px solid var(--border); padding-top: 1.5rem;">
                    <label class="input-label">Punya Kode Promo?</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="voucher_code" class="input-box" placeholder="SPECTEVE10" style="margin-bottom:0; text-transform:uppercase;">
                        <button type="button" class="btn-voucher" onclick="applyVoucher()">TERAPKAN</button>
                    </div>
                </div>

                <button type="button" class="btn-pay" id="btn_pay_main" onclick="triggerOTP()">KIRIM OTP & BAYAR</button>
            </div>
        </div>
    </div>

    <div id="otpModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.9); z-index:9999; align-items:center; justify-content:center; backdrop-filter:blur(10px);">
        <div class="c-card" style="width:90%; max-width:400px; text-align:center;">
            <h2 class="c-title">VERIFIKASI WHATSAPP</h2>
            <p style="color:var(--text-sub); margin-bottom:20px;">Masukan kode 4 digit yang dikirim ke WhatsApp kamu.</p>
            <input type="number" id="otp_input" class="input-box" placeholder="----" style="text-align:center; font-size:2.5rem; letter-spacing:15px; font-weight:900;">
            <button type="button" class="btn-pay" onclick="verifyOTP()" id="btn_verify">KONFIRMASI KODE</button>
            <div id="otp_error" style="color:#ff6b6b; margin-top:10px; font-weight:800; display:none;">KODE SALAH!</div>
        </div>
    </div>

    <script>
        // === LOGIKA VOUCHER ===
        let subtotal = {{ $subtotal }};
        
        function applyVoucher() {
            let code = document.getElementById('voucher_code').value.toUpperCase();
            let discount = 0;

            if(code === 'SPECTEVE10') { 
                discount = subtotal * 0.1; 
                alert('Voucher SPECTEVE10 Berhasil! Kamu hemat 10%.'); 
            } else { 
                alert('Kode voucher tidak valid atau sudah kadaluwarsa.'); 
                return; 
            }

            document.getElementById('discount_row').style.display = 'flex';
            document.getElementById('discount_amount').innerText = '- Rp ' + discount.toLocaleString('id-ID');
            
            let finalPrice = subtotal - discount;
            document.getElementById('final_total').innerText = 'Rp ' + finalPrice.toLocaleString('id-ID');
            
            if(!document.getElementById('hidden_voucher')) {
                let input = document.createElement('input'); 
                input.type = 'hidden'; 
                input.id = 'hidden_voucher'; 
                input.value = code;
                document.getElementById('checkoutForm').appendChild(input);
            } else {
                document.getElementById('hidden_voucher').value = code;
            }
        }

        // === LOGIKA OTP FONNTE ===
        function triggerOTP() {
            const wa = document.getElementById('wa_number').value;
            const names = Array.from(document.querySelectorAll('.ticket-name')).map(el => el.value);
            const emails = Array.from(document.querySelectorAll('.ticket-email')).map(el => el.value);

            if(!wa || names.includes('') || emails.includes('')) return alert('Lengkapi semua data tiket dan WA dulu ya!');

            const btn = document.getElementById('btn_pay_main');
            const originalText = btn.innerText;
            btn.innerText = "MENGIRIM OTP... ⏳"; btn.disabled = true;

            fetch('{{ route("otp.send") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ whatsapp: wa })
            })
            .then(res => res.json())
            .then(data => {
                btn.innerText = originalText; btn.disabled = false;
                if(data.success) {
                    document.getElementById('otpModal').style.display = 'flex';
                    document.getElementById('otp_error').style.display = 'none';
                } else { alert("Gagal kirim OTP: " + (data.message || "Terjadi kesalahan.")); }
            }).catch(err => { btn.innerText = originalText; btn.disabled = false; alert("Error Server!"); });
        }

        function verifyOTP() {
            const otp = document.getElementById('otp_input').value;
            const wa = document.getElementById('wa_number').value;
            const names = Array.from(document.querySelectorAll('.ticket-name')).map(el => el.value);
            const emails = Array.from(document.querySelectorAll('.ticket-email')).map(el => el.value);
            const btnVerify = document.getElementById('btn_verify');
            
            const voucherCode = document.getElementById('hidden_voucher') ? document.getElementById('hidden_voucher').value : '';
            
            btnVerify.innerText = "MEMPROSES... ⏳"; btnVerify.disabled = true;

            fetch('{{ route("checkout.process") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ 
                    otp: otp, 
                    whatsapp: wa, 
                    names: names, 
                    emails: emails, 
                    payment: document.querySelector('input[name="payment"]:checked').value,
                    voucher_code: voucherCode
                })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) { window.location.href = data.redirect; } 
                else {
                    btnVerify.innerText = "KONFIRMASI KODE"; btnVerify.disabled = false;
                    document.getElementById('otp_error').innerText = data.message || "Kode Salah!";
                    document.getElementById('otp_error').style.display = 'block';
                }
            });
        }
    </script>
</x-app-layout>