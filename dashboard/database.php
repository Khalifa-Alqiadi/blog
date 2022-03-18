<?php
class DB{
	private static $instance = null;
	private $dbh = null, 
			$table, $columns = [], $sql, $bindValues, $join,
			$where, $orWhere, $whereCount=0, $isOrWhere = false,
			$rowCount=0, $limit, $orderBy, $lastIDInserted = 0;

    private $dsn;
    private $user = "root";
    private $pass = "";

	private function __construct()
	{
        $this->dsn = 'mysql:host=localhost;dbname=products2';
        $this->user = 'root';
        $this->password = '';
		try {
			$this->dbh = new PDO($this->dsn, $this->user, $this->pass);
			$this->dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
			// $db_config = null;
		} catch (Exception $e) {
			die("Error establishing a database connection.");
		}

	}

	public static function getInstance()
	{
		if (!self::$instance) {
			self::$instance = new DB();
		}
		return self::$instance;
	}
	public function exec(){
		$this->sql .= $this->where;
		$this->getSQL = $this->sql;
		$stmt = $this->dbh->prepare($this->sql);
		$stmt->execute($this->bindValues);
		return $stmt->rowCount();
	}

	private function resetQuery(){
		$this->table = null;
		$this->columns = null;
		$this->sql = null;
		$this->bindValues = null;
		$this->join = null;
		$this->limit = null;
		$this->orderBy = null;
		$this->where = null;
		$this->orWhere = null;
		$this->whereCount = 0;
		$this->isOrWhere = false;
		$this->lastIDInserted = 0;
	}

	public function delete($table_name, $id=null)
	{
		$this->resetQuery();

		$this->sql = "DELETE FROM `{$table_name}`";
		
		if (isset($id)) {
			// if there is an ID
			if (is_numeric($id)) {
				$ids = '';
				$this->sql .= " WHERE $id = ?";
				$this->bindValues[] = $id;
			// if there is an Array
			}elseif (is_array($id)) {
				$arr = $id;
				$count_arr = count($arr);
				$x = 0;

				foreach ($arr as  $param) {
					if ($x == 0) {
						$this->where .= " WHERE ";
						$x++;
					}else{
						if ($this->isOrWhere) {
							$this->where .= " Or ";
						}else{
							$this->where .= " AND ";
						}
						
						$x++;
					}
					$count_param = count($param);
					if ($count_param == 1) {
						$this->where .= "`$param[0]` = ?";
						$this->bindValues[] =  $param[0];
					}elseif ($count_param == 2) {
						$operators = explode(',', "=,>,<,>=,>=,<>");
						$operatorFound = false;

						foreach ($operators as $operator) {
							if ( strpos($param[0], $operator) !== false ) {
								$operatorFound = true;
								break;
							}
						}

						if ($operatorFound) {
							$this->where .= $param[0]." ?";
						}else{
							$this->where .= "`".trim($param[0])."` = ?";
						}

						$this->bindValues[] =  $param[1];
					}elseif ($count_param == 3) {
						$this->where .= "`".trim($param[0]). "` ". $param[1]. " ?";
						$this->bindValues[] =  $param[2];
					}

				}
			}
			$this->sql .= $this->where;
			$this->getSQL = $this->sql;
			$stmt = $this->dbh->prepare($this->sql);
			$stmt->execute($this->bindValues);
			return $stmt->rowCount();
		}
		return $this;
	}

	public function update($table_name, $fields = [], $id=null)
	{
		$this->resetQuery();
		$set ='';
		$x = 1;

		foreach ($fields as $column => $field) {
			$set .= "`$column` = ?";
			$this->bindValues[] = $field;
			if ( $x < count($fields) ) {
				$set .= ", ";
			}
			$x++;
		}

		$this->sql = "UPDATE `{$table_name}` SET $set";
		
		if (isset($id)) {
			// if there is an ID
			if (is_numeric($id)) {
				$this->sql .= " WHERE `$id` = ?";
				$this->bindValues[] = $id;
			// if there is an Array
			}elseif (is_array($id)) {
				$arr = $id;
				$count_arr = count($arr);
				$x = 0;

				foreach ($arr as  $param) {
					if ($x == 0) {
						$this->where .= " WHERE ";
						$x++;
					}else{
						if ($this->isOrWhere) {
							$this->where .= " Or ";
						}else{
							$this->where .= " AND ";
						}
						
						$x++;
					}
					$count_param = count($param);

					if ($count_param == 1) {
						$this->where .= "$param[0] = ?";
						$this->bindValues[] =  $param[0];
					}elseif ($count_param == 2) {
						$operators = explode(',', "=,>,<,>=,>=,<>");
						$operatorFound = false;

						foreach ($operators as $operator) {
							if ( strpos($param[0], $operator) !== false ) {
								$operatorFound = true;
								break;
							}
						}

						if ($operatorFound) {
							$this->where .= $param[0]." ?";
						}else{
							$this->where .= "`".trim($param[0])."` = ?";
						}

						$this->bindValues[] =  $param[1];
					}elseif ($count_param == 3) {
						$this->where .= "`".trim($param[0]). "` ". $param[1]. " ?";
						$this->bindValues[] =  $param[2];
					}

				}
				//end foreach
			}
			// end if there is an Array
			$this->sql .= $this->where;

			$this->getSQL = $this->sql;
			$stmt = $this->dbh->prepare($this->sql);
			$stmt->execute($this->bindValues);
			return $stmt->rowCount();
		}// end if there is an ID or Array
		// $this->getSQL = "<b>Attention:</b> This Query will update all rows in the table, luckily it didn't execute yet!, use exec() method to execute the following query :<br>". $this->sql;
		// $this->getSQL = $this->sql;
		return $this;
	}

