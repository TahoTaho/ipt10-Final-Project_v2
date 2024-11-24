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

    // Get all media files
    public function getMediaFiles()
    {
        $sql = "SELECT id, file_name FROM media";
        $statement = $this->db->prepare($sql);
        $statement->execute();
        
        return $statement->fetchAll(PDO::FETCH_ASSOC); // Corrected to return all media
    }

    // Fetch all media files using the fetchAll method in BaseModel
    public function getAllMediaFiles()
    {
        $sql = "SELECT id, file_name FROM media";  
        return $this->fetchAll($sql);  // Now it will use the fetchAll method in BaseModel
    }
    
    protected function fetchAll($sql)
    {
        $statement = $this->db->prepare($sql);
        $statement->execute();
        
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
