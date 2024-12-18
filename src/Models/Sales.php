<?php

namespace App\Models;

use \PDO;
use App\Models\BaseModel;

class Sales extends BaseModel
{

    public function getTop10ProductsBySales() {
        $query = "
            SELECT products.name AS product_name, 
                   SUM(sales.qty * sales.total_sales) AS total_sales
            FROM sales
            JOIN products ON sales.product_id = products.id
            GROUP BY products.id
            ORDER BY total_sales DESC
            LIMIT 10
        ";
        $result = $this->db->query($query);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveSale($product_id, $quantity, $total, $sale_date)
    {
        $query = "INSERT INTO sales (product_id, quantity, total, sale_date) 
                VALUES (:product_id, :quantity, :total, :sale_date)";

        $params = [
            ':product_id' => $product_id,
            ':quantity' => $quantity,
            ':total' => $total,
            ':sale_date' => $sale_date,
        ];
        return $this->execute($query, $params);
    }

    public function getSalesByDateRange($startDate, $endDate)
    {
        if ($startDate && $endDate) {
            $sql = "
                SELECT
                    s.id AS sale_id,
                    p.name AS product_name,
                    p.buy_price,
                    p.sale_price,
                    s.qty AS quantity_sold,
                    (s.qty * p.sale_price) AS total,
                    (s.qty * p.buy_price) AS cost_of_goods_sold,
                    s.date AS sale_date
                FROM
                    sales s
                JOIN
                    products p ON s.product_id = p.id
                WHERE
                    s.date BETWEEN :start_date AND :end_date
                ORDER BY
                    s.date DESC;
            ";
    
            $statement = $this->db->prepare($sql);
            $statement->bindValue(':start_date', $startDate);
            $statement->bindValue(':end_date', $endDate);
            $statement->execute();
            $salesData = $statement->fetchAll(PDO::FETCH_ASSOC);

            $grandTotal = 0;
            $profit = 0;
    
            foreach ($salesData as $sale) {
                $grandTotal += $sale['total'];
                $profit += ($sale['total'] - $sale['cost_of_goods_sold']);
            }

            return [
                'sales' => $salesData,
                'grand_total' => $grandTotal,
                'profit' => $profit
            ];
        }
        return [];
    }

    public function getAllSales()
    {
            $query = "
            SELECT
                s.id AS sale_id,
                p.name AS product_name,
                s.qty AS quantity,
                (s.qty * p.sale_price) AS total,
                s.date AS sale_date
            FROM
                sales s
            JOIN
                products p ON s.product_id = p.id
            ORDER BY
                s.date DESC;
        ";
        
        $sales = $this->fetchAll($query);
        foreach ($sales as $key => &$sale) {
            $sale['sequence'] = $key + 1; 
        }
        return $sales;
    }

    public function getMonthlySales()
    {
        $sql = "
            SELECT
                s.id AS sale_id,
                p.name AS product_name,
                s.qty AS quantity_sold,
                (s.qty * p.sale_price) AS total,  
                s.date AS sale_date
            FROM
                sales s
            JOIN
                products p ON s.product_id = p.id
            WHERE
                MONTH(s.date) = MONTH(CURRENT_DATE())  
            ORDER BY
                s.date DESC;
        ";
        return $this->fetchAll($sql);
    }

    public function getDailySales()
    {
        $sql = "
            SELECT
                s.id AS sale_id,
                p.name AS product_name,
                s.qty AS quantity_sold,
                (s.qty * p.sale_price) AS total,  
                s.date AS sale_date
            FROM
                sales s
            JOIN
                products p ON s.product_id = p.id
            WHERE
                DATE(s.date) = CURDATE() 
            ORDER BY
                s.date DESC;
        ";
        return $this->fetchAll($sql);
    }

    public function getSaleById($id)
    {
        $sql = "SELECT * FROM sales WHERE id = :id";
        $statement = $this->db->prepare($sql);
        $statement->bindValue(':id', $id);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function updateSale($id, $product_id, $qty, $total_sales, $date)
    {
        $sql = "UPDATE sales 
                SET product_id = :product_id, qty = :qty, total_sales = :total_sales, date = :date 
                WHERE id = :id";

        $statement = $this->db->prepare($sql);
        $this->bindAndExecute($statement, [
            ':id' => $id,
            ':product_id' => $product_id,
            ':qty' => $qty,
            ':total_sales' => $total_sales,
            ':date' => $date,
        ]);
        return $statement->rowCount();
    }

    public function deleteSale($id)
    {
        $sql = "DELETE FROM sales WHERE id = :id";
        $statement = $this->db->prepare($sql);
        $statement->bindValue(':id', $id);
        $statement->execute();
        return $statement->rowCount();
    }

    public function addSale($productId, $quantity, $total_sales, $saleDate)
    {
        $sql = "INSERT INTO sales (product_id, qty, total_sales, date) 
                VALUES (:product_id, :quantity, :total_sales, :sale_date)";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':product_id' => $productId,
            ':quantity' => $quantity,
            ':total_sales' => $total_sales,
            ':sale_date' => $saleDate
        ]);
    }

    public function getHighestSellingProducts()
    {
        $sql = "
            SELECT 
                p.name AS product_name,
                SUM(s.qty) AS total_quantity,
                COUNT(s.product_id) AS total_sales
            FROM
                sales s
            JOIN
                products p ON s.product_id = p.id
            GROUP BY
                p.name
            ORDER BY
                total_quantity DESC
            LIMIT 10; 
        ";
        return $this->fetchAll($sql);
    }

    public function getLatestSales()
    {
        $sql = "
            SELECT 
                s.id AS sale_id,
                p.name AS product_name,
                s.date AS date,
                (s.qty * s.total_sales) AS total_sale
            FROM
                sales s
            JOIN
                products p ON s.product_id = p.id
            ORDER BY
                s.date DESC
            LIMIT 10;
        ";
        $sales = $this->fetchAll($sql);

        foreach ($sales as $key => &$sale) {
            $sale['sequence'] = $key + 1; 
        }
        return $sales;
    }

    private function fetchAll($query, $class = null)
    {
        $statement = $this->db->prepare($query);
        $statement->execute();
        return $class ? $statement->fetchAll(PDO::FETCH_CLASS, $class) : $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    private function bindAndExecute($statement, $parameters)
    {
        foreach ($parameters as $key => $value) {
            $statement->bindValue($key, $value);
        }
    
        try {
            $statement->execute();
        } catch (\PDOException $e) {
            error_log("Error executing statement: " . $e->getMessage()); 
            throw new \Exception("Error executing statement: " . $e->getMessage());
        }
    }
}
