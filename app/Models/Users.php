<?php 
	namespace app\Models; 
	use lib\Core\Model; 

	class Users extends Model{
		private $usr_id; 
		public function create($name,$email,$password,$avatar = null)
		{
			if (!emailExist($email)){
				$hash = password_hash($password,PASSWORD_DEFAULT); 
				$sql = "INSERT INTO users 
				        (name,email,password,avatar) 
				        VALUES (:name, :email,
				                :password,:avatar:"; 
			    $sql = $this->db->prepare($sql); 
			    $sql->bindValue(":name",$name);
			    $sql->bindValue(":email",$email); 
			    $sql->bindValue(":password",$hash);
			    $sql->bindValue(":avatar",$avatar);
			    $sql->execute();
			    $this->usr_id = $this->db->lastInsertId(); 
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
		public function createJwt()
		{
			$jwt = new Jwt();
			return $jwt->create(array('usr_id' => $this->usr_id));
		}
	}
