<html>
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="/css/stock.css">
    </head>
    <body>
        <div id="app" class="container">
            <h2 id="title" class="text-center text-black font-weight-bold" style="margin-bottom:20px;">圖片解析</h2>
            <upload :upload-url="saveVisionUrl"></upload>
            <keyword :key-word-url="url"></keyword>
        </div>
    </body>
    <script src="{{mix('js/app.js')}}"></script>
    <script src="{{mix('js/vision.js')}}"></script>
</html>