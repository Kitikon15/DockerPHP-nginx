<?php
require_once 'pdo_data.php';

// Pagination parameters
$limit = 20;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Search and filter parameters
$search   = isset($_GET['search']) ? trim($_GET['search']) : '';
$pclass   = isset($_GET['pclass']) ? trim($_GET['pclass']) : '';
$survived = isset($_GET['survived']) ? trim($_GET['survived']) : '';

// Build dynamic WHERE clause with prepared statement
$whereClauses = [];
$params = [];

if ($search !== '') {
    $whereClauses[] = "(Name LIKE :search OR Ticket LIKE :search OR Cabin LIKE :search)";
    $params[':search'] = "%$search%";
}

if ($pclass !== '' && in_array($pclass, ['1', '2', '3'])) {
    $whereClauses[] = "Pclass = :pclass";
    $params[':pclass'] = $pclass;
}

if ($survived !== '' && in_array($survived, ['0', '1'])) {
    $whereClauses[] = "Survived = :survived";
    $params[':survived'] = $survived;
}

$whereSql = '';
if (!empty($whereClauses)) {
    $whereSql = 'WHERE ' . implode(' AND ', $whereClauses);
}

// 1. Get total matching records for pagination
$countQuery = "SELECT COUNT(*) AS total FROM titanic $whereSql";
$countStmt = $conn->prepare($countQuery);
$countStmt->execute($params);
$totalRecords = (int)$countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);
if ($totalPages < 1) $totalPages = 1;
if ($page > $totalPages) $page = $totalPages;

// 2. Fetch records for the current page
$dataQuery = "SELECT * FROM titanic $whereSql ORDER BY PassengerId ASC LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($dataQuery);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$passengers = $stmt->fetchAll();

// 3. Overview Statistics
$stats = [
    'total' => 0,
    'survived' => 0,
    'died' => 0,
    'rate' => 0
];
try {
    $statsStmt = $conn->query("SELECT 
        COUNT(*) AS total,
        SUM(CASE WHEN Survived = 1 THEN 1 ELSE 0 END) AS survived,
        SUM(CASE WHEN Survived = 0 THEN 1 ELSE 0 END) AS died
        FROM titanic");
    $statsData = $statsStmt->fetch();
    if ($statsData) {
        $stats['total'] = (int)$statsData['total'];
        $stats['survived'] = (int)$statsData['survived'];
        $stats['died'] = (int)$statsData['died'];
        $stats['rate'] = $stats['total'] > 0 ? round(($stats['survived'] / $stats['total']) * 100, 1) : 0;
    }
} catch (Exception $e) {
    // Ignore stats query failure
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แสดงข้อมูลผู้โดยสารเรือไททานิค (Titanic Dataset)</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Sarabun', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .header-box {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: #fff;
            padding: 30px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .stat-card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
        }
        .table-container {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }
        .table th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
            vertical-align: middle;
        }
        .badge-survived {
            background-color: #198754;
            color: white;
        }
        .badge-died {
            background-color: #dc3545;
            color: white;
        }
        .badge-male {
            background-color: #0d6efd;
            color: white;
        }
        .badge-female {
            background-color: #d63384;
            color: white;
        }
    </style>
</head>
<body>

