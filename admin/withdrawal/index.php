<?php

include("../../server/connection.php");


?>


<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $sitename ?> | Withdrawal-History </title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $domain ?>/images/favicon.png">
    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="<?php echo $domain ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo $domain ?>/vendor/toastr/toastr.min.css">
</head>

<body class="dashboard">


    <div id="main-wrapper">
        <!-- header -->
        <?php include("../include/nav.php") ?>

        <?php include("../include/sidenav.php") ?>

       <?php


/* ============================
   COUNT WITHDRAWALS BY STATUS
============================ */
$statusCounts = [
    'pending' => 0,
    'approved' => 0,
    'failed' => 0
];

$countStatusSql = "
    SELECT status, COUNT(*) AS total
    FROM withdrawals
    GROUP BY status
";
$countStatusResult = mysqli_query($connection, $countStatusSql);

while ($row = mysqli_fetch_assoc($countStatusResult)) {
    $statusCounts[$row['status']] = $row['total'];
}

/* ============================
   FILTER BY STATUS
============================ */
$statusFilter = "";
$status = "";

if (!empty($_GET['status'])) {
    $status = $_GET['status'];
    $statusFilter = " AND withdrawals.status = ?";
}

/* ============================
   FETCH WITHDRAWALS
============================ */
$sql = "
    SELECT 
        withdrawals.id,
        withdrawals.amount,
        withdrawals.which_account,
        withdrawals.status,
        withdrawals.date,
        users.fullname
    FROM withdrawals
    JOIN users ON withdrawals.user_id = users.id
    WHERE 1=1
    $statusFilter
    ORDER BY withdrawals.id DESC
";

$stmt = mysqli_prepare($connection, $sql);

if (!empty($statusFilter)) {
    mysqli_stmt_bind_param($stmt, "s", $status);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="content-body">
    <div class="container">

        <!-- PAGE TITLE -->
        <div class="row">
            <div class="col-12">
                <div class="page-title">
                    <h3>Withdrawal History</h3>
                    <p class="mb-2">Welcome To <?= htmlspecialchars($sitename) ?> Management</p>
                </div>
            </div>
        </div>

        <!-- STATUS CARDS -->
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-md-4">
                            <div class="stat-widget-1">
                                <h6><i class="fi fi-rr-money-bill-wave me-2"></i>Pending Withdrawal</h6>
                                <p class="mb-0 text-lg"><?= $statusCounts['pending'] ?> </p>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="stat-widget-1">
                                <h6><i class="fi fi-rr-donate me-2"></i>Declined Withdrawal</h6>
                                <p class="mb-0 text-lg"><?= $statusCounts['failed'] ?> </p>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="stat-widget-1">
                                <h6><i class="fi fi-rr-exchange me-2"></i>Approved Withdrawal</h6>
                                <p class="mb-0 text-lg"><?= $statusCounts['approved'] ?> </p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- FILTER -->
        <div class="row mt-4">
            <div class="col-md-4">
                <form method="GET">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">All Withdrawals</option>
                        <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="approved" <?= $status == 'approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="failed" <?= $status == 'failed' ? 'selected' : '' ?>>Declined</option>
                    </select>
                </form>
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
                                    <th>S/N</th>
                                    <th>ACCOUNT HOLDER</th>
                                    <th>ACCOUNT</th>
                                    <th>AMOUNT</th>
                                    <th>DATE</th>
                                    <th>STATUS</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php if (mysqli_num_rows($result) > 0): $sn = 0; ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)): $sn++; ?>
                                        <tr>
                                            <td><?= $sn ?></td>
                                            <td><?= htmlspecialchars($row['fullname']) ?></td>
                                            <td><?= htmlspecialchars($row['which_account']) ?></td>
                                            <td>₦<?= number_format($row['amount'], 2) ?></td>
                                            <td><?= date("Y-m-d", strtotime($row['date'])) ?></td>
                                            <td>
                                                <span class="badge 
                                                    <?= $row['status'] == 'approved' ? 'bg-success' :
                                                        ($row['status'] == 'failed' ? 'bg-danger' : 'bg-warning') ?>">
                                                    <?= ucfirst($row['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="./details/?id=<?= $row['id'] ?>">
                                                    <span class="badge bg-info p-2 text-white">View Details</span>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No withdrawal records found</td>
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


        <div class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-xl-6">
                        <div class="copyright">
                            <p>© Copyright
                                <script>
                                    var CurrentYear = new Date().getFullYear()
                                    document.write(CurrentYear)
                                </script>
                                <a href="#"><?= $sitename ?></a> I All Rights Reserved
                            </p>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="footer-social">
                            <ul>
                                <li><a href="settings-api.html#"><i class="fi fi-brands-facebook"></i></a></li>
                                <li><a href="settings-api.html#"><i class="fi fi-brands-twitter"></i></a></li>
                                <li><a href="settings-api.html#"><i class="fi fi-brands-linkedin"></i></a></li>
                                <li><a href="settings-api.html#"><i class="fi fi-brands-youtube"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="<?php echo $domain ?>/vendor/jquery/jquery.min.js"></script>
    <script src="<?php echo $domain ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo $domain ?>/js/scripts.js"></script>
</body>

</html>