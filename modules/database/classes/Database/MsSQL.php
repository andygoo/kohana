<?php defined('SYSPATH') or die('No direct script access.'); 

class Database_MsSQL extends Database_PDO {
	
	public function query($sql, $as_object = FALSE)
	{
		if(preg_match("/OFFSET ([0-9]+)/i", $sql, $matches))
		{
			list($replace, $offset) = $matches;
			$sql = str_replace($replace, '', $sql);
		}

		if(preg_match("/LIMIT ([0-9]+)/i", $sql, $matches))
		{
			list($replace, $limit) = $matches;
			$sql = str_replace($replace, '', $sql);
		}

		if(isset($limit) || isset($offset))
		{
			if (!isset($offset)) 
			{
				$sql = preg_replace("/^(SELECT|DELETE|UPDATE)\s/i", "$1 TOP " . $limit . ' ', $sql);
			} 
			else 
			{
				$ob_count = (int)preg_match_all('/ORDER BY/i', $sql, $ob_matches, PREG_OFFSET_CAPTURE);

				if($ob_count < 1) 
				{
					$over = 'ORDER BY (SELECT 0)';
				} 
				else 
				{
					$ob_last = array_pop($ob_matches[0]);
					$orderby = strrchr($sql, $ob_last[0]);
					$over = preg_replace('/[^,\s]*\.([^,\s]*)/i', 'inner_tbl.$1', $orderby);
					
					$sql = substr($sql, 0, $ob_last[1]);
				}
				
				// Add ORDER BY clause as an argument for ROW_NUMBER()
				$sql = "SELECT ROW_NUMBER() OVER ($over) AS KOHANA_DB_ROWNUM, * FROM ($sql) AS inner_tbl";
			   
				$start = $offset + 1;
				$end = $offset + $limit;

				$sql = "WITH outer_tbl AS ($sql) SELECT * FROM outer_tbl WHERE KOHANA_DB_ROWNUM BETWEEN $start AND $end";
			}
		}
		
		return parent::query($sql, $as_object);
	}
	/*
	public function insert_id()
	{
		$table = preg_match('/^insert\s+into\s+(.*?)\s+/i', $this->last_query, $match) ? arr::get($match,1) : NULL;
		if (!empty($table)) $query = 'SELECT IDENT_CURRENT(\'' . $this->quote_identifier($table) . '\') AS insert_id';
		else $query = 'SELECT SCOPE_IDENTITY() AS insert_id';

		$data = $this->query($query,FALSE)->current();
		return Arr::get($data, 'insert_id');
		
		$STH = $DBH->query("SELECT CAST(COALESCE(SCOPE_IDENTITY(), @@IDENTITY) AS int)"); 
		$STH->execute(); 
		$result = $STH->fetch(); 
		print $result[0]; 

	}*/
}
