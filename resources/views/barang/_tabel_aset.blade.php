@forelse ($barang as $brg)
<tr>
    <td>{{ $loop->iteration + ($barang->currentPage() - 1) * $barang->perPage() }}</td>
    <td>{{ $brg->nama_barang }}</td>
    <td>{{ $brg->kode_barang ?? '-' }}</td>
    <td>{{ $brg->register ?? '-' }}</td>
    <td>{{ $brg->merk_type ?? '-' }}</td>
    <td>{{ $brg->tahun_pembelian ?? '-' }}</td>
    <td>{{ $brg->lokasi->nama_lokasi ?? 'N/A' }}</td>
    <td>{{ $brg->kondisi->nama_kondisi ?? 'N/A' }}</td>
    <td>
        {{-- Kita beri warna statusnya --}}
        @php
            $statusClass = 'bg-secondary'; // Default
            if ($brg->statusAset->nama_status == 'Tersedia') $statusClass = 'bg-success';
            if ($brg->statusAset->nama_status == 'Dipinjam') $statusClass = 'bg-warning text-dark';
            if ($brg->statusAset->nama_status == 'Hilang' || $brg->statusAset->nama_status == 'Dihapuskan') $statusClass = 'bg-danger';
        @endphp
        <span class="badge {{ $statusClass }}">{{ $brg->statusAset->nama_status ?? 'N/A' }}</span>
    </td>
    <td>
        <a href="{{ route('barang.show', $brg->id) }}" class="btn btn-sm btn-info">Detail</a>
        <a href="{{ route('barang.edit', $brg->id) }}" class="btn btn-sm btn-warning">Edit</a>
        <form action="{{ route('barang.destroy', $brg->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin hapus aset ini?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
        </form>
    </td>
</tr>
@empty
<tr>
    <td colspan="10" class="text-center">Data aset tidak ditemukan.</td>
</tr>
@endforelse