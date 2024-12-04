<?php

namespace App\Models;

use \PDO;
use App\Models\BaseModel;

class Category extends BaseModel
{
    public function save($category_name) {
        $sql = "INSERT INTO categories (name) VALUES (:name)";
        $statement = $this->db->prepare($sql);
        $this->bindAndExecute($statement, [
            ':name' => $category_name,
        ]);
        return $statement->rowCount();
    }

    public function getAllCategories()
    {
        $query = "SELECT id, name FROM categories ORDER BY id ASC";
        $categories = $this->fetchAll($query);
        foreach ($categories as $key => &$category) {
            $category['sequence'] = $key + 1;
        }
        
        return $categories;
    }

    public function getCategoryData() {
        $sql = "SELECT * FROM categories";
        return $this->fetchAll($sql, '\App\Models\Category');
    }

    private function fetchAll($query, $class = null) {
        $statement = $this->db->prepare($query);
        $statement->execute();
        
        return $class ? $statement->fetchAll(PDO::FETCH_CLASS, $class) : $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    private function bindAndExecute($statement, $parameters) {
        foreach ($parameters as $key => $value) {
            $statement->bindValue($key, $value);
        }

        try {
            $statement->execute();
        } catch (\PDOException $e) {
            throw new \Exception("Error executing statement: " . $e->getMessage());
        }
    }

    public function getCategoryById($id)
    {
    $sql = "SELECT id, name FROM categories WHERE id = :id";
    $statement = $this->db->prepare($sql);
    $statement->bindValue(':id', $id, PDO::PARAM_INT); 
    $statement->execute();
    $category = $statement->fetch(PDO::FETCH_ASSOC);
    return $category;
    }


    public function update($id, $category_name)
    {
        $sql = "UPDATE categories SET name = :name WHERE id = :id";
        $statement = $this->db->prepare($sql);
        $statement->bindValue(':name', $category_name);
        $statement->bindValue(':id', $id);
        $statement->execute();
        return $statement->rowCount();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM categories WHERE id = :id";
        $statement = $this->db->prepare($sql);
        $statement->bindValue(':id', $id);
        $statement->execute();
        
        return $statement->rowCount();
    }

    public function getCategories()
    {
            $sql = "SELECT id, name FROM categories";
            return $this->fetchAll($sql);
    }
}
