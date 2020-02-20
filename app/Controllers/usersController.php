<?php
	namespace app\Controllers; 
	use lib\Core\Controller;
	use app\Models\Users;  
	class usersController extends Controller {
		public function login()
		{
			$array = array('error' => '');
			$method = $this->getMethod();
			$data = $this->getRequestData();
			$method = "POST"; 
			if ($method == 'POST'){
				$ioUsers = new Users(); 
				if(!empty($data['email']) && !empty($data['password'])){
					if ($ioUsers->checkCredentials($data['email'],$data['password'])){
						$ioUsers->createJwt(); 

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
	}
