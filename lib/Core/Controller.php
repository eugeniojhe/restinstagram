<?php
	namespace lib\Core;
 	class Controller {
 		public function getMethod()
 		{
 			return $_SERVER['REQUEST_METHDO'];
 		}
 		public function getRequestData()
 		{
 			switch($this->getMethod()){
 				case 'GET':
 					return $_GET; 
 				case 'POST'; 
 				case 'PUT':
 				case 'DELETE':

 			}

 		}
 		public function jsonReturn()
 		{
 			
 		}

 	} 
	 


