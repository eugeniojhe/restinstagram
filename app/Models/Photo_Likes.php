<?php 
    namespace app\Models; 
    use lib\Core\Model; 
	class Photo_Likes extends Model{
		public function countLikes($photoId){
			$response = 0;
			$sql = "SELECT count(id) as c 
			        FROM photo_likes 
			        WHERE id = {$photoId}"; 
			$sql = $this->db->query($sql); 
			if ($sql->rowCount()>0){
				$c = $sql->fetch(); 
				$response = $c['c'];
			}
			return $response;
		}

        // Make photo's likes where 
		public function delPhotosLikes($idUser,$isUserConnected = false)
		{
			$response = array();
			//
			if ($isUserConnected){
				$sql = "UPDATE photo_likes
				        SET id_active = upper('N')  
			       	  WHERE id_user = :id_user"; 
			    $sql = $this->db->prepare($sql);
			    try{
			    	$sql->bindValue(":id_user",$idUser);
			    	$sql->execute(); 
			    }catch(Exception $e){
			    	$response['error'] = $e->getMessage(); 
			    }
				}else{
					$response['error'] = 'You are not allowed to delete this record '; 
				}
				
		    return $response; 
		}  

		
		
	}
