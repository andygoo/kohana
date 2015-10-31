<?php

class Controller_Permit extends Controller_Website {

    public function action_list() {
        $m_permit = Model::factory('permit');

        $where = array();
        $where['ORDER'] = 'id DESC';
        $cat = Arr::get($_GET, 'cat');
        $where['cat'] = $cat;
        $where = array_filter($where);
        
        $total = $m_permit->count($where);
        $pager = new Pager($total, 20);
        $list = $m_permit->select($pager->offset, $pager->size, $where)->as_array();

        $m_permit = Model::factory('permit');
        $cats = $m_permit->getAll()->as_array(null,'cat');
        $cats = array_unique($cats);
        
        $this->content = View::factory('permit_list');
        $this->content->list = $list;
        $this->content->pager = $pager;
        $this->content->cats = $cats;
    }
    
    public function action_add() {
        $m_permit = Model::factory('permit');

        if (!empty($_POST)) {
            $data = $this->_get_data($_POST);
            $ret = $m_permit->insert($data);
            if ($ret !== false) {
                $this->redirect('permit/list');
            }
        }

        $menu = Kohana::config('menu');
        $cats = array_keys($menu);
        
        $this->content = View::factory('permit_add');
        $this->content->info = null;
        $this->content->cats = $cats;
    }

    public function action_edit() {
        $id = $_GET['id'];
        $m_permit = Model::factory('permit');
        
        if (!empty($_POST)) {
            $data = $this->_get_data($_POST);
            $ret = $m_permit->update($data, $id);
            if ($ret !== false) {
                $this->redirect('permit/list');
            }
        }
        $info = $m_permit->getRow($id);

        $menu = Kohana::config('menu');
        $cats = array_keys($menu);
        
        $this->content = View::factory('permit_edit');
        $this->content->info = $info;
        $this->content->cats = $cats;
    }

    public function action_del() {
        $id = $_GET['id'];
        $model = Model::factory('permit');
        $ret = $model->delete($id);
        if ($ret !== false) {
            $this->redirect(Request::$referrer);
        }
    }
    
    protected function _get_data($post) {
        $data = array();

        $data['cat'] = Arr::get($post, 'cat');
        $data['name'] = Arr::get($post, 'name');
        $data['url'] = Arr::get($post, 'url');
        
        $data = array_map('trim', $data);
        $data = array_filter($data, 'strlen');
        return $data;
    }
}

