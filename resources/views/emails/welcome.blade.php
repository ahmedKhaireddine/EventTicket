<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">



        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #f5f5f5;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .logo{
                width: 180px;
                margin: 30px auto;
            }

            .logo img{
                width: 100%;
                height: auto;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .description{
                margin: 50px auto;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #42b5ba;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div class="content">
                <div class="logo">
                    <img src = "{{asset('/logo.png')}}" />
                </div>
                <h1> {{ $greeting }} </h1>
                <div class="description">
                    {{ $description }}
                </div>
                <div class="links">
                    <a href="#">{{ $website }}</a>
                    <a href="#">{{ $admin }}</a>
                    <a href="#">{{ $news }}</a>
                </div>
            </div>
        </div>
    </body>
</html>