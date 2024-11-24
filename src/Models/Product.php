<?php

namespace App\Models;

use \PDO;
use App\Models\BaseModel;
use App\Models\Media;


class Product extends BaseModel
{

    protected $categoryModel;
    protected $mediaModel;

    public function __construct()
    {
        parent::__construct();
        $this->categoryModel = new Category();
        $this->mediaModel = new Media();
    }

    public function save($name, $quantity, $buy_price, $sale_price, $categorie_id, $media_id = 0)
    {
        if (empty($name) || empty($quantity) || empty($buy_price) || empty($sale_price) || empty($categorie_id)) {
            return false;  // Return false if validation fails
        }

        $sql = "INSERT INTO products (name, quantity, buy_price, sale_price, categorie_id, media_id, date) 
                VALUES (:name, :quantity, :buy_price, :sale_price, :categorie_id, :media_id, NOW())";

        $statement = $this->db->prepare($sql);

        try {
            $this->bindAndExecute($statement, [
                ':name' => $name,
                ':quantity' => $quantity,
                ':buy_price' => $buy_price,
                ':sale_price' => $sale_price,
                ':categorie_id' => $categorie_id,
                ':media_id' => $media_id,
            ]);
        } catch (Exception $e) {
            // Log or print error for debugging
            echo "Error: " . $e->getMessage();
            return false;
        }

        return $statement->rowCount();
    }


    // Get all products
    public function getAllProducts()
    {
        $sql = "
            SELECT
                p.id AS product_id,
                p.name AS product_name,
                p.quantity,
                p.buy_price,
                p.sale_price,
                p.date AS product_date,
                c.name AS category_name,
                m.file_name AS media_file_name
            FROM
                products p
            JOIN
                categories c ON p.categorie_id = c.id
            LEFT JOIN
                media m ON p.media_id = m.id
            ORDER BY
                p.id;
        ";
        
        return $this->fetchAll($sql);
    }

    // Get a single product by ID
    public function getProductById($id) 
    {
        {
            $sql = "SELECT 
                        p.id, 
                        p.name, 
                        p.quantity, 
                        p.buy_price, 
                        p.sale_price, 
                        p.date, 
                        c.id AS category_id, 
                        c.name AS category, 
                        m.id AS media_id,
                        m.file_name AS media_file_name
                    FROM products p
                    LEFT JOIN categories c ON p.categorie_id = c.id
                    LEFT JOIN media m ON p.media_id = m.id
                    WHERE p.id = :id";
            
            $statement = $this->db->prepare($sql);
            $statement->execute(['id' => $id]);
            
            $product = $statement->fetch(PDO::FETCH_ASSOC);
    
            // Fetch categories and mark the selected category
            $categories = $this->categoryModel->getAllCategories();
            foreach ($categories as &$category) {
                $category['is_selected'] = ($category['id'] == $product['category_id']);
            }
    
            // Fetch media files and mark the selected media
            $media_files = $this->mediaModel->getAllMediaFiles();
            foreach ($media_files as &$media) {
                $media['is_selected'] = ($media['file_name'] == $product['media_file_name']);
            }
    
            // Attach the categories and media files to the product
            $product['categories'] = $categories;
            $product['media_files'] = $media_files;
    
            return $product;
        }
    }

    public function getCategories()
    {
        $sql = "SELECT id, name FROM categories";  
        return $this->fetchAll($sql);
    }

    public function getMediaFiles() {
        $sql = "SELECT id, file_name FROM media";  
        return $this->fetchAll($sql);
    }

    // Update product information
    public function update($id, $name, $quantity, $buy_price, $sale_price, $category_id, $media_id = 0)
    {
        $sql = "UPDATE products 
        SET name = :name, 
            quantity = :quantity, 
            buy_price = :buy_price, 
            sale_price = :sale_price, 
            categorie_id = :category_id,  -- Correct column name here
            media_id = :media_id 
        WHERE id = :id";

        // Prepare the statement
        $statement = $this->db->prepare($sql);

        // Bind values
        $statement->bindValue(':name', $name);
        $statement->bindValue(':quantity', $quantity);
        $statement->bindValue(':buy_price', $buy_price);
        $statement->bindValue(':sale_price', $sale_price);
        $statement->bindValue(':category_id', $category_id);  // Updated to match the correct column name
        $statement->bindValue(':media_id', $media_id);
        $statement->bindValue(':id', $id);

        // Execute the query
        $statement->execute();

        // Return the number of affected rows
        return $statement->rowCount();
    }

    // Delete a product by ID
    public function delete($id)
    {
        $sql = "DELETE FROM products WHERE id = :id";
        $statement = $this->db->prepare($sql);
        $statement->bindValue(':id', $id);
        $statement->execute();
        
        return $statement->rowCount();
    }

    // Private method to fetch all data
    private function fetchAll($query, $class = null)
    {
        $statement = $this->db->prepare($query);
        $statement->execute();
        
        return $class ? $statement->fetchAll(PDO::FETCH_CLASS, $class) : $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // Private method to bind and execute the query
    private function bindAndExecute($statement, $parameters)
    {
        foreach ($parameters as $key => $value) {
            $statement->bindValue($key, $value);
        }
        
        try {
            $statement->execute();
        } catch (\PDOException $e) {
            throw new \Exception("Error executing statement: " . $e->getMessage());
        }
    }


    // update product nhet

        public function updates($id, $product_name, $category_id, $quantity, $buy_price, $sale_price, $media_file_name)
    {
        $sql = "UPDATE products SET 
                    product_name = :product_name, 
                    category_id = :category_id, 
                    quantity = :quantity, 
                    buy_price = :buy_price, 
                    sale_price = :sale_price, 
                    media_file_name = :media_file_name
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':product_name', $product_name);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':buy_price', $buy_price);
        $stmt->bindParam(':sale_price', $sale_price);
        $stmt->bindParam(':media_file_name', $media_file_name);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    public function updateProductName($product_id, $product_name)
    {
        $sql = "UPDATE products SET name = :name WHERE id = :product_id";
        
        $statement = $this->db->prepare($sql);
        $statement->execute([
            ':name' => $product_name,
            ':product_id' => $product_id
        ]);

        return $statement->rowCount() > 0; // Returns true if the update was successful
    }
}
