@extends('front.admin.index')

@section('title', 'Data Shitf Hari ini')
@section('page-title', 'Daftar Shift Hari ini')
@vite('resources/css/admin/member.css')
@vite('resources/js/admin/index.js')
@push('styles')
    <style>
        .btn.success {
            background-color: #16a34a;
            /* hijau */
            color: white;
            border: none;
        }

        .btn.success:hover {
            background-color: #15803d;
        }
    </style>
@endpush
@section('top-controls')
    <form method="GET" action="{{ route('admin.shift') }}" class="filter-bar">
        <div class="filter-group">
            {{-- Filter Nama Staff --}}
            <div class="form-field">
                <label>Nama Staff</label>
                <input type="text" name="staff_name" value="{{ request('staff_name') }}" placeholder="Cari Nama Staff">
            </div>

            {{-- Filter Status --}}
            <div class="form-field">
                <label>Status</label>
                <select name="status">
                    <option value="">Semua</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Di Buka</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Di Tutup</option>
                </select>
            </div>
        </div>

        {{-- Tombol Filter --}}
        <button type="submit" class="btn primary">Terapkan Filter</button>

        {{-- Tombol Reset --}}
        <a href="{{ route('admin.shift') }}" class="btn reset">Reset</a>

        {{-- Tombol Export --}}
        <a href="{{ route('admin.shift.export', request()->query()) }}" class="btn success">Export</a>
    </form>
@endsection




@push('styles')
    <style>
        .status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 0.875rem;
            /* text-sm */
        }

        .status.new {
            background-color: #fee2e2;
            /* bg-red-100 */
            color: #b91c1c;
            /* text-red-700 */
            border: 1px solid #fca5a5;
            /* border-red-300 */
        }

        .status.pending {
            background-color: #dcfce7;
            /* bg-green-100 */
            color: #166534;
            /* text-green-700 */
            border: 1px solid #86efac;
            /* border-green-300 */
        }


        .pagination-wrapper {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }

        .pagination {
            list-style: none;
            display: flex;
            gap: 5px;
            padding: 0;
        }

        .pagination li {
            display: inline-block;
        }

        .pagination li a,
        .pagination li span {
            padding: 6px 12px;
            border: 1px solid #ddd;
            background: #fff;
            color: #333;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s;
        }

        .pagination li a:hover {
            background: #007bff;
            color: #fff;
            border-color: #007bff;
        }

        .pagination li.active span {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }

        .pagination li.disabled span {
            background: #f1f1f1;
            color: #999;
            cursor: not-allowed;
        }
    </style>
@endpush


@section('content')
    {{-- <div class="cards">
        <div class="card">
            <h3>Kasir Aktif</h3>
            <p>{{ $staff->name ?? $staff->username }}</p>
        </div>
        <div class="card">
            <h3>Saldo Awal</h3>
            <p>Rp {{ number_format($saldoAwal, 0, ',', '.') }}</p>
        </div>
        <div class="card">
            <h3>Total Transaksi</h3>
            <p>{{ $totalTransaksi }}</p>
        </div>
        <div class="card">
            <h3>Pendapatan Hari Ini</h3>
            <p>Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</p>
        </div>
        <div class="card">
            <h3>Pendapatan + Saldo Awal</h3>
            <p>Rp {{ number_format($pendapatanDenganSaldo, 0, ',', '.') }}</p>
        </div>
        <div class="card">
            <h3>Menunggu</h3>
            <p>{{ $pending }}</p>
        </div>
    </div> --}}


    <section class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama Staff</th>
                    <th>Saldo Awal</th>
                    <th>Saldo Akhir</th>
                    <th>Penjualan FnB</th>
                    <th>Minus Balance</th>
                    <th>Waktu Buka</th>
                    <th>Waktu Tutup</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($shift as $sh)
                    <tr>
                        <td>{{ $sh->staff?->name ?? '-' }}</td>
                        <td>Rp {{ number_format($sh->saldo_awal, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($sh->saldo_akhir, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($sh->pendapatan_fnb, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($sh->minus_balance, 0, ',', '.') }}</td>
                        <td>{{ $sh->waktu_buka }}</td>
                        <td>{{ $sh->waktu_tutup }}</td>
                        <td>
                            @if ($sh->status == 0)
                                <span class="status new">Di Tutup</span>
                            @elseif($sh->status == 1)
                                <span class="status pending">Di Buka</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="small">Belum ada Shift hari ini</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            <ul class="pagination">
                {{-- Tombol Previous --}}
                @if ($shift->onFirstPage())
                    <li class="disabled"><span>&laquo; Prev</span></li>
                @else
                    <li><a href="{{ $shift->previousPageUrl() }}">&laquo; Prev</a></li>
                @endif

                {{-- Nomor Halaman --}}
                @foreach ($shift->getUrlRange(1, $shift->lastPage()) as $page => $url)
                    @if ($page == $shift->currentPage())
                        <li class="active"><span>{{ $page }}</span></li>
                    @else
                        <li><a href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach

                {{-- Tombol Next --}}
                @if ($shift->hasMorePages())
                    <li><a href="{{ $shift->nextPageUrl() }}">Next &raquo;</a></li>
                @else
                    <li class="disabled"><span>Next &raquo;</span></li>
                @endif
            </ul>
        </div>
    </section>
@endsection
