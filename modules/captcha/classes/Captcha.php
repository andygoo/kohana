<?php

abstract class Captcha {
    public static $instance;
    public static $config = array();
    protected $response;
    protected $image;
    protected $image_type = 'png';

    public static function instance($group = 'alpha') {
        if (!isset(Captcha::$instance)) {
            $config = Kohana::config('captcha');
            Captcha::$config = $config;
            
            //$style = $config['style'];
            $class = 'Captcha_' . ucfirst($group);
            Captcha::$instance = new $class($config);
        }
        
        return Captcha::$instance;
    }

    public function __construct($config = NULL) {
        if (!empty($config['background'])) {
            Captcha::$config['background'] = str_replace('\\', '/', realpath($config['background']));
            
            if (!is_file(Captcha::$config['background'])) throw new Kohana_Exception('The specified file, :file, was not found.', array(
                ':file' => Captcha::$config['background'] 
            ));
        }
        
        if (!empty($config['fonts'])) {
            Captcha::$config['fontpath'] = str_replace('\\', '/', realpath($config['fontpath'])) . '/';
            
            foreach($config['fonts'] as $font) {
                if (!is_file(Captcha::$config['fontpath'] . $font)) throw new Kohana_Exception('The specified file, :file, was not found.', array(
                    ':file' => Captcha::$config['fontpath'] . $font 
                ));
            }
        }
        
        $this->response = $this->generate_challenge();
    }

    public function update_response_session() {
        Session::instance()->set('captcha_response', sha1(strtoupper($this->response)));
    }

    public static function valid($response) {
        $result = (bool)(sha1(strtoupper($response)) === Session::instance()->get('captcha_response'));
        return $result;
    }

    public function __toString() {
        return $this->render(TRUE);
    }

    public function image_type($filename) {
        switch(strtolower(substr(strrchr($filename, '.'), 1))) {
            case 'png':
                return 'png';
            case 'gif':
                return 'gif';
            case 'jpg':
            case 'jpeg':
                return 'jpeg';
            default:
                return FALSE;
        }
    }

    /**
	 * Creates an image resource with the dimensions specified in config.
	 * If a background image is supplied, the image dimensions are used.
	 *
	 * @throws Kohana_Exception If no GD2 support
	 * @param string $background Path to the background image file
	 * @return void
	 */
    public function image_create($background = NULL) {
        // Check for GD2 support
        if (!function_exists('imagegd2')) throw new Kohana_Exception('captcha.requires_GD2');
        
        // Create a new image (black)
        $this->image = imagecreatetruecolor(Captcha::$config['width'], Captcha::$config['height']);
        
        // Use a background image
        if (!empty($background)) {
            // Create the image using the right function for the filetype
            $function = 'imagecreatefrom' . $this->image_type($background);
            $this->background_image = $function($background);
            
            // Resize the image if needed
            if (imagesx($this->background_image) !== Captcha::$config['width'] or imagesy($this->background_image) !== Captcha::$config['height']) {
                imagecopyresampled($this->image, $this->background_image, 0, 0, 0, 0, Captcha::$config['width'], Captcha::$config['height'], imagesx($this->background_image), imagesy($this->background_image));
            }
            
            // Free up resources
            imagedestroy($this->background_image);
        }
    }

    /**
	 * Fills the background with a gradient.
	 *
	 * @param resource $color1 GD image color identifier for start color
	 * @param resource $color2 GD image color identifier for end color
	 * @param string $direction Direction: 'horizontal' or 'vertical', 'random' by default
	 * @return void
	 */
    public function image_gradient($color1, $color2, $direction = NULL) {
        $directions = array(
            'horizontal',
            'vertical' 
        );
        
        // Pick a random direction if needed
        if (!in_array($direction, $directions)) {
            $direction = $directions[array_rand($directions)];
            
            // Switch colors
            if (mt_rand(0, 1) === 1) {
                $temp = $color1;
                $color1 = $color2;
                $color2 = $temp;
            }
        }
        
        // Extract RGB values
        $color1 = imagecolorsforindex($this->image, $color1);
        $color2 = imagecolorsforindex($this->image, $color2);
        
        // Preparations for the gradient loop
        $steps = ($direction === 'horizontal') ? Captcha::$config['width'] : Captcha::$config['height'];
        
        $r1 = ($color1['red'] - $color2['red']) / $steps;
        $g1 = ($color1['green'] - $color2['green']) / $steps;
        $b1 = ($color1['blue'] - $color2['blue']) / $steps;
        
        if ($direction === 'horizontal') {
            $x1 = & $i;
            $y1 = 0;
            $x2 = & $i;
            $y2 = Captcha::$config['height'];
        } else {
            $x1 = 0;
            $y1 = & $i;
            $x2 = Captcha::$config['width'];
            $y2 = & $i;
        }
        
        // Execute the gradient loop
        for($i = 0; $i <= $steps; $i++) {
            $r2 = $color1['red'] - floor($i * $r1);
            $g2 = $color1['green'] - floor($i * $g1);
            $b2 = $color1['blue'] - floor($i * $b1);
            $color = imagecolorallocate($this->image, $r2, $g2, $b2);
            
            imageline($this->image, $x1, $y1, $x2, $y2, $color);
        }
    }

    public function image_render() {
        header('Content-Type: image/' . $this->image_type);
        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        header('Pragma: no-cache');
        header('Connection:close');
        
        $function = 'image' . $this->image_type;
        $function($this->image);
        
        imagedestroy($this->image);
    }

    public function generate_challenge() {
        $text = Text::random('distinct', max(1, Captcha::$config['complexity']));
        return $text;
    }

    abstract public function render();
}
