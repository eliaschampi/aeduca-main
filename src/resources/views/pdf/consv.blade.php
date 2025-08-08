@extends('layout.main')
@section('content')
<h3 class="title font-bold">
    <i>
        El que suscribe Director de la Institución Educativa de Gestión Privada
        <br>
        {{ $data['name'] }}
    </i>
</h3>
<h3 class="font-bold mt-1"><i>Otorga lo presente:</i></h3>
<h2 class="text-center text-primary">CONSTANCIA DE VACANTE {{ date('Y') }}</h2>

<table class="printable">
    <tbody>
        <tr>
            <td class="text-table-title">
                ESTUDIANTE
            </td>
            <td colspan="3">
                <b>{{ strtoupper($data['student']) }}</b>
            </td>
        </tr>
        <tr>
            <td class="text-table-title">
                INSTITUCIÓN EDUCATIVA
            </td>
            <td>
                <b>{{ $data['modular_code'] }}</b>
            </td>
            <td colspan="2">
                <b>{{ $data['name'] }}</b>
            </td>
        </tr>
        <tr>
            <td class="text-table-title">
                NIVEL EDUCATIVO
            </td>
            <td>
                <b>{{ $data['full_cycle'] }}</b>
            </td>
            <td class="text-table-title">
                GRADO EDUCATIVO
            </td>
            <td>
                <b>{{ $data['full_degree'] }}</b>
            </td>
        </tr>
        <tr>
            <td class="text-table-title">
                SECCIÓN
            </td>
            <td>
                <b>{{ $data['section'] }}</b>
            </td>
            <td class="text-table-title">
                TURNO
            </td>
            <td>
                <b>MAÑANA</b>
            </td>
        </tr>
    </tbody>
</table>

<h3 class="font-bold mt-1">
    <i>Se expide la presente a petición del apoderado para fines de traslado.</i>
</h3>

<p class="float-end mt-1" style="text-align: right;"><i>CUSCO, {{ now()->isoFormat('D [de] MMMM [del] YYYY') }}</i></p>

<p class="text-center" style="margin-top: 6em;">______________________________ <br>
    <small>
        Firma - Post Firma y Sello
        <br>
        Director(a)
    </small>
</p>

<div class="additional" style="margin-top: 5em;">
    <p class="font-bold"><u>Requísitos para la matrícula</u></p>
    <ol>
        <li><i>Ficha única de matrícula con el código del educando y código modular de la I.E de Procedencia.</i></li>
        <li><i>Fotocopia del DNI del alumno y apoderados.</i></li>
        <li><i>Partida de Nacimiento original.</i></li>
        <li><i>Certificado de Estudios oficiales.</i></li>
        <li><i>Constancia de no deudor.</i></li>
        <li><i>Dos Fotos tamaño carnet.</i></li>
        <li><i>Resolución Directoral de Autorización de traslado.</i></li>
        <li><i>Traer todos los documentos en una mica.</i></li>
    </ol>
</div>
@endsection