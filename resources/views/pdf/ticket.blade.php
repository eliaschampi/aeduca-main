@extends('layout.main')
@section('content')
<h4 class="title text-primary">NOTA DE VENTA</h4>
<hr />
<table class="printable">
    <tr>
        <td width="30%">
            <strong> N° de Comprobante: </strong>
        </td>
        <td width="70%">
            <p>{{ $income['correlative'] }}</p>
        </td>
    </tr>
    <tr>
        <td width="30%">
            <strong> Razon Social: </strong>
        </td>
        <td width="70%">
            <p>{{ $income['name']['name'] }}</p>
        </td>
    </tr>
    <tr>
        <td width="30%">
            <strong> Fecha de emisión: </strong>
        </td>
        <td width="70%">
            {{ \Carbon\Carbon::parse($income['created_at'])->isoFormat('D [de] MMMM [del] YYYY') }}
        </td>
    </tr>
</table>
<hr/>
<table class="printable">
    <thead>
        <tr>
            <td colspan="4" class="text-primary font-bold">
                DESCRIPCIÓN
            </td>
        </tr>
        <tr>
            <th>Modalidad</th>
            <th>Concepto</th>
            <th>Monto a pagar</th>
            <th>Descuento</th>
            <th>Importe</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($details as $item)
        <tr>
            <td>{{ $item['actiontype']['name'] }}</td>
            <td>{{ $item['title'] }}</td>
            <td>S/: {{ $item['topay'] }}</td>
            <td>S/: {{ $item['discount'] }}</td>
            <td>S/: {{ $item['paid'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<p class="text-right mt-5">
    Total: S/ {{ $income['total'] }}
</p>

<div class="text-center mt-10">
    _______________________
    <div>Recibí conforme</div>
    <div class="text-small text-accent">Firma</div>
</div>
@endsection