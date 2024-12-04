<?php
namespace App\Controllers;

use App\Models\Sales;
use App\Models\Product;

class DashboardController extends BaseController
{
    public function __construct()
    {
        $this->startSession(); 
    }

    public function showDashboard()
    {
        $salesModel = new Sales();
        $highestSellingProducts = $salesModel->getHighestSellingProducts();
        $latestSales = $salesModel->getLatestSales();
        $productModel = new Product();
        $recentProducts = $productModel->getRecentlyAddedProducts();

        $data = [
            'highest_selling_products' => $highestSellingProducts,
            'latest_sales' => $latestSales,
            'recent_products' => $recentProducts
        ];
        unset($_SESSION['msg'], $_SESSION['msg_type']);
        echo $this->renderPage('dashboard', $data);
    }

}
