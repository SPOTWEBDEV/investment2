<?php
include("../../server/connection.php");

// Handle delete or toggle actions
if (isset($_GET['action'], $_GET['id'])) {
    $plan_id = (int)$_GET['id'];
    $action = $_GET['action'];

    if ($action === "delete") {
        $stmt = mysqli_prepare($connection, "DELETE FROM investment_plans WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $plan_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: ./"); // refresh
        exit;
    }

    if ($action === "toggle") {
        // Get current status
        $stmt = mysqli_prepare($connection, "SELECT status FROM investment_plans WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $plan_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $plan = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($plan) {
            $new_status = ($plan['status'] === 'active') ? 'unavailable' : 'available';
            $stmt = mysqli_prepare($connection, "UPDATE investment_plans SET status = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "si", $new_status, $plan_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        header("Location: ./"); // refresh
        exit;
    }
}

// Fetch all investment plans
$sql = "
    SELECT 
        id, plan_name, price, duration, profit_per_day, total_profit, status, created_at
    FROM investment_plans
    ORDER BY id DESC
";

$stmt = mysqli_prepare($connection, $sql);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($sitename) ?> | Investment Plans</title>
    <link rel="icon" type="image/png" sizes="16x16" href="<?= $domain ?>/images/favicon.png">
    <link rel="stylesheet" href="<?= $domain ?>/css/style.css">
    <link rel="stylesheet" href="<?= $domain ?>/vendor/toastr/toastr.min.css">
</head>

<body class="dashboard">

<div id="main-wrapper">
    <!-- HEADER -->
    <?php include("../include/nav.php") ?>
    <!-- SIDENAV -->
    <?php include("../include/sidenav.php") ?>

    <div class="content-body">
        <div class="container">

            <div class="row">
                <div class="col-12">
                    <div class="page-title">
                        <div class="row align-items-center justify-content-between">
                            <div class="col-xl-4">
                                <div class="page-title-content">
                                    <h3>Investment Plans</h3>
                                    <p class="mb-2"><?= $sitename ?> Management</p>
                                </div>
                            </div>
                            <div class="col-auto">
                                <a href="./add/"><button class="btn btn-primary mr-2">Add Investment Plan</button></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABLE -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Plan Name</th>
                                    <th>Duration (Days)</th>
                                    <th>Price</th>
                                    <th>Daily Profit</th>
                                    <th>Total Profit</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (mysqli_num_rows($result) > 0): $sn = 0; ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)): $sn++; ?>
                                        <tr>
                                            <td><?= $sn ?></td>
                                            <td><?= htmlspecialchars($row['plan_name']) ?></td>
                                            <td><?= (int)$row['duration'] ?></td>
                                            <td>₦<?= number_format($row['price'], 2) ?></td>
                                            <td>₦<?= number_format($row['profit_per_day'], 2) ?></td>
                                            <td>₦<?= number_format($row['total_profit'], 2) ?></td>
                                            <td>
                                                <span class="badge <?= $row['status'] === 'available' ? 'bg-success' : 'bg-warning' ?>">
                                                    <?= ucfirst($row['status']) ?>
                                                </span>
                                            </td>
                                            <td><?= date("Y-m-d H:i:s", strtotime($row['created_at'])) ?></td>
                                            <td>
                                                <div class="d-flex g-3">
                                                    <a href="?action=toggle&id=<?= $row['id'] ?>" class="btn btn-sm <?= $row['status'] === 'available' ? 'btn-warning' : 'btn-success' ?>">
                                                    <?= $row['status'] === 'available' ? 'Deactivate' : 'Activate' ?>
                                                </a>
                                                <a href="?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this plan?')" class="btn btn-sm btn-danger ml-2">
                                                    Delete
                                                </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No investment plans found</td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<script src="<?= $domain ?>/vendor/jquery/jquery.min.js"></script>
<script src="<?= $domain ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= $domain ?>/js/scripts.js"></script>
</body>
</html>
