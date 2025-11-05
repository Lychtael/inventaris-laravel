    @forelse($barang as $brg)
    <tr>
        @if ($barang instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <td>{{ $loop->iteration + ($barang->currentPage() - 1) * $barang->perPage() }}</td>
        @else
            <td>{{ $loop->iteration }}</td>
        @endif
        
        <td>{{ $brg->nama_barang }}</td>
        <td>{{ $brg->jumlah }}</td>
        <td>{{ $brg->satuan }}</td>
        <td>{{ $brg->jenis->nama_jenis ?? '-' }}</td>
        <td>{{ $brg->sumber->nama_sumber ?? '-' }}</td>
        <td>
            {{ \Illuminate\Support\Str::limit($brg->keterangan, 40) }}
        </td>
        <td>
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
        <td colspan="8" class="text-center">Data tidak ditemukan.</td>
    </tr>
    @endforelse