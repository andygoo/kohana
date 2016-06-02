<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Myadmin extends Controller {

	public $template = 'myadmin/layout';
	
	protected $curr_db;
	protected $curr_table;
    protected $content;

	public function before() {
	    parent::before();
	    
	    $this->curr_db = $this->request->param('database', 'default');
	    
	    $model = new Model($this->curr_db);
	    $tables = $model->list_tables()->as_array();
	    $tables = array_map('array_pop', $tables);

	    $this->curr_table = $this->request->param('table');
	    
	    View::bind_global('tables', $tables);
	    View::bind_global('curr_db', $this->curr_db);
	    View::bind_global('curr_table', $this->curr_table);
	    View::bind_global('action', $this->request->action);
        View::bind_global('content', $this->content);
	}

	public function action_index() {
	    $model = new Model($this->curr_db);
	    $attributes = array(
	            "AUTOCOMMIT", "ERRMODE", "CASE", "CLIENT_VERSION", "CONNECTION_STATUS",
	            "ORACLE_NULLS", "PERSISTENT", 
	            //"PREFETCH", 
	            "SERVER_INFO", "SERVER_VERSION",
	            //"TIMEOUT"
	    );
	    $info = array();
	    foreach ($attributes as $val) {
	        $info[$val] = $model->getAttribute(constant("PDO::ATTR_$val"));
	    }
	    $this->content = View::factory('myadmin/index');
	    $this->content->info = $info;
	}
	
	public function action_list() {
	    $database = $this->curr_db;
	    $curr_table = $this->curr_table;
        $model = Model::factory($curr_table, $this->curr_db);

        $db = Cookie::get('db');
        $db = json_decode($db, true);
	    
	    if(!isset($db[$database][$curr_table]) || !is_array($db[$database][$curr_table])) {
    	    $columns = $model->list_columns($curr_table)->as_array(null, 'Field');
	        $columns = array_map(function($n) {return $n>7 ? 0 : 1;}, array_flip($columns));
    	    $db[$database][$curr_table] = $columns;
    	    
	        Cookie::set('db', json_encode($db));
	    } else {
	        $columns = $db[$database][$curr_table];
	    }
	    
	    $where = array();
	    
	    $field_name = Arr::get($_GET, 'field_name');
	    $field_value = Arr::get($_GET, 'field_value');
	    $op = Arr::get($_GET, 'op', '=');
	    if(!empty($field_name)) {
	        $where[$field_name] = array($op, $field_value);
	    }
	    $where = array_filter($where, 'strlen');
	    
	    if (isset($_GET['sort'])) {
	        list($field, $order) = explode('|', $_GET['sort']);
	        $fields = array_keys($columns);
    	    if (in_array($field, $fields) && in_array($order, array('asc', 'desc'))) {
    	        $where['ORDER'] = $field . ' ' . $order;
    	    }
	    }
	    
	    $total = $model->count($where);
	    $pager = new Pager($total, 10);
	    $list = $model->select($pager->offset, $pager->size, $where);
	    
	    $this->content = View::factory('myadmin/list');
	    
	    $this->content->columns = $columns;
	    $this->content->list = $list;
	    $this->content->field = isset($field) ? $field : '';
	    $this->content->order = isset($order) ? $order : '';
	    $this->content->field_name = $field_name;
	    $this->content->field_value = $field_value;
	    $this->content->op = $op;
	    $this->content->pager = $pager->render('myadmin/pager');
	}

	public function action_desc() {
	    $curr_table = $this->curr_table;
        $model = Model::factory($curr_table, $this->curr_db);
	    
	    $table_desc = $model->desc_table($curr_table)->as_array("Table", "Create Table");
	    $table_desc = $table_desc[$curr_table];
	    
    	$columns = $model->list_columns($curr_table);
    	
        $this->content = View::factory('myadmin/desc');
	    $this->content->columns = $columns;
	    $this->content->table_desc = $table_desc;
	}
	
	public function action_display() {
	    $this->auto_render = FALSE;

	    $database = $this->curr_db;
	    $table = $this->curr_table;
	    $field = Arr::get($_GET, 'field');
	    $status = Arr::get($_GET, 'status');

	    $db = Cookie::get('db');
	    $db = json_decode($db, true);
	    
	    $db[$database][$table][$field] = $status;
	    Cookie::set('db', json_encode($db));
	}

	public function after() {
	    parent::after();
	
	    if ($this->auto_render !== TRUE) {
	        $this->request->response = (string)$this->content;//->render();
	    }
	}
}