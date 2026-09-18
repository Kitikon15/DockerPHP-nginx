<?php
/**
 * Database connection using PHP Data Objects (PDO)
 */
$servername = "db";
$username = "admin";
$password = "1234";
$dbname = "sample_db";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo = $conn; // Reference variable for compatibility
} catch (PDOException $e) {
    // Fallback if dbname is titanic
    try {
        $dbname = "titanic";
        $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo = $conn;
    } catch (PDOException $ex) {
        die("<div style='font-family:sans-serif;padding:20px;background:#fee;color:#c00;border:1px solid #fcc;border-radius:5px;'>
            <h3>Database Connection Failed</h3>
            <p>" . htmlspecialchars($ex->getMessage()) . "</p>
        </div>");
    }
}

// If accessed directly from the browser, display connection status
if (isset($_SERVER['SCRIPT_FILENAME']) && basename($_SERVER['SCRIPT_FILENAME']) === 'pdo_data.php') {
    $count = 0;
    try {
        $stmt = $conn->query("SELECT COUNT(*) AS total FROM titanic");
        $count = $stmt->fetchColumn();
    } catch (Exception $e) {
        $count = 0;
    }
    ?>
    <!DOCTYPE html>
    <html lang="th">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>PDO Connection Status</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    </head>

    <body class="bg-light">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-7">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4 text-center">
                            <div class="text-success mb-3">
                                <i class="bi bi-check-circle-fill" style="font-size: 3rem;"></i>
                            </div>
                            <h3 class="card-title text-success fw-bold">เชื่อมต่อฐานข้อมูลสำเร็จ (PDO Connected)</h3>
                            <p class="text-muted">ระบบเชื่อมต่อ MariaDB ผ่าน PHP Data Objects เรียบร้อยแล้ว</p>

                            <ul class="list-group list-group-flush text-start my-4">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-hdd-network me-2 text-primary"></i>Host</span>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($servername) ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-database me-2 text-primary"></i>Database</span>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($dbname) ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-person me-2 text-primary"></i>User</span>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($username) ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-table me-2 text-primary"></i>จำนวนข้อมูลในตาราง titanic</span>
                                    <span class="badge bg-success fs-6"><?= number_format($count) ?> แถว</span>
                                </li>
                            </ul>

                            <a href="show_data.php" class="btn btn-primary btn-lg px-4">
                                <i class="bi bi-table me-2"></i>ไปที่หน้าแสดงข้อมูล (show_data.php)
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>

    </html>
    <?php
}
?>