<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ $judul ?? 'Edit Jenis Barang' }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3>Formulir Edit Jenis Barang</h3>
                    <hr class="my-3">
                    <form action="{{ route('jenisbarang.update', $jenisBarang->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="nama_jenis" class="form-label">Nama Jenis</label>
                            <input type.text" class="form-control @error('nama_jenis') is-invalid @enderror" 
                                   id="nama_jenis" name="nama_jenis" value="{{ old('nama_jenis', $jenisBarang->nama_jenis) }}" required>
                            @error('nama_jenis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <hr class="my-3">
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('jenisbarang.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>