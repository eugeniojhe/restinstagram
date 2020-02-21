<?php
	namespace app\Controllers; 
	use lib\Core\Controller;
	use app\Models\Users;  
	class usersController extends Controller {
		public function login()
		{
			$array = array('error' => 'Login realizado com sucesso');
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
							$array['error'] = "Failed - Creating user"; 
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

	}
