<?php
// get_ticket_history.php
// Returns resolved and closed tickets for the branch
header('Content-Type: application/json');
include '../connection/connection.php';

// Check if branch_id is provided
$branch_id = isset($_GET['branch_id']) ? intval($_GET['branch_id']) : 0;
if (!$branch_id) {
    echo json_encode(['success' => false, 'error' => 'Missing branch_id']);
    exit;
}

try {
    // Fetch resolved and closed tickets for the branch using assigned_staff_id and branch_staff
    $stmt = $conn->prepare("
        SELECT 
            t.ticket_id AS id, 
            t.order_reference AS orderId, 
            t.created_at AS date, 
            t.updated_at AS resolvedDate, 
            t.ticket_status AS status, 
            t.issue_type AS issue, 
            t.issue_description AS description, 
            u.full_name AS customerName, 
            u.email AS customerEmail, 
            u.phone_number AS customerPhone, 
            u.full_address AS customerAddress
        FROM customer_tickets t
        LEFT JOIN users u ON t.user_id = u.id
        LEFT JOIN branch_staff bs ON t.assigned_staff_id = bs.staff_id
        WHERE (bs.branch_id = ? OR t.assigned_staff_id IS NULL)
          AND (t.ticket_status = 'Resolved' OR t.ticket_status = 'Closed')
        ORDER BY t.updated_at DESC
    ");
    $stmt->execute([$branch_id]);
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Process tickets to add calculated fields
    foreach ($tickets as &$ticket) {
        // Calculate resolution time
        $created = strtotime($ticket['date']);
        $resolved = strtotime($ticket['resolvedDate']);
        $days = ($created && $resolved) ? ceil(($resolved - $created) / 86400) : 0;
        $ticket['resolutionTime'] = $days . ' days';
        
        // Format dates for display
        $ticket['resolvedDate'] = date('M j, Y', strtotime($ticket['resolvedDate']));
        $ticket['date'] = date('M j, Y', strtotime($ticket['date']));
        
        // Create timeline data
        $ticket['timeline'] = [
            [
                'date' => date('M j, Y g:i A', $created),
                'action' => 'Ticket Created', 
                'description' => 'Support ticket was opened by customer'
            ],
            [
                'date' => date('M j, Y g:i A', $resolved),
                'action' => $ticket['status'], 
                'description' => 'Ticket was marked as ' . $ticket['status']
            ]
        ];
    }
    
    // Return success response with tickets data
    echo json_encode([
        'success' => true, 
        'tickets' => $tickets,
        'count' => count($tickets)
    ]);
    
} catch (Exception $e) {
    // Return error response
    echo json_encode([
        'success' => false, 
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>