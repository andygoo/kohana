<?php defined('SYSPATH') or die('No direct script access.');

class Controller_ImageFly extends Controller {
    
    public $auto_render = FALSE;
    
    public function action_index() {
        new ImageFly();
    }
}
