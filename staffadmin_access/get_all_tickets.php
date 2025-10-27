<?php
// get_all_tickets.php
session_start();
include '../connection/connection.php';
header('Content-Type: application/json');

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to users

// Function to send consistent JSON responses
function sendResponse($success, $data = null, $error = null) {
    $response = ['success' => $success];
    if ($data !== null) $response = array_merge($response, $data);
    if ($error !== null) $response['error'] = $error;
    echo json_encode($response);
    exit();
}

try {
    // Get branch_id from multiple possible sources
    $branch_id = null;
    
    // First try GET parameter
    if (isset($_GET['branch_id']) && !empty($_GET['branch_id'])) {
        $branch_id = intval($_GET['branch_id']);
    }
    // Then try POST parameter
    else if (isset($_POST['branch_id']) && !empty($_POST['branch_id'])) {
        $branch_id = intval($_POST['branch_id']);
    }
    // Finally try session
    else if (isset($_SESSION['branch_id']) && !empty($_SESSION['branch_id'])) {
        $branch_id = intval($_SESSION['branch_id']);
    }
    
    // Validate branch_id
    if ($branch_id === null || $branch_id <= 0) {
        sendResponse(false, null, 'Valid Branch ID is required. Received: ' . $branch_id);
    }
    
    // Check database connection
    if (!$conn) {
        sendResponse(false, null, 'Database connection failed');
    }
    
    // Modified query to handle cases where order_id might be null or relationships might be missing
    $sql = "SELECT 
                ct.ticket_id, 
                ct.order_reference, 
                ct.issue_type, 
                ct.issue_description, 
                ct.ticket_status, 
                ct.created_at, 
                ct.damage_time, 
                ct.user_id, 
                ct.awaiting_customer_at,
                u.full_name as customer_name, 
                u.email as customer_email, 
                u.phone_number as customer_phone, 
                u.house_address, 
                u.full_address,
                o.branch_id
            FROM customer_tickets ct 
            LEFT JOIN users u ON ct.user_id = u.id 
            LEFT JOIN orders o ON ct.order_id = o.order_id 
            WHERE o.branch_id = ? AND ct.order_id IS NOT NULL
            ORDER BY ct.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        sendResponse(false, null, 'Failed to prepare SQL statement: ' . implode(', ', $conn->errorInfo()));
    }
    
    $execute_result = $stmt->execute([$branch_id]);
    
    if (!$execute_result) {
        sendResponse(false, null, 'Failed to execute query: ' . implode(', ', $stmt->errorInfo()));
    }
    
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Check if we got any tickets
    if ($tickets === false) {
        sendResponse(false, null, 'Failed to fetch tickets from database');
    }
    
    // Format dates and ensure all required fields exist
    foreach ($tickets as &$ticket) {
        // Ensure all expected fields exist
        $ticket['ticket_id'] = $ticket['ticket_id'] ?? 'N/A';
        $ticket['order_reference'] = $ticket['order_reference'] ?? 'N/A';
        $ticket['issue_type'] = $ticket['issue_type'] ?? 'Unknown Issue';
        $ticket['issue_description'] = $ticket['issue_description'] ?? 'No description provided';
        $ticket['ticket_status'] = $ticket['ticket_status'] ?? 'Pending';
        $ticket['customer_name'] = $ticket['customer_name'] ?? 'Unknown Customer';
        $ticket['customer_email'] = $ticket['customer_email'] ?? 'No email provided';
        $ticket['customer_phone'] = $ticket['customer_phone'] ?? 'No phone provided';
        
        // Format date if it exists
        if (!empty($ticket['created_at'])) {
            $date = new DateTime($ticket['created_at']);
            $ticket['created_at'] = $date->format('Y-m-d H:i:s');
        }
    }
    unset($ticket); // Break the reference
    
    sendResponse(true, ['tickets' => $tickets, 'count' => count($tickets)]);
    
} catch (PDOException $e) {
    sendResponse(false, null, 'Database error: ' . $e->getMessage());
} catch (Exception $e) {
    sendResponse(false, null, 'General error: ' . $e->getMessage());
}
?>