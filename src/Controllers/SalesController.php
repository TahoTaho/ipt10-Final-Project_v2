<?php
namespace App\Controllers;

use App\Models\Sales;
use App\Models\Product;

class SalesController extends BaseController
{
    public function __construct()
    {
        $this->startSession();
    }

    public function showAddSales()
    {
        $productModel = new Product();
        $products = $productModel->getAllProducts();
        $data = [
            'title' => 'Add Sales', 
            'products' => $products,
        ];
        echo $this->renderPage('add-sales', $data);
    }

    public function showManageSales()
    {
        $salesModel = new Sales();
        $salesData = $salesModel->getAllSales();
        $data = [
            'sales' => $salesData,
            'message' => $_SESSION['msg'] ?? null,
            'msg_type' => $_SESSION['msg_type'] ?? null
        ];
        unset($_SESSION['msg'], $_SESSION['msg_type']);
        echo $this->renderPage('manage-sales', $data);
    }

    public function editSale()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            $_SESSION['msg'] = 'Invalid sale ID.';
            $_SESSION['msg_type'] = 'danger';
            $this->redirect('/manage-sales');
        }

        $salesModel = new Sales();
        $sale = $salesModel->getSaleById($id);
        if (!$sale) {
            $_SESSION['msg'] = 'Sale not found.';
            $_SESSION['msg_type'] = 'danger';
            $this->redirect('/manage-sales');
        }

        $productModel = new Product();
        $product = $productModel->getProductById($sale['product_id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_name = $_POST['product_name'] ?? $product['name'];
            $quantity = (int)$_POST['quantity'];
            $price = (float)$_POST['price'];
            $sale_date = $_POST['sale_date'];
        
            $total = $quantity * $price;
        
            $result = $salesModel->updateSale($id, $product['id'], $quantity, $total, $sale_date);
        
            if ($result > 0) {
                $_SESSION['msg'] = 'Sale updated successfully.';
                $_SESSION['msg_type'] = 'success';
            } else {
                $_SESSION['msg'] = 'Failed to update sale.';
                $_SESSION['msg_type'] = 'danger';
            }
        
            $this->redirect('/manage-sales');
        }

        $data = [
            'title' => 'Edit Sale',
            'sale' => $sale,
            'product' => $product,
            'sale_id' => $sale['id']
        ];
        echo $this->renderPage('edit-sale', $data);
    }

    public function deleteSale() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            $_SESSION['msg'] = 'Invalid sale ID.';
            $_SESSION['msg_type'] = 'danger';
            $this->redirect('/manage-sales');
        }

        $salesModel = new Sales();
        $result = $salesModel->deleteSale($id);

        if ($result > 0) {
            $_SESSION['msg'] = 'Sale deleted successfully.';
            $_SESSION['msg_type'] = 'success'; 
        } else {
            $_SESSION['msg'] = 'Failed to delete sale.';
            $_SESSION['msg_type'] = 'danger';
        }

        $this->redirect('/manage-sales');
    }

    public function addSale()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['product_id']) && !empty($_POST['product_id'])) {
                foreach ($_POST['product_id'] as $productId) {
                    if (isset($_POST['quantity'][$productId], $_POST['price'][$productId], $_POST['sale_date'][$productId])) {
                        $quantity = $_POST['quantity'][$productId];
                        $price = $_POST['price'][$productId];
                        $saleDate = $_POST['sale_date'][$productId];

                        if (empty($quantity) || empty($price) || empty($saleDate)) {
                            $_SESSION['msg'] = 'Error: Missing or invalid fields.';
                            $_SESSION['msg_type'] = 'danger'; 
                        } else {
                            $saleModel = new Sales();
                            $result = $saleModel->addSale($productId, $quantity, $price, $saleDate);
    
                            if ($result) {
                                $_SESSION['msg'] = 'Sale added successfully for product ID ' . $productId;
                                $_SESSION['msg_type'] = 'success';
                            } else {
                                $_SESSION['msg'] = 'Failed to add sale for product ID ' . $productId;
                                $_SESSION['msg_type'] = 'danger';
                            }
                        }
                    } else {
                        $_SESSION['msg'] = 'Error: Missing required data for product ID ' . $productId;
                        $_SESSION['msg_type'] = 'danger';
                    }
                }
            } else {
                $_SESSION['msg'] = 'Error: Invalid or missing product IDs.';
                $_SESSION['msg_type'] = 'danger';
            }
            $this->redirect('/manage-sales');
        }
    }
}
