<?php

class Pager {
    
    protected $config = array();
    protected $total_items;
    protected $size;
    protected $total_pages;
    protected $current_page;
    protected $current_first_item;
    protected $current_last_item;
    protected $previous_page;
    protected $next_page;
    protected $first_page;
    protected $last_page;
    protected $offset;

    public function __construct($total, $size, $page_key = 'page', $source = '') {
        $this->config = array(
            'source' => $source,
            'total_items' => $total,
            'items_per_page' => $size,
            'current_page_key' => $page_key 
        );
        $this->setup($this->config);
    }

    public function setup(array $config = array()) {
        if ($this->current_page === NULL or isset($config['current_page']) or isset($config['total_items']) or isset($config['items_per_page'])) {
            if (isset($this->config['source']) && $this->config['source'] == 'route') {
                $this->current_page = (int)Request::instance()->param($this->config['current_page_key'], 1);
            } else {
                $this->current_page = isset($_GET[$this->config['current_page_key']]) ? (int)$_GET[$this->config['current_page_key']] : 1;
            }
            
            $this->total_items = (int)max(0, $this->config['total_items']);
            $this->size = (int)max(1, $this->config['items_per_page']);
            $this->total_pages = (int)ceil($this->total_items / $this->size);
            $this->current_page = (int)min(max(1, $this->current_page), max(1, $this->total_pages));
            $this->current_first_item = (int)min((($this->current_page - 1) * $this->size) + 1, $this->total_items);
            $this->current_last_item = (int)min($this->current_first_item + $this->size - 1, $this->total_items);
            $this->previous_page = ($this->current_page > 1) ? $this->current_page - 1 : FALSE;
            $this->next_page = ($this->current_page < $this->total_pages) ? $this->current_page + 1 : FALSE;
            $this->first_page = ($this->current_page === 1) ? FALSE : 1;
            $this->last_page = ($this->current_page >= $this->total_pages) ? FALSE : $this->total_pages;
            $this->offset = (int)(($this->current_page - 1) * $this->size);
        }
        return $this;
    }

    public function url($page = 1, $opts = array()) {
        $page = max(1, (int)$page);
        
        if (isset($this->config['source']) && $this->config['source'] == 'route') {
            return URL::site(Request::instance()->uri(array(
                $this->config['current_page_key'] => $page 
            ))) . URL::query();
        } else {
            $params = array(
                $this->config['current_page_key'] => $page 
            );
            if (!empty($opts)) {
                $params = $opts + $params;
            }
            return URL::site(Request::instance()->uri()) . URL::query($params);
        }
    }

    public function render($view = 'pager') {
        if ($this->total_pages <= 1) return '';
        return View::factory($view, get_object_vars($this))->set('page', $this)->render();
    }

    public function __toString() {
        return $this->render('pager');
    }

    public function __get($key) {
        return isset($this->$key) ? $this->$key : NULL;
    }

    public function __set($key, $value) {
        $this->setup(array(
            $key => $value 
        ));
    }
}
