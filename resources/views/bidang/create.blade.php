<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ $judul ?? 'Tambah Bidang Baru' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <h3>Formulir Bidang Baru</h3>
                    <hr class="my-3">

                    <form action="{{ route('bidang.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="id_dinas" class="form-label">Induk Dinas</label>
                            <select class="form-select @error('id_dinas') is-invalid @enderror" id="id_dinas" name="id_dinas" required>
                                <option value="">-- Pilih Dinas --</option>
                                @foreach ($dinas as $item)
                                    <option value="{{ $item->id }}" {{ old('id_dinas') == $item->id ? 'selected' : '' }}>{{ $item->nama_dinas }}</option>
                                @endforeach
                            </select>
                            @error('id_dinas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nama_bidang" class="form-label">Nama Bidang</label>
                            <input type="text" class="form-control @error('nama_bidang') is-invalid @enderror" 
                                   id="nama_bidang" name="nama_bidang" value="{{ old('nama_bidang') }}" required>
                            @error('nama_bidang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <hr class="my-3">
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('bidang.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Bidang</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>