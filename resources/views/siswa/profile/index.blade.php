@extends('layouts.siswa')

@section('title', 'Profil Saya')
@section('page_title', 'Profil Saya')
@section('breadcrumb', 'Pengaturan / Profil')

@section('content')
<div class="form-card" style="margin: 0 auto; margin-top: 1rem;">
    <div style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--text);">Ganti Password</h2>
        <p style="font-size: 0.85rem; color: var(--muted); margin-top: 0.25rem;">
            Ganti password default Anda untuk melindungi akses akun menggunakan NISN.
        </p>
    </div>

    <form method="POST" action="{{ route('siswa.profile.update') }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label" for="current_password">Password Saat Ini</label>
            <input type="password" name="current_password" id="current_password" 
                class="form-control @error('current_password') is-invalid @enderror" 
                placeholder="Masukkan password saat ini" required>
            @error('current_password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password Baru</label>
            <input type="password" name="password" id="password" 
                class="form-control @error('password') is-invalid @enderror" 
                placeholder="Minimal 6 karakter" required>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group" style="margin-bottom: 2rem;">
            <label class="form-label" for="password_confirmation">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" id="password_confirmation" 
                class="form-control" 
                placeholder="Ketik ulang password baru" required>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/></svg>    
                Simpan Perubahan
            </button>
            <a href="{{ route('siswa.dashboard') }}" class="btn btn-ghost">Batal</a>
        </div>
    </form>
</div>
@endsection
