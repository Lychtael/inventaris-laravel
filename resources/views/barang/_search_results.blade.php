@forelse($barang as $brg)
<tr>
    {{-- Logika nomor urut (Paginasi atau Non-Paginasi) --}}
    @if ($barang instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <td>{{ $loop->iteration + ($barang->currentPage() - 1) * $barang->perPage() }}</td>
    @else
        <td>{{ $loop->iteration }}</td>
    @endif
    
    <td>{{ $brg->nama_barang }}</td>
    <td>{{ $brg->jumlah }}</td>
    <td>{{ $brg->satuan }}</td>

    {{-- ++ KOLOM BARU YANG DIPERBAIKI ++ --}}
    <td>{{ $brg->kondisi->nama_kondisi ?? '-' }}</td>
    
    <td>{{ $brg->jenis->nama_jenis ?? '-' }}</td>
    <td>{{ $brg->sumber->nama_sumber ?? '-' }}</td>
    <td>
        {{-- Limit keterangan agar tidak terlalu panjang --}}
        {{ \Illuminate\Support\Str::limit($brg->keterangan, 40) }}
    </td>
    <td>
        {{-- 
          Catatan: Route model binding lebih baik pakai $brg (objek) 
          daripada $brg->id (angka), tapi ini juga valid.
        --}}
        <a href="{{ route('barang.show', $brg->id) }}" class="btn btn-sm btn-info">Detail</a>
        <a href="{{ route('barang.edit', $brg->id) }}" class="btn btn-sm btn-warning">Edit</a>
        <form action="{{ route('barang.destroy', $brg->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
        </form>
    </td>
</tr>
@empty
<tr>
    {{-- ++ PERBAIKI COLSPAN MENJADI 9 ++ --}}
    <td colspan="9" class="text-center">Data tidak ditemukan.</td>
</tr>
@endforelse