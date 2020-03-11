<?php 
    namespace app\Models; 
    use lib\Core\Model; 
	class Photos extends Model{
		public function getCountPhotos($usrId)
		{
			$response = 0; 
			$sql = "SELECT COUNT(id) as c
			        FROM photos
			        WHERE id_user = :id_user";
			$sql = $this->db->prepare($sql); 
			$sql->bindValue(":id_user",$usrId);
            $sql->execute(); 
            if ($sql->rowCount() > 0){
            	$r = $sql->fetch(); 
            	$response = $r['c']; 
            }
			return $response; 
		}
        public function getFeedPhotos($usersFollowingIds,$offset,$itemsPerPage)
        {
        	$response = array();
        	$ioPhotoLikes = new Photo_likes(); 
        	$ioPhotoComments = new Photo_comments(); 
        	if (count($usersFollowingIds) > 0 ){
                $sql = "SELECT p.*, u.name, u.avatar
                        FROM photos p
                        LEFT JOIN users u ON(u.id = p.id_user)  
                        WHERE id_user IN(".implode(',',$usersFollowingIds).")
                        ORDER BY id DESC
                        LIMIT ".$offset.",".$itemsPerPage;                     
        		try{
					 $sql = $this->db->query($sql);
				}catch(Exception $e){
					$respose['error'] =  $e->getMessage(); 
				}
				 
				if ($sql->rowCount() > 0){
					$response = $sql->fetchall(\PDO::FETCH_ASSOC);
					foreach($response as $key => $value){
						$response[$key]['url'] =  BASE_URL."media/images/".$response[$key]['url'];
						 $response[$key]['likes'] = $ioPhotoLikes->countLikes($value['id']);
						 $response[$key]['avatar'] = BASE_URL."media/images/".$response[$key]['avatar']; 
						 $response[$key]['comments'] = $ioPhotoComments->getComments($value['id']); 
					} 
				}
        	}
        	return $response; 
        }


        public function randomPhotos($itemsPerPage,$except)
        {
        	$response = array();
        	foreach($except as $k => $v){
        		$except[$k] = intval($v); 
        	}
        	if (count($except) > 0){
        		 $sql = "SELECT p.*, u.name, u.avatar
                     FROM photos p
                     LEFT JOIN users u ON(u.id = p.id_user)  
                     WHERE p.id NOT IN(".implode(',', $except).")"." 
                     ORDER BY RAND()  
                     LIMIT ".$itemsPerPage; 
        	}else{
        		$sql = "SELECT p.*, u.name, u.avatar
                    FROM photos p
                    LEFT JOIN users u ON(u.id = p.id_user)  
                    ORDER BY RAND()
                    LIMIT ".$itemsPerPage; 
        	}

        	$sql = $this->db->query($sql);
        	if ($sql->rowCount() > 0){
        		$response = $sql->fetchall(\PDO::FETCH_ASSOC); 
        	}
        	return $response; 

        }
        public function getPhotos($userId,$offset,$itemsPerPage)
        {
        	$response = array();
        	$ioPhotoLikes = new Photo_likes(); 
        	$ioPhotoComments = new Photo_comments();
            $sql = "SELECT p.*, u.name, u.avatar
                    FROM photos p
                    LEFT JOIN users u ON(u.id = p.id_user)  
                    WHERE id_user  = :id_user
                    ORDER BY id DESC
                    LIMIT ".$offset.",".$itemsPerPage;               
    		try{
    			 $sql = $this->db->prepare($sql); 
    			 $sql->bindValue(":id_user",$userId); 
				 $sql->execute();
			}catch(Exception $e){
				$response['error'] = $e->getMessage(); 
			}
			 
			if ($sql->rowCount() > 0){
				$response = $sql->fetchall(\PDO::FETCH_ASSOC);
				foreach($response as $key => $value){
					$response[$key]['url'] =  BASE_URL."media/images/".$response[$key]['url'];
					 $response[$key]['likes'] = $ioPhotoLikes->countLikes($value['id']);
					 $response[$key]['avatar'] = BASE_URL."media/images/".$response[$key]['avatar']; 
					 $response[$key]['comments'] = $ioPhotoComments->getComments($value['id']); 
				} 
			}
        	return $response; 
        }


        public function get($photoId)
        {
        	$response = array();
        	$ioPhotoLikes = new Photo_likes(); 
        	$ioPhotoComments = new Photo_comments();
            $sql = "SELECT p.*, u.name, u.avatar
                    FROM photos p
                    LEFT JOIN users u ON(u.id = p.id_user)  
                    WHERE p.id  = :id
                    AND (p.id_active = UPPER('S') 
			        OR p.id_active IS NULL)";         
    		try{
    			 $sql = $this->db->prepare($sql); 
    			 $sql->bindValue(":id",$photoId); 
				 $sql->execute();
			}catch(Exception $e){
				$response['error'] = $e->getMessage(); 
			}
			 
			if ($sql->rowCount() > 0){
				$response = $sql->fetch(\PDO::FETCH_ASSOC);
			    $response['url'] =  BASE_URL."media/images/".$response['url'];
				 $response['likes'] = $ioPhotoLikes->countLikes($response['id']);
				 $response['avatar'] = BASE_URL."media/images/".$response['avatar']; 
				 $response['comments'] = $ioPhotoComments->getComments($response['id']); 			 
			}else{
                $response['error'] = "Photo not existe or was deleted"; 
            }
           	return $response; 
        }

        public function delete($photoId,$usrId)
        {
        	$this->db->beginTransaction(); 
            $response = array(); 
        	$sql = "SELECT id FROM photos 
        	        WHERE id = :id
                    AND id_user= :id_user
                    AND (id_active = UPPER('S') 
                    OR  id_active IS NULL)"; 
        	$sql = $this->db->prepare($sql); 
        	$sql->bindValue(":id",$photoId); 
        	$sql->bindValue(":id_user",$usrId); 
        	$sql->execute(); 
        	if ($sql->rowCount() > 0){
        		$sql =  "UPDATE photos
                        SET id_active ='N'
        		         WHERE id = :id";      		
        		try{
                    $sql = $this->db->prepare($sql);
                    $sql->bindValue(":id",$photoId);
        			$sql->execute();
                }catch(Exception $e){
                    $response['error'] = $e->getMessage(); 
                }

            } else {
                $response['error'] =  "You are not allowed to delete this photo or it inexist";
            }  
            if (!isset($response['error'])){
                $sql = "UPDATE photo_likes 
                        SET id_active = 'N'  
                        WHERE id_photo = :id_photo";
                 try{
                     $sql = $this->db->prepare($sql);
                     $sql->bindValue(":id_photo",$photoId1);
                      $sql->execute();
                    }catch(Exception $e){
                        $response['error'] = $e->getMessage();
                    }                
            }

            if (!isset($response['error'])){
                    $sql = "UPDATE photo_comments 
                             SET id_active = 'N'
                             WHERE id_photo = :id_photo";
                    try{
                        $sql = $this->db->prepare($sql);
                         $sql->bindValue(":id_photo",$photoId);
                         $sql->execute();
                    }catch(Exception $e){
                        $response['error'] = $e->getMessage();
                    }             
            }
   
            if (isset($response['error']) && !empty($response['error'])){
                $this->db->rollback(); 
            }else{
                $this->db->commit(); 
            }
        	return $response; 
        }
	}