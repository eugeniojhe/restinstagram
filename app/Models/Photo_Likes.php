<?php 
    namespace app\Models; 
    use lib\Core\Model; 
	class Photo_Likes extends Model{
		public function countLikes($photoId){
			$response = 0;
			$sql = "SELECT count(id) as c 
			        WHERE photo_likes id = {$photoId}"; 
			$sql = $this->db->query($sql); 
			if ($sql->rowCount()>0){
				$c = $sql->fetch(); 
				$response = $c['c'];
			}
			return $response; 

		}
		
	}
