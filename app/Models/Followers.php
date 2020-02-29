<?php
    namespace app\Models;
    use lib\core\Model; 
    Class Followers extends Model{
        //Count users are following you(user who is connected)   
    	public function getCountFollowed($usrId)
    	{
    		$response = 0; 
    		$sql = "SELECT COUNT(id) as c
                    FROM followers  
    		        WHERE id_followed = :id_followed"; 
    		$sql = $this->db->prepare($sql);
    		$sql->bindValue(":id_followed",$usrId); 
    		$sql->execute();
    		$r = $sql->fetch();
    	    $response = $r['c']; 
    		return $response; 
    	}

        //Count users you(user connected) are following()
    	public function getCountFollowing($usrId)
    	{
    		$response = 0; 
    		$sql = "SELECT COUNT(id) as c 
                    FROM followers 
    		        WHERE id_follower = :id_follower"; 
    		$sql = $this->db->prepare($sql);
    		$sql->bindValue(":id_follower",$usrId); 
    		$sql->execute();
    		$r = $sql->fetch();
    	    $response = $r['c']; 
    		return $response;
    	}

	}
