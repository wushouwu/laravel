<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title></title>
    <style>
        html,body,#app{
            width:100%;
            height:100%;
            padding:0px;
            margin:0px;
            min-width:800px;
        }
    </style>
</head>
<body>
    <div id="app"><router-view></router-view></div>  
</body>
<script src="{{ mix('js/manifest.js') }}"></script>
<script src="{{ mix('js/vendor.js') }}"></script>
<script src="{{ mix('js/app.js') }}"></script>  
</html>