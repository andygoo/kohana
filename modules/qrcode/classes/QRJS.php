<?php 

class QRJS {
    
    public static function makecode($text, $width = 200) {
        if (empty($text)) {
            return '';
        }
        $config = array();
        $config['text'] = $text;
        $width = intval($width);
        if (!empty($width)) {
            $config['width'] = $config['height'] = $width;
        }
        $config = json_encode($config);
        $id = 'qr'.rand(1111,9999);
        return "<div id=\"$id\"></div><script>new QRCode('$id', {$config});</script>";
    }
}