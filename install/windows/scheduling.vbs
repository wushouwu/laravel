Set ws = CreateObject("Wscript.Shell")   
ws.run "cmd /c php D:\program\wamp64\www\laravel\artisan schedule:run 1>>NUL 2>&1",vbhide
