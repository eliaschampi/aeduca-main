@extends("layout.main")
@section('content')
<h4 class="title text-primary">NOTA DE EGRESO N° {{ $expense->code }}</h4>
<hr />
<table class="smalltable">
    <tr>
        <td width="30%">
            <strong> Usuario: </strong>
        </td>
        <td width="70%">
            <p>{{ $expense->cash->user->name }}</p>
        </td>
    </tr>
    <tr>
        <td width="30%">
            <strong> Modalidad: </strong>
        </td>
        <td width="70%">
            <p>{{ $expense->actiontype->name }}</p>
        </td>
    </tr>
    <tr>
        <td width="30%">
            <strong> Concepto: </strong>
        </td>
        <td width="70%">
            <p>{{ $expense->description }}</p>
        </td>
    </tr>
    <tr>
        <td width="30%">
            <strong> Número de Comprobante: </strong>
        </td>
        <td width="70%">
            <p>{{ $expense->voucher_num }}</p>
        </td>
    </tr>
    <tr>
        <td width="30%">
            <strong> Monto: </strong>
        </td>
        <td width="70%">
            <p>S/: {{ $expense->total }}</p>
        </td>
    </tr>
</table>

@if (count($expense->detail) > 0)
<table class="printable">
    <thead>
        <tr>
            <td colspan="4" class="text-primary font-bold">
                DETALLE DEL GASTO
            </td>
        </tr>
        <tr>
            <th>Descripción</th>
            <th>Precio Unitario</th>
            <th>Cantidad</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($expense->detail as $item)
        <tr>
            <td>{{ $item->description }}</td>
            <td>S/: {{ $item->unit_price }}</td>
            <td>{{ $item->quantity }}</td>
            <td>S/: {{ $item->quantity * $item->unit_price }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
<p class="text-right mt-5">
    <b>Cusco, </b>
    {{ \Carbon\Carbon::parse($expense->created_at)->isoFormat('D [de] MMMM [del] YYYY') }}
</p>

<div class="text-center mt-10">
    _______________________
    <div>Recibí conforme</div>
    <div class="text-small text-accent">Firma y DNI</div>
</div>
@endsection