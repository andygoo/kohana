<?php

class Cart {
    public $_cart_contents = array();
    public static $expiration = 36000000;

    public function __construct() {
        $cart_contents = Cookie::get('cart_contents');
        if ($cart_contents != NULL) {
            $cart_contents = unserialize($cart_contents);
            $this->_cart_contents = $cart_contents;
        } else {
            $this->_cart_contents['cart_total'] = 0;
            $this->_cart_contents['total_items'] = 0;
        }
    }

    /**
		$data = array(
			array(
				'id'      => 'sku_123ABC',
				'qty'     => 1,
				'price'   => 39.95,
				'options' => array('Size' => 'L', 'Color' => 'Red')
			),
			array(
				'id'      => 'sku_567ZYX',
				'qty'     => 1,
				'price'   => 9.95,
			)
		);
		$this->cart->insert($data);
	 */
    public function insert($items = array()) {
        if (!is_array($items) or count($items) == 0) {
            return FALSE;
        }
        
        if (isset($items['id'])) {
            $this->_insert($items);
        } else {
            foreach($items as $val) {
                if (is_array($val) and isset($val['id'])) {
                    $this->_insert($val);
                }
            }
        }
        
        return $this->_save_cart();
    }

    /**
		$data = array(
			array(
				'rowid'   => 'b99ccdf16028f015540f341130b6d8ec',
				'qty'     => 3
			),
			array(
				'rowid'   => 'xw82g9q3r495893iajdh473990rikw23',
				'qty'     => 4
			)
		);
		$this->cart->update($data);
	 */
    public function update($items = array()) {
        if (!is_array($items) or count($items) == 0) {
            return FALSE;
        }
        
        if (isset($items['rowid']) and isset($items['qty'])) {
            $this->_update($items);
        } else {
            foreach($items as $val) {
                if (is_array($val) and isset($val['rowid']) and isset($val['qty'])) {
                    $this->_update($val);
                }
            }
        }
        
        return $this->_save_cart();
    }

    protected function _insert($items = array()) {
        if (!isset($items['id']) || !isset($items['qty']) || !isset($items['price'])) {
            return FALSE;
        }
        
        $items['qty'] = (float)(string)$items['qty'];
        $items['price'] = (float)(string)$items['price'];
        
        if (isset($items['options']) and count($items['options']) > 0) {
            $rowid = md5($items['id'] . implode('', $items['options']));
        } else {
            $rowid = md5($items['id']);
        }
        
        unset($this->_cart_contents[$rowid]);
        
        foreach($items as $key => $val) {
            $this->_cart_contents[$rowid][$key] = $val;
        }
    }

    protected function _update($items = array()) {
        if (!isset($items['qty']) || !isset($items['rowid']) || !isset($this->_cart_contents[$items['rowid']])) {
            return FALSE;
        }
        
        if (!is_numeric($items['qty'])) {
            return FALSE;
        }
        
        if ($this->_cart_contents[$items['rowid']]['qty'] == $items['qty']) {
            return FALSE;
        }
        
        $items['qty'] = (float)(string)$items['qty'];
        
        if ($items['qty'] == 0) {
            unset($this->_cart_contents[$items['rowid']]);
        } else {
            $this->_cart_contents[$items['rowid']]['qty'] = $items['qty'];
        }
    }

    protected function _save_cart() {
        unset($this->_cart_contents['total_items']);
        unset($this->_cart_contents['cart_total']);
        
        $total = 0;
        $total_items = 0;
        foreach($this->_cart_contents as $key => $val) {
            if (!isset($val['price']) or !isset($val['qty'])) {
                continue;
            }
            
            $subtotal = $val['price'] * $val['qty'];
            $this->_cart_contents[$key]['subtotal'] = $subtotal;
            
            $total += $subtotal;
            $total_items += $val['qty'];
        }
        
        $this->_cart_contents['cart_total'] = $total;
        $this->_cart_contents['total_items'] = $total_items;
        
        $cart_contents = serialize($this->_cart_contents);
        Cookie::set('cart_contents', $cart_contents, self::$expiration);
        return true;
    }

    public function total() {
        return $this->_cart_contents['cart_total'];
    }

    public function total_items() {
        return $this->_cart_contents['total_items'];
    }

    public function contents() {
        $cart = $this->_cart_contents;
        
        unset($cart['total_items']);
        unset($cart['cart_total']);
        
        return array(
            'contents' => $cart,
            'items' => $this->total_items(),
            'total' => $this->total() 
        );
    }

    public function has_options($rowid = '') {
        if (empty($this->_cart_contents[$rowid]['options'])) {
            return FALSE;
        }
        
        return TRUE;
    }

    public function product_options($rowid = '') {
        if (!isset($this->_cart_contents[$rowid]['options'])) {
            return array();
        }
        
        return $this->_cart_contents[$rowid]['options'];
    }

    public function format_number($n = '') {
        if ($n == '') {
            return '';
        }
        
        $n = trim(preg_replace('/([^0-9\.])/i', '', $n));
        
        return number_format($n, 2, '.', ',');
    }

    public function destroy() {
        unset($this->_cart_contents);
        
        $this->_cart_contents['cart_total'] = 0;
        $this->_cart_contents['total_items'] = 0;
        
        Cookie::delete('cart_contents');
    }
}
