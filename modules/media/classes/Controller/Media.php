<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Media extends Controller{

    public function before() {
        parent::before();

        header("Last-Modified: " . gmdate('D, d M Y H:i:s T', time()));
        header("Expires: " . gmdate('D, d M Y H:i:s T', time()+315360000));
        header("Cache-Control: max-age=315360000");
    }
    
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

	public function action_minicss() {
		header('Content-Type: text/css; charset=utf-8');
	    $filegroup = $this->request->param('file');
	    $config = Kohana::config('media.css');
	    $content = '';
	    if (isset($config[$filegroup])) {
            ob_start();
	        foreach ($config[$filegroup] as $file) {
	            $file = APPPATH . $file;
	            if (file_exists($file)) {
	                include $file;
	            }
	        }
	        $buffer = ob_get_clean();
	        $content = $this->_compress($buffer);
	    }
	    echo $content;
	    exit;
	} 
	
	public function action_minijs() {
	    header('Content-Type: text/javascript; charset=utf-8');
	    $filegroup = $this->request->param('file');
	    $config = Kohana::config('media.js');
	    if (isset($config[$filegroup])) {
	        foreach ($config[$filegroup] as $file) {
	            $file = APPPATH . $file;
	            if (file_exists($file)) {
	                include $file;
	            }
	        }
	    }
	    exit;
	}
	
	protected function _compress($buffer) {
	    /* remove comments */
	    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
	    /* remove tabs, spaces, newlines, etc. */
	    $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
	    return $buffer;
	}
}