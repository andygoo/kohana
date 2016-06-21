<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Media extends Controller{

	public function action_index() {
	    $filename = $this->request->param('file');
	    $format = $this->request->param('format');
	    switch ($format) {
	        case 'js':
		        header('Content-Type: text/javascript; charset=utf-8');break;
		    case 'css':
		        header('Content-Type: text/css; charset=utf-8');break;
		    case 'jpg':
		        header('Content-Type: image/jpeg');break;
		    case 'png':
		        header('Content-Type: image/png');break;
		    case 'gif':
		        header('Content-Type: image/gif');break;
		    case 'svg':
		        header('Content-Type: image/svg+xml');break;
		    case 'swf':
		        header('Content-Type: application/x-shockwave-flash');break;
		    case 'xap':
		        header('Content-Type: application/x-silverlight-app');break;
	    }
	    $file1 = APPPATH.'media/'.$filename.'.'.$format;
	    $file2 = MODPATH.'media/static/'.$filename.'.'.$format;
	    if (file_exists($file1)) {
		    include $file1;
	    } elseif (file_exists($file2)) {
		    include $file2;
	    }
		exit;
	}
}