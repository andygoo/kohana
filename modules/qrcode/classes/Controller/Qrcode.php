<?php

class Controller_Qrcode extends Controller {

    public function action_create() {
		// 纠错级别：L、M、Q、H
		$level = 'L';
		// 点的大小：1到10,用于手机端4就可以了
		$size = Arr::get($_GET, 'size', 4);
		$margin = Arr::get($_GET, 'margin', 1);
		$data = Arr::get($_GET, 'data');
		
		QRcode::png($data, false, $level, $size, $margin);
        exit;
    }
}
       