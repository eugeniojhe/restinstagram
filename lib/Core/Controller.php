<?php
	namespace lib\Core;
 	class Controller {
 		public function getMethod()
 		{
 			return $_SERVER['REQUEST_METHOD'];
 		}
 		public function getRequestData()
 		{
 			switch($this->getMethod()){
 				case 'GET':
 					return $_GET;
 				/*Methods PUT and DELETE Recebe o conteúdo a query string(name=pedro&idade=xxx)
				e o parse_str coloca o conteúdo na variável $data*/ 
 				case 'PUT':
 				case 'DELETE':
 				  	parse_str(file_get_contents('php://input'),$data);
					return (array) $data;
					break; 
				//Method post os dados sao recebidos em forma de json 
				case 'POST':
					$data = json_decode(file_get_contents('php://input'));
					//Se veio através de formulário, pega a da variavel global $_POST
					if (is_null($data)){
						$data = $_POST; 
					}
					return (array) $data;
					break; 
 			}

 		}
 		public function jsonReturn($array)
 		{
 			header("Content-type:application/json");
 			echo json_encode($array); 			
 		}

 	} 
	 


