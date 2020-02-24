<?php 
	namespace app\Controllers; 
     use lib\Core\Controller; 

	class homeController extends Controller{
		public function index(){
			echo "Method: ".$this->getMethod()."\n"; 
			print_r($this->getRequestData());
			$this->jsonReturn($this->getRequestData());  
		}
	}

