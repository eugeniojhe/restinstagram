<?php 
    namespace app\Models; 
    use lib\Core\Model; 
	class Photo_Comments extends Model{
		public function getComments($photoId){
			$response = 0;
			$sql = "SELECT * photo_comments 
			        WHERE photo_comments id_photo =:id_photo"; 
			$sql = $this->db->prepare($sql); 
			$sql->bindValue(":id_photo",$photoId); 
			$sql->execute(); 
			if ($sql->rowCount() > 0){
				$response = $sql->fetchall(); 
			}
			return $response; 
		}		
	}
