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
            color: #003766;
            font-family: Montserrat,sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
            font-size: 15px;
        }


        .content {
            text-align: center;
            max-width: 1050px;
            margin: 0 auto;
            padding: 10%;
            background-color: #f5f5f5;
            color: #003766;
            font-family: Montserrat,sans-serif;
        }

        .logo{
            width: 180px;
            margin: 30px auto;
        }

        .logo img{
            width: 100%;
            height: auto;
        }

        .bloc{
            margin: 0 auto;
            width: 500px;
            margin-bottom: 10px;
            word-wrap: break-word;
        }

        .main{
            margin-top:30px;
        }

        button{
            background-color: #42b5ba !important;
            border-color: #42b5ba !important;
            color: white;
            padding: 10px;
            border-radius: 3px;
            margin-top:10px;
            text-transform: uppercase;
            cursor: pointer;
            min-width: 200px;
            font-weight: bold;
        }

        .links > a {
            color: #1ac8aa;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .content hr {
            display: block;
            clear: both;
            height: 2px;
            width: 70%;
            margin: 0 auto;
            padding: 0;
            border: 0;
            font-family: arial;
            text-align: center;
            font-size: 100px;
            line-height: 1;
            background-color: #42b5ba;
        }

        </style>
    </head>
    <body>
        <div class="content">

            <div class="logo">
                <img src = "{{asset('/logo.png')}}" />
            </div>

            <h1> {{ $title }} </h1>

            <hr/>

            <div class="bloc main">
                <p>
                    {{ $description }}
                </p>
            </div>

            <div class="bloc" style="margin-top:20px">
                    <a href="{{ $url }}">
                    <button onclick="{{ $url }}" type="button">
                        {{ $button_sentance }}
                    </button>
                </a>
            </div>

            <div class="bloc main">
                <p>
                    {{ $additional_information }}
                </p>
            </div>

            <hr/>

            <div class="bloc">
                <p>
                    {{ $ending_sentance }}
                    </br>
                    {{ $team }}
                </p>
            </div>
        </div>
    </body>
</html>