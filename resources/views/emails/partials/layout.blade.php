<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;">
    <title>FilesCollector</title>

    <style type="text/css">

        div, p, a, li, td {
            -webkit-text-size-adjust: none;
        }

        * {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .ReadMsgBody {
            width: 100%;
            background-color: #ffffff;
        }

        .ExternalClass {
            width: 100%;
            background-color: #ffffff;
        }

        body {
            width: 100%;
            height: 100%;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }

        html {
            width: 100%;
            background-color: #ffffff;
        }

        @font-face {
            font-family: 'proxima_novalight';
            src: url('http://rocketway.net/themebuilder/products/font/proximanova-light-webfont.eot');
            src: url('http://rocketway.net/themebuilder/products/font/proximanova-light-webfont.eot?#iefix') format('embedded-opentype'), url('http://rocketway.net/themebuilder/products/font/proximanova-light-webfont.woff') format('woff'), url('http://rocketway.net/themebuilder/products/font/proximanova-light-webfont.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'proxima_nova_rgregular';
            src: url('http://rocketway.net/themebuilder/products/font/proximanova-regular-webfont.eot');
            src: url('http://rocketway.net/themebuilder/products/font/proximanova-regular-webfont.eot?#iefix') format('embedded-opentype'), url('http://rocketway.net/themebuilder/products/font/proximanova-regular-webfont.woff') format('woff'), url('http://rocketway.net/themebuilder/products/font/proximanova-regular-webfont.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'proxima_novasemibold';
            src: url('http://rocketway.net/themebuilder/products/font/proximanova-semibold-webfont.eot');
            src: url('http://rocketway.net/themebuilder/products/font/proximanova-semibold-webfont.eot?#iefix') format('embedded-opentype'), url('http://rocketway.net/themebuilder/products/font/proximanova-semibold-webfont.woff') format('woff'), url('http://rocketway.net/themebuilder/products/font/proximanova-semibold-webfont.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'proxima_nova_rgbold';
            src: url('http://rocketway.net/themebuilder/products/font/proximanova-bold-webfont.eot');
            src: url('http://rocketway.net/themebuilder/products/font/proximanova-bold-webfont.eot?#iefix') format('embedded-opentype'), url('http://rocketway.net/themebuilder/products/font/proximanova-bold-webfont.woff') format('woff'), url('http://rocketway.net/themebuilder/products/font/proximanova-bold-webfont.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'proxima_novathin';
            src: url('http://rocketway.net/themebuilder/products/font/proximanova-thin-webfont.eot');
            src: url('http://rocketway.net/themebuilder/products/font/proximanova-thin-webfont.eot?#iefix') format('embedded-opentype'), url('http://rocketway.net/themebuilder/products/font/proximanova-thin-webfont.woff') format('woff'), url('http://rocketway.net/themebuilder/products/font/proximanova-thin-webfont.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'proxima_novaextrabold';
            src: url('http://rocketway.net/themebuilder/products/font/proximanova-extrabold-webfont.eot');
            src: url('http://rocketway.net/themebuilder/products/font/proximanova-extrabold-webfont.eot?#iefix') format('embedded-opentype'), url('http://rocketway.net/themebuilder/products/font/proximanova-extrabold-webfont.woff2') format('woff2'), url('http://rocketway.net/themebuilder/products/font/proximanova-extrabold-webfont.woff') format('woff'), url('http://rocketway.net/themebuilder/products/font/proximanova-extrabold-webfont.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        p {
            padding: 0 !important;
            margin-top: 0 !important;
            margin-right: 0 !important;
            margin-bottom: 0 !important;
            margin-left: 0 !important;
        }

        .hover:hover {
            opacity: 0.85;
            filter: alpha(opacity=85);
        }

        .image73 img {
            width: 73px;
            height: auto;
        }

        .image42 img {
            width: 42px;
            height: auto;
        }

        .image400 img {
            width: 400px;
            height: auto;
        }

        .icon49 img {
            width: 49px;
            height: auto;
        }

        .image113 img {
            width: 113px;
            height: auto;
        }

        .image70 img {
            width: 70px;
            height: auto;
        }

        .image67 img {
            width: 67px;
            height: auto;
        }

        .image80 img {
            width: 80px;
            height: auto;
        }

        .image35 img {
            width: 35px;
            height: auto;
        }

        .icon49 img {
            width: 49px;
            height: auto;
        }

    </style>


    <style type="text/css"> @media only screen and (max-width: 479px) {
            body {
                width: auto !important;
            }

            table[class=full] {
                width: 100% !important;
                clear: both;
            }

            table[class=mobile] {
                width: 100% !important;
                padding-left: 30px;
                padding-right: 30px;
                clear: both;
            }

            table[class=fullCenter] {
                width: 100% !important;
                text-align: center !important;
                clear: both;
            }

            td[class=fullCenter] {
                width: 100% !important;
                text-align: center !important;
                clear: both;
            }

            *[class=erase] {
                display: none;
            }

            *[class=buttonScale] {
                float: none !important;
                text-align: center !important;
                display: inline-block !important;
                clear: both;
            }

            .image400 img {
                width: 100% !important;
                height: auto;
            }
        }

        }</style>


</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" yahoo="fix">

<div class="ui-sortable" id="sort_them">

    @yield('content')

</div>
</body>
<style>body {
        background: url("http://rocketway.net/themebuilder/products/notifications/patterns/noise_pattern_with_crosslines.png") !important;
    } </style>