<?php
	namespace app\Controllers; 
	use lib\Core\Controller;
	use app\Models\Users;  
	class usersController extends Controller {
		public function login()
		{
			$array = array('error' => 'User is now logged');
			$method = $this->getMethod();
			$data = $this->getRequestData();
			if ($method == 'POST'){
				$ioUsers = new Users(); 
				if(!empty($data['email']) && !empty($data['password'])){
					if ($ioUsers->validateCredentials($data['email'],$data['password'])){
						$array['jwt'] = $ioUsers->createJwt(); 

					}else {
						$array['error'] = 'Senha/Email invalido'; 
					}

				}else{
					$array['error'] =  "Password and Email are required"; 
				}
			}else{
				$array['error'] = 'Acesso negado - Invalid Method'; 

			}
			$this->jsonReturn($array); 
		}
		public function create()
		{
			$ioUser = new Users(); 
			$array = array('error' => ''); 
			$method = $this->getMethod();
			$data = $this->getRequestData();
			if ($method = "POST"){
				if (!empty($data['name']) && !empty($data['email']) && !empty($data['password'])){
					if (filter_var($data['email'],FILTER_VALIDATE_EMAIL)){
						if ($ioUser->create($data['name'],$data['email'],$data['password'])){
							$array['jwt'] = $ioUser->createJwt(); 

						}else{
							$array['error'] = "Failed - Creating user - Email already exist"; 
						}

					}else{
						$array['error'] = "Email invalid"; 
					}
					
				}else{
					$array['error'] = "Required fields are empty ";
				}
			}else{
			 	$array['error'] = "Invalid http method"; 
			}
			 $this->jsonReturn($array);
		}

		public function view($usr_id)
		{
			$return = array(
				    'error'  => '',
				    'logged' => false); 
			$method = $this->getMethod();
			$data = $this->getRequestData(); 
			$ioUsers = new Users(); 
			if (!empty($data['jwt']) && $ioUsers->validateJwt($data['jwt'])){
				$return['logged'] = true;
				$return['isMe'] = false; 
				if($usr_id == $ioUsers->getId()){
					$return['isMe'] = true;
				} 

			}else{
				$return['error'] = "Acces Denied"; 
			}

			switch($method){
				case "GET":
				   $return['user_info'] = $ioUser->loadInfo($usr_id);
					break;
				case "PUT":
				    break; 
				case "DELETE":
				    break;
				 default:
				    $return['error'] = "Invalid Methodo for this app";  
				    break; 
			}
			$this->jsonReturn($return); 
		}

	}
