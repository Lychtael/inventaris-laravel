<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $judul ?? 'Data Sumber Barang' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <h3>Data Sumber Barang</h3>
                    <hr class="my-3">

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <button type="button" class="btn btn-success mb-3 tombolTambahData" data-bs-toggle="modal" data-bs-target="#formModal">
                        Tambah Sumber Barang
                    </button>

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Nama Sumber</th>
                                <th style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sumber_barang as $sb)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $sb->nama_sumber }}</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-warning tampilModalUbah" data-bs-toggle="modal" data-bs-target="#formModal" data-id="{{ $sb->id }}">Edit</a>
                                    
                                    <form action="{{ route('sumberbarang.destroy', $sb->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">Data tidak ditemukan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="judulModal" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="judulModal">Tambah Sumber Barang</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            
            <form action="{{ route('sumberbarang.store') }}" method="post">
              @csrf
              <input type="hidden" name="id" id="id">
              <div class="form-group">
                <label for="nama_sumber">Nama Sumber</label>
                <input type="text" class="form-control" id="nama_sumber" name="nama_sumber" required>
              </div>
            
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Simpan Data</button>
            </form>
          </div>
        </div>
      </div>
    </div>
    
    @push('scripts')
    <script>
    $(function() {
        
        // 1. Script untuk Tombol TAMBAH
        $('.tombolTambahData').on('click', function() {
            $('#judulModal').html('Tambah Data Sumber Barang');
            $('.modal-body form').attr('action', '{{ route("sumberbarang.store") }}');
            // Hapus input _method jika ada
            if ($('input[name="_method"]').length) {
                $('input[name="_method"]').remove();
            }
            // Reset form
            $('#nama_sumber').val('');
            $('#id').val('');
        });

        // 2. Script untuk Tombol UBAH
        $('.tampilModalUbah').on('click', function() {
            $('#judulModal').html('Ubah Data Sumber Barang');
            
            const id = $(this).data('id');
            // Buat URL update
            const url = '{{ url("sumberbarang") }}/' + id;
            $('.modal-body form').attr('action', url);
            
            // Tambahkan input _method('PUT') jika belum ada
            if (!$('input[name="_method"]').length) {
                $('.modal-body form').prepend('<input type="hidden" name="_method" value="PUT">');
            }

            // AJAX untuk ambil data
            $.ajax({
                url: '{{ route("sumberbarang.getubah") }}',
                data: {
                    id : id,
                    _token: '{{ csrf_token() }}' // Tambahkan CSRF token
                },
                method: 'post',
                dataType: 'json',
                success: function(data) {
                    $('#nama_sumber').val(data.nama_sumber);
                    $('#id').val(data.id);
                }
            });
        });

    });
    </script>
    @endpush
</x-app-layout>