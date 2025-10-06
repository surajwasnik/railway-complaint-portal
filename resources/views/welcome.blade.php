<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Railway Complaint Portal | Login</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            background-color: #1c388e;
            font-family: 'Figtree', sans-serif;
            color: #fff;
            text-align: center;
        }
        .logo {
            max-width: 180px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 28px;
            font-weight: 600;
        }
    </style>
</head>
<body class="antialiased">

    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">

    <div class="title">
        {{Voyager::setting('admin.title', 'VOYAGER')}}
    </div>

</body>
</html>
