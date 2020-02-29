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
	}