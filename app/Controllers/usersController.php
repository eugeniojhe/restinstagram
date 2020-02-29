<?php
	namespace app\Controllers; 
	use lib\Core\Controller;
	use app\Models\Users;  
	class usersController extends Controller {
		private $idUser; 
        public function __construct()
        {
        	$this->ioUser = new Users(); 
        }

        //Validate user login 
		public function login()
		{
			$response = array('error' => 'User is now logged');
			$method = $this->getMethod();
			$data = $this->getRequestData();
			if ($method == 'POST'){
				if(!empty($data['email']) && !empty($data['password'])){
					if ($this->ioUser->validateCredentials($data['email'],$data['password'])){
						$response['jwt'] = $this->ioUser->createJwt(); 

					}else {
						$response['error'] = 'Senha/Email invalido'; 
					}

				}else{
					$response['error'] =  "Password and Email are required"; 
				}
			}else{
				$response['error'] = 'Acesso negado - Invalid Method'; 

			}
			$this->jsonReturn($response); 
		}

		//Create a new user - 
		public function create()
		{
			$response = array('error' => ''); 
			$method = $this->getMethod();
			$data = $this->getRequestData();
			if ($method = "POST"){
				if (!empty($data['name']) && !empty($data['email']) && !empty($data['password'])){
					if (filter_var($data['email'],FILTER_VALIDATE_EMAIL)){
						if ($this->ioUser->create($data['name'],$data['email'],$data['password'])){
							$response['jwt'] = $this->ioUser->createJwt(); 

						}else{
							$response['error'] = "Failed - Creating user - Email already exist"; 
						}

					}else{
						$response['error'] = "Email invalid"; 
					}
					
				}else{
					$response['error'] = "Required fields are empty ";
				}
			}else{
			 	$response['error'] = "Invalid http method {$method}"; 
			}
			 $this->jsonReturn($response);
		}

        //Return data user 
		public function view($usr_id)
		{
			$response = array(
		    'error'  => '',
		    'logged' => false); 
			$method = $this->getMethod();
			$data = $this->getRequestData(); 
			if (!empty($data['jwt']) && $this->ioUser->validateJwt($data['jwt'])){
				$response['logged'] = true;
				$response['isMe'] = false; 
				if($usr_id == $this->ioUser->getId()){
					$response['isMe'] = true;
				} 
				switch($method){
					case "GET":
		   				$response['user_info'] = $this->ioUser->loadInfo($usr_id);
		   				if (count($response['user_info']) == 0) {
		   				$response['error'] = "Invalid user Code"; 
		   				}
						break;
					case "PUT":
			    		$response = $this->ioUser->edit($usr_id,$data);
		    			break; 
					case "DELETE":
					    $response = $this->ioUser->delete($usr_id); 
		    			break;
		 			default:
		 				$response['error'] = "Invalid Method {$method} for this app";  
		    		break;
		    	}
		    }else{
				$response['error'] = "Access Denied - Please enter jwt hash";
			}	
			$this->jsonReturn($response); 
		}
	}
