<?php
include("../../../server/connection.php");

// Handle form submission
$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['add_plan'])) {
    $plan_name      = trim($_POST['plan_name'] ?? '');
    $price          = (float)($_POST['price'] ?? 0);
    $duration       = (int)($_POST['duration'] ?? 0);
    $profit_per_day = (float)($_POST['profit_per_day'] ?? 0);
    $status         = ($_POST['status'] ?? 'unavailable');

    // Simple validation
    if (empty($plan_name)) $error = "Plan name is required.";
    elseif ($price <= 0) $error = "Price must be greater than 0.";
    elseif ($duration <= 0) $error = "Duration must be greater than 0.";
    elseif ($profit_per_day < 0) $error = "Profit per day cannot be negative.";
    elseif (!in_array($status, ['active', 'inactive'])) $error = "Invalid status.";

    if (empty($error)) {
        // Calculate total profit
        $total_profit = $profit_per_day * $duration;

        $stmt = mysqli_prepare($connection, "
            INSERT INTO investment_plans 
            (plan_name, price, duration, profit_per_day, total_profit, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        mysqli_stmt_bind_param(
            $stmt,
            "siddds",
            $plan_name,
            $price,
            $duration,
            $profit_per_day,
            $total_profit,
            $status
        );

        if (mysqli_stmt_execute($stmt)) {
            $success = "Investment plan added successfully!";
            echo "<script>setTimeout(()=>{window.location.href='../'},1000)</script>";
        } else {
            $error = "Failed to add plan. Try again.";
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($sitename) ?> | Add Investment Plan</title>
    <link rel="icon" type="image/png" sizes="16x16" href="<?= $domain ?>/images/favicon.png">
    <link rel="stylesheet" href="<?= $domain ?>/css/style.css">
    <link rel="stylesheet" href="<?= $domain ?>/vendor/toastr/toastr.min.css">
</head>

<body class="dashboard">
    <div id="main-wrapper">
        <?php include("../../include/nav.php") ?>
        <?php include("../../include/sidenav.php") ?>

        <div class="content-body">
            <div class="container">
                <div class="row justify-content-center mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Add New Investment Plan</h4>
                            </div>
                            <div class="card-body">

                                <!-- Success / Error -->
                                <?php if (!empty($success)) : ?>
                                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($error)) : ?>
                                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                                <?php endif; ?>

                                <form method="POST">

                                    <div class="mb-3">
                                        <label>Plan Name</label>
                                        <input type="text" name="plan_name" class="form-control" placeholder="Starter Plan" required>
                                    </div>

                                    <div class="mb-3">
                                        <label>Price (₦)</label>
                                        <input type="number" step="0.01" name="price" class="form-control" placeholder="1000" required>
                                    </div>

                                    <div class="mb-3">
                                        <label>Duration (Days)</label>
                                        <input type="number" name="duration" class="form-control" placeholder="60" required>
                                    </div>

                                    <div class="mb-3">
                                        <label>Daily Profit (₦)</label>
                                        <input type="number" step="0.01" name="profit_per_day" class="form-control" placeholder="25" required>
                                    </div>

                                    <div class="mb-3">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option value="available">Active</option>
                                            <option value="unavailable">Inactive</option>
                                        </select>
                                    </div>

                                    <button type="submit" name="add_plan" class="btn btn-success w-100">Add Plan</button>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-xl-6">
                        <p>© <?= date('Y') ?> <?= htmlspecialchars($sitename) ?> | All Rights Reserved</p>
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
