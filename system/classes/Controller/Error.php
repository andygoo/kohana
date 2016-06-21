<?php

class Controller_Error extends Controller {

    public function action_404() {
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
       