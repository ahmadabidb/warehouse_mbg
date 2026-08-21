<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BahanBakuRequest extends FormRequest
{
    /**
     * Hanya pengguna dengan izin 'bahan_baku.manage' yang dapat mengakses.
     */
    public function authorize(): bool
    {
        return $this->user()->can('bahan_baku.manage');
    }

    /**
     * Aturan validasi data bahan baku.
     */
    public function rules(): array
    {
        return [
            'nama_bahan'   => 'required|max:150',
            'kategori_id'  => 'required|exists:categories,id',
            'satuan'       => 'required|in:Kg,Gram,Liter,Ml,Pcs,Pack',
            'stok'         => 'required|numeric|min:0',
            'stok_minimum' => 'required|numeric|min:0',
            'deskripsi'    => 'nullable',
        ];
    }
}
