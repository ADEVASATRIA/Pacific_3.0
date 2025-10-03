@extends('front.admin.index')

@section('title', 'Data Transaksi')
@section('page-title', 'Daftar Transaksi')

@section('top-controls')
  <button class="btn">Export</button>
  {{-- <button class="btn primary">Buat Transaksi</button> --}}
@endsection

@section('content')
  <div class="cards">
    <div class="card">
      <h3>Total Transaksi</h3>
      <p>{{ $totalTransaksi }}</p>
    </div>
    <div class="card">
      <h3>Pendapatan Hari Ini</h3>
      <p>Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</p>
    </div>
    <div class="card">
      <h3>Menunggu</h3>
      <p>{{ $pending }}</p>
    </div>
  </div>

  <section class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Customer</th>
          <th>Tanggal</th>
          <th>Items</th>
          <th>Total</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($transactions as $tx)
          <tr>
            <td><strong>{{ $tx->id }}</strong></td>
            <td>{{ $tx->customer?->name ?? '-' }}</td>
            <td class="small">{{ $tx->created_at->format('Y-m-d H:i') }}</td>
            <td>
              <ul>
                @foreach($tx->purchaseDetails as $detail)
                  <li>{{ $detail->name }} (x{{ $detail->qty }})</li>
                @endforeach
              </ul>
            </td>
            <td>Rp {{ number_format($tx->total, 0, ',', '.') }}</td>
            <td>
              @if($tx->status == 0)
                <span class="status new">Baru</span>
              @elseif($tx->status == 1)
                <span class="status pending">Pending</span>
              @elseif($tx->status == 2)
                <span class="status paid">Paid</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="small">Belum ada transaksi hari ini</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </section>
@endsection
