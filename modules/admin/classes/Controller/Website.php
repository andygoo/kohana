<?php

abstract class Controller_Website extends Controller {
    
    protected $permission = array();
    
    protected $user_menu = array();
    protected $user_permission = array();
    
    protected $user;
    protected $content;

    public function __construct(Request $req) {
        parent::__construct($req);

        $auth = Model::factory('auth');
        if (!$auth->logged_in()) {
            $this->redirect('auth/login');
        } else {
            $this->user = $auth->get_user();
            
            $menu = Kohana::config('menu');
            $allmenu = Arr::flatten($menu);

            $m_permit = Model::factory('permit');
            $permission= $m_permit->getAll()->as_array(null,'url');
            $permission = array_merge($permission, array_values($allmenu));
            $this->permission = array_unique($permission);
            
            $role_id = $this->user['role_id'];
            $m_role = Model::factory('role');
            $role_list = $m_role->getAll(array('status'=>'normal'))->as_array('id');
            if (isset($role_list[$role_id])) {
                $permit_ids = $role_list[$role_id]['permit_ids'];
                if ($permit_ids == '*') {
                    $this->user_permission = $this->permission;
                } else {
                    $permit_ids = explode(',', $permit_ids);
                    $permit_ids = array_filter($permit_ids);
                    $this->user_permission = $m_permit->getAll(array('id'=>$permit_ids))->as_array(null,'url');
                }
            }
            
            foreach ($menu as $name=>$items) {
                foreach ($items as $sub_name=>$url) {
                    if(!in_array($url, $this->user_permission)) {
                        unset($menu[$name][$sub_name]);
                    }
                }
            }
            $this->user_menu = array_filter($menu);
        }
    }

    public function before() {
        parent::before();

        if ($this->auto_render === TRUE) {
            View::bind_global('user', $this->user);
            View::bind_global('uri', $this->request->uri);
            View::bind_global('controller', $this->request->controller);
            View::bind_global('action', $this->request->action);
            View::bind_global('menu', $this->user_menu);
            View::bind_global('content', $this->content);
        }

        $uri = $this->request->uri;
        if (in_array($uri, $this->permission) && !in_array($uri, $this->user_permission)) {
            if ($this->auto_render === TRUE) {
                $this->content = '<h3 class="page-header">没有权限</h3>';
                echo $this->template;exit;
            }
            exit('Permission denied');
        }
    }
    
    public function after() {
        parent::after();

        if ($this->auto_render !== TRUE) {
            $this->request->response = (string)$this->content;//->render();
        }
    }
} 
