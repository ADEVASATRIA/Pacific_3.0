@extends('front.admin.index')

@section('title', 'Data Member')
@section('page-title', 'Daftar Member Pacific')

@section('top-controls')
  <form method="GET" class="filter-bar">
    <div class="filter-group">
      <input type="text" name="name" placeholder="Cari Nama" value="{{ request('name') }}">
      <input type="text" name="phone" placeholder="No. Telp" value="{{ request('phone') }}">
      <input type="date" name="dob" value="{{ request('dob') }}">
      <input type="date" name="awal_masa" value="{{ request('awal_masa') }}">
      <input type="date" name="akhir_masa" value="{{ request('akhir_masa') }}">
      <select name="active">
        <option value="">Status</option>
        <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>Aktif</option>
        <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>Nonaktif</option>
      </select>
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
            $endDate = $member->tiketTerbaru?->date_end ? \Carbon\Carbon::parse($member->tiketTerbaru->date_end) : null;
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
            <td>{{ $member->tiketTerbaru?->date_start ? \Carbon\Carbon::parse($member->tiketTerbaru->date_start)->format('d M Y') : '-' }}</td>
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
  </section>
@endsection
