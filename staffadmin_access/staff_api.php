<?php
session_start();
require_once '../connection/connection.php';
header('Content-Type: application/json');

$conn = $db_connection;
$action = $_POST['action'] ?? $_GET['action'] ?? '';

function respond($success, $message, $data = []) {
    echo json_encode(array_merge([
        'success' => $success,
        'message' => $message
    ], $data));
    exit;
}

if ($action === 'add') {
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $user_role = $_POST['user_role'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    $branch_id = null;
    if ($user_role === 'ADMIN') {
        $branch_id = isset($_POST['branch_id']) ? (int)$_POST['branch_id'] : null;
        if (!$branch_id) {
            respond(false, 'Please select a branch for Admin.');
        }
    } else {
        // For other staff, use the branch_id of the logged-in user
        $branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : null;
        if (!$branch_id) {
            respond(false, 'Branch assignment failed. Please re-login.');
        }
    }

    // Validation
    if (!$email || !$phone || !$password || !$confirm_password || !$user_role || !$full_name) {
        respond(false, 'All fields are required.');
    } elseif ($password !== $confirm_password) {
        respond(false, 'Password and Confirm Password do not match.');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@gmail\.com$/i', $email)) {
        respond(false, 'Email must be a valid Gmail address.');
    } elseif (strpos($phone, '+639') !== 0) {
        respond(false, 'Phone number must start with +639.');
    } elseif (!preg_match('/^\+639[0-9]{9}$/', $phone)) {
        respond(false, 'Phone number must be in the format +639XXXXXXXXX (13 characters).');
    }
    // Check uniqueness
    $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        respond(false, 'Email already exists.');
    }
    $stmt2 = $conn->prepare('SELECT id FROM users WHERE phone_number = ?');
    $stmt2->execute([$phone]);
    if ($stmt2->fetch()) {
        respond(false, 'Phone number already exists.');
    }
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    $insert = $conn->prepare('INSERT INTO users (email, phone_number, password_hash, user_role, full_name, branch_id, account_status) VALUES (?, ?, ?, ?, ?, ?, "active")');
    if ($insert->execute([$email, $phone, $password_hash, $user_role, $full_name, $branch_id])) {
        $id = $conn->lastInsertId();
        // Fetch branch name for response
        $branch_name = null;
        if ($branch_id) {
            $stmt_branch = $conn->prepare('SELECT branch_name FROM branches WHERE branch_id = ?');
            $stmt_branch->execute([$branch_id]);
            $row = $stmt_branch->fetch();
            if ($row) $branch_name = $row['branch_name'];
        }
        respond(true, 'Staff account added successfully!', [
            'staff' => [
                'id' => $id,
                'full_name' => $full_name,
                'email' => $email,
                'phone_number' => $phone,
                'user_role' => $user_role,
                'account_status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'branch_id' => $branch_id,
                'branch_name' => $branch_name
            ]
        ]);
    } else {
        respond(false, 'Error adding staff account.');
    }
} elseif ($action === 'deactivate') {
    $id = intval($_POST['id'] ?? 0);
    if (!$id) respond(false, 'Invalid staff ID.');
    $stmt = $conn->prepare('UPDATE users SET account_status = "inactive" WHERE id = ?');
    if ($stmt->execute([$id])) {
        respond(true, 'Staff account deactivated.', ['id' => $id, 'new_status' => 'inactive']);
    } else {
        respond(false, 'Failed to deactivate staff.');
    }
} elseif ($action === 'activate') {
    $id = intval($_POST['id'] ?? 0);
    if (!$id) respond(false, 'Invalid staff ID.');
    $stmt = $conn->prepare('UPDATE users SET account_status = "active" WHERE id = ?');
    if ($stmt->execute([$id])) {
        respond(true, 'Staff account activated.', ['id' => $id, 'new_status' => 'active']);
    } else {
        respond(false, 'Failed to activate staff.');
    }
} else {
    respond(false, 'Invalid action.');
}
