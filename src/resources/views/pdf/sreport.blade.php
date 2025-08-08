@extends("layout.main")
@section('content')
<h4 class="title text-primary">REPORTE DE NOTAS</h4>
<table class="smalltable">
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
            <strong> Matrícula: </strong>
        </td>
        <td width="70%">
            <b>{{ $register_code }}</b>
            <span class="text-accent">
                {{ substr($reg->section_code, -2) . ' de ' . $reg->level }}
            </span>
        </td>
    </tr>

</table>
<table class="printable mt-1">
    <tr>
        <th> Curso </th>
        <th> Título </th>
        <th> Fecha </th>
        <th> Nota </th>
        <th> Observaciones </th>
    </tr>
    @foreach ($lessons as $item)
    <tr>
        <td>{{ $item->name }}</td>
        <td>{{ $item->title }}</td>
        <td> {{ \Carbon\Carbon::parse($item->exam_day)->isoFormat('D [de] MMMM') }}</td>
        <td>
            <b>{{ $item->score }}</b>
        </td>
        <td><small>{{ $item->obs }}</small></td>
    </tr>
    @endforeach
</table>
<hr />
@endsection