<?php
namespace App\Controllers;

use App\Models\Sales;
use FPDF;

class SalesReportController extends BaseController
{
    public function __construct()
    {
        $this->startSession(); 
    }

    public function showSalesByDate()
    {
        $message = isset($_SESSION['msg']) ? $_SESSION['msg'] : '';
        $msgType = isset($_SESSION['msg_type']) ? $_SESSION['msg_type'] : 'info'; 

        $data = [
            'title' => 'Sales by Date',
            'message' => $message,
            'msg_type' => $msgType,
        ];
        unset($_SESSION['msg']);
        unset($_SESSION['msg_type']);
        echo $this->renderPage('sales-by-date', $data);
    }

    public function showMonthlySales()
    {
        $salesModel = new Sales();
        $salesData = $salesModel->getMonthlySales();

        $data = [
            'title' => 'Monthly Sales',
            'sales' => $salesData,        
        ];
        echo $this->renderPage('sales', $data);
    }

    public function showDailySales()
    {
        $salesModel = new Sales();
        $salesData = $salesModel->getDailySales();

        $data = [
            'title' => 'Daily Sales',
            'sales' => $salesData,        
        ];
        echo $this->renderPage('sales', $data);
    }

    public function showSalesByDateRange()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $startDate = $_POST['start-date'] ?? null;
            $endDate = $_POST['end-date'] ?? null;
    
            if (empty($startDate) || empty($endDate)) {
                $_SESSION['msg'] = 'Please select a valid date range.';
                $_SESSION['msg_type'] = 'danger';
                $this->redirect('/sales-by-date');
                exit;
            }
            $salesModel = new Sales();
            $salesData = $salesModel->getSalesByDateRange($startDate, $endDate);
            $grandTotal = number_format($salesData['grand_total'], 2, '.', ',');
            $profit = number_format($salesData['profit'], 2, '.', ',');

        } else {
            $salesData = ['sales' => [], 'grand_total' => 0, 'profit' => 0];
            $grandTotal = '0.00';
            $profit = '0.00';
            $startDate = null;
            $endDate = null;
        }
    
        $message = $_SESSION['msg'] ?? '';
        $msg_type = $_SESSION['msg_type'] ?? 'info';
    
        $data = [
            'title' => 'Sales Report',
            'sales' => $salesData['sales'],
            'grand_total' => $grandTotal,
            'profit' => $profit,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'message' => $message,
            'msg_type' => $msg_type,
        ];
        echo $this->renderPage('sales-by-date', $data);
    }

    public function exportSalesToPDF()
    {
        $startDate = $_GET['start-date'] ?? null;
        $endDate = $_GET['end-date'] ?? null;

        if (empty($startDate) || empty($endDate)) {
            $_SESSION['msg'] = 'You must specify a start date and a end date before exporting';
            $_SESSION['msg_type'] = 'danger';
            
            $this->redirect('/sales-by-date');  
            exit;
        }

        $salesModel = new Sales();
        $salesData = $salesModel->getSalesByDateRange($startDate, $endDate);

        $grandTotal = number_format($salesData['grand_total'], 2, '.', ',');
        $profit = number_format($salesData['profit'], 2, '.', ',');

        $pdf = new FPDF();
        $pdf->AddPage();

        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(190, 10, 'Sales Report', 0, 1, 'C');

        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(190, 10, 'Date Range: ' . $startDate . ' to ' . $endDate, 0, 1, 'C');
        $pdf->Ln(10);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(30, 10, 'Date', 1);
        $pdf->Cell(40, 10, 'Product Title', 1);
        $pdf->Cell(30, 10, 'Buying Price', 1);
        $pdf->Cell(30, 10, 'Selling Price', 1);
        $pdf->Cell(30, 10, 'Quantity Sold', 1);
        $pdf->Cell(30, 10, 'Total', 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 12);
        foreach ($salesData['sales'] as $sale) {
            $pdf->Cell(30, 10, $sale['sale_date'], 1);
            $pdf->Cell(40, 10, $sale['product_name'], 1);
            $pdf->Cell(30, 10, number_format($sale['buy_price'], 2), 1);
            $pdf->Cell(30, 10, number_format($sale['sale_price'], 2), 1);
            $pdf->Cell(30, 10, $sale['quantity_sold'], 1);
            $pdf->Cell(30, 10, number_format($sale['total'], 2), 1);
            $pdf->Ln();
        }
        $pdf->Cell(160, 10, 'Grand Total', 1);
        $pdf->Cell(30, 10, $grandTotal, 1, 1, 'R');
        
        $pdf->Cell(160, 10, 'Profit', 1);
        $pdf->Cell(30, 10, $profit, 1, 1, 'R');

        $pdf->Output('D', 'sales_report_' . $startDate . '_to_' . $endDate . '.pdf'); 
    }
}
