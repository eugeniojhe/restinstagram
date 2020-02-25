<?php 
	namespace app\Models; 
	use lib\Core\Model; 
	use lib\Models\Jwt; 

	class Users extends Model{
		private $usr_id; 
		public function create($name,$email,$password,$avatar = null)
		{
			if (!$this->emailExist($email)){
				$hash = password_hash($password,PASSWORD_DEFAULT); 
				$sql = "INSERT INTO users 
				        (name,email,password,avatar) 
				        VALUES (:name, :email,
				                :password,:avatar)"; 
			    $sql = $this->db->prepare($sql); 
			    $sql->bindValue(":name",$name);
			    $sql->bindValue(":email",$email); 
			    $sql->bindValue(":password",$hash);
			    $sql->bindValue(":avatar",$avatar);
			    try{
			    	$sql->execute();
			    }catch(Exception $e){
			    	return false; 
			    }
			    
			    $this->usr_id = $this->db->lastInsertId();
			    return true;  
			}else{
				return false; 
			}
		}
		public function emailExist($email)
		{
			$sql = "SELECT id FROM users 
			        WHERE email = :email"; 
			$sql = $this->db->prepare($sql); 
			$sql->bindValue(":email",$email);
			$sql->execute(); 
			if ($sql->rowCount() > 0){
				return true; 
			}else{
				return false; 
			}
		}
		public function validateCredentials($email,$password)
		{
			$sql = "SELECT id,password 
			        FROM users
			        WHERE email = :email"; 
			$sql = $this->db->prepare($sql); 
			$sql->bindValue(":email",$email);
			$sql->execute();
			if ($sql->rowCount() > 0){
				$userInfo = $sql->fetch();
				if (password_verify($password,$userInfo['password'])){
					$this->usr_id = $userInfo['id']; 
					return true; 
				}else{
					return false; 
				}

			}else{
				return false; 
			} 


		}

		public function getId()
		{
			return $this->usr_id; 
		}
		public function createJwt()
		{
			$jwt = new Jwt();
			return $jwt->create(array('usr_id' => $this->usr_id));
		}
		public function validateJwt($token)
		{
			$ioJwt = new Jwt();
			$jwtDecode = $ioJwt->validate($token); 
			if (isset($jwtDecode->usr_id)){
				$this->usr_id  = $jwtDecode->usr_id;
				return true; 
			}else{
				return false; 
			}
		}

		public function loadInfo($usrId)
		{

		}
	}
