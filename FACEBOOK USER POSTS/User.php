<?php
class User {
	private $dbHost = DB_HOST;
	private $dbUsername = DB_UNAME;
	private $dbPassword = DB_PWD;
	private $dbName = DB_NAME;
	private $userTable = DB_USER_TABLE;
	private $postTable = DB_POST_TABLE;
	
	function _construct() {
		if (isset($this->db)) {
			// db connection
			
			$conn = new mysqli($this->dbHost, $this->dbUsername, $this-> dbPassword, $this->dbName);
			if($conn->connect_error) {
				die("Failed to connect to MySQL Database due to : " . $conn->connect_error);
			}else{
				$this->db = $conn;
			}
		}
	}
	
	function checkUser($userData = array()) {
		if(!empty($userData)){
			// check if already exists
			$query1 = "SELECT * FROM " .$this->userTable. " WHERE oauth_provider = '" .$userData['oauth_provider']. "' AND oauth_uid = '" .$userData['oauth_uid']. "'";
			$result1 = $this->db->query($query1);
			if($result1->num_rows > 0){
				// update if already exists
				$query2 = "UPDATE '" .$this->userTable. "' SET first_name = '" .$userData['firstname']. "', last_name = '" .$userData['last_name']. "', email = '" .$userData['email']. "', gender = '" .$userData['gender']. "', picture = '" .$userData['picture']. "',link = '" .$userData['link]. "', modified = NOW() WHERE oauth_provider= '" .$userData['oauth_provider']. "' AND oauth_uid = '" .$userData['oauth_uid']. "'"";
				$update = $this->db->query($query2);
			} else {
				$query2 = "INSERT INTO ".$this->userTbl." SET oauth_provider = '".$userData['oauth_provider']."', oauth_uid = '".$userData['oauth_uid']."', first_name = '".$userData['first_name']."', last_name = '".$userData['last_name']."', email = '".$userData['email']."', gender = '".$userData['gender']."', picture = '".$userData['picture']."', link = '".$userData['link']."', created = NOW(), modified = NOW()"; 
				$insert = $this->db->query($query2);
			}
			$result = $this->db->query($query1);
			$userData = $result->fetch_assoc();
		}
		return $userData;
	}
	
	public function getPosts($conditions = array()){
		$sql = 'SELECT *';
		$sql .= ' FROM ' .$this->postTable;
		if(array_key_exists("where", $conditions)){
			$sql .= ' WHERE ';
			$i = 0;
			foreach($conditions['where'] as $key => $value) {
				$pre = ($i > 0)?' AND ': '';
				$sql .= $pre.$key." = '".$value."'";
				$i++;
			}
		}
		
		if(array_key_exists("order_by",$conditions)){ 
            $sql .= ' ORDER BY '.$conditions['order_by'];  
        }else{ 
            $sql .= ' ORDER BY created_time DESC ';  
        }
		
		if(array_key_exists("start",$conditions) && array_key_exists("limit",$conditions)){ 
            $sql .= ' LIMIT '.$conditions['start'].','.$conditions['limit'];  
        }elseif(!array_key_exists("start",$conditions) && array_key_exists("limit",$conditions)){ 
            $sql .= ' LIMIT '.$conditions['limit'];  
        } 
         
        $result = $this->db->query($sql);
		
		if(array_key_exists("return_type",$conditions) && $conditions['return_type'] != 'all'){ 
            switch($conditions['return_type']){ 
                case 'count': 
                    $data = $result->num_rows; 
                    break; 
                case 'single': 
                    $data = $result->fetch_assoc(); 
                    break; 
                default: 
                    $data = ''; 
            } 
        }else{ 
            if($result->num_rows > 0){ 
                while($row = $result->fetch_assoc()){ 
                    $data[] = $row; 
                } 
            } 
        } 
        return !empty($data)?$data:false;
	}
	
	function insertPost($data){ 
        if(!empty($data) && is_array($data)){ 
            $columns = ''; 
            $values  = ''; 
            $i = 0; 
            foreach($data as $key=>$val){ 
                $pre = ($i > 0)?', ':''; 
                $columns .= $pre.$key; 
                $values  .= $pre."'".$this->db->real_escape_string($val)."'"; 
                $i++; 
            } 
            $query = "INSERT INTO ".$this->postTable." (".$columns.") VALUES (".$values.")"; 
            $insert = $this->db->query($query); 
            return $insert?$this->db->insert_id:false; 
        }else{ 
            return false; 
        } 
    } 
	
	public function deletePosts($userID){ 
        $query = "DELETE FROM ".$this->postTable." WHERE user_id = $userID"; 
        $delete = $this->db->query($query); 
        return $delete?true:false; 
    } 
}
?>