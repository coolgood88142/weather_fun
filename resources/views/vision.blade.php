<html>
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <body>
        <div id="app" class="container">
            <input type="hidden" value="{{ $images }}">
            <h2 id="title" class="text-center text-black font-weight-bold" style="margin-bottom:20px;">圖片解析</h2>
            <table id="example" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th></th>
                        <th>圖片</th>
                        <th>keyword</th>
                    </tr>
                </thead>
                <tbody>
                    <!--用checkbox可以做到刪除多個-->
                    <tr v-for="image in images">
                        <td><img src=@{{ image.image }}></td>
                        <td>@{{ image.keyword }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </body>
    <script src="{{mix('js/app.js')}}"></script>  
</html>