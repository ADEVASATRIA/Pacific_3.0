@extends('front.admin.index')

@section('title', 'Data Member')
@section('page-title', 'Daftar Member Pacific')
@vite('resources/css/admin/member.css')
@stack('styles')

@section('top-controls')
    <form method="GET" class="filter-bar">
        <div class="filter-group">
            <div class="form-field">
                <label>Nama Customer</label>
                <input type="text" name="name" value="{{ request('name') }}" placeholder="Cari Nama">
            </div>
            <div class="form-field">
                <label>No. Telepon</label>
                <input type="text" name="phone" value="{{ request('phone') }}" placeholder="08xxxx">
            </div>
            <div class="form-field">
                <label>Tanggal Lahir</label>
                <input type="date" name="dob" value="{{ request('dob') }}">
            </div>
            <div class="form-field">
                <label>Awal Masa Berlaku</label>
                <input type="date" name="awal_masa" value="{{ request('awal_masa') }}">
            </div>
            <div class="form-field">
                <label>Akhir Masa Berlaku</label>
                <input type="date" name="akhir_masa" value="{{ request('akhir_masa') }}">
            </div>
            <div class="form-field">
                <label>Status</label>
                <select name="active">
                    <option value="">Semua</option>
                    <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn primary">Terapkan Filter</button>
        <a href="{{ route('admin.member') }}" class="btn reset">Reset</a>
    </form>
@endsection




@section('content')
    <div class="cards">
        <div class="card">
            <h3>Total Member Aktif</h3>
            <p>{{ $totalActive }}</p>
        </div>
        <div class="card">
            <h3>Total Member All-Time</h3>
            <p>{{ $totalAll }}</p>
        </div>
    </div>

    <section class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Member</th>
                    <th>Phone</th>
                    <th>Masa Awal</th>
                    <th>Masa Akhir</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $member)
                    @php
                        $today = \Carbon\Carbon::today();
                        $endDate = $member->tiketTerbaru?->date_end
                            ? \Carbon\Carbon::parse($member->tiketTerbaru->date_end)
                            : null;
                        if ($endDate && $endDate->gte($today)) {
                            $status = 'Aktif';
                            $statusClass = 'status success';
                        } else {
                            $status = 'Nonaktif';
                            $statusClass = 'status danger';
                        }
                    @endphp
                    <tr>
                        <td>{{ $member->id }}</td>
                        <td>{{ $member->name }}</td>
                        <td>{{ $member->phone }}</td>
                        <td>{{ $member->tiketTerbaru?->date_start ? \Carbon\Carbon::parse($member->tiketTerbaru->date_start)->format('d M Y') : '-' }}
                        </td>
                        <td>{{ $endDate?->format('d M Y') ?? '-' }}</td>
                        <td><span class="{{ $statusClass }}">{{ $status }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="small">Belum ada data member</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination-wrapper">
            <ul class="pagination">
                {{-- Tombol Previous --}}
                @if ($members->onFirstPage())
                    <li class="disabled"><span>&laquo; Prev</span></li>
                @else
                    <li><a href="{{ $members->previousPageUrl() }}">&laquo; Prev</a></li>
                @endif

                {{-- Nomor Halaman --}}
                @foreach ($members->getUrlRange(1, $members->lastPage()) as $page => $url)
                    @if ($page == $members->currentPage())
                        <li class="active"><span>{{ $page }}</span></li>
                    @else
                        <li><a href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach

                {{-- Tombol Next --}}
                @if ($members->hasMorePages())
                    <li><a href="{{ $members->nextPageUrl() }}">Next &raquo;</a></li>
                @else
                    <li class="disabled"><span>Next &raquo;</span></li>
                @endif
            </ul>
        </div>


    </section>
@endsection
