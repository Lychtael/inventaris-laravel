<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ $judul ?? 'Edit Status Aset' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <h3>Formulir Edit Status Aset</h3>
                    <hr class="my-3">

                    <form action="{{ route('status-aset.update', $statusAset->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="nama_status" class="form-label">Nama Status</label>
                            <input type.text" class="form-control @error('nama_status') is-invalid @enderror" 
                                   id="nama_status" name="nama_status" value="{{ old('nama_status', $statusAset->nama_status) }}" required>
                            @error('nama_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <hr class="my-3">
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('status-aset.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Update Status</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>