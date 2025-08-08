 @php
     $config = App\Helpers\MainHelper::vueGlobalVariables();
 @endphp
 <!DOCTYPE html>
 <html lang="es">

 <head>
     <meta charset="utf-8">
     <meta http-equiv="X-UAs-Compatible" content="IE=edge">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <meta name="description" content="Aeduca administrativo">
     <meta name="author" content="EliasChampi">
     <title>{{ config('app.name') }}</title>
     <link rel="icon" href="/favicon.ico">
     <link rel="stylesheet" href="/css/ionicons.min.css">
     @vite(["resources/sass/app.scss", "resources/js/app.js"])
     <script>
         window.Laravel = {!! $config !!}
     </script>
 </head>

 <body>
     <div id="app"></div>
 </body>

 </html>
