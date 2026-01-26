<table>
    <thead>
        <tr>
            <th colspan="8"
                style="background-color: #2563eb; color: white; font-size: 16px; font-weight: bold; text-align: center; padding: 12px;">
                DATA CUSTOMER
            </th>
        </tr>
        <tr>
            <th colspan="8"
                style="background-color: #eff6ff; color: #1e40af; font-size: 12px; text-align: center; padding: 8px;">
                Exported on: {{ now()->translatedFormat('d F Y H:i') }}
            </th>
        </tr>
        <tr></tr>
        <tr style="background-color: #f3f4f6; font-weight: bold; text-align: center;">
            <th style="border: 1px solid #d1d5db; padding: 10px; background-color: #e5e7eb;">No</th>
            <th style="border: 1px solid #d1d5db; padding: 10px; background-color: #e5e7eb;">Nama</th>
            <th style="border: 1px solid #d1d5db; padding: 10px; background-color: #e5e7eb;">No. Telepon</th>
            <th style="border: 1px solid #d1d5db; padding: 10px; background-color: #e5e7eb;">Tanggal Lahir</th>
            <th style="border: 1px solid #d1d5db; padding: 10px; background-color: #e5e7eb;">Tipe Customer</th>
            <th style="border: 1px solid #d1d5db; padding: 10px; background-color: #e5e7eb;">Kategori</th>
            <th style="border: 1px solid #d1d5db; padding: 10px; background-color: #e5e7eb;">Clubhouse</th>
            <th style="border: 1px solid #d1d5db; padding: 10px; background-color: #e5e7eb;">Catatan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($customers as $index => $customer)
            <tr style="{{ $index % 2 == 0 ? 'background-color: #ffffff;' : 'background-color: #f9fafb;' }}">
                <td style="border: 1px solid #d1d5db; padding: 8px; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #d1d5db; padding: 8px;">{{ $customer->name }}</td>
                <td style="border: 1px solid #d1d5db; padding: 8px;">{{ $customer->phone }}</td>
                <td style="border: 1px solid #d1d5db; padding: 8px; text-align: center;">
                    {{ $customer->dob ? \Carbon\Carbon::parse($customer->dob)->format('d M Y') : '-' }}
                </td>
                <td style="border: 1px solid #d1d5db; padding: 8px; text-align: center;">
                    @if($customer->tipe_customer == 1)
                        Pria
                    @elseif($customer->tipe_customer == 2)
                        Wanita
                    @elseif($customer->tipe_customer == 3)
                        Anak-anak
                    @endif
                </td>
                <td style="border: 1px solid #d1d5db; padding: 8px; text-align: center;">
                    @if($customer->kategory_customer == 1)
                        Umum
                    @elseif($customer->kategory_customer == 2)
                        Coach
                    @elseif($customer->kategory_customer == 3)
                        Private
                    @endif
                </td>
                <td style="border: 1px solid #d1d5db; padding: 8px;">
                    {{ $customer->clubhouse->name ?? '-' }}
                </td>
                <td style="border: 1px solid #d1d5db; padding: 8px;">{{ $customer->catatan ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="8"
                style="border-top: 2px solid #2563eb; padding: 10px; text-align: right; font-weight: bold; background-color: #eff6ff;">
                Total: {{ count($customers) }} Customer
            </td>
        </tr>
    </tfoot>
</table>