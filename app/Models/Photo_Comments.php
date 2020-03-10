<?php 
    namespace app\Models; 
    use lib\Core\Model; 
	class Photo_Comments extends Model{
		public function getComments($photoId){
			$response = 0;
			$sql = "SELECT pc.id_photo, 
			               pc.id_user,
			               pc.comment,
			               u.name 
			        FROM photo_comments pc 
			        LEFT JOIN users u
			        ON (u.id = pc.id_user)
			        WHERE id_photo =:id_photo"; 
			$sql = $this->db->prepare($sql); 
			$sql->bindValue(":id_photo",$photoId); 
			$sql->execute(); 
			if ($sql->rowCount() > 0){
				$response = $sql->fetchall(\PDO::FETCH_ASSOC); 
			}
			return $response; 
		}		
	}
