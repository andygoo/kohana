<?php

class Controller_Admin extends Controller_Website {

    protected $role_list;
    
    public function __construct(Request $req) {
        parent::__construct($req);
    
        $m_role = Model::factory('role');
        $this->role_list = $m_role->getAll(array('status'=>'normal'))->as_array('id');
    
        View::bind_global('role_list', $this->role_list);
    }
    
    public function action_list() {
        $m_admin = Model::factory('admin');

        $total = $m_admin->count();
        $pager = new Pager($total, 10);
        $list = $m_admin->select($pager->offset, $pager->size)->as_array();

        $m_role = Model::factory('role');
        $this->role_list = $m_role->getAll()->as_array('id');
        foreach ($list as &$item) {
            $role_id = $item['role_id'];
            $item['rolename'] = $item['rolestatus'] = '';
            if (isset($this->role_list[$role_id])) {
                $item['rolename'] = $this->role_list[$role_id]['name'];
                $item['rolestatus'] = $this->role_list[$role_id]['status'];
            } 
        }
        unset($item);
        
        $this->content = View::factory('admin_list');
        $this->content->list = $list;
        $this->content->pager = $pager;
    }

    public function action_add() {
        if (!empty($_POST)) {
            $data = $this->_get_data($_POST);
            $m_admin = Model::factory('admin');
            $ret = $m_admin->insert($data);
            if ($ret !== false) {
                $this->redirect('admin/list');
            }
        }
        $this->content = View::factory('admin_add');
    }
    
    public function action_edit() {
        $id = $_GET['id'];
        $m_admin = Model::factory('admin');
        $userInfo = $m_admin->getRow($id);

        if (!empty($_POST)) {
            $data = $this->_get_data($_POST);
            $ret = $m_admin->update($data, $id);
            if ($ret !== false) {
                $this->redirect('admin/list');
            }
        }
        $this->content = View::factory('admin_edit');
        $this->content->userInfo = $userInfo;
    }

    public function action_password() {
        if (!empty($_POST)) {
            $old_password = $_POST['old_password'];
            $new_password = $_POST['new_password'];
            
            $auth = Model::factory('auth');
            $ret = $auth->check_password($old_password);
            if ($ret) {
                $ret = $auth->change_password($new_password);
                if ($ret !== false) {
                    $this->redirect('admin/logout');
                }
            } else {
                $this->content = '旧密码不匹配';
            }
        } else {
            $this->content = View::factory('admin_password');
        }
    }

    public function action_disable() {
        $id = $_GET['id'];
        $data = array(
            'status' => 'disable',
        );
        $m_admin = Model::factory('admin');
        $ret = $m_admin->update($data, $id);
        if ($ret !== false) {
            $this->redirect(Request::$referrer);
        }
    }

    public function action_enable() {
        $id = $_GET['id'];
        $data = array(
            'status' => 'normal',
        );
        $m_admin = Model::factory('admin');
        $ret = $m_admin->update($data, $id);
        if ($ret !== false) {
            $this->redirect(Request::$referrer);
        }
    }
    
    public function action_logout() {
        $auth = Model::factory('auth');
        $ret = $auth->logout();
        if ($ret !== false) {
            $this->redirect('auth/login');
        }
    }

    protected function _get_data($post) {
        $data = array();

        $data['role_id'] = Arr::get($_POST, 'role_id');
        if($data['role_id'] == 1) {
            $data['role_id'] = 0;
        }
        $data['username'] = Arr::get($_POST, 'username');
        $password = Arr::get($_POST, 'password');
        
        if (!empty($password)) {
            $auth = Model::factory('auth');
            $password = $auth->hash($password);
            $data['password'] = $password;
        }
    
        $data = array_filter($data, 'strlen');
        return $data;
    }
}
