<?php
require('libs/fpdf/fpdf186/fpdf.php');
include('include/init.php'); // Initialise, includes the database connection

// Fetch inventory data
$sql = "SELECT 
            parts.part_id, 
            parts.part_name, 
            parts.description, 
            parts.genre, 
            parts.manufacturer, 
            parts.unit_price, 
            parts.quantity_in_stock, 
            parts.reorder_level, 
            suppliers.supplier_name,
            branches.branch_name,
            branches.active AS branch_active
        FROM parts 
        INNER JOIN suppliers ON parts.supplier_id = suppliers.supplier_id
        LEFT JOIN branches ON parts.branch_id = branches.branch_id
        ORDER BY parts.part_id";
$result = $con->query($sql);

// Create a new PDF document in landscape orientation, 'L' for landscape
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();

// Set font
$pdf->SetFont('Arial', 'B', 16);

// Add a title
$pdf->Cell(0, 10, 'Inventory Report', 0, 1, 'C');

// Add table headers
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(20, 10, 'Part ID', 1);
$pdf->Cell(40, 10, 'Part Name', 1);
$pdf->Cell(30, 10, 'Genre', 1);
$pdf->Cell(30, 10, 'Manufacturer', 1);
$pdf->Cell(20, 10, 'Price', 1);
$pdf->Cell(20, 10, 'Stock', 1);
$pdf->Cell(30, 10, 'Reorder Level', 1);
$pdf->Cell(40, 10, 'Branch', 1);
$pdf->Ln();

// Add table rows
$pdf->SetFont('Arial', '', 12);
while ($row = $result->fetch_assoc()) { // Loop through each row of the result set
    $branch_status = $row['branch_active'] ? '' : ' (Closed)'; // Add branch status if closed
    $branch_name = $row['branch_name'] . $branch_status;
    $pdf->Cell(20, 10, $row['part_id'], 1);
    $pdf->Cell(40, 10, $row['part_name'], 1);
    $pdf->Cell(30, 10, $row['genre'], 1);
    $pdf->Cell(30, 10, $row['manufacturer'], 1);
    $pdf->Cell(20, 10, iconv('UTF-8', 'ISO-8859-1', '£' . number_format($row['unit_price'], 2)), 1);

    // Set color based on stock level
    if ($row['quantity_in_stock'] < $row['reorder_level']) {
        $pdf->SetTextColor(255, 0, 0); // Red
    } elseif ($row['quantity_in_stock'] <= $row['reorder_level'] + 3) {
        $pdf->SetTextColor(255, 165, 0); // Orange
    } else {
        $pdf->SetTextColor(0, 0, 0); // Black
    }
    $pdf->Cell(20, 10, $row['quantity_in_stock'], 1);

    // Reset text color
    $pdf->SetTextColor(0, 0, 0);

    $pdf->Cell(30, 10, $row['reorder_level'], 1);
    $pdf->Cell(40, 10, $branch_name, 1);
    $pdf->Ln();
}

// Output the PDF
$pdf->Output('I', 'Inventory_Report.pdf');
?>