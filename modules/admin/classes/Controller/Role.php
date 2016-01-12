<?php

class Controller_Role extends Controller_Website {

    public $permits;
    
    public function __construct(Request $req) {
        parent::__construct($req);
    
        $m_permit = Model::factory('permit');
        $this->permits = $m_permit->getAll()->as_array('id');

        $permits = array();
        foreach ($this->permits as $permit) {
            $cat = $permit['cat'];
            $permits[$cat][] = $permit;
        }
        View::bind_global('permits', $permits);
    }
    
    public function action_list() {
        $m_role = Model::factory('role');
    
        $total = $m_role->count();
        $pager = new Pager($total, 20);
        $list = $m_role->select($pager->offset, $pager->size)->as_array();

        foreach ($list as &$item) {
            $permit = array();
            $permit_ids = explode(',', $item['permit_ids']);
            $permit_ids = array_filter($permit_ids);
            foreach ($permit_ids as $permit_id) {
                if (isset($this->permits[$permit_id])) {
                    $permit[] = $this->permits[$permit_id]['name'];
                }
            }
            $item['permit'] = implode(' | ', $permit);
        }
    
        $this->content = View::factory('role_list');
        $this->content->list = $list;
        $this->content->pager = $pager;
    }
    
    public function action_add() {
        if (!empty($_POST)) {
            $data = $this->_get_data($_POST);
            $m_role = Model::factory('role');
            $ret = $m_role->insert($data);
            if ($ret !== false) {
                $this->redirect('role/list');
            }
        }
        $this->content = View::factory('role_add');
        $this->content->info = null;
    }

    public function action_edit() {
        $id = Arr::get($_GET, 'id');
        if ($id==1) {
            $this->content = '<h3 class="page-header">没有权限</h3>';
            return ;
        }
        $m_role = Model::factory('role');
        
        if (!empty($_POST)) {
            $data = $this->_get_data($_POST);
            $ret = $m_role->updateById($data, $id);
            if ($ret !== false) {
                $this->redirect('role/list');
            }
        }
        $info = $m_role->getRowById($id);
        
        $this->content = View::factory('role_edit');
        $this->content->info = $info;
    }

    public function action_disable() {
        $id = $_GET['id'];
        $data = array(
            'status' => 'disable',
        );
        $m_role = Model::factory('role');
        $ret = $m_role->updateById($data, $id);
        if ($ret !== false) {
            $this->redirect(Request::$referrer);
        }
    }
    
    public function action_enable() {
        $id = $_GET['id'];
        $data = array(
            'status' => 'normal',
        );
        $m_role = Model::factory('role');
        $ret = $m_role->updateById($data, $id);
        if ($ret !== false) {
            $this->redirect(Request::$referrer);
        }
    }
    
    protected function _get_data($post) {
        $data = array();
        
        $data['name'] = Arr::get($post, 'name');
        $data['status'] = Arr::get($post, 'status');

        $permit_ids = Arr::get($post, 'permit_ids', array());
        $data['permit_ids'] = implode(',', $permit_ids);
        
        return $data;
    }
    
}

