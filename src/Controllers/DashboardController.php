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
        $latestSales = $salesModel->getLatestSales();
        $productModel = new Product();
        $recentProducts = $productModel->getRecentlyAddedProducts();
        $top10Products = $salesModel->getTop10ProductsBySales();

        $data = [
            'latest_sales' => $latestSales,
            'recent_products' => $recentProducts,
            'top_10_products' => json_encode($top10Products),
        ];

        unset($_SESSION['msg'], $_SESSION['msg_type']);
        echo $this->renderPage('dashboard', $data);
    }

}
