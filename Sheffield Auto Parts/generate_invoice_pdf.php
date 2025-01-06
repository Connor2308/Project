<?php
require('libs/fpdf/fpdf186/fpdf.php');
include('include/init.php'); 

// Get the order_id from the URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Fetch order details
$order_sql = "SELECT 
                orders.order_id,
                orders.user_id,
                orders.order_date,
                orders.order_time,
                orders.total_cost,
                orders.order_status,
                orders.recipient_name,
                users.username
              FROM 
                orders
              LEFT JOIN 
                users 
              ON 
                orders.user_id = users.user_id
              WHERE 
                orders.order_id = ?";
$order_stmt = $con->prepare($order_sql);
$order_stmt->bind_param('i', $order_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

if ($order_result->num_rows != 1) {
    die("Order not found.");
}

$order = $order_result->fetch_assoc();

// Fetch invoice details
$invoice_sql = "SELECT * FROM invoices WHERE order_id = ?";
$invoice_stmt = $con->prepare($invoice_sql); 
$invoice_stmt->bind_param('i', $order_id);
$invoice_stmt->execute();
$invoice_result = $invoice_stmt->get_result();

if ($invoice_result->num_rows != 1) {
    die("Invoice not found.");
}

$invoice = $invoice_result->fetch_assoc();

// Fetch order items
$items_sql = "SELECT 
                order_items.order_item_id,
                parts.part_name,
                order_items.order_quantity,
                order_items.order_price
              FROM 
                order_items
              LEFT JOIN 
                parts 
              ON 
                order_items.part_id = parts.part_id
              WHERE 
                order_items.order_id = ?";
$items_stmt = $con->prepare($items_sql);
$items_stmt->bind_param('i', $order_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();

// Create a new PDF document
$pdf = new FPDF();
$pdf->AddPage();

// Set font
$pdf->SetFont('Arial', 'B', 16);

// Add a title
$pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', 'Invoice'), 0, 1, 'C');

// Add order details
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', 'Order ID: ' . $order['order_id']), 0, 1);
$pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', 'User ID: ' . $order['user_id']), 0, 1);
$pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', 'Username: ' . $order['username']), 0, 1);
$pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', 'Order Date: ' . $order['order_date']), 0, 1);
$pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', 'Order Time: ' . $order['order_time']), 0, 1);
$pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', 'Order Status: ' . $order['order_status']), 0, 1);
$pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', 'Recipient Name: ' . $order['recipient_name']), 0, 1);
$pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', 'Total Cost: £' . number_format($order['total_cost'], 2, '.', ',')), 0, 1);

// Add invoice details
$pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', 'Total Paid: £' . number_format($invoice['total_paid'], 2, '.', ',')), 0, 1);

// Add a line break
$pdf->Ln(10);

// Add order items
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, iconv('UTF-8', 'ISO-8859-1', 'Item ID'), 1);
$pdf->Cell(80, 10, iconv('UTF-8', 'ISO-8859-1', 'Part Name'), 1);
$pdf->Cell(30, 10, iconv('UTF-8', 'ISO-8859-1', 'Quantity'), 1);
$pdf->Cell(40, 10, iconv('UTF-8', 'ISO-8859-1', 'Collective Price'), 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);
while ($item = $items_result->fetch_assoc()) {
    $pdf->Cell(40, 10, iconv('UTF-8', 'ISO-8859-1', $item['order_item_id']), 1);
    $pdf->Cell(80, 10, iconv('UTF-8', 'ISO-8859-1', $item['part_name']), 1);
    $pdf->Cell(30, 10, iconv('UTF-8', 'ISO-8859-1', $item['order_quantity']), 1);
    $pdf->Cell(40, 10, iconv('UTF-8', 'ISO-8859-1', '£' . number_format($item['order_price'], 2, '.', ',')), 1);
    $pdf->Ln();
}

// Output the PDF
$pdf->Output('I', 'Invoice_' . $order['order_id'] . '.pdf');
?>