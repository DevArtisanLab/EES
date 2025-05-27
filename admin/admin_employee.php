<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.2/dist/tailwind.min.css">
    <style>
        .toolbar {
            display: flex;
            gap: 10px;
            margin: 20px 0;
        }

        .search-input,
        select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .employee-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .employee-table th, .employee-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .status {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.9em;
            color: white;
        }

        .status.passed { background-color: #4ade80; }
        .status.failed { background-color: #f87171; }
        .status.pending {
            background-color: #facc15;
            color: black;
        }

        .action {
            background: none;
            border: none;
            cursor: pointer;
            margin-right: 5px;
            font-size: 1em;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans">

<div class="flex min-h-screen">
    
    <!-- Sidebar Include -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 p-8">
        <h1 class="text-2xl font-semibold mb-6">Employee Management</h1>

        <div class="toolbar">
            <input type="text" placeholder="Search employee..." class="search-input w-1/3">
            <select>
                <option>All Positions</option>
            </select>
            <select>
                <option>All Status</option>
            </select>
            <select>
                <option>Newest First</option>
            </select>
        </div>

        <table class="employee-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NAME</th>
                    <th>EMAIL</th>
                    <th>POSITION</th>
                    <th>SCORE</th>
                    <th>STATUS</th>
                    <th>DATE</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $employee = [
                    ['APL-001', 'John Doe', 'john.doe@example.com', 'Software Developer', '87%', 'Passed', '2023-11-15'],
                    ['APL-002', 'Jane Smith', 'jane.smith@example.com', 'Data Analyst', '92%', 'Passed', '2023-11-14'],
                    ['APL-003', 'Robert Johnson', 'robert.j@example.com', 'Network Engineer', '65%', 'Failed', '2023-11-14'],
                    ['APL-004', 'Sarah Williams', 'sarah.w@example.com', 'UI/UX Designer', '89%', 'Passed', '2023-11-13'],
                    ['APL-005', 'Michael Brown', 'm.brown@example.com', 'Project Manager', 'N/A', 'Pending', '2023-11-12']
                ];

                foreach ($employee as $app) {
                    echo "<tr>
                        <td>{$app[0]}</td>
                        <td>{$app[1]}</td>
                        <td>{$app[2]}</td>
                        <td>{$app[3]}</td>
                        <td>{$app[4]}</td>
                        <td><span class='status " . strtolower($app[5]) . "'>{$app[5]}</span></td>
                        <td>{$app[6]}</td>
                        <td>
                            <button class='action view' title='View'>👁️</button>
                            <button class='action edit' title='Edit'>✏️</button>
                            <button class='action delete' title='Delete'>🗑️</button>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </main>
</div>

</body>
</html>
