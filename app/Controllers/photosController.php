<?php
	namespace app\Controllers; 
	use lib\Core\Controller;
	use app\Models\Users; 
	use app\Models\Photos; 

	class photosController extends Controller {
		private $ioUser; 
		private $ioPhotos; 
        public function __construct()
        {
        	$this->ioUser = new Users(); 
        	$this->ioPhotos = new Photos(); 
        }


        public function random()
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
						 $itemsPerPage = intval((!empty($data['itemspage'])?$data['itemspage']:10));
						$except = array(); 
						if (!empty($data['except'])){
							$except[] = $data['except']; 
						}
					    $response['aleatory_photos'] = $this->ioPhotos->randomPhotos($itemsPerPage,$except);	     
					}else {
						$response['error'] = "Please enter a valid jwt"; 
					}

				}else{
					$response['error'] = "Please jwt is required"; 
				}

			}else{
				$response['error'] = "Please inter a valid method. It must be GET method"; 
			}
			$this->jsonReturn($response);
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
			 				$response['error'] = "Invalid Method {$method} for this app";  
			    		break;
			    	}	    	

				}else{
					$response['error'] = "jwt is not valid for this user"; 
				}
			}else{
				$response['error'] = "Access Denied - Please enter JWT hash";
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


	}
