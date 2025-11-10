<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ $judul ?? 'Tambah Sumber Barang' }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3>Formulir Sumber Barang Baru</h3>
                    <hr class="my-3">
                    <form action="{{ route('sumberbarang.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="nama_sumber" class="form-label">Nama Sumber</label>
                            <input type="text" class="form-control @error('nama_sumber') is-invalid @enderror" 
                                   id="nama_sumber" name="nama_sumber" value="{{ old('nama_sumber') }}" required>
                            @error('nama_sumber') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <hr class="my-3">
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('sumberbarang.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>