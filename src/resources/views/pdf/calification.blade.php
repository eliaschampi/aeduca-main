@extends('layout.main')
@section('content')
<h4 class="title text-table-title text-center">
    CONSTANCIA DE NOTAS N°{{ $lesson->code }}
</h4>
<p>
    Esta constancia ha sido generada por la plataforma <b>Aeduca</b> a partir de las respuestas de su exámen.
    Tiene la finalidad de comunicar al interesado el desempeño del estudiante.
</p>
<table class="printable">
    <tr>
        <td width="30%">
            <strong> Exámen </strong>
        </td>
        <td width="70%">
            {{ $lesson->title }}
        </td>
    </tr>
    <tr>
        <td width="30%">
            <strong> Fecha del exámen </strong>
        </td>
        <td width="70%">
            {{ \Carbon\Carbon::parse($lesson->exam_day)->isoFormat('D [de] MMMM [del] YYYY') }}
        </td>
    </tr>
    <tr>
        <td width="30%">
            <strong> Estudiante: </strong>
        </td>
        <td width="70%">
            {{ $register->student->person->name . ' ' . $register->student->person->lastname }}
        </td>
    </tr>
    <tr>
        <td width="30%">
            <strong> Modalidad </strong>
        </td>
        <td width="70%">
            {{ $register->level }}
        </td>
    </tr>
    <tr>
        <td width="30%">
            <strong> Grupo/Sección </strong>
        </td>
        <td width="70%">
            {{ substr($register->section_code, -2) }}
        </td>
    </tr>
    <tr>
        <td width="30%">
            <strong> Código </strong>
        </td>
        <td width="70%">
            {{ $register->code }}
        </td>
    </tr>
</table>
<div class="text-table-title mt-1">Clave de respuestas</div>
<hr />
<div class="scores">
    @for ($i = 0; $i < strlen($allkeys); $i++)
        <div class="scorebox">
            <div class="head">
                {{ $allkeys[$i] }}
            </div>
            <div class="sub">
                {{ $lesson->keys[$i] !== ' ' ? $lesson->keys[$i] : '-' }}
            </div>
        </div>
    @endfor
</div>
<table class="smalltable">
    <tr>
        <th>N° preguntas</th>
        <th>Rptas Correctas</th>
        <th>Rptas Incorrectas</th>
        <th>Rptas Blancas</th>
        <th>Puntaje obtenido</th>
    </tr>
    <tr>
        <td>{{ strlen($allkeys) }}</td>
        <td>{{ $oks }}</td>
        <td>{{ $bads }}</td>
        <td>{{ $empties }}</td>
        <td>{{ $lesson->score }}</td>
    </tr>
    @foreach ($formated as $item)
    <tr>
        <td colspan="3"></td>
        <td>
            <b class="text-primary">{{ $item['course'] }}</b>
        </td>
        <td>{{ $item['score'] }}</td>
    </tr>
    @endforeach
</table>
<i class="mt-1 text-small"><b>Observaciones:</b>{{ $lesson->obs }}</i>
@endsection