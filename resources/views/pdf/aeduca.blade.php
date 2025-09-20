@extends('layout.main')
@section('content')
<h4 class="title text-primary text-center">
    COMPROMISO DE USO DE LA PLATAFORMA AEDUCA
</h4>
<hr />
<p> 
    <b>Aeduca</b> es una plataforma educativa integral en la nube que ofrece funcionalidades de gestión
    de procesos académicos y administrativos de forma centralizada y eficiente, tambien provee funciones 
    interactivas y contenido educativo bajo demanda para su comunidad de usuarios.
    <br>
    Al firmar este documento, el usuario se compromente al uso responsable y respetuoso de su cuenta
</p>
<div class="mt-1 text-table-title">DATOS IMPORTANTES DE ACCESO</div>
<hr>
<table class="printable">
    <tr>
        <td class="w-40 text-table-title">
            Url de acceso:
        </td>
        <td>
            <a class="text-primary" href="https://aula.carrioneduca.edu.pe">https://aula.carrioneduca.edu.pe</a>
        </td>
    </tr>
    <tr>
        <td class="w-40 text-table-title">
            Usuario de acceso
        </td>
        <td>
            {{ $profile->person_dni }}
        </td>
    </tr>
    <tr>
        <td class="w-40 text-table-title">
            Correo para recuperación de cuenta
        </td>
        <td>
            {{ $profile->person->email }}
        </td>
    </tr>
    <tr>
        <td class="w-40 text-table-title">
            Contraseña preestablecida:
        </td>
        <td>
            {{ $profile->original_password }}
        </td>
    </tr>
    <tr>
        <td class="text-muted" colspan="2">
            Esta contraseña es generada por el sistema. Dado que hay riesgo con la contraseña original, es
            responsabilidad
            del apoderado, estudiante o docente protejer su contraseña, por ello es necesario cambiar posteriormente
            aquí <a href="https://aula.carrioneduca.edu.pe/profile/security">https://aula.carrioneduca.edu.pe/profile/security</a>
        </td>
    </tr>
</table>
<div class="text-center mt-5">
    ________________________
    <div>{{ $profile->person->name . ' ' . $profile->person->lastname }}</div>
    <span class="text-accent text-small">Firma del usuario ó apoderado</span>
</div>
@endsection