<?php

class Controller_Qrcode extends Controller {

    public $auto_render = true;
    
    public function action_create() {
		// 纠错级别：L、M、Q、H
		$level = Arr::get($_GET, 'level', 'L');
		// 点的大小：1到10,用于手机端4就可以了
		$size = Arr::get($_GET, 'size', 4);
		$margin = Arr::get($_GET, 'margin', 1);
		$text = Arr::get($_GET, 'text');
		QRcode::png($text, false, $level, $size, $margin);
        exit;
    }
    
    public function action_make() {
        echo HTML::script('media/js/qrcode.min.js');
        $text = Arr::get($_GET, 'text', '');
        $width = Arr::get($_GET, 'width', '');
        echo QRJS::makecode($text, $width);
        exit;
    }
}
       