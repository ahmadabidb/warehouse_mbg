<x-app-layout>
    <x-slot name="title">Profile</x-slot>
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4"><div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Informasi Profil</h6></div><div class="card-body">@include('profile.partials.update-profile-information-form')</div></div>
            <div class="card shadow mb-4"><div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Ubah Password</h6></div><div class="card-body">@include('profile.partials.update-password-form')</div></div>
            <div class="card shadow mb-4 border-left-danger"><div class="card-header py-3"><h6 class="m-0 font-weight-bold text-danger">Hapus Akun</h6></div><div class="card-body">@include('profile.partials.delete-user-form')</div></div>
        </div>
    </div>
</x-app-layout>
