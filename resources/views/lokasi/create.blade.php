<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ $judul ?? 'Tambah Lokasi Baru' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <h3>Formulir Lokasi Baru</h3>
                    <hr class="my-3">

                    <form action="{{ route('lokasi.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="nama_lokasi" class="form-label">Nama Lokasi/Ruangan</label>
                            <input type="text" class="form-control @error('nama_lokasi') is-invalid @enderror" 
                                   id="nama_lokasi" name="nama_lokasi" value="{{ old('nama_lokasi') }}" required>
                            @error('nama_lokasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="penanggung_jawab" class="form-label">Penanggung Jawab (Opsional)</label>
                            <input type="text" class="form-control @error('penanggung_jawab') is-invalid @enderror" 
                                   id="penanggung_jawab" name="penanggung_jawab" value="{{ old('penanggung_jawab') }}">
                            @error('penanggung_jawab') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <hr class="my-3">
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('lokasi.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Lokasi</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>