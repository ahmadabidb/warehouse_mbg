<?php

namespace App\Http\Controllers;

use App\Http\Requests\BahanBakuRequest;
use App\Models\{BahanBaku, Category};
use App\Services\BahanBakuService;
use Illuminate\Http\Request;

class BahanBakuController extends Controller
{
    public function __construct()
    {
        // Seluruh aksi memerlukan izin bahan_baku.view atau bahan_baku.manage
        $this->middleware('permission:bahan_baku.view|bahan_baku.manage');
    }

    /**
     * Menampilkan daftar bahan baku dengan fitur pencarian dan filter kategori.
     */
    public function index(Request $request)
    {
        $items = BahanBaku::with('category')
            ->when($request->q, fn ($q, $v) => $q->where(
                fn ($x) => $x->where('nama_bahan', 'like', "%$v%")
                              ->orWhere('kode_bahan', 'like', "%$v%")
            ))
            ->when($request->kategori_id, fn ($q, $v) => $q->where('kategori_id', $v))
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('nama_kategori')->get();

        return view('bahan-bakus.index', compact('items', 'categories'));
    }

    /** Menampilkan form tambah bahan baku baru. */
    public function create()
    {
        return view('bahan-bakus.form', [
            'item'       => new BahanBaku,
            'categories' => Category::all(),
        ]);
    }

    /** Menyimpan bahan baku baru. Kode bahan dibuat otomatis oleh Service. */
    public function store(BahanBakuRequest $request, BahanBakuService $service)
    {
        $service->create($request->validated());

        return to_route('bahan-bakus.index')
            ->with('success', 'Bahan baku berhasil ditambahkan. Kode dibuat otomatis.');
    }

    /** Menampilkan form edit bahan baku. */
    public function edit(BahanBaku $bahanBaku)
    {
        return view('bahan-bakus.form', [
            'item'       => $bahanBaku,
            'categories' => Category::all(),
        ]);
    }

    /** Memperbarui data bahan baku. */
    public function update(BahanBakuRequest $request, BahanBaku $bahanBaku)
    {
        $bahanBaku->update($request->validated());

        return to_route('bahan-bakus.index')
            ->with('success', 'Bahan baku berhasil diperbarui.');
    }

    /** Menghapus bahan baku. */
    public function destroy(BahanBaku $bahanBaku)
    {
        $bahanBaku->delete();

        return back()->with('success', 'Bahan baku berhasil dihapus.');
    }
}
