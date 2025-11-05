<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $judul ?? 'Edit Data Barang' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <h3>Edit Data Barang</h3>
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

                    <form action="{{ route('barang.update', $barang->id) }}" method="post">
                        @csrf
                        @method('PUT') 

                        <div class="mb-3">
                            <label for="nama_barang" class="form-label">Nama Barang</label>
                            <input type="text" class="form-control @error('nama_barang') is-invalid @enderror" id="nama_barang" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}" required>
                            @error('nama_barang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="jumlah" class="form-label">Kuantitas</label>
                                <input type="number" class="form-control @error('jumlah') is-invalid @enderror" id="jumlah" name="jumlah" value="{{ old('jumlah', $barang->jumlah) }}" required min="0">
                                @error('jumlah')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="satuan" class="form-label">Satuan</label>
                                <input type="text" class="form-control @error('satuan') is-invalid @enderror" id="satuan" name="satuan" value="{{ old('satuan', $barang->satuan) }}" required>
                                @error('satuan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="id_jenis" class="form-label">Jenis Barang</label>
                            <select class="form-select @error('id_jenis') is-invalid @enderror" id="id_jenis" name="id_jenis" required>
                                @foreach ($jenis as $j)
                                    <option value="{{ $j->id }}" {{ (old('id_jenis', $barang->id_jenis) == $j->id) ? 'selected' : '' }}>
                                        {{ $j->nama_jenis }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_jenis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="id_sumber" class="form-label">Sumber Barang</label>
                            <select class="form-select @error('id_sumber') is-invalid @enderror" id="id_sumber" name="id_sumber" required>
                                @foreach ($sumber as $s)
                                    <option value="{{ $s->id }}" {{ (old('id_sumber', $barang->id_sumber) == $s->id) ? 'selected' : '' }}>
                                        {{ $s->nama_sumber }}
                                    </option>
                                @endforeach
                            </select>
                             @error('id_sumber')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $barang->keterangan) }}</textarea>
                             @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <a href="{{ route('barang.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Update Data</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>