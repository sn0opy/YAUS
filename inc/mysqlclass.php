<?php

class db {	
	private $db;
	private $num;	
	private $res;	
	private $row;
	
	public function __construct() {
		$this->connect();
	}
	
	public function connect() {	
		$this->db = mysql_connect(HOST, USER, PWD);
		@mysql_select_db(DB);
	}
	
	public function disconnect() {	
		mysql_close($this->db);		
	}
	
	public function query($query) {						
		$this->res = mysql_query($query);			
		return $this->res;								
	}
	
	public function numRows() {	
		$this->num = @mysql_num_rows($this->res);
		return $this->num;		
	}

	public function result() {	
		return $this->res;			
	}
	
	public function fetch() {	
		$this->row = @mysql_fetch_array($this->res);
		return $this->row;			
	}
	
	public function row($field) {				
		return $this->row[$field];		
	}
	
	public function insertID() {
		$this->id = mysql_insert_id();
		return $this->id;
	}
}
	
?>