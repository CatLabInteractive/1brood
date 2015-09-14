<?php

class Core_Database
{

	private $mlink = null;
	private $lastSQL;
	private $qcounter = 0;
	
	private $allQueries = array ();

	public static function __getInstance ($id = 'general')
	{
		static $in;
		
		if (isset ($in[$id]))
		{
		
			return $in[$id];
		
		}
		
		else 
		{
		
			$in[$id] = new Core_Database ();
			return $in[$id];
		
		}
	}
	
	public function connect ($server, $user, $password, $database)
	{
		$link = mysqli_connect ($server, $user, $password);
		$this->mlink = $link;

		if ($this->mlink) {
			if (!mysqli_select_db($this->mlink, $database)) {
				echo mysqli_error($this->mlink);
				exit ();
			}
		} else {
			echo 'Database Trouble: ' . mysqli_error($this->mlink);
			exit ();
		}

		// Encoding: the whole site runs on UTF-8
		$this->query ("SET NAMES 'utf8'");
	}
	
	public function customQuery ($sql)
	{
		return $this->query ($sql);
	}
	
	private function query ($sql_query)
	{
		/* If no connection has been set: open the default connection */
		if ($this->mlink === null)
		{
			$this->connect (DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
		}
	
		$sql = mysqli_query ($this->mlink, $sql_query);
		
		$this->lastSQL = $sql_query;
		$this->qcounter ++;
		
		$this->allQueries[] = $sql_query;
		
		//echo $sql_query."\n";
		if (mysqli_error($this->mlink)) {
		
			echo '<b>'.$sql_query.'</b><br>';
			echo mysqli_error($this->mlink);
			exit ();
		
		}
		
		else {
		
			return $sql;
		
		}
	}
	
	public function insert ($table, $data)
	{
		/* If no connection has been set: open the default connection */
		if ($this->mlink === null)
		{
		
			$this->connect (DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
		
		}

		$table = $this->makeTableSafe ($table);

		$sql = "INSERT INTO ".$table." SET ";
			
		foreach ($data as $k => $v)
		{

			if ($v === 'NOW()')
			{
				$sql.= "$k = NOW(), ";
			}
			else
			{
				$sql.= "$k = '".mysqli_real_escape_string ($this->mlink, $v)."', ";
			}
		
		}
		
		$sql = substr ($sql, 0, -2);
		
		$this->query ($sql);
		return mysqli_insert_id ($this->mlink);
	}
	
	public function makeSafe ($value)
	{
		if ($this->mlink === null)
		{
			$this->connect (DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
		}
		return mysqli_real_escape_string ($this->mlink, $value);
	}
	
	public function update ($table, $data, $where)
	{
		/* If no connection has been set: open the default connection */
		if ($this->mlink === null)
		{
			$this->connect (DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
		}

		$table = $this->makeTableSafe ($table);
		$sql = "UPDATE $table SET ";

		$totalSets = 0;
		
		foreach ($data as $k => $v)
		{
		
			if ($v === '++')
			{
			
				$sql.= "$k = ($k + 1), ";
				$totalSets ++;
			
			}
			
			elseif ($v === '--')
			{
			
				$sql.= "$k = ($k - 1), ";
				$totalSets ++;
			
			}
			
			elseif (substr ($v, 0, 2) === '++')
			{
				if (substr ($v, 2) != 0)
				{
					$sql.= "$k = ($k + ".substr ($v, 2)."), ";
					$totalSets ++;
				}
			}
			
			elseif (substr ($v, 0, 2) === '--')
			{
				if (substr ($v, 2) != 0)
				{
					$sql.= "$k = ($k - ".substr ($v, 2)."), ";
					$totalSets ++;
				}
			}

			elseif ($v == 'NOW()')
			{
				$sql.= "$k = NOW(), ";
				$totalSets ++;
			}
			
			else {
		
				$sql.= "$k = '".mysqli_real_escape_string ($this->mlink, $v)."', ";
				$totalSets ++;
			
			}
		
		}

		$sql = substr ($sql, 0, -2);
		
		$sql.= ' WHERE '.$where;

		if ($totalSets > 0)
		{
			$this->query ($sql);
			return mysqli_affected_rows ($this->mlink);
		}

		else
		{
			return 0;
		}
	}
	
	private function makeTableSafe ($table)
	{
		$table = explode (",", $table);
		
		$o = "";
		foreach ($table as $v)
		{
		
			$o .= "`".trim ($v)."`, ";
		
		}
		
		$o = substr ($o, 0, -2);
	
		return $o;
	}
	
	public function select ($table, $data, $where = false, $order = false, $limiet = false, $forUpdate = false)
	{
		$sql = "SELECT ";

		foreach ($data as $k => $v)
		{
		
			$sql.= "$v, ";
		
		}
		
		$sql = substr ($sql, 0, -2);
		
		/* Make tables safe */
		$table = $this->makeTableSafe ($table);
		
		$sql.= ' FROM '.$table;
		
		if ($where)
		{
		
			$sql.= ' WHERE '.$where;
		
		}
		
		if ($order)
		{
		
			$sql.= ' ORDER BY '.$order;
		
		}
		
		if ($limiet)
		{
		
			$sql.= ' LIMIT '.$limiet;
		
		}
		
		if ($forUpdate)
		{
		
			$sql.= " FOR UPDATE";
		
		}

		return $this->getDataFromQuery ($this->query ($sql));
	}
	
	public function getDataFromQuery ($sql)
	{
		$o = array ();
		while ($row = mysqli_fetch_assoc ($sql))
		{
		
			$o[] = $row;
		
		}
		
		return $o;
	}
	
	public function remove ($table, $where, $forUpdate = false)
	{
		$table = $this->makeTableSafe ($table);
		$sql = "DELETE FROM $table WHERE $where ";
		
		if ($forUpdate)
		{
			$sql.= " FOR UPDATE";
		}
		
		$this->query ($sql);
		
		return mysqli_affected_rows ($this->mlink);
	}
	
	public function getLatestQuery ()
	{
		return $this->lastSQL;
	}
	
	public function getCounter ()
	{
		return $this->qcounter;
	}
	
	public function beginWork ()
	{
		$this->query ("START TRANSACTION;");
	}
	
	public function commit ()
	{
		$this->query ("COMMIT");
	}
	
	public function rollBack ()
	{
		$this->query ("Rollback");
	}
	
	public function getAllQueries ()
	{
		$o = '';
		foreach ($this->allQueries as $v)
		{
			$v = str_replace ("\n", ' ', $v);
			$v = str_replace ("\t", '', $v);
			$o .= trim ($v)."\n";
		}
		return $o;
	}

	public function escape ($escape)
	{
		/* If no connection has been set: open the default connection */
		if ($this->mlink === null)
		{
			$this->connect (DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
		}
		return mysqli_real_escape_string ($this->mlink, $escape);
	}
}

?>
