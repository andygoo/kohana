<?php

class Controller_Devtools extends Controller {
    public $template = 'devtools/template';

    public function before() {
        parent::before();
    
        View::bind_global('content', $this->content);
    }
    
    /**
	 * Dump constants, Kohana::init() settings, loaded modules, and install.php
	 */
    public function action_info() {
        $this->content = View::factory('devtools/info');
    }

    /**
	 * Dump PHP info.
     *
     * @link http://phpbb.com Uses phpBB's phpinfo() cleaning functions form includes/acp/acp_php_info.php
	 */
    public function action_phpinfo() {
        $this->content = View::factory('devtools/phpinfo');
    }

    /**
	 * Test a route
	 */
    public function action_routetest() {
        // Check if a url was provided
        $url = Arr::get($_POST, 'url', $this->request->uri);
        
        $this->content = View::factory('devtools/route-test', array(
            // Get all the tests
            'tests' => Devtools_Route_Tester::create_tests($url) 
        ));
    }

    /**
	 * Dump all routes
	 */
    public function action_routes() {
        $this->content = View::factory('devtools/route-dump');
    }

    public function after() {
        parent::after();
    
        if ($this->auto_render !== TRUE) {
            $this->request->response = (string)$this->content;
        }
    }
}