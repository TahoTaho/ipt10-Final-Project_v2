<?php

namespace App\Models;

use \PDO;
use App\Models\BaseModel;

class User extends BaseModel
{
    public function getPassword($username) {
        $sql = "SELECT password_hash FROM users WHERE username = :username";
        $statement = $this->db->prepare($sql);
        $statement->execute(['username' => $username]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result['password_hash'] ?? null;
    }

    public function updateLastLogin($username) {
        $query = "UPDATE users SET last_login = NOW() WHERE username = :username";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
    }
}