<?php 
     namespace app\Controllers; 
     use lib\Core\Controller; 

	class homeController extends Controller{
		public function index(){
			echo "Method: ".$this->getMethod()."\n"; 
			print_r($this->getRequestData());
			echo "-----"."\n";
			$this->jsonReturn($this->getRequestData());  
		}

	    public function ola()
	    {
	    	echo "Bem meu amigos, então ola"; 
	    }

	}

