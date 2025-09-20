@extends('layout.main')
@section('content')
<h4 class="title text-center text-table-title">INFORMACIÓN DEL APODERADO</h4>
<hr />
<table style="width: 100%;">
    <tr>
        <td style="width: 30%;">
            <img src="default/deleted.png" alt="" width="160px" />
        </td>
        <td>
            <table class="printable">
                <tr>
                    <td class="w-40 text-table-title">
                        Nombre y Apellidos:
                    </td>
                    <td>
                        <b>{{ $family->person->name }}</b>
                        {{ $family->person->lastname }}
                    </td>
                </tr>
                <tr>
                    <td class="w-40 text-table-title">
                        DNI:
                    </td>
                    <td>{{ $family->dni }}</td>
                </tr>
                <tr>
                    <td class="w-40 text-table-title">
                        Fecha de Nacimiento:
                    </td>
                    <td>{{ \Carbon\Carbon::parse($family->person->birthdate)->isoFormat('D [de] MMMM [del] YYYY') }}</td>
                </tr>
                <tr>
                    <td class="w-40 text-table-title">
                        Estudiantes:
                    </td>
                    <td>{{ count($family->students) }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<div class="mt-1 text-table-title">INFORMACIÓN DE CONTACTO</div>
<hr>
<table class="smalltable">
    <tr>
        <td class="w-40 text-table-title">
            Distrito:
        </td>
        <td>
            {{ $family->person->ubigeo }} - {{ $family->person->district }}
        </td>
    </tr>
    <tr>
        <td class="w-40 text-table-title">
            Dirección:
        </td>
        <td>
            {{ $family->person->address }}
        </td>
    </tr>
    <tr>
        <td class="w-40 text-table-title">
            Teléfono/celular de contacto:
        </td>
        <td>
            {{ $family->person->phone }} {{ '- ' . $family->telephone }}
        </td>
    </tr>
    <tr>
        <td class="w-40 text-table-title">
            Correo:
        </td>
        <td>
            {{ $family->person->email }}
        </td>
    </tr>
</table>
<div class="mt-1 text-table-title">INFORMACIÓN ADICIONAL</div>
<hr>
<table class="printable">
    <tr>
        <td class="w-40 text-table-title">
            Profesión:
        </td>
        <td>
            {{ $family->profession }}
        </td>
    </tr>
    <tr>
        <td class="w-40 text-table-title">
            Nivel de estudios:
        </td>
        <td>
            {{ $family->degree }}
        </td>
    </tr>
    <tr>
        <td class="w-40 text-table-title">
            I.E. Básica regular
        </td>
        <td>
            {{ $family->institute }}
        </td>
    </tr>
</table>

<div class="mt-1 text-table-title">ESTUDIANTES</div>
<hr>
<table class="printable">
    <thead>
        <tr class="bg-info">
            <th>Estudiante: </th>
            <th>Parentezco: </th>
            <th>¿Encargado Principal?</th>
        </tr>
    </thead>
    @if (count($family->students) > 0)
    @foreach ($family->students as $student)
    <tbody>
        <tr>
            <td>{{ $student->person->name . ' ' . $student->person->lastname }}</td>
            <td>{{ $rt[$student->pivot->relation_type] }}</td>
            <td>{{ $student->pivot->is_main ? 'Si' : 'No' }}</td>
        </tr>
    </tbody>
    @endforeach
    @else
    <tbody>
        <tr>
            <td class="text-muted text-center" colspan="4">Aun no hay Estudiantes</td>
        </tr>
    </tbody>
    @endif
</table>
<div class="text-small mt-1 text-right">
    <b>Observaciones: </b>
    {{ $family->person->obs }}
</div>
@endsection