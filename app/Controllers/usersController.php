<?php
	namespace app\Controllers; 
	use lib\Core\Controller;
	use app\Models\Users; 
	use app\Models\Photos; 
	use app\Models\Followers; 

	class usersController extends Controller {
		private $ioUser;
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
						$response['error'] = 'Senha/Email invalido or user is inactive'; 
					}

				}else{
					$response['error'] =  "Password and Email are required"; 
				}
			}else{
				$response['error'] = 'Acesso negado - You must use the POST method'; 

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
			 	$response['error'] = "Invalid http method. You must use POST method"; 
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
			if (is_int(intval($usr_id)) && intval($usr_id) > 0){
		    	if (!empty($data['jwt'])){
					if ($this->ioUser->validateJwt($data['jwt'])){
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
							    $response['msg'] = $this->ioUser->delete($usr_id,$data); 
				    			break;
				 			default:
				 				$response['error'] = "Invalid Method {$method} for that action ".__METHOD__;  
				    		break;
				    	}	    	

					}else{
						$response['error'] = "jwt is not valid for this user and action ".__METHOD__; 
					}
				}else{
					$response['error'] = "Access Denied - Please enter JWT hash for action ".__METHOD__;
				}

		    }else{
		    	$response['error'] = "Parameter [{$usr_id}] must be numeric for action or greater than zeros ".__METHOD__; 
		    }
							
			$this->jsonReturn($response); 
		}

		public function  feed()
		{
			$response = array(
		    'error'  => '',
		    'logged' => false);
		    $method = $this->getMethod();
			$data = $this->getRequestData();
			if ($method = "GET"){
				if (!empty($data['jwt'])){
					if ($this->ioUser->validateJwt($data['jwt'])){
						$response['logged'] = true;
						   $offset = intval((!empty($data['offset'])?$data['offset']:0));
					    $itemsPerPage = intval((!empty($data['itemspage'])?$data['itemspage']:10));
					    $response['users_feed'] = $this->ioUser->feed($offset,$itemsPerPage);	     
					}else {
						$response['error'] = "Please enter a valid jwt"; 
					}

				}else{
					$response['error'] = "Please jwt is required"; 
				}

			}else{
				$response['error'] = "Please inter a valid method. It must be GET methdo"; 
			}
			$this->jsonReturn($response); 
		}

		public function  photos($usr_id)
		{
			$ioPhotos = new Photos();
			$response = array(
		    'error'  => '',
		    'logged' => false);
		    $method = $this->getMethod();
			$data = $this->getRequestData();
			if ($method = "GET"){
				if (!empty($data['jwt'])){
					if ($this->ioUser->validateJwt($data['jwt'])){
						$response['logged'] = true;
						   $offset = intval((!empty($data['offset'])?$data['offset']:0));
					    $itemsPerPage = intval((!empty($data['itemspage'])?$data['itemspage']:10));
					    $response['isMe'] = false; 
					    if($usr_id == $this->ioUser->getId()){
							$response['isMe'] = true;
						} 
					    $response['user_photos'] = $ioPhotos->getPhotos($usr_id,$offset,$itemsPerPage);	     
					}else {
						$response['error'] = "Please enter a valid jwt"; 
					}

				}else{
					$response['error'] = "Please jwt is required"; 
				}

			}else{
				$response['error'] = "Please inter a valid method. It must be GET methdo"; 
			}
			$this->jsonReturn($response); 
		}

		public function follow($usr_id)
		{
			$response = array(
		    'error'  => '',
		    'logged' => false);
		    $ioFollowers = new Followers();  
		    $method = $this->getMethod();
			$data = $this->getRequestData();
			
			if (!empty($data['jwt'])){
				if ($this->ioUser->validateJwt($data['jwt'])){
					$response['logged'] = true;
					$response['isMe'] = false; 
					if($usr_id == $this->ioUser->getId()){
							$response['isMe'] = true;
					} 

					switch($method){
						case "POST":
						    if ($response['isMe']){
						    	$response['error'] = 'You can not follow yourself'; 
						    }else{
						    	$response['error'] = $ioFollowers->store($this->ioUser->getId(),$usr_id,);	
						    } 
						    
							break;
						case "DELETE":
							$response['error'] = $ioFollowers->delete($this->ioUser->getId(),$usr_id,);
						    break; 
						default:
						  $reponse['error'] = "Please enter a valide method. It must be POST or DELETE method"; 
					}
					        
				}else {
					$response['error'] = "Please enter a valid jwt"; 
				}

			}else{
					$response['error'] = "Please jwt is required for this method"; 
			}
			$this->jsonReturn($response); 
		}
	}
