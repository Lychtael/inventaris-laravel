<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $judul ?? 'Catat Peminjaman Baru' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <h3>Catat Peminjaman Baru</h3>
                    <hr class="my-3">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong> Ada masalah dengan input Anda.<br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('peminjaman.store') }}" method="post">
                        @csrf
                        <div class="mb-3">
                            <label for="id_barang" class="form-label">Pilih Barang</label>
                            <select class="form-select @error('id_barang') is-invalid @enderror" id="id_barang" name="id_barang" required>
                                <option value="" disabled {{ old('id_barang') ? '' : 'selected' }}>-- Pilih Barang --</option>
                                @foreach ($barang as $brg)
                                    <option value="{{ $brg->id }}" {{ old('id_barang') == $brg->id ? 'selected' : '' }}>
                                        (Stok: {{ $brg->jumlah }}) {{ $brg->nama_barang }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_barang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="peminjam" class="form-label">Nama Peminjam</label>
                            <input type="text" class="form-control @error('peminjam') is-invalid @enderror" id="peminjam" name="peminjam" value="{{ old('peminjam') }}" required>
                            @error('peminjam')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="jumlah_dipinjam" class="form-label">Jumlah Pinjam</label>
                                <input type="number" class="form-control @error('jumlah_dipinjam') is-invalid @enderror" id="jumlah_dipinjam" name="jumlah_dipinjam" value="{{ old('jumlah_dipinjam') }}" required min="1">
                                @error('jumlah_dipinjam')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam</label>
                                <input type="date" class="form-control @error('tanggal_pinjam') is-invalid @enderror" id="tanggal_pinjam" name="tanggal_pinjam" value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" required>
                                @error('tanggal_pinjam')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>