	public function insert( $table_name, $fields = [] ){
		$this->resetQuery();

		$keys = implode('`, `', array_keys($fields));
		$values = '';
		$x=1;
		foreach ($fields as $field => $value) {
			$values .='?';
			$this->bindValues[] =  $value;
			if ($x < count($fields)) {
				$values .=', ';
			}
			$x++;
		}
 
		$this->sql = "INSERT INTO `{$table_name}` (`{$keys}`) VALUES ({$values})";
		$this->getSQL = $this->sql;
		$stmt = $this->dbh->prepare($this->sql);
		$stmt->execute($this->bindValues);
		$this->lastIDInserted = $this->dbh->lastInsertId();

		return $this->lastIDInserted;
	}

	public function table($table_name){
		$this->resetQuery();
		$this->table = $table_name;
		return $this;
	}

	public function select($columns){
		$columns = explode(',', $columns);
		foreach ($columns as $key => $column) {
			$columns[$key] = $column;
		}
		
		$columns = implode('`, `', $columns);
		$this->columns = `[$columns]`;
		return $this;
	}
	// public function select(string ...$column_name)
    // {
    //     $this->columns = $column_name;
    //     return $this;
    // }
	// public function join($join, $on){
		
	// 	$this->join = "items.*, categories.ID AS name FORM $this->table JOIN $join ON $on";
	// 	return $this;
	// }
	public function join(string $table_name, $FK, $PK):DB
    {
        $this->join = " JOIN  $table_name  ON  $FK  =  $PK";
        return $this;
    }

	public function where(){
		if ($this->whereCount == 0) {
			$this->where .= " WHERE ";
			$this->whereCount+=1;
		}else{
			$this->where .= " AND ";
		}

		$this->isOrWhere= false;
		
		$num_args = func_num_args();
		$args = func_get_args();
		if ($num_args == 1) {
			if (is_numeric($args[0])) {
				$ids = NULL;
				$this->where .= "$args[0] = ?";
				$this->bindValues[] =  $args[0];
            }
		}elseif ($num_args == 2) {
			$operators = explode(',', "=,>,<,>=,>=,<>");
			$operatorFound = false;
			foreach ($operators as $operator) {
				if ( strpos($args[0], $operator) !== false ) {
					$operatorFound = true;
					break;
				}
			}

			if ($operatorFound) {
				$this->where .= $args[0]." ?";
			}else{
				$this->where .= "`".trim($args[0])."` = ?";
			}

			$this->bindValues[] =  $args[1];

		}elseif ($num_args == 3) {
			
			$this->where .= "`".trim($args[0]). "` ". $args[1]. " ?";
			$this->bindValues[] =  $args[2];
		}

		return $this;
	}

	public function orWhere(){
		if ($this->whereCount == 0) {
			$this->where .= " WHERE ";
			$this->whereCount+=1;
		}else{
			$this->where .= " OR ";
		}
		$this->isOrWhere= true;
		$num_args = func_num_args();
		$args = func_get_args();
		if ($num_args == 1) {
			if (is_numeric($args[0])) {
				$this->where .= "$args[0] = ?";
				$this->bindValues[] =  $args[0];
			}
		}elseif ($num_args == 2) {
			$operators = explode(',', "=,>,<,>=,>=,<>");
			$operatorFound = false;
			foreach ($operators as $operator) {
				if ( strpos($args[0], $operator) !== false ) {
					$operatorFound = true;
					break;
				}
			}
			if ($operatorFound) {
				$this->where .= $args[0]." ?";
			}else{
				$this->where .= "`".trim($args[0])."` = ?";
			}
			$this->bindValues[] =  $args[1];

		}elseif ($num_args == 3) {
			$this->where .= "`".trim($args[0]). "` ". $args[1]. " ?";
			$this->bindValues[] =  $args[2];
		}
		return $this;
	}

	public function get(){
		$this->assimbleQuery();
		$this->getSQL = $this->sql;

		$stmt = $this->dbh->prepare($this->sql);
		$stmt->execute($this->bindValues);
		$this->rowCount = $stmt->rowCount();

		$rows = $stmt->fetchAll();
		$collection= [];
		foreach ($rows as $key => $row) {
			$collection[] = (array) $row;
		}

		return $collection;
	}

