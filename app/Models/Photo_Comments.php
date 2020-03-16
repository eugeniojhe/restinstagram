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

		public function store($photoId,$userId,$comment)
		{
			$response = ''; 
			$sql = "INSERT INTO photo_comments 
			        (id_photo, id_user, comment,dt_comment, id_active) 
			        VALUES(:id_photo, :id_user, :comment, NOW(), :id_active)"; 
			try{
				$sql = $this->db->prepare($sql); 
				$sql->bindValue(":id_photo",$photoId);
				$sql->bindValue(":id_user",$userId);
				$sql->bindValue(":comment",$comment);
				$sql->bindValue(":id_active","S"); 
				$sql->execute(); 
			}catch(Exception $e){
				$response = $e->getMessage(); 
			}
			return $response; 
		}

		public function delete($commentId,$userId)
		{
			$response = ''; 
			$sql = "SELECT id FROM photo_comments 
			        WHERE id = :id 
			        AND id_user = :id_user"; 
			$sql = $this->db->prepare($sql); 
			$sql->bindValue(":id",$commentId);
			$sql->bindValue(":id_user",$userId); 
			$sql->execute();
			if ($sql->rowCount()> 0){
				$sql = "UPDATE photo_comments 
				    SET id_active = :id_active 
			        WHERE id = :id 
			        AND id_user = :id_user"; 
				$sql = $this->db->prepare($sql);
				$sql->bindValue(":id",$commentId);
				$sql->bindValue(":id_user",$userId);
				$sql->bindValue(":id_active","N");
				$sql->execute();
			}else{
				$response = "Comment does not existe or you are not to owner";
			}
			return $response; 
		}	
	}
