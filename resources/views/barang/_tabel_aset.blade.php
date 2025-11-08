@forelse ($barang as $brg)
<tr>
    {{-- 1. Nomor (Paginasi atau Non-Paginasi) --}}
    @if ($barang instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <td>{{ $loop->iteration + ($barang->currentPage() - 1) * $barang->perPage() }}</td>
    @else
        <td>{{ $loop->iteration }}</td>
    @endif
    
    {{-- 2. Nama Barang --}}
    <td>{{ $brg->nama_barang }}</td>
    {{-- 3. Kode Barang --}}
    <td>{{ $brg->kode_barang ?? '-' }}</td>
    {{-- 4. Register --}}
    <td>{{ $brg->register ?? '-' }}</td>
    {{-- 5. Merk/Type --}}
    <td>{{ $brg->merk_type ?? '-' }}</td>
    
    {{-- 6. No. Spek (Baru) --}}
    <td>{{ $brg->nomor_spek ?? '-' }}</td>
    {{-- 7. Bahan (Baru) --}}
    <td>{{ $brg->bahan ?? '-' }}</td>
    
    {{-- 8. Tahun --}}
    <td>{{ $brg->tahun_pembelian ?? '-' }}</td>

    {{-- 9. Ukuran (Baru) --}}
    <td>{{ $brg->ukuran ?? '-' }}</td>
    {{-- 10. Satuan (Baru) --}}
    <td>{{ $brg->satuan ?? '-' }}</td>
    
    {{-- 11. Harga (Baru) --}}
    <td>{{ number_format($brg->harga ?? 0, 0, ',', '.') }}</td>
    
    {{-- 12. Lokasi --}}
    <td>{{ $brg->lokasi->nama_lokasi ?? 'N/A' }}</td>
    {{-- 13. Kondisi --}}
    <td>{{ $brg->kondisi->nama_kondisi ?? 'N/A' }}</td>
    {{-- 14. Status --}}
    <td>
        @php
            $statusClass = 'bg-secondary'; // Default
            if ($brg->statusAset->nama_status == 'Tersedia') $statusClass = 'bg-success';
            if ($brg->statusAset->nama_status == 'Dipinjam') $statusClass = 'bg-warning text-dark';
            if (in_array($brg->statusAset->nama_status, ['Hilang', 'Dihapuskan', 'Rusak Berat (RB)', 'Kurang Baik (KB)'])) $statusClass = 'bg-danger';
        @endphp
        <span class="badge {{ $statusClass }}">{{ $brg->statusAset->nama_status ?? 'N/A' }}</span>
    </td>

    {{-- 15. Pengguna (Baru) --}}
    <td>{{ $brg->pengguna ?? '-' }}</td>

    {{-- 16. Keterangan (Baru) --}}
    <td>{{ \Illuminate\Support\Str::limit($brg->keterangan, 30) }}</td>
    
    {{-- 17. Aksi --}}
    <td>
        <a href="{{ route('barang.show', $brg->id) }}" class="btn btn-sm btn-info">Detail</a>
        <a href="{{ route('barang.edit', $brg->id) }}" class="btn btn-sm btn-warning">Edit</a>
        <form action="{{ route('barang.destroy', $brg->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin hapus aset ini?');">
            @csrf
            @method('DELETE')
            <button typeD="submit" class="btn btn-sm btn-danger">Hapus</button>
        </form>
    </td>
</tr>
@empty
<tr>
    {{-- (Pastikan colspan-nya 17, sesuai jumlah <th> baru) --}}
    <td colspan="17" class="text-center">Data aset tidak ditemukan.</td>
</tr>
@endforelse