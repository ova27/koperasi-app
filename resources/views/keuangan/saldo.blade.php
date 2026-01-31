@extends('layouts.main')

@section('title', 'Saldo Kas')

@section('content')
<div class="container">
    <h4>Saldo Kas & Bank</h4>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Rekening</th>
                <th>Jenis</th>
                <th class="text-end">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr>
                    <td>{{ $row['nama'] }}</td>
                    <td>{{ ucfirst($row['jenis']) }}</td>
                    <td class="text-end">
                        Rp {{ number_format($row['saldo'], 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="fw-bold">
                <td colspan="2">TOTAL</td>
                <td class="text-end">
                    Rp {{ number_format($totalSaldo, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>
</div>
@endsection
