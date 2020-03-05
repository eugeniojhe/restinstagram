<?php 
	namespace app\Models; 
	use lib\Core\Model; 
	use lib\Models\Jwt; 
	use \PDO; 


	class Users extends Model{
		//Insert a new user in BD 
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
		//Verify if email exist 
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


		//Validate user email and password 
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
		//Create a jwt 
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

        //get and return user data 
		public function loadInfo($usrId)
		{
		    $ioFollowers = new Followers(); 
		    $ioPhotos = new Photos(); 
		    $response = array(); 
			$sql = "SELECT id, name, email, avatar FROM users 
			        WHERE id = :id
			        AND id_active <> 'N' 
			        OR id_active IS NULL"; 
			$sql = $this->db->prepare($sql); 
			$sql->bindValue(":id",$usrId); 
			$sql->execute(); 
			if ($sql->rowCount() > 0){
				$response = $sql->fetch(PDO::FETCH_ASSOC); 
				if (!empty($response['avatar'])){
                    $response['avatar'] = BASE_URL.'app/media/images/'.$response['avatar']; 
				}else{
					$response['avatar'] = BASE_URL.'app/media/images/default.jpg';
				}
				$response['following'] = $ioFollowers->getCountFollowing($usrId);
				$response['followed']  = $ioFollowers->getCountFollowed($usrId); 
				$response['user_photos'] = $ioPhotos->getCountPhotos($usrId); 
			}
			return $response; 
		}
		public function edit($usrId,$data)
				{
				  $dataToChange = array(); 
				  if ($usrId === $this->getId()){
				  	if (!empty($data['name'])){
				  	   $data['name'] = $data['name'];	
				  	} 
				  	if (!empty($data['password'])){
				  		$dataToChange['password'] = password_hash($data['password'],PASSWORD_DEFAULT); 
				  	}
				    if (!empty($data['email'])){
				    	if(filter_var($data['email'],FILTER_VALIDATE_EMAIL) )
				    	{
				    		if (!$this->emailExist($data['email'])){
				    			$dataToChange['email'] = $data['email'];

				    		}else {
				    			return "Email already exist"; 
				    		}
				    		 
				    	}else{
				    		return "Invalid Email"; 
				    	}
				    }
				     if (!empty($data['id_ativo'])){
				     	$dataToChange['id_ativo'] = $data['id_ativo']; 
				     }
				     if (count($dataToChange) >0){
				     	foreach($dataToChange as $key => $value){
				     		$fieldsToChange[] = $key. ' = '.':'.$key; 
				     	}
				    	 $sql = "UPDATE users SET ".implode(',',$fieldsToChange)." WHERE id = :id";
				    	$sql = $this->db->prepare($sql); 
				    	$sql->bindValue(":id",$usrId); 
				    	foreach($dataToChange as $key => $value){
				    		$sql->bindValue(":".$key,$value); 
				    	}  
				    	$sql->execute();
				    	return "";  
				     }
                     
		 		 }else{
		  			return "You are not able to edit this user info"; 
		  		}
		}

        //It will not delete from data base, 
        //this will be updated with "S" meaning active or "N" meaning inactive
		public function delete($usrId,$data)
		{
			if ($usrId === $this->getId()){
				if ($data['id_active'] == 'S' || $data['id_active'] == 'N'){
						$sql = "UPDATE users SET id_active = :id_active
						    WHERE id = :id";
					    $sql = $this->db->prepare($sql);
					    $sql->bindValue(":id_active",strtoupper($data['id_active']));
					    $sql->bindValue(":id",$usrId); 
					    $sql->execute(); 
					    return 'Edit user went ok'; 
					
				}else{
					return "id_active must be 'N' or 'S'"; 
				}

			}else{
				return "You are not able to edit this user"; 
			}
		}
	}
