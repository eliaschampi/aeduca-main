@extends('layout.main')
@section('content')
<h4 class="title text-primary">
    FICHA DE {{ Str::of($title)->upper() }}
</h4>
<hr />
<div class="text-primary font-bold">Motivo:</div>
<p>{{ $incidence->title }}</p>
<div class="text-primary font-bold">Desarrollo:</div>
<div>{!! $incidence->description !!}</div>
<div class="text-primary font-bold">Conclusión y acuerdos:</div>
<p class="text-justify">{{ $incidence->agreement }}</p>
<strong class="text-primary">Involucrados</strong>

<table class="printable">
    <thead>
        <tr>
            <th>Es:</th>
            <th>DNI</th>
            <th>Nombre y Apellidos</th>
            <th>Rol</th>
            <th>Huella/Firma</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($incidence->persons as $item)
        <tr>
            <td>{{ config('main.atype.' . $item->pivot->entity_type) }}</td>
            <td>{{ $item->dni }}</td>
            <td>{{ $item->name . ' ' . $item->lastname }}</td>
            <td>{{ $item->pivot->actor_type }}</td>
            <td></td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="text-right mt-5">
    <p>
        <em><b>Cusco: </b>
            {{ \Carbon\Carbon::parse($incidence->created_at)->isoFormat('D [de] MMMM [del] YYYY') }}
        </em>
    </p>
</div>
<table class="w-full mt-10">
    <tr>
        <td width="50%" class="text-center">
            ________________________
            <div>{{ $incidence->user->name . ' ' . $incidence->user->lastname }}</div>
            <span class="text-accent text-small">{{ $incidence->user->rol->name }}</span>
        </td>
        <td width="50%" class="text-center">
            _______________________
            <br>
            <span>Firma del apoderado</span>
        </td>
    </tr>
</table>
@if (!empty($incidence->file_attached))
<i class="mt-5 text-small text-right"><i>* Este documento tiene adjuntos</i></i>
@endif
@endsection