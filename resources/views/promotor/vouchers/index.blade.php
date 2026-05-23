<x-app-layout>
    <style>
        .voucher-container { max-width: 1000px; margin: 0 auto; padding: 3rem 1.5rem; }
        .voucher-header { margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-end; }
        .voucher-title { font-size: 2.5rem; font-weight: 900; font-style: italic; color: #fff; margin: 0; text-transform: uppercase; }
        .voucher-subtitle { color: var(--text-sub); font-size: 0.9rem; margin-top: 5px; }
        
        .grid-layout { display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; }
        @media (max-width: 768px) { .grid-layout { grid-template-columns: 1fr; } }

        /* Card Form */
        .form-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 1.5rem; padding: 2rem; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-sub); margin-bottom: 8px; text-transform: uppercase; }
        .form-control { width: 100%; background: var(--bg-input); border: 1px solid var(--border); color: #fff; padding: 12px 16px; border-radius: 10px; font-weight: 700; outline: none; transition: 0.3s; }
        .form-control:focus { border-color: var(--accent); box-shadow: var(--glow); }
        .btn-submit { width: 100%; background: var(--accent); color: #000; font-weight: 900; padding: 14px; border-radius: 10px; border: none; cursor: pointer; transition: 0.3s; font-size: 0.9rem; text-transform: uppercase; margin-top: 1rem; }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: var(--glow); }

        /* Table List */
        .list-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 1.5rem; overflow: hidden; }
        .table-wrap { width: 100%; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background: rgba(255,255,255,0.02); padding: 15px 20px; font-size: 0.75rem; color: var(--text-sub); text-transform: uppercase; font-weight: 800; border-bottom: 1px solid var(--border); }
        td { padding: 15px 20px; font-size: 0.9rem; border-bottom: 1px solid rgba(255,255,255,0.05); font-weight: 600; color: var(--text-main); }
        tr:last-child td { border-bottom: none; }
        
        .code-badge { background: rgba(255,255,255,0.1); padding: 5px 10px; border-radius: 6px; font-family: monospace; letter-spacing: 1px; color: #fff; }
        
        /* Status Badges */
        .status { padding: 5px 10px; border-radius: 100px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; display: inline-block; }
        .status.pending { background: rgba(255, 193, 7, 0.1); color: #ffc107; border: 1px solid #ffc107; }
        .status.active { background: rgba(29, 185, 84, 0.1); color: var(--accent); border: 1px solid var(--accent); }
        .status.rejected { background: rgba(255, 68, 68, 0.1); color: #ff4444; border: 1px solid #ff4444; }

        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; font-weight: 700; font-size: 0.85rem; }
        .alert-success { background: rgba(29, 185, 84, 0.1); border: 1px solid var(--accent); color: var(--accent); }
        .alert-error { background: rgba(255, 68, 68, 0.1); border: 1px solid #ff4444; color: #ff4444; }
    </style>

    <div class="voucher-container">
        <div class="voucher-header">
            <div>
                <h1 class="voucher-title">VOUCHER PROMO</h1>
                <p class="voucher-subtitle">Ajukan kode diskon khusus untuk acaramu (Menunggu ACC Owner).</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-error">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="grid-layout">
            <div class="form-card">
                <h3 style="margin: 0 0 1.5rem; font-size: 1.1rem; font-weight: 900; color: var(--accent); border-bottom: 1px solid var(--border); padding-bottom: 10px;">PENGAJUAN BARU</h3>
                
                <form action="{{ route('promotor.vouchers.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Kode Voucher</label>
                        <input type="text" name="code" class="form-control" placeholder="Cth: SPECTIX50" required style="text-transform: uppercase;">
                    </div>
                    
                    <div class="form-group">
                        <label>Tipe Diskon</label>
                        <select name="type" class="form-control" required>
                            <option value="nominal">Nominal Fix (Rp)</option>
                            <option value="percent">Persentase (%)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Jumlah Diskon</label>
                        <input type="number" name="amount" class="form-control" placeholder="Cth: 50000 atau 20" required min="1">
                        <small style="color: var(--text-sub); font-size: 0.65rem; display: block; margin-top: 5px;">*Jika tipe persentase, isi dari 1 sampai 100.</small>
                    </div>

                    <div class="form-group">
                        <label>Batas Kuota Pemakaian</label>
                        <input type="number" name="quota" class="form-control" value="0" required min="0">
                        <small style="color: var(--text-sub); font-size: 0.65rem; display: block; margin-top: 5px;">*Isi 0 jika kuota tidak terbatas (unlimited).</small>
                    </div>

                    <button type="submit" class="btn-submit">KIRIM PENGAJUAN</button>
                </form>
            </div>

            <div class="list-card">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nilai Diskon</th>
                                <th>Kuota</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vouchers as $v)
                                <tr>
                                    <td><span class="code-badge">{{ $v->code }}</span></td>
                                    <td>
                                        @if($v->type == 'nominal')
                                            Rp {{ number_format($v->amount, 0, ',', '.') }}
                                        @else
                                            {{ $v->amount }}%
                                        @endif
                                    </td>
                                    <td>
                                        <span style="color: var(--text-sub); font-size: 0.8rem;">
                                            {{ $v->used }} / {{ $v->quota == 0 ? '∞' : $v->quota }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status {{ $v->status }}">
                                            {{ $v->status == 'pending' ? 'Menunggu ACC' : ($v->status == 'active' ? 'Aktif' : 'Ditolak') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align: center; color: var(--text-sub); padding: 2rem; font-style: italic;">Belum ada pengajuan voucher.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>