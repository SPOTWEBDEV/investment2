<?php
include("../../../server/connection.php");






if(isset($_GET['approve_withdraw'])) {
    $withdraw_id = mysqli_real_escape_string($connection, $_GET['approve_withdraw']);
    $amount = mysqli_real_escape_string($connection, $_GET['amount']);
    $user_id = mysqli_real_escape_string($connection, $_GET['user_id']);

    // Update withdrawal status to approved
    $update_sql = "UPDATE withdrawals SET status='approved' WHERE id='$withdraw_id'";
    mysqli_query($connection, $update_sql);

    // Optionally, you can also update the user's balance here

    echo "<script>alert('Withdrawal approved successfully.'); window.location.href='./index.php?id=$withdraw_id';</script>";
    exit();
}


if(isset($_GET['decline_withdraw'])) {
    $withdraw_id = mysqli_real_escape_string($connection, $_GET['decline_withdraw']);
    $amount = mysqli_real_escape_string($connection, $_GET['amount']);
    $user_id = mysqli_real_escape_string($connection, $_GET['user_id']);

    // Update withdrawal status to declined
    $update_sql = "UPDATE withdrawals SET status='failed' WHERE id='$withdraw_id'";
    mysqli_query($connection, $update_sql);

    // Optionally, you can also update the user's balance here

    echo "<script>alert('Withdrawal declined successfully.'); window.location.href='./index.php?id=$withdraw_id';</script>";
    exit();
}



?>


<!DOCTYPE html>



<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $sitename ?> | Transfer-History </title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $domain ?>/images/favicon.png">
    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="<?php echo $domain ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo $domain ?>/vendor/toastr/toastr.min.css">
</head>

<body class="dashboard">

    <div id="main-wrapper">
        <!-- header -->
        <?php include("../../include/nav.php") ?>

        <!-- side nav -->

        <?php include("../../include/sidenav.php") ?>
        <div class="content-body">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="page-title">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-xl-4">
                                    <div class="page-title-content">
                                        <h3>Deposit Details</h3>
                                        <p class="mb-2">Welcome To <?= $sitename ?> Management</p>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="breadcrumbs"><a href="<?php echo $domain  ?>/admin/dashboard/">Home </a>
                                        <span><i class="fi fi-rr-angle-small-right"></i></span>
                                        <a href="<?php echo $domain  ?>/admin/deposit/details">Deposit Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-12">

                    <?php

                    // Validate ID
                    if (!isset($_GET['id'])) {
                        echo "<div class='alert alert-danger'>Invalid withdrawal ID</div>";
                        exit;
                    }

                    $id = mysqli_real_escape_string($connection, $_GET['id']);

                    // Fetch withdrawal + user
                    $sql = "
    SELECT 
        withdrawals.*, 
        users.fullname ,
        users.id AS user_id
    FROM withdrawals
    INNER JOIN users ON users.id = withdrawals.user_id
    WHERE withdrawals.id = '$id'
    LIMIT 1
";

                    $query = mysqli_query($connection, $sql);

                    if ($query->num_rows > 0) {
                        $withdraw = mysqli_fetch_assoc($query);
                    } else {
                        echo "<div class='alert alert-danger'>Withdrawal not found</div>";
                        exit;
                    }

                    ?>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                Withdrawal Details for <?= htmlspecialchars($withdraw['fullname']) ?>
                            </h4>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>

                                        <tr>
                                            <td><strong>Amount:</strong></td>
                                            <td>$<?= htmlspecialchars($withdraw['amount']) ?></td>
                                        </tr>

                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="
                                    text-white px-4 py-2 rounded-lg
                                    <?php
                                    echo ($withdraw['status'] == 'pending') ? 'bg-warning' : (($withdraw['status'] == 'approved') ? 'bg-success' : 'bg-danger');
                                    ?>
                                ">
                                                    <?= htmlspecialchars($withdraw['status']) ?>
                                                </span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><strong>Date:</strong></td>
                                            <td><?= htmlspecialchars($withdraw['date']) ?></td>
                                        </tr>

                                        <tr>
                                            <td><strong>Bank Name:</strong></td>
                                            <td><?= htmlspecialchars($withdraw['bank_name']) ?></td>
                                        </tr>

                                        <tr>
                                            <td><strong>Account Number:</strong></td>
                                            <td><?= htmlspecialchars($withdraw['account_number']) ?></td>
                                        </tr>

                                        <tr>
                                            <td><strong>Account Name:</strong></td>
                                            <td><?= htmlspecialchars($withdraw['account_name']) ?></td>
                                        </tr>
                                        <tr>
                                                <td>Action</td>
                                                <td>
                                                    <a href="<?php echo $domain ?>/admin/withdrawal/">
                                                        <button class="btn btn-primary btn-sm">Back to Withdrawal</button>
                                                    </a>
                                                    <a href="?approve_withdraw=<?= $withdraw['id'] ?>&amount=<?= $withdraw['amount'] ?>&user_id=<?= $withdraw['user_id'] ?>">
                                                        <button class="btn btn-success btn-sm approve-deposit">Approve</button>
                                                    </a>
                                                    <a href="?decline_withdraw=<?= $withdraw['id'] ?>&amount=<?= $withdraw['amount'] ?>&user_id=<?= $withdraw['user_id'] ?>">
                                                        <button class="btn btn-danger btn-sm decline-deposit">Decline</button>
                                                    </a>
                                                </td>

                                            </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>


            </div>
        </div>
    </div>
    <script src="<?php echo $domain ?>/vendor/jquery/jquery.min.js"></script>
    <script src="<?php echo $domain ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!--  -->
    <!--  -->
    <script src="<?php echo $domain ?>/js/scripts.js"></script>
</body>

</html>