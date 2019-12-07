<!doctype html>
<html lang="it">
    <head>
        <title>@yield('title')</title>

        <style>
            @import url('https://fonts.googleapis.com/css?family=IBM+Plex+Sans:600&display=swap');

            * {
                font-family: 'IBM Plex Sans', sans-serif;
                color: #424548;
            }

            header {
                color: #FFFFFF;
                padding: 15px;
                background: linear-gradient(-45deg, #0056C1, #003476);
            }

            header h1 {
                margin-top: 0;
                margin-bottom: 0;
                color: white;
            }

            #wrapper {
                display: flex;
            }

            #container {
                max-width: 600px;
                border: 1px solid #dedede;
                margin-right: auto;
                margin-left: auto;
            }

            .mail-footer {
                background-color: #212121;
                color: #FFFFFF;
                padding: 10px 15px;
                margin-top: 60px;
            }

            .mail-footer p {
                color: #FFFFFF !important;
            }

            .btn {
                height: 36px;
                letter-spacing: 0.3px;
                line-height: 36px;
                padding-left: 15px;
                padding-right: 15px;
                cursor: pointer;
                text-decoration: none;
                border-radius: 4px;
                display: inline-block;
                font-size: 14px;
                font-weight: 600;
                text-transform: uppercase;
            }

            .btn-outline-primary {
                background-color: #FFFFFF;
                border: 1px solid #0056C1;
                text-transform: uppercase;
            }

            .btn-primary {
                background-color: #0056C1;
                color: white !important;
            }

            .btn-primary:hover {
                background-color: #003476;
            }

            .btn-outline-primary:hover {
                background-color: #0056C1;
                color: #FFFFFF;
            }

            .text-muted {
                color: #8D9196;
            }

            .mr-3 {
                margin-right: 1em;
            }

            .mb-3 {
                margin-bottom: 1em;
            }

            .mb-4 {
                margin-bottom: 1.5em;
            }

            .mb-5 {
                margin-bottom: 2em;
            }

            .mt-3 {
                margin-top: 1em;
            }

            .mt-4 {
                margin-top: 1.5em;
            }

            .mt-5 {
                margin-top: 2em;
            }

            .px-5 {
                margin-left: 2em;
                margin-right: 2em;
            }

            .center {
                margin-left: auto;
                margin-right: auto;
            }

            .text-bold {
                font-weight: bold;
            }

            .d-flex {
                display: flex;
            }

            .align-items-center {
                align-items: center;
            }

            .data-row {
                border-bottom: 1px solid #ededed;
            }

            .data-row > div {
                width: 150px;
            }
        </style>
    </head>
    <body>
        <div id="wrapper">
            <div id="container">
                <header>
                    <h1>ZANICHELLI</h1>
                </header>

                @yield('body')

                <div class="mail-footer">
                    <p>&copy; 2019-2020 Zanichelli Editore S.p.a.</p>
                </div>
            </div>
        </div>
    </body>
</html>