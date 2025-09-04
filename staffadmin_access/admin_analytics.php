<?php include '../includes/sidebar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Analytics Dashboard</title>
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 80px;
            --transition-speed: 0.3s;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            transition: padding-left var(--transition-speed);
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: margin-left var(--transition-speed);
        }

        /* Adjust main content when sidebar is collapsed */
        html.sidebar-collapsed .main-content {
            margin-left: var(--sidebar-collapsed-width);
        }

        .dashboard-header {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .dashboard-header h1 {
            font-size: 24px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .dashboard-content {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .dashboard-content p {
            color: #666;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="dashboard-header">
            <h1>Dashboard this contains the analytics, but no need to include the word analytics</h1>
        </div>
        <div class="dashboard-content">
            <p>Wag niyo burahin yung nasa taas, proceed na agad kayo sa laman, also make it responsive</p>
        </div>
    </div>
</body>
</html>