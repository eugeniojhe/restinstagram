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

        public function getFollowing($usrId)
        {            
            $response = array();
            $sql = "SELECT id_followed 
                    FROM followers 
                    WHERE id_follower = :id_follower";
            $sql = $this->db->prepare($sql);
            $sql->bindValue(":id_follower",$usrId); 
            $sql->execute(); 
            if ($sql->rowCount() > 0){
                $ids = $sql->fetchall();
                foreach($ids as  $id){
                    $response[] = $id['id_followed'];
                }                
            }
            return $response; 
        }

        public function store($followerId,$followedId)
        {
            $this->db->beginTransaction(); 
            $response = '';
            if ($followerId == $followedId) $response = "Yo can follow yourself";  
            $sql = "SELECT id FROM followers 
                    WHERE id_follower = :id_follower
                    AND id_followed = :id_followed";
           $sql = $this->db->prepare($sql); 
           try{
                $sql->bindValue(":id_follower",$followerId); 
                $sql->bindValue(":id_followed",$followedId); 
                $sql->execute(); 
           }catch(Exception $e){
               $response = "Failed to insert follower"; 
           }
            if ($response == '' && !$sql->rowCount() > 0)
            {
                $sql = "INSERT INTO 
                        followers (id_follower, id_followed,dt_follow)
                        VALUES (:id_follower,:id_followed, NOW())"; 
                $sql = $this->db->prepare($sql); 
                try{
                    $sql->bindValue(":id_follower",$followerId);
                    $sql->bindValue(":id_followed",$followedId); 
                    $sql->execute();                
                }catch(Exception $e){
                     $response = $e->getMessage(); 
                }
                
            }else{
                $response = "You are already following this user"; 
            }

            if ($response == ''){
                 $this->db->commit(); 
            }else{
                 $this->db->rollback(); 
            }
            return $response; 
        }

        public function delete($followerId,$followedId)
        {
            $this->db->beginTransaction(); 
            $response = '';
            $sql = "SELECT id WHERE id_follower = :id_follower AND id_followed = :id_followed";
           $sql = $this->db->prepare($sql); 
           try{
                $sql->bindValue(":id_follower",$followerId); 
                $sql->bindValue(":id_followed",$followedId); 
                $sql->execute(); 
           }catch(Exception $e){
               $this->db->rollback(); 
               $response = "You are not following this user"; 
           }
            if ($reponse == '' && $sql->rowCount() > 0)
            {
                $sql = "DELETE followers 
                        WHERE id_follower = :id_follower 
                        AND id_followed = :id_followed"; 

                try{
                    $sql->bindValue(":id_follower",$followerId);
                    $sql->bindValue(":id_followed",$followedId); 
                    $sql->execute();                   
                }catch(Exception $e){
                    $this->db->rollback(); 
                    $response = $e->getMessage(); 
                }
                
            }
            if ($respose == ''){
                $this->db->commit(); 
            }else{
                $this->db->rollback(); 
            }
            return $response; 
        }
	}
