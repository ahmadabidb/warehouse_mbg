<x-app-layout>
    <x-slot name="title">Pengguna</x-slot>

    <!-- Add Button -->
    <div class="text-end mb-3">
        <a class="btn btn-primary" href="{{ route('users.create') }}">
            + Tambah User
        </a>
    </div>

    <!-- Table Section -->
    <div class="card border-0 shadow-sm table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->getRoleNames()->join(', ') }}</td>
                        <td class="text-end">
                            <a 
                                class="btn btn-sm btn-outline-primary" 
                                href="{{ route('users.edit', $user) }}"
                            >
                                Edit
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
