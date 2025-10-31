<?php
// START SESSION AND ERROR REPORTING
session_start();
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../error_log.txt');
error_reporting(E_ALL);

// PREVENT TIMEOUTS FOR LARGE REPORTS
set_time_limit(300); // 5 minutes

// INCLUDES
require_once '../connection/connection.php';
require_once __DIR__ . '/../vendor/autoload.php';

// USE STATEMENTS FOR SPREADSHEET WRITERS
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

// --- 1. GET AND VALIDATE PARAMETERS ---

// Get parameters from the URL
$format = $_GET['format'] ?? 'excel';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

// Get branch_id from session (set by sidebar.php)
$branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 1; 

// Basic validation for PDF/CSV
if (($format == 'pdf' || $format == 'csv') && (empty($start_date) || empty($end_date))) {
    error_log("Report Error: Missing dates for PDF/CSV. Start: $start_date, End: $end_date");
    die("Error: Missing required parameters (start_date, end_date) for PDF/CSV report.");
}

// Ensure the end date is inclusive for database queries
$end_date_inclusive = $end_date . ' 23:59:59';
$current_year = date('Y');
$current_month = date('n');


// --- 2. HELPER FUNCTIONS ---

// Helper to get Excel column letter
function colLetter($colNum) {
    $dividend = $colNum;
    $columnName = '';
    while ($dividend > 0) {
        $modulo = ($dividend - 1) % 26;
        $columnName = chr(65 + $modulo) . $columnName;
        $dividend = (int)(($dividend - $modulo) / 26);
    }
    return $columnName;
}

// Helper function to add a title and headers to a sheet
function setupSheet($sheet, $title, $headers, $start_row = 3) {
    $sheet->setTitle(substr($title, 0, 31)); // Max 31 chars for tab title
    
    // Calculate header range
    $header_range = 'A1:' . colLetter(count($headers)) . '1';
    if (count($headers) > 0) {
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells($header_range);
    } else {
        $sheet->setCellValue('A1', $title);
    }

    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Header row styling
    foreach ($headers as $col => $header) {
        $col_letter = colLetter($col + 1);
        $cell = $col_letter . $start_row;
        $sheet->setCellValue($cell, $header);
        $sheet->getStyle($cell)->getFont()->setBold(true);
        $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFDDEEFF');
        $sheet->getStyle($cell)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getColumnDimension($col_letter)->setAutoSize(true);
    }
    // Freeze header row
    if(count($headers) > 0) {
        $sheet->freezePane('A' . ($start_row + 1));
    }
}

/**
 * FIXED Helper function to populate data.
 * Now accepts headers array and a start column.
 */
function populateSheet($sheet, $data, $headers, $start_row = 4, $start_col = 1) {
    $current_row = $start_row;
    foreach ($data as $row) {
        $col_idx = $start_col; // Start at the specified column
        $header_idx = 0; // Use a separate index for the headers array

        foreach ($row as $value) {
            $cell = colLetter($col_idx) . $current_row;
            $sheet->setCellValue($cell, $value);
            // Add border to cell
            $sheet->getStyle($cell)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            // Get the header name for this column
            $header_name = isset($headers[$header_idx]) ? strtolower($headers[$header_idx]) : '';
            
            // FIXED: Format currency columns based on header name, not hardcoded column
            if (is_numeric($value) && (strpos($header_name, 'revenue') !== false || strpos($header_name, 'amount') !== false)) {
                $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('₱#,##0.00');
            }
            
            $col_idx++;
            $header_idx++;
        }
        // Alternating row color
        if ($current_row % 2 == 0) {
            $sheet->getStyle(colLetter($start_col) . $current_row . ':' . colLetter($col_idx - 1) . $current_row)
                ->getFill()->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFF7F7F7');
        }
        $current_row++;
    }
}


// --- 3. GENERATE THE SPREADSHEET ---

$spreadsheet = new Spreadsheet();

