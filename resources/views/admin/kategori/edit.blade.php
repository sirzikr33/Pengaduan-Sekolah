@extends('layouts.admin')

@section('title', 'Edit Kategori')
@section('page_title', 'Edit Kategori')
@section('breadcrumb', 'Data / Kategori / Edit')

@section('content')
    <div class="form-card">
        <form method="POST" action="{{ route('admin.kategori.update', $kategori) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label" for="nama_kategori">Nama Kategori</label>
                <input
                    type="text"
                    id="nama_kategori"
                    name="nama_kategori"
                    class="form-control {{ $errors->has('nama_kategori') ? 'is-invalid' : '' }}"
                    value="{{ old('nama_kategori', $kategori->nama_kategori) }}"
                    placeholder="Contoh: Fasilitas, Kurikulum..."
                    autofocus
                >
                @error('nama_kategori')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div style="display:flex; gap:0.5rem; margin-top:1.75rem;">
                <button type="submit" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/></svg>
                    Perbarui
                </button>
                <a href="{{ route('admin.kategori.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
@endsection
