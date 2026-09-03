<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockRequest extends FormRequest
{
    /**
     * Otorisasi berdasarkan jenis transaksi (masuk atau keluar) dan
     * metode HTTP (create/store vs edit/update/destroy).
     */
    public function authorize(): bool
    {
        $isIncoming = $this->is('stok-masuk*');

        // Operasi tulis-perubahan (update) memerlukan izin kelola
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return $this->user()->can($isIncoming ? 'stok_masuk.manage' : 'stok_keluar.manage');
        }

        // Create/store tetap menggunakan izin yang sudah ada
        return $this->user()->can($isIncoming ? 'stok_masuk.create' : 'stok_keluar.create');
    }

    /**
     * Aturan validasi disesuaikan dengan jenis transaksi.
     */
    public function rules(): array
    {
        $isIncoming = $this->is('stok-masuk*');

        return [
            'tanggal_masuk'   => [$isIncoming ? 'required' : 'nullable', 'date'],
            'tanggal_keluar'  => [$isIncoming ? 'nullable' : 'required', 'date'],
            'bahan_baku_id'   => 'required|exists:bahan_baku,id',
            'jumlah'          => 'required|numeric|gt:0',
            'supplier'        => 'nullable|max:150',
            'tanggal_expired' => 'nullable|date',
            'keterangan'      => 'nullable',
        ];
    }
}