	private function assimbleQuery()
	{
		if ( $this->columns !== null ) {
			$select = $this->columns;
		}else{
			$select = "*";
		}

		$this->sql = "SELECT $select FROM `$this->table`";
        if ($this->join !== null) {
			$this->sql .= $this->join;
		}

		if ($this->where !== null) {
			$this->sql .= $this->where;
		}

		if ($this->orderBy !== null) {
			$this->sql .= $this->orderBy;
		}

		if ($this->limit !== null) {
			$this->sql .= $this->limit;
		}
	}

	public function limit($limit, $offset=null)
	{
		if ($offset ==null ) {
			$this->limit = " LIMIT {$limit}";
		}else{
			$this->limit = " LIMIT {$limit} OFFSET {$offset}";
		}

		return $this;
	}
	public function orderBy($field_name, $order = 'ASC'){
		$field_name = trim($field_name);

		$order =  trim(strtoupper($order));
		if ($field_name !== null && ($order == 'ASC' || $order == 'DESC')) {
			if ($this->orderBy ==null ) {
				$this->orderBy = " ORDER BY $field_name $order";
			}else{
				$this->orderBy .= ", $field_name $order";
			}	
		}
		return $this;
	}

	public function count()
	{
		// Start assimble Query
		$countSQL = "SELECT COUNT(*) FROM `$this->table`";

		if ($this->where !== null) {
			$countSQL .= $this->where;
		}

		if ($this->limit !== null) {
			$countSQL .= $this->limit;
		}
		// End assimble Query

		$stmt = $this->dbh->prepare($countSQL);
		$stmt->execute($this->bindValues);

		$this->getSQL = $countSQL;

		return $stmt->fetch(PDO::FETCH_NUM)[0];
	}

	public function redirectHome($TheMsg, $url = null, $seconds = 3){

        if($url === null){
            $url = 'index.php';
            $link = 'HomePage';
        }else{
            if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] !== ''){

                $url = $_SERVER['HTTP_REFERER'];
                $link = 'Previous Page';

            }else{
                
                $url = 'index.php'; 
                $link = 'HomePage';
            } 
        }

        echo "<div class='container'>";
        echo $TheMsg;
        echo "<div class='alert alert-info'>You Will Redirected To $link After $seconds Seconds.</div>";

            header("refresh:$seconds;url=$url");
            
            exit(); 

        echo "</div>";
    }

	function showSharer($url, $message){ ?>
		<a class="sharing-button me-2" href="https://twitter.com/intent/tweet/?text=<?php echo urlencode($message) ?>&amp;url=<?php echo urlencode($url) ?>" target="_blank" rel="noopener" aria-label="Share on Twitter">
			<i class="fa fa-twitter p-2 fs-3 bg-info  text-white rounded"></i>
		</a>
		<a class="sharing-button me-2" href="mailto:?subject=<?php echo urlencode($message) ?>&amp;body=<?php echo urlencode($url) ?>" target="_self" rel="noopener" aria-label="Share by E-Mail">
			<i class="fa fa-envelope p-2 fs-3 bg-danger text-white rounded"></i>
		</a>
		<a class="sharing-button me-2" href="https://mail.google.com/mail/u/0/#inbox?compose=<?php echo urlencode($message) ?>&amp;body=<?php echo urlencode($url) ?>" target="_self" rel="noopener" aria-label="Share by E-Mail">
			<i class="fa fa-at p-2 fs-3 bg-dark text-white rounded"></i>
		</a>
		<a class="sharing-button me-2" href="https://facebook.com/sharer/sharer.php?u=<?php echo urlencode($url) ?>" target="_blank" rel="noopener" aria-label="Share on Facebook">
			<i class="fa fa-facebook p-2 px-3 fs-3 bg-primary text-white rounded"></i>
		</a>
		<a class="sharing-button me-2" href="https://pinterest.com/pin/create/button/?url=<?php echo urlencode($url) ?>&amp;media=<?php echo urlencode($url) ?>&amp;description=<?php echo urlencode($message) ?>" target="_blank" rel="noopener" aria-label="Share on Pinterest">
			<i class="fa fa-pinterest p-2 fs-3 bg-dark text-white rounded"></i>
		</a>
		<a class="sharing-button me-2" href="https://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo urlencode($url) ?>&amp;title=<?php echo urlencode($message) ?>&amp;summary=<?php echo urlencode($message) ?>&amp;source=<?php echo urlencode($url) ?>" target="_blank" rel="noopener" aria-label="Share on LinkedIn">
			<i class="fa fa-linkedin p-2 fs-3 bg-primary text-white rounded"></i>
		</a>
		<!-- <i class="fa-brands fa-pinterest-square"></i> -->
<?php
	}
}

?>