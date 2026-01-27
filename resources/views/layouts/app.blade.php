<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'NewOLS')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .header {
            background: #343a40;
            color: white;
            padding: 15px;
        }

        .sidebar {
            width: 220px;
            background: #f8f9fa;
            height: 100vh;
            float: left;
            padding: 15px;
        }

        .content {
            margin-left: 220px;
            padding: 20px;
        }

        .footer {
            background: #f1f1f1;
            padding: 10px;
            text-align: center;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        a {
            text-decoration: none;
            color: #333;
            display: block;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    @include('partials.header')

    @include('partials.sidebar')

    <div class="content">
        @yield('content')
    </div>

    @include('partials.footer')

</body>

</html>
