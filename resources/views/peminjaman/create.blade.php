<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ $judul ?? 'Catat Peminjaman Aset' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <h3>Formulir Peminjaman Aset</h3>
                    <hr class="my-3">

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('peminjaman.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="id_barang" class="form-label">Aset yang Tersedia (Hanya status "Tersedia" yang tampil)</label>
                            <select class="form-select @error('id_barang') is-invalid @enderror" 
                                    id="id_barang" name="id_barang" required>
                                <option value="">-- Pilih Aset --</option>
                                @forelse ($barang as $item)
                                    <option value="{{ $item->id }}" {{ old('id_barang') == $item->id ? 'selected' : '' }}>
                                        {{ $item->nama_barang }} (Reg: {{ $item->register }}) (Bidang: {{ $item->bidang->nama_bidang ?? 'N/A' }})
                                    </option>
                                @empty
                                    <option value="" disabled>Tidak ada aset yang tersedia untuk dipinjam.</option>
                                @endforelse
                            </select>
                            @error('id_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="peminjam_eksternal" class="form-label">Nama Peminjam</label>
                            <input type="text" class="form-control @error('peminjam_eksternal') is-invalid @enderror" 
                                   id="peminjam_eksternal" name="peminjam_eksternal" value="{{ old('peminjam_eksternal') }}" required>
                            @error('peminjam_eksternal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam</label>
                            <input type="date" class="form-control @error('tanggal_pinjam') is-invalid @enderror" 
                                   id="tanggal_pinjam" name="tanggal_pinjam" value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" required>
                            @error('tanggal_pinjam') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                      id="keterangan" name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
                            @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <hr class="my-3">
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Peminjaman</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>