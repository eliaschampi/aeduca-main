@extends('layout.main')
@section('content')
<h4 class="title text-primary">CONSTANCIA DE ASISTENCIA</h4>
<hr />
<table class="smalltable">
    @if(!empty($reg))
        <tr>
            <td width="30%">
                <strong> Estudiante: </strong>
            </td>
            <td width="70%">
                {{ $reg->student->person->name . ' ' . $reg->student->person->lastname }}
            </td>
        </tr>
        <tr>
            <td width="30%">
                <strong> Última Matrícula: </strong>
            </td>
            <td width="70%">
                <b>{{ $reg->code }}</b>
                <span class="text-accent">
                    {{ substr($reg->section_code, -2) . ' de ' . $reg->level }}
                </span>
            </td>
        </tr>
    @endif
    <tr>
        <td width="30%">
            <strong> Reporte: </strong>
        </td>
        <td width="70%">
            {{ \Carbon\Carbon::parse($from)->isoFormat('D [de] MMMM') }} -
            {{ \Carbon\Carbon::parse($to)->isoFormat('D [de] MMMM') }}
        </td>
    </tr>
</table>
<table class="printable mt-1">
    <tr>
        <th>Fecha</th>
        <th>Turno</th>
        <th>Hora de ingreso</th>
        <th>Estado</th>
        <th>Justificación</th>
    </tr>
    @foreach ($attendances as $item)
    <tr>
        <td>{{ \Carbon\Carbon::parse($item->created_at)->isoFormat('D [de] MMMM') }}</td>
        <td>{{ $item->priority === 1 ? 'Mañana' : 'Tarde' }}</td>
        <td>{{ $item->entry_time }}</td>
        <td>{{ $item->state }}</td>
        <td>{{ empty($item->justification) ? ' ' : 'Envió justificación' }}</td>
    </tr>
    @endforeach
</table>
@endsection
