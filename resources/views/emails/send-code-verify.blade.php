<!DOCTYPE html>
<html>
<head>
    <title>Verification Code</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 16px;
            line-height: 1.5;
            color: #333;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        h2 {
            font-size: 36px; /* Increase the font size here */
            margin-bottom: 10px;
        }
        p {
            margin-bottom: 10px;
        }
        .panel {
            background-color: #f0f0f0;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: #4CAF50;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #3e8e41;
        }
    </style>
</head>
<body>
<div style="max-width: 600px; margin: 0 auto;">
    <h1 style="text-align: center;">Verification Code</h1>

    <p>Your verification code is :</p>

    <div class="panel">
        <h2 style="text-align: center; margin-top: 0; font-size: 36px;">{{ $code }}</h2> <!-- Add font-size: 36px; here -->
    </div>

    <p>Please enter this code on the verification page to complete your registration .</p>

    <p>Thank you for choosing our service !</p>
    <p>{{ config('app.name') }} Team .</p>
</div>
</body>
</html>
