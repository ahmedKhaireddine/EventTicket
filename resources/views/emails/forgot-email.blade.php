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
            width: 300px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .main{
            margin-top:30px;
        }

        button{
            background-color: #1ac8aa !important;
            border-color: #1ac8aa !important;
            color: white;
            padding: 10px;
            border-radius: 3px;
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

        </style>
    </head>
    <body>
        <div class="content">

            <div class="logo">
                <img src = "logo.png" />
            </div>

            <h1> {{ $title }} </h1>

            <div class="bloc main">
                <h3> {{ $greeting }} </h3>
                <p> {{ $description_line }} </p>
            </div>

            <div class="bloc" style="margin-top:20px">
                <a href="{{ $url }}">
                    <button onclick="{{ $url }}" type="button">
                        {{ $button_sentence }}
                    </button>
                </a>
            </div>

            <div class="bloc">
                <p> {{ $additional_information }} </p>
                </br>
                <p>
                    {{ $ending_sentence }}
                    </br>
                    {{ $team_sentance }}
                </p>
            </div>
        </div>
    </body>
</html>