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
        public getFeedPhotos($usersFollowindIds,$offset,$itemsPerPage)
        {
        	$response = array();
        	$ioPhotoLikes = new Photo_like(); 
        	$ioPhotoComments = new Photo_comments(); 
        	if (count($usersFollowindIds) > 0 ){
        		$sql = "SELECT p.*, u.*
        				FROM photos p  
						LEFT JOIN USERS  u ON (u.id = p.id_user)
						LEFT JOIN photo_coments pl ON(u.id = pl.
						AND p.id_user IN(".implode(',',$usersFollowindIds).")
						ORDER BY DESC p.id 
						LIMIT ".$offset." ,".$itemsPerPage;
				$sql = $this->db->query($sql); 
				if ($sql->rowCount() > 0){
					$response = $sql->fetchall(\PDO::FETCH_ASSOC);
					foreach($response as $key => $value){
						$response[$key]['url'] =  BASE_URL."	media/images/".$response[$key]['url']; 
						 $response[$key]['likes'] = $ioPhotoLikes->countLikes($value['id']);
						 $response[$key]['comments'] = $ioPhotoComments->getComments($value['id']); 
					} 

				}
        	}

        	return $response;. 
        }

	}