<x-app-layout>
    <x-slot name="title">Kategori</x-slot>

    <!-- Search & Add Button -->
    <div class="d-flex justify-content-between mb-3">
        <form class="d-flex gap-2" method="GET">
            <input 
                class="form-control" 
                name="q" 
                value="{{ request('q') }}" 
                placeholder="Cari kategori"
            >
            <button class="btn btn-outline-secondary">Cari</button>
        </form>
        <a class="btn btn-primary" href="{{ route('categories.create') }}">
            + Tambah Kategori
        </a>
    </div>

    <!-- Table Section -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>{{ $category->nama_kategori }}</td>
                            <td>{{ $category->deskripsi }}</td>
                            <td class="text-end">
                                <a 
                                    class="btn btn-sm btn-outline-primary" 
                                    href="{{ route('categories.edit', $category) }}"
                                >
                                    Edit
                                </a>
                                <form class="d-inline" method="POST" action="{{ route('categories.destroy', $category) }}">
                                    @csrf 
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">Belum ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-3">
        {{ $categories->links() }}
    </div>
</x-app-layout>
