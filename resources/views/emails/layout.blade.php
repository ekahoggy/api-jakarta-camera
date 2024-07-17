<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f6f6f6;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(90deg, #004A7C, #007ACC);
            color: #ffffff;
            text-align: center;
            padding: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 20px;
        }

        .content h2 {
            color: #333333;
        }

        .content p {
            color: #666666;
            line-height: 1.5;
        }

        .footer {
            background-color: #f1f1f1;
            text-align: center;
            padding: 10px;
            font-size: 12px;
            color: #666666;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            font-size: 16px;
            color: #ffffff !important;
            background: linear-gradient(90deg, #004A7C, #007ACC);
            text-decoration: none;
            border-radius: 5px;
        }

        .button:hover {
            background: linear-gradient(90deg, #007ACC, #004A7C);
        }

        .content {
            padding: 20px;
        }

        .content h2 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .content p {
            line-height: 1.5;
        }

        .product-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .product-list li {
            margin-bottom: 20px;
        }

        .product-list img {
            display: block;
            width: 150px;
            height: 150px;
            margin-bottom: 10px;
        }

        .product-list h3 {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .product-list p {
            margin-bottom: 0;
        }

        .product-list .price {
            font-weight: bold;
            color: #f00;
        }

        .product-list .discount {
            text-decoration: line-through;
            color: #ccc;
            margin-right: 5px;
        }

        .cta {
            text-align: center;
            margin-top: 20px;
        }

        .cta a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }

        @media (max-width: 600px) {

            .content,
            .header,
            .footer {
                padding: 15px;
            }

            .header h1 {
                font-size: 20px;
            }

            .button {
                padding: 10px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="https://jakartacamera-admin.moodstudio.id/assets/img/logo.png" alt="logo-jakcam">
        </div>
        <div class="content">
            @yield('content')
        </div>
        <div class="footer">
            <p>Jl. Kedoya Duri Raya No. 20A Jakarta Barat 11520</p>
            <p>021-58354635 – 58354655 | WA : 0812 9876 7036</p>
            <p>© Jakarta Kamera {{ date('Y') }} | PT. JAKCAM SUKSES MANDIRI</p>
        </div>
    </div>
</body>

</html>
