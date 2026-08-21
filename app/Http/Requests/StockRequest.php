<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockRequest extends FormRequest
{
    /**
     * Otorisasi berdasarkan jenis transaksi (masuk atau keluar).
     */
    public function authorize(): bool
    {
        $permission = $this->is('stok-masuk*')
            ? 'stok_masuk.create'
            : 'stok_keluar.create';

        return $this->user()->can($permission);
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
