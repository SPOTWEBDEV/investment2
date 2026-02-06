<?php
include("../../../server/connection.php");


$id = mysqli_real_escape_string($connection, $_GET['id']);

$sql = "
    SELECT 
        deposits.id,
        deposits.user_id,
        deposits.type_id,
        deposits.amount,
        deposits.status,
        deposits.date,

        users.fullname AS user_fullname,
        users.id AS user_id,
        users.email AS user_email,

        payment_account.type AS account_type,
        payment_account.routing_number,
        payment_account.account_number,
        payment_account.bank_name,
        payment_account.fullname AS account_fullname,
        payment_account.network,
        payment_account.wallet_address,
        payment_account.label
    FROM deposits
    INNER JOIN users ON users.id = deposits.user_id
    INNER JOIN payment_account ON payment_account.id = deposits.type_id
    WHERE deposits.id = '$id'
";

$query = $connection->query($sql);



if (isset($_GET['approve_deposit'])) {
    $deposit_id = mysqli_real_escape_string($connection, $_GET['approve_deposit']);
    $amount = mysqli_real_escape_string($connection, $_GET['amount']);
    $user_id = mysqli_real_escape_string($connection, $_GET['user_id']);

    $approve_sql = "UPDATE deposits SET status = 'approved' WHERE id = $deposit_id";
    $connection->query($approve_sql);
    $query = $connection->query("UPDATE users SET balance = balance + $amount WHERE id = $user_id");

    echo "<script>alert('Deposit approved successfully.'); window.location.href='./index.php?id=$deposit_id';</script>";
    exit();
}


if (isset($_GET['decline_deposit'])) {
    $deposit_id = mysqli_real_escape_string($connection, $_GET['decline_deposit']);
    $amount = mysqli_real_escape_string($connection, $_GET['amount']);
    $user_id = mysqli_real_escape_string($connection, $_GET['user_id']);

    $decline_sql = "UPDATE deposits SET status = 'declined' WHERE id = $deposit_id";
    $connection->query($decline_sql);

    echo "<script>alert('Deposit declined successfully.'); window.location.href='./index.php?id=$deposit_id';</script>";
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


                <?php if ($query->num_rows > 0) {
                    $deposit = $query->fetch_assoc();
                ?>
                    <div class="col-12">

                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    Deposit Details for <?php echo $deposit['user_fullname']; ?>
                                </h4>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tbody>

                                            <tr>
                                                <td>Account User</td>
                                                <td><?php echo $deposit['user_fullname']; ?></td>
                                            </tr>
                                            <tr>
                                                <td>User Email</td>
                                                <td><?php echo $deposit['user_email']; ?></td>
                                            </tr>

                                            <!-- Deposit Amount -->
                                            <tr>
                                                <td><span class="text-primary">Deposit Amount:</span></td>
                                                <td><span class="text-primary">$<?php echo number_format($deposit['amount'], 2); ?></span></td>
                                            </tr>

                                            <!-- Status -->
                                            <tr>
                                                <td>Status:</td>
                                                <td>
                                                    <span class="badge 
                                    <?php
                                    echo ($deposit['status'] == 'pending') ? 'bg-warning' : (($deposit['status'] == 'approved') ? 'bg-success' : 'bg-danger');
                                    ?>">
                                                        <?php echo ucfirst($deposit['status']); ?>
                                                    </span>
                                                </td>
                                            </tr>

                                            <!-- Date -->
                                            <tr>
                                                <td>Date:</td>
                                                <td><?php echo $deposit['date']; ?></td>
                                            </tr>

                                            <!-- Payment Account Used -->
                                            <tr>
                                                <td><strong>Payment Method Used:</strong></td>
                                                <td><?php echo ucfirst($deposit['account_type']); ?></td>
                                            </tr>

                                            <!-- Bank Details (if bank) -->
                                            <?php if ($deposit['bank_name'] != "") { ?>
                                                <tr>
                                                    <td>Bank Name:</td>
                                                    <td><?php echo $deposit['bank_name']; ?></td>
                                                </tr>

                                                <tr>
                                                    <td>Account Number:</td>
                                                    <td><?php echo $deposit['account_number']; ?></td>
                                                </tr>

                                                <tr>
                                                    <td>Account Name:</td>
                                                    <td><?php echo $deposit['account_fullname']; ?></td>
                                                </tr>

                                                <tr>
                                                    <td>Routing Number:</td>
                                                    <td><?php echo $deposit['routing_number']; ?></td>
                                                </tr>
                                            <?php } ?>

                                            <!-- Crypto Wallet (if crypto) -->
                                            <?php if ($deposit['wallet_address'] != "") { ?>
                                                <tr>
                                                    <td>Network:</td>
                                                    <td><?php echo $deposit['network']; ?></td>
                                                </tr>

                                                <tr>
                                                    <td>Wallet Address:</td>
                                                    <td><?php echo $deposit['wallet_address']; ?></td>
                                                </tr>

                                                <tr>
                                                    <td>Label:</td>
                                                    <td><?php echo $deposit['label']; ?></td>
                                                </tr>
                                            <?php } ?>
                                            <tr>
                                                <td>Action</td>
                                                <td>
                                                    <a href="<?php echo $domain ?>/admin/deposits/">
                                                        <button class="btn btn-primary btn-sm">Back to Deposits</button>
                                                    </a>
                                                    <a href="?approve_deposit=<?= $deposit['id'] ?>&amount=<?= $deposit['amount'] ?>&user_id=<?= $deposit['user_id'] ?>">
                                                        <button class="btn btn-success btn-sm approve-deposit">Approve</button>
                                                    </a>
                                                    <a href="?decline_deposit=<?= $deposit['id'] ?>&amount=<?= $deposit['amount'] ?>&user_id=<?= $deposit['user_id'] ?>">
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

                <?php } else { ?>
                    <div class='alert alert-danger'>Deposit not found</div>
                <?php } ?>

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