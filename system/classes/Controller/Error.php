<?php

class Controller_Error extends Controller {

    public function action_403() {
        header('HTTP/1.1 403 Forbidden');
        echo <<<EOF
<html>
<head><title>403 Forbidden.</title></head>
<body bgcolor="white">
<center><h1>403 Forbidden</h1></center>
<hr><center>nginx</center>
</body>
</html>
EOF;
        exit;
    }
    
    public function action_404() {
        header("HTTP/1.1 404 Not Found");
        echo <<<EOF
<html>
<head><title>404 Not Found.</title></head>
<body bgcolor="white">
<center><h1>404 Not Found</h1></center>
<hr><center>nginx</center>
</body>
</html>
EOF;
        exit;
    }
}
       