<div class="container-fluid py-4 px-md-5">

    <!-- Header -->
    <div class="header-box">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-1"><i class="bi bi-water me-2"></i>ระบบแสดงข้อมูลผู้โดยสารเรือไททานิค (Titanic Dataset)</h2>
                <p class="mb-0 opacity-75">ดึงข้อมูลผ่าน <strong>PHP Data Objects (PDO)</strong> ร่วมกับ MariaDB Database</p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="pdo_data.php" class="btn btn-outline-light btn-sm me-2">
                    <i class="bi bi-hdd-network me-1"></i> ตรวจสอบการเชื่อมต่อ PDO
                </a>
                <a href="show_data.php" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-clockwise me-1"></i> รีเฟรชข้อมูล
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card p-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary me-3">
                        <i class="bi bi-people-fill fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small">ผู้โดยสารทั้งหมด</div>
                        <h4 class="fw-bold mb-0"><?= number_format($stats['total']) ?> คน</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card p-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success me-3">
                        <i class="bi bi-heart-pulse-fill fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small">รอดชีวิต (Survived)</div>
                        <h4 class="fw-bold text-success mb-0"><?= number_format($stats['survived']) ?> คน</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card p-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3 text-danger me-3">
                        <i class="bi bi-x-circle-fill fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small">เสียชีวิต (Died)</div>
                        <h4 class="fw-bold text-danger mb-0"><?= number_format($stats['died']) ?> คน</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card p-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3 text-info me-3">
                        <i class="bi bi-pie-chart-fill fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small">อัตราการรอดชีวิต</div>
                        <h4 class="fw-bold text-info mb-0"><?= $stats['rate'] ?>%</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="show_data.php" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-secondary">ค้นหา (ชื่อผู้โดยสาร, ตั๋ว, ห้องพัก)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="พิมพ์ชื่อผู้โดยสาร..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-secondary">ชั้นที่นั่ง (Class)</label>
                    <select name="pclass" class="form-select">
                        <option value="">-- ทั้งหมดทุกชั้น --</option>
                        <option value="1" <?= $pclass === '1' ? 'selected' : '' ?>>1st Class (ชั้น 1)</option>
                        <option value="2" <?= $pclass === '2' ? 'selected' : '' ?>>2nd Class (ชั้น 2)</option>
                        <option value="3" <?= $pclass === '3' ? 'selected' : '' ?>>3rd Class (ชั้น 3)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-secondary">สถานะการรอดชีวิต</label>
                    <select name="survived" class="form-select">
                        <option value="">-- ทั้งหมด --</option>
                        <option value="1" <?= $survived === '1' ? 'selected' : '' ?>>รอดชีวิต (Survived)</option>
                        <option value="0" <?= $survived === '0' ? 'selected' : '' ?>>เสียชีวิต (Died)</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="bi bi-funnel-fill me-1"></i> กรอง
                    </button>
                    <?php if ($search !== '' || $pclass !== '' || $survived !== ''): ?>
                        <a href="show_data.php" class="btn btn-outline-secondary" title="ล้างตัวกรอง">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Section -->
    <div class="table-container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-list-ul me-2"></i>รายการข้อมูลผู้โดยสาร 
                <span class="badge bg-light text-dark border ms-2">พบ <?= number_format($totalRecords) ?> รายการ</span>
            </h5>
            <span class="text-muted small">
                หน้า <strong><?= $page ?></strong> จาก <strong><?= $totalPages ?></strong> หน้า
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 70px;">ID</th>
                        <th>ชื่อ-นามสกุลผู้โดยสาร</th>
                        <th class="text-center">เพศ</th>
                        <th class="text-center">อายุ</th>
                        <th class="text-center">ชั้นที่นั่ง</th>
                        <th class="text-center">ตั๋ว</th>
                        <th class="text-end">ค่าโดยสาร ($)</th>
                        <th class="text-center">ห้องพัก</th>
                        <th class="text-center">ท่าเรือ</th>
                        <th class="text-center">สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($passengers) > 0): ?>
                        <?php foreach ($passengers as $row): ?>
                            <tr>
                                <td class="text-center fw-semibold text-secondary">
                                    #<?= htmlspecialchars($row['PassengerId'] ?? '') ?>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($row['Name'] ?? '-') ?></div>
                                    <div class="text-muted small">ญาติ/พี่น้อง: <?= htmlspecialchars($row['SibSp'] ?? 0) ?> | บิดามารดา/บุตร: <?= htmlspecialchars($row['Parch'] ?? 0) ?></div>
                                </td>
                                <td class="text-center">
                                    <?php if (strtolower($row['Sex'] ?? '') === 'female'): ?>
                                        <span class="badge badge-female"><i class="bi bi-gender-female"></i> หญิง</span>
                                    <?php else: ?>
                                        <span class="badge badge-male"><i class="bi bi-gender-male"></i> ชาย</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?= !empty($row['Age']) ? htmlspecialchars($row['Age']) . ' ปี' : '<span class="text-muted">-</span>' ?>
                                </td>
                                <td class="text-center">
                                    <?php if (($row['Pclass'] ?? '') == 1): ?>
                                        <span class="badge bg-warning text-dark"><i class="bi bi-star-fill"></i> ชั้น 1</span>
                                    <?php elseif (($row['Pclass'] ?? '') == 2): ?>
                                        <span class="badge bg-info text-dark">ชั้น 2</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">ชั้น 3</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <code><?= htmlspecialchars($row['Ticket'] ?? '-') ?></code>
                                </td>
                                <td class="text-end fw-semibold">
                                    <?= isset($row['Fare']) ? number_format((float)$row['Fare'], 2) : '0.00' ?>
                                </td>
                                <td class="text-center">
                                    <?= !empty($row['Cabin']) ? '<span class="badge bg-light text-dark border">' . htmlspecialchars($row['Cabin']) . '</span>' : '<span class="text-muted">-</span>' ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                        $emb = strtoupper(trim($row['Embarked'] ?? ''));
                                        if ($emb === 'S') echo '<span title="Southampton">Southampton (S)</span>';
                                        elseif ($emb === 'C') echo '<span title="Cherbourg">Cherbourg (C)</span>';
                                        elseif ($emb === 'Q') echo '<span title="Queenstown">Queenstown (Q)</span>';
                                        else echo '<span class="text-muted">-</span>';
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php if (($row['Survived'] ?? '') == 1): ?>
                                        <span class="badge badge-survived px-3 py-2">
                                            <i class="bi bi-check-circle me-1"></i> รอดชีวิต
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-died px-3 py-2">
                                            <i class="bi bi-x-circle me-1"></i> เสียชีวิต
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                ไม่พบข้อมูลที่ตรงตามเงื่อนไข
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <?php
                // Preserve filter query params in pagination links
                $queryParams = $_GET;
                unset($queryParams['page']);
                $baseQuery = http_build_query($queryParams);
                $urlPrefix = 'show_data.php?' . ($baseQuery ? $baseQuery . '&' : '') . 'page=';
            ?>
            <nav class="d-flex justify-content-center mt-4">
                <ul class="pagination">
                    <!-- First & Prev -->
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $urlPrefix ?>1">&laquo; แรกสุด</a>
                    </li>
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $urlPrefix . ($page - 1) ?>">ก่อนหน้า</a>
                    </li>

                    <!-- Page Number Window -->
                    <?php
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);
                        for ($i = $startPage; $i <= $endPage; $i++):
                    ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link" href="<?= $urlPrefix . $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <!-- Next & Last -->
                    <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $urlPrefix . ($page + 1) ?>">ถัดไป</a>
                    </li>
                    <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $urlPrefix . $totalPages ?>">ท้ายสุด &raquo;</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>

    </div>

    <!-- Footer -->
    <div class="text-center text-muted small mt-4">
        &copy; <?= date('Y') ?> Titanic Data Explorer | Powered by PHP PDO & Nginx Docker
    </div>

</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>