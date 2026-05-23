<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voucher;
use Illuminate\Support\Facades\Auth;

class VoucherController extends Controller
{
    // Menampilkan halaman pengajuan voucher untuk Promotor
    public function index()
    {
        $vouchers = Voucher::where('user_id', Auth::id())->latest()->get();
        return view('promotor.vouchers.index', compact('vouchers'));
    }

    // Proses simpan pengajuan voucher (status otomatis 'pending')
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:vouchers,code|alpha_dash',
            'type' => 'required|in:nominal,percent',
            'amount' => 'required|numeric|min:1',
            'quota' => 'required|integer|min:0',
        ], [
            'code.unique' => 'Kode Voucher ini sudah ada yang pakai, cari kode lain!',
            'code.alpha_dash' => 'Kode Voucher tidak boleh pakai spasi, gunakan huruf, angka, strip, atau underscore.',
        ]);

        Voucher::create([
            'user_id' => Auth::id(),
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'amount' => $request->amount,
            'quota' => $request->quota,
            'status' => 'pending', // Wajib pending sampai di-ACC Superowner
        ]);

        return redirect()->back()->with('success', 'Pengajuan Voucher berhasil dikirim! Menunggu ACC dari Owner.');
    }
}