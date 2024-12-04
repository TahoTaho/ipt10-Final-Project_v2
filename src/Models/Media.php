<?php

namespace App\Models;

use \PDO;
use App\Models\BaseModel;

class Media extends BaseModel
{
    public function getMediaById($mediaId)
    {
        $sql = "SELECT file_name FROM media WHERE id = :id";
        $statement = $this->db->prepare($sql);
        $statement->execute(['id' => $mediaId]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function addMedia($fileName, $fileType)
    {
        $sql = "INSERT INTO media (file_name, file_type) VALUES (:file_name, :file_type)";
        $statement = $this->db->prepare($sql);
        $statement->execute([
            'file_name' => $fileName,
            'file_type' => $fileType,
        ]);
    }

    public function deleteMedia($mediaId)
    {
        $sql = "DELETE FROM media WHERE id = :id";
        $statement = $this->db->prepare($sql);
        $statement->execute(['id' => $mediaId]);
    }

    public function getMediaFiles()
    {
        $sql = "SELECT id, file_name, file_type FROM media";
        $statement = $this->db->prepare($sql);
        $statement->execute();
        
        return $statement->fetchAll(PDO::FETCH_ASSOC); 
    }

    public function getAllMediaFiles()
    {
        $sql = "SELECT id, file_name, FROM media";  
        return $this->fetchAll($sql);
    }
    
    protected function fetchAll($sql)
    {
        $statement = $this->db->prepare($sql);
        $statement->execute();
        
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
