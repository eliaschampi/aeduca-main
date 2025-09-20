<html>

<head>
    <meta charset="utf-8">
    <style>
        @page {
            size: 5.4cm 8.6cm landscape;
            padding: 0;
            margin: 0;
        }

        body {
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 21px;
        }

        .carnet {
            overflow: hidden;
            width: 8.6cm;
            height: 5.4cm;
            display: inline-block;
        }

        .carnet>img {
            position: absolute;
            z-index: -100;
            width: 8.6cm;
            height: 5.6cm;
            page-break-inside: avoid;
        }

        .carnet .qr-wrapper {
            display: block;
            position: absolute;
            width: 3.5cm;
            height: 3.5cm;
            margin-top: 1.1cm;
            margin-left: 0.1cm;
            z-index: 10;
        }

        .carnet .qr-wrapper img {
            width: 85%;
            margin-left: 2mm;
            margin-top: 2mm;
        }

        .carnet .user {
            display: block;
            position: absolute;
            overflow: hidden;
            width: 2.6cm;
            height: 2.9cm;
            margin-left: 5.28cm;
            border-bottom: 4px solid rgb(230, 84, 0);
            border-right: 3px solid rgb(230, 84, 0);
            border-left: 3px solid rgb(230, 84, 0);
            border-radius: 1.5mm;
            padding: 0;
        }

        .carnet .user img {
            width: 2.6cm;
            margin-top: 0.4cm;
            margin-left: 0;
            margin-right: 0;
            margin-bottom: 0;
        }

        .carnet p.dni {
            font-weight: bold;
            text-align: center;
            font-size: 13px;
            color: #0B1C2E;
            margin-left: 0.2cm;
            margin-top: 2.6cm;
            transform: rotate(90deg);
            letter-spacing: 1mm;
        }

        .carnet .data {
            width: 3.7cm;
            height: 2cm;
            overflow: hidden;
            margin-top: -0.2cm;
            margin-left: 4.85cm;
            text-align: center;
        }

        .carnet .data span.name {
            display: block;
            color: #223e9c;
            font-size: 3mm;
        }

        .carnet .data .title,
        .carnet .data i.acode {
            font-size: 2.5mm;
        }

        .carnet .data .title {
            margin-top: 1mm;
            color: #de6134;
        }

        .carnet .data i.acode {
            margin-top: 1mm;
            display: block;
            color: #000000;
            letter-spacing: 1mm;
        }
    </style>
</head>

<body>
    <div class="carnet">
        <img src="default/card.png" alt="bitmap">
        <div class="qr-wrapper">
            <img src="data:image/png;base64, {!! base64_encode(
                QrCode::style('round')->format('png')->size(500)->generate($person->dni),
            ) !!}" alt="qr" />
        </div>
        <div class="user">
            @if (!empty($person->profile) && !empty($person->profile->image))
            <img src="default/{!! $person->profile->image !!}" alt="Foto" />
            @else
            <img src="default/deleted.png" alt="Foto" />
            @endif
        </div>
        <p class="dni">{{ $person->dni }}</p>
        <div class="data">
            <span class="name">
                {{ $person->name }}
                <br />
                {{ $person->lastname }}
            </span>
            <div class="title">
                {{ $title }}
            </div>
            <i class="acode">{{ $acode }}</i>
        </div>
    </div>
</body>

</html>