if ($format == 'excel') {
    // --- *** LOGIC FOR 4-TAB EXCEL DASHBOARD REPORT *** ---
    $filename = "RichAnneTiles_Dashboard_Report_" . date('Y-m-d');
    
    // --- Sheet 1: Overview Dashboard ---
    $overviewSheet = $spreadsheet->getActiveSheet();
    $overviewSheet->setTitle('Overview');

    $stmt = $db_connection->prepare('SELECT SUM(o.total_amount) AS revenue FROM orders o WHERE o.branch_id = :branch_id');
    $stmt->execute(['branch_id' => $branch_id]);
    $total_revenue = $stmt->fetchColumn() ?: 0;
    $stmt = $db_connection->prepare('SELECT COUNT(*) FROM orders WHERE branch_id = :branch_id');
    $stmt->execute(['branch_id' => $branch_id]);
    $orders_count = $stmt->fetchColumn() ?: 0;
    $stmt = $db_connection->prepare('SELECT COUNT(DISTINCT o.user_id) FROM orders o WHERE o.branch_id = :branch_id');
    $stmt->execute(['branch_id' => $branch_id]);
    $customers_count = $stmt->fetchColumn() ?: 0;
    $stmt = $db_connection->prepare('SELECT SUM(oi.quantity) FROM order_items oi INNER JOIN orders o ON oi.order_id = o.order_id WHERE o.branch_id = :branch_id');
    $stmt->execute(['branch_id' => $branch_id]);
    $products_sold = $stmt->fetchColumn() ?: 0;

    $overviewSheet->setCellValue('A1', 'Key Metrics (Branch ' . $branch_id . ')');
    $overviewSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $overviewSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // Aligned left
    
    $metrics = [
        ['Total Revenue', $total_revenue],
        ['Total Orders', $orders_count],
        ['Total Customers', $customers_count],
        ['Total Products Sold', $products_sold]
    ];
    foreach ($metrics as $i => $row) {
        $overviewSheet->setCellValue('A' . ($i + 2), $row[0]);
        $overviewSheet->setCellValue('B' . ($i + 2), $row[1]);
        $overviewSheet->getStyle('A' . ($i + 2))->getFont()->setBold(true);
        $overviewSheet->getStyle('A' . ($i + 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $overviewSheet->getStyle('B' . ($i + 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // Aligned left
        $overviewSheet->getStyle('A' . ($i + 2) . ':B' . ($i + 2))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        if ($i == 0) {
            $overviewSheet->getStyle('B2')->getNumberFormat()->setFormatCode('₱#,##0.00');
        }
    }
    $overviewSheet->getColumnDimension('A')->setAutoSize(true);
    $overviewSheet->getColumnDimension('B')->setAutoSize(true);

    $category_headers = ['Category', 'Units Sold'];
    $stmt = $db_connection->prepare('SELECT tc.classification_name, SUM(oi.quantity) AS sold FROM order_items oi INNER JOIN products p ON oi.product_id = p.product_id INNER JOIN product_classifications pc ON p.product_id = pc.product_id INNER JOIN tile_classifications tc ON pc.classification_id = tc.classification_id INNER JOIN orders o ON oi.order_id = o.order_id WHERE o.branch_id = :branch_id GROUP BY tc.classification_id ORDER BY sold DESC LIMIT 6');
    $stmt->execute(['branch_id' => $branch_id]);
    $category_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $overviewSheet->setCellValue('D1', 'Top Selling Categories');
    $overviewSheet->getStyle('D1')->getFont()->setBold(true)->setSize(14);
    $overviewSheet->getStyle('D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    foreach ($category_headers as $col => $header) {
        $cell = colLetter($col + 4) . '2'; // Start at Column D
        $overviewSheet->setCellValue($cell, $header);
        $overviewSheet->getStyle($cell)->getFont()->setBold(true);
        $overviewSheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $overviewSheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFDDEEFF');
        $overviewSheet->getStyle($cell)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $overviewSheet->getColumnDimension(colLetter($col + 4))->setAutoSize(true);
    }
    // FIXED: Added headers and start column (4 for D)
    populateSheet($overviewSheet, $category_data, $category_headers, 3, 4);

    $design_headers = ['Design', 'Units Sold', 'Revenue'];
    $stmt = $db_connection->prepare('SELECT td.design_name, SUM(oi.quantity) AS sold, SUM(oi.quantity * p.product_price) AS revenue FROM order_items oi INNER JOIN products p ON oi.product_id = p.product_id INNER JOIN product_designs pd ON p.product_id = pd.product_id INNER JOIN tile_designs td ON pd.design_id = td.design_id INNER JOIN orders o ON oi.order_id = o.order_id WHERE o.branch_id = :branch_id GROUP BY td.design_id ORDER BY sold DESC LIMIT 5');
    $stmt->execute(['branch_id' => $branch_id]);
    $design_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $overviewSheet->setCellValue('G1', 'Popular Tile Designs');
    $overviewSheet->getStyle('G1')->getFont()->setBold(true)->setSize(14);
    $overviewSheet->getStyle('G1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    foreach ($design_headers as $col => $header) {
        $cell = colLetter($col + 7) . '2'; // Start at Column G
        $overviewSheet->setCellValue($cell, $header);
        $overviewSheet->getStyle($cell)->getFont()->setBold(true);
        $overviewSheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $overviewSheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFDDEEFF');
        $overviewSheet->getStyle($cell)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $overviewSheet->getColumnDimension(colLetter($col + 7))->setAutoSize(true);
    }
    // FIXED: Added headers and start column (7 for G)
    populateSheet($overviewSheet, $design_data, $design_headers, 3, 7);

    // --- Sheet 2: Predictive Data (Recent 1000 Orders) ---
    $predictiveSheet = $spreadsheet->createSheet();
    $predictive_headers = [];
    $predictiveStmt = $db_connection->query("SELECT * FROM orders ORDER BY order_id DESC LIMIT 1000");
    $predictiveData = $predictiveStmt->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($predictiveData)) {
        $predictive_headers = array_keys($predictiveData[0]);
    }
    setupSheet($predictiveSheet, 'Predictive Data (Last 1000 Orders)', $predictive_headers);
    // FIXED: Added headers and start column (1 for A)
    populateSheet($predictiveSheet, $predictiveData, $predictive_headers, 4, 1);

    // --- Sheet 3: Sales Heatmaps Data ---
    $heatmapSheet = $spreadsheet->createSheet();
    $heatmapSheet->setTitle('Sales Heatmaps Data');

    $monthly_sales = [];
    $stmt = $db_connection->prepare('
        SELECT MONTH(o.order_date) as month_num, MONTHNAME(o.order_date) as month_name, SUM(o.total_amount) as revenue, COUNT(DISTINCT o.order_id) as orders
        FROM orders o 
        WHERE o.branch_id = :branch_id AND YEAR(o.order_date) = :year
        GROUP BY month_num, month_name
        ORDER BY month_num ASC
    ');
    $stmt->execute(['branch_id' => $branch_id, 'year' => $current_year]);
    $monthly_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $monthly_headers = ['Month Number', 'Month', 'Revenue', 'Orders'];

    $heatmapSheet->setCellValue('A1', 'Monthly Sales (' . $current_year . ')');
    $heatmapSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $heatmapSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    foreach ($monthly_headers as $col => $header) {
        $cell = colLetter($col + 1) . '2'; // Start at Column A
        $heatmapSheet->setCellValue($cell, $header);
        $heatmapSheet->getStyle($cell)->getFont()->setBold(true);
        $heatmapSheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $heatmapSheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFDDEEFF');
        $heatmapSheet->getStyle($cell)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $heatmapSheet->getColumnDimension(colLetter($col + 1))->setAutoSize(true);
    }
    // FIXED: Added headers and start column (1 for A)
    populateSheet($heatmapSheet, $monthly_data, $monthly_headers, 3, 1);

    $daily_sales_data = [];
    $stmt = $db_connection->prepare('
        SELECT DAY(o.order_date) as day, SUM(o.total_amount) as revenue, COUNT(DISTINCT o.order_id) as orders
        FROM orders o 
        WHERE o.branch_id = :branch_id AND MONTH(o.order_date) = :month AND YEAR(o.order_date) = :year
        GROUP BY DAY(o.order_date)
        ORDER BY day ASC
    ');
    $stmt->execute(['branch_id' => $branch_id, 'month' => $current_month, 'year' => $current_year]);
    $daily_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $daily_headers = ['Day', 'Revenue', 'Orders'];

    $heatmapSheet->setCellValue('F1', 'Daily Sales (' . date('F Y') . ')');
    $heatmapSheet->getStyle('F1')->getFont()->setBold(true)->setSize(14);
    $heatmapSheet->getStyle('F1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    foreach ($daily_headers as $col => $header) {
        $cell = colLetter($col + 6) . '2'; // Start at Column F
        $heatmapSheet->setCellValue($cell, $header);
        $heatmapSheet->getStyle($cell)->getFont()->setBold(true);
        $heatmapSheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $heatmapSheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFDDEEFF');
        $heatmapSheet->getStyle($cell)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $heatmapSheet->getColumnDimension(colLetter($col + 6))->setAutoSize(true);
    }
    // FIXED: Added headers and start column (6 for F)
    populateSheet($heatmapSheet, $daily_data, $daily_headers, 3, 6);
    
    foreach (range('A', 'H') as $col) {
        $heatmapSheet->getColumnDimension($col)->setAutoSize(true);
    }

    // --- Sheet 4: Customer Activity Trail (Last 1000 Events) ---
    $trailSheet = $spreadsheet->createSheet();
    $trail_headers = [];
    $trailStmt = $db_connection->query('
        SELECT t.created_at, u.full_name, b.branch_name, t.event_type 
        FROM customer_branch_trail t 
        INNER JOIN users u ON t.user_id = u.id 
        INNER JOIN branches b ON t.branch_id = b.branch_id 
        ORDER BY t.created_at DESC 
        LIMIT 1000
    ');
    $trailData = $trailStmt->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($trailData)) {
        $trail_headers = array_keys($trailData[0]);
    }
    setupSheet($trailSheet, 'Customer Activity Trail (Last 1000)', $trail_headers);
    // FIXED: Added headers and start column (1 for A)
    populateSheet($trailSheet, $trailData, $trail_headers, 4, 1);
    
    $spreadsheet->setActiveSheetIndex(0); // Set active sheet to the first one

} else {
    // --- *** LOGIC FOR DATE-BASED PDF/CSV SALES REPORT *** ---
    $report_title = 'Sales Report';
    $filename = "Sales_Report_{$start_date}_to_{$end_date}";
    
    $stmt = $db_connection->prepare('
        SELECT 
            o.order_date, 
            o.order_reference, 
            u.full_name AS customer,
            o.total_amount, 
            o.order_status,
            b.branch_name
        FROM orders o 
        INNER JOIN users u ON o.user_id = u.id
        INNER JOIN branches b ON o.branch_id = b.branch_id
        WHERE o.order_date BETWEEN :start_date AND :end_date
        ORDER BY o.order_date DESC
    ');
    $stmt->execute([
        'start_date' => $start_date,
        'end_date' => $end_date_inclusive
    ]);
    $report_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($report_data)) {
        $report_headers = array_keys($report_data[0]);
    } else {
        $report_headers = ['Date', 'Reference', 'Customer', 'Amount', 'Status', 'Branch'];
    }

    $sheet = $spreadsheet->getActiveSheet();
    setupSheet($sheet, $report_title, $report_headers, 4); // Start headers on row 4
    
    // Add Date Range info
    $sheet->setCellValue('A2', "Date Range: $start_date to $end_date");
    $sheet->mergeCells('A2:' . colLetter(count($report_headers)) . '2');
    $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // FIXED: Replaced manual loop with the new populateSheet function for consistency
    populateSheet($sheet, $report_data, $report_headers, 5, 1);
}


// --- 4. OUTPUT THE FILE IN THE REQUESTED FORMAT ---

// Clear any previous output
while (ob_get_level()) {
    ob_end_clean();
}

switch ($format) {
    case 'excel':
        $filename .= '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $writer = new Xlsx($spreadsheet);
        break;

    case 'pdf':
        $filename .= '.pdf';
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Ensure mPDF is installed: composer require mpdf/mpdf
        \PhpOffice\PhpSpreadsheet\IOFactory::registerWriter('Pdf', Mpdf::class);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Pdf');
        break;

    case 'csv':
        $filename .= '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $writer = new Csv($spreadsheet);
        break;
        
    default:
        die("Invalid format specified.");
}

$writer->save('php://output');
exit;