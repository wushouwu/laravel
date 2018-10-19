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
<script>
    const USER=@json(Auth::user());
</script>
<script src="{{ mix('js/manifest.js') }}"></script>
<script src="{{ mix('js/vendor.js') }}"></script>
<script src="//{{ Request::getHost() }}:6001/socket.io/socket.io.js"></script>
<script src="{{ mix('js/app.js') }}"></script>
<script>
try{
    window.Echo = new echo({
        broadcaster: 'socket.io',
        host: window.location.hostname + ':6001'
    });
    var audio=document.createElement("audio");
    audio.src="media/audio/notice.mp3";
    document.body.appendChild(audio);
    Echo.channel('notice')
    .listen('notice', (e) => {
        if(e.notice.role.indexOf(USER.role)>=0 || e.notice.dept.indexOf(USER.dept)>=0 || e.notice.user.indexOf(USER.id)>=0){
            var Notification=APP.$notify({
                title: e.notice.title,
                dangerouslyUseHTMLString:true,
                message: e.notice.content,
                duration:0,
                position:'bottom-right',
                onClick:function(){
                    var title='消息-'+e.notice.id
                    APP.$children[0].addTab({
                        value:title,
                        label:title,
                        content:'formVue',
                        query:{TABLE_NAME:'notices',view_name:'数据-编辑',url:'admin/table/viewView',row:e.notice}
                    });
                    Notification.close();
                }
            });
            audio.play();
        } 
    });   
}catch(e){
    console.log(e);
}
</script>
</html>