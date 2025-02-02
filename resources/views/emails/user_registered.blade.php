<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your questionary was confirmed</title>
    <style>

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
        }
        p {
            color: #555;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Welcome, {{ $user->name }}!</h1>
    <p>Admin confirmed your questionary, now you can login with {{ $user->email }} on <a href="{{ url('/') }}">{{ url('/') }}</a></p>
</div>
</body>
</html>
