@extends('layout.main')
@section('content')
<h4 class="title text-center text-table-title">INFORMACIÓN DEL ESTUDIANTE</h4>
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
                        <b>{{ $student->person->name }}</b>
                        {{ $student->person->lastname }}
                    </td>
                </tr>
                <tr>
                    <td class="w-40 text-table-title">
                        DNI:
                    </td>
                    <td>{{ $student->dni }}</td>
                </tr>
                <tr>
                    <td class="w-40 text-table-title">
                        Fecha de Nacimiento:
                    </td>
                    <td>{{ \Carbon\Carbon::parse($student->person->birthdate)->isoFormat('D [de] MMMM [del] YYYY') }}
                    </td>
                </tr>
                <tr>
                    <td class="w-40 text-table-title">
                        Sede:
                    </td>
                    <td>{{ $student->branch->name }}</td>
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
            {{ $student->person->ubigeo }} - {{ $student->person->district }}
        </td>
    </tr>
    <tr>
        <td class="w-40 text-table-title">
            Dirección:
        </td>
        <td>
            {{ $student->person->address }}
        </td>
    </tr>
    <tr>
        <td class="w-40 text-table-title">
            Celular o Telf:
        </td>
        <td>
            {{ $student->person->phone }}
        </td>
    </tr>
    <tr>
        <td class="w-40 text-table-title">
            Correo:
        </td>
        <td>
            {{ $student->person->email }}
        </td>
    </tr>
</table>
<div class="mt-1 text-table-title">INFORMACIÓN ACADÉMICA</div>
<hr>
<table class="printable">
    <thead>
        <tr class="bg-info">
            <th>Año</th>
            <th>Ciclo</th>
            <th>Grado y Sección</th>
            <th>Estado</th>
            <th>Mensualidad</th>
        </tr>
    </thead>
    @if (count($student->registers) > 0)
    @foreach ($student->registers as $register)
    <tbody>
        <tr>
            <td>{{ substr($register->section_code, 0, 4) }}</td>
            <td>
                {{ $cycles[substr($register->section_code, 4, 2)] }}
            </td>
            <td>
                {{ substr($register->section_code, -2) }}
            </td>
            <td>{{ $status[$register->state] }}</td>
            <td>S:/ {{ $register->monthly }}</td>
        </tr>
    </tbody>
    @endforeach
    @else
    <tbody>
        <tr>
            <td class="text-muted text-center" colspan="5">Aun no hay registros</td>
        </tr>
    </tbody>
    @endif
</table>
<div class="mt-1 text-table-title">APODERADOS</div>
<hr>
<table class="printable">
    <thead>
        <tr class="bg-info">
            <th>Apoderado: </th>
            <th>Como: </th>
            <th>¿Encargado Principal?</th>
            <th>Celular</th>
        </tr>
    </thead>
    @if (count($student->families) > 0)
    @foreach ($student->families as $family)
    <tbody>
        <tr>
            <td>{{ $family->person->name . ' ' . $family->person->lastname }}</td>
            <td>{{ $rt[$family->pivot->relation_type] }}</td>
            <td>{{ $family->pivot->is_main ? 'Si' : 'No' }}</td>
            <td>{{ $family->person->phone }}</td>
        </tr>
    </tbody>
    @endforeach
    @else
    <tbody>
        <tr>
            <td class="text-muted text-center" colspan="4">Aun no hay apoderados</td>
        </tr>
    </tbody>
    @endif
</table>
<div class="text-small mt-1 text-right">
    <b>Observaciones: </b>
    {{ $student->person->obs }}
</div>
@endsection