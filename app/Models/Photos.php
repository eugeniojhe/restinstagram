<?php 
    namespace app\Models; 
    use lib\Core\Model; 
	class Photos extends Model{
		public function getCountPhotos($usrId)
		{
			$response = 0; 
			$sql = "SELECT count(id) as c
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
        		$sql = "SELECT p.id,p.id_user, p.url, u.id, u.name, pc.id_user AS user_comment, pc.comment
        				FROM photos p  
						LEFT JOIN USERS  u ON (u.id = p.id_user)
						LEFT JOIN photo_comments pc ON(p.id = pc.id_photo)
						WHERE p.id_user IN(".implode(',',$usersFollowingIds).")
						ORDER BY p.id DESC
						LIMIT ".$offset." ,".$itemsPerPage;
                        
        		try{
					 $sql = $this->db->query($sql);
				}catch(Exception $e){
					return $e->getMessage(); 
				}
				 
				if ($sql->rowCount() > 0){
					$response = $sql->fetchall(\PDO::FETCH_ASSOC);
					foreach($response as $key => $value){
						$response[$key]['url'] =  BASE_URL."media/images/".$response[$key]['url'];
						 // $response[$key]['likes'] = $ioPhotoLikes->countLikes($value['id']);
						 // $response[$key]['comments'] = $ioPhotoComments->getComments($value['id']); 
					} 

				}
        	}
        	print_r($response); 
        	//return $response; 
        }

	}