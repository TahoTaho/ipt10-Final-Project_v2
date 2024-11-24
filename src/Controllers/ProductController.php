<?php
namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;

class ProductController extends BaseController
{
    private $productModel;

    public function __construct()
    {
        $this->startSession(); // Ensures session is started
        $this->productModel = new Product(); // Corrected to instantiate Product model
    }

    // Manage products
    public function manageProducts()
    {
        // Fetch all products using the getAllProducts method
        $products = $this->productModel->getAllProducts();

        // Fetch session messages if available
        $msg = isset($_SESSION['msg']) ? $_SESSION['msg'] : null;
        $msg_type = isset($_SESSION['msg_type']) ? $_SESSION['msg_type'] : null;

        // Clear session messages after fetching
        unset($_SESSION['msg'], $_SESSION['msg_type']);

        // Prepare data for rendering
        $data = [
            'products' => $products,
            'msg' => $msg,
            'msg_type' => $msg_type,
        ];

        // Render the page with the data
        echo $this->renderPage('managed-products', $data);
    }

    // Show Add New Product form
    public function showAddNewProducts()
    {
        // Fetch categories and all products
        $categories = $this->productModel->getCategories();
        $products = $this->productModel->getAllProducts();

        // Prepare data for rendering
        $data = [
            'products' => $products,
            'categories' => $categories,
        ];

        // Render the Add Product page
        echo $this->renderPage('add-product', $data);
    }

    // Add a new product
    public function addProduct()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_name'])) {
            // Extract POST data
            $product_name = $_POST['product_name'];
            $category_id = $_POST['category_id'];
            $quantity = $_POST['quantity'];
            $buy_price = $_POST['buy_price'];
            $sale_price = $_POST['sale_price'];
            $media_file_name = isset($_POST['media_file_name']) ? $_POST['media_file_name'] : null;

            if (empty($product_name) || empty($category_id) || empty($quantity) || empty($buy_price) || empty($sale_price)) {
                $_SESSION['msg'] = 'All fields are required.';
                $_SESSION['msg_type'] = 'danger'; // Set message type to error
            } else {
                // Call the model to save the product
                $result = $this->productModel->save($product_name, $quantity, $buy_price, $sale_price, $category_id, $media_file_name);

                if ($result > 0) {
                    $_SESSION['msg'] = 'Product added successfully.';
                    $_SESSION['msg_type'] = 'success'; // Set message type to success
                } else {
                    $_SESSION['msg'] = 'Failed to add product.';
                    $_SESSION['msg_type'] = 'danger'; // Set message type to error
                }
            }
            $this->redirect('/managed-products');
        }
    }

    // Edit a product
    public function editProduct()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($id <= 0) {
        $_SESSION['msg'] = 'Invalid product ID.';
        $_SESSION['msg_type'] = 'danger'; // Set message type to error
        $this->redirect('/managed-products');
        return;
    }

    // Fetch product details and categories from the model
    $product = $this->productModel->getProductById($id);
    $categories = $this->productModel->getCategories();
    $media_files = $this->productModel->getMediaFiles();

    // Pre-select the category and media file based on the current product's data
    foreach ($categories as &$category) {
        $category['is_selected'] = ($category['id'] == $product['category_id']) ? true : false;
    }

    foreach ($media_files as &$media_file) {
        $media_file['is_selected'] = ($media_file['file_name'] == $product['media_file_name']) ? true : false;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_name'])) {
        
        $product_name = $_POST['product_name'];
        $category_id = $_POST['category_id'];
        $quantity = $_POST['quantity'];
        $buy_price = $_POST['buy_price'];
        $sale_price = $_POST['sale_price'];
        $media_file_name = isset($_POST['media_file_name']) ? $_POST['media_file_name'] : null;
        echo "<script>console.log(" . json_encode($_POST) . ");</script>";

        if (empty($product_name) || empty($category_id) || empty($quantity) || empty($buy_price) || empty($sale_price)) {
            $_SESSION['msg'] = 'All fields are required.';
            $_SESSION['msg_type'] = 'danger'; // Set message type to error
            $this->redirect('/edit-product?id=' . $id);
            return;
        }

        $result = $this->productModel->update($id, $product_name, $category_id, $quantity, $buy_price, $sale_price, $media_file_name);
        if ($result > 0) {
            $_SESSION['msg'] = 'Product updated successfully.';
            $_SESSION['msg_type'] = 'success'; // Set message type to success
        } else {
            $_SESSION['msg'] = 'Failed to update product.';
            $_SESSION['msg_type'] = 'danger'; // Set message type to error
        }

        // Redirect to the manage products page
        $this->redirect('/managed-products');
        return;
    }

    // Prepare data for the view
    $data = [
        'product' => $product,
        'categories' => $categories,
        'media_files' => $media_files,
        'product_id' => $product['id']
    ];

    // Render the edit product page with the data
    echo $this->renderPage('edit-product', $data);
    }

    // Delete a product
    public function deleteProduct()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            $_SESSION['msg'] = 'Invalid product ID.';
            $_SESSION['msg_type'] = 'danger'; // Set message type to error
            $this->redirect('/managed-products');
            return;
        }

        $result = $this->productModel->delete($id);

        if ($result > 0) {
            $_SESSION['msg'] = 'Product Deleted Successfully.';
            $_SESSION['msg_type'] = 'success'; // Set message type to success
        } else {
            $_SESSION['msg'] = 'Failed to delete product.';
            $_SESSION['msg_type'] = 'danger'; // Set message type to error
        }

        $this->redirect('/managed-products');
    }
}
