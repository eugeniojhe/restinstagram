<?php 
    namespace app\Models; 
    use lib\Core\Model; 
	class Photo_Likes extends Model{

		public function store($photoId,$userId) 
		{
			$response = "";
			$sql = "SELECT id FROM photo_likes 
			        WHERE id_photo = :id_photo
			        AND   id_user = :id_user"; 
			$sql = $this->db->prepare($sql);
			$sql->bindValue(":id_photo",$photoId);
			$sql->bindValue(":id_user",$userId); 
			$sql->execute();
			if ($sql->rowCount() >0){
				$response = "You have already  liked this photo"; 
			}else{
				$sql = "INSERT INTO photo_likes 
				        (id_photo, id_user, dt_like, id_active)
                        VALUES (:id_photo, :id_user, NOW(),:id_active)";
                $sql = $this->db->prepare($sql); 
                $sql->bindValue(":id_photo",$photoId);
                $sql->bindValue(":id_user",$userId);
                $sql->bindValue(":id_active","S"); 
                $sql->execute();  
			}
			return $response; 
		}

		public function delete($photoId,$userId) 
		{
			$response = "";
			$sql = "SELECT id FROM photo_likes 
			        WHERE id_photo = :id_photo
			        AND   id_user = :id_user"; 
			$sql = $this->db->prepare($sql);
			$sql->bindValue(":id_photo",$photoId);
			$sql->bindValue(":id_user",$userId); 
			$sql->execute();
			if ($sql->rowCount()>0){
				$sql = "UPDATE  photo_likes 
				        SET id_active = :id_active
				         WHERE id_photo = :id_photo
			        	AND   id_user = :id_user";                   
                $sql = $this->db->prepare($sql); 
                $sql->bindValue(":id_photo",$photoId);
                $sql->bindValue(":id_user",$userId);
                $sql->bindValue(":id_active","N"); 
                $sql->execute();
			}else{
				$response = "We are not liked this photo";					 
			}
			return $response; 
		}


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
