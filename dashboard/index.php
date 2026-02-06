<?php
include("../server/connection.php");
if (!isset($_SESSION['user_id'])) {
    header("location: {$domain}/auth/sign_in/");
    exit;
}


$id = $_SESSION['user_id'];

// default values (avoid errors)
$fullname = "";
$balance = 0;
$loan_balance = 0;
$crypto_balance = 0;
$virtual_card_balance = 0;
$limit = 0;

$sql = "SELECT fullname, balance
        FROM users
        WHERE id = ? LIMIT 1";
$stmt = mysqli_prepare($connection, $sql);

if (!$stmt) {
    die("Query error: " . mysqli_error($connection));
}

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
if ($result && mysqli_num_rows($result) === 1) {
    $user = mysqli_fetch_assoc($result);

    $fullname = $user['fullname'] ?? "";
    $balance = (float)($user['balance'] ?? 0);
    $loan_balance = (float)($user['loan_balance'] ?? 0);
    $crypto_balance = (float)($user['crypto_balance'] ?? 0);
    $virtual_card_balance = (float)($user['virtual_card_balance'] ?? 0);
    $limit = (float)($user['limit'] ?? 0);
} else {
    // session user_id not found in DB
    session_destroy();
    header("Location: {$domain}/auth/sign_in/");
    exit;
}

mysqli_stmt_close($stmt);

// helper to format money
function money($amount)
{
    return number_format((float)$amount, 2);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $sitename ?> | dashboard</title>
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $domain ?>/images/favicon.png">
    <link rel="stylesheet" href="<?php echo $domain ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo $domain ?>/vendor/toastr/toastr.min.css">
</head>

<body class="dashboard">

    <div id="main-wrapper">
        <!-- nav bar -->
        <?php include("../include/header.php") ?>

        <!-- side nav -->
        <?php include("../include/sidenav.php") ?>

        <div class="content-body">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="page-title">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-xl-4">
                                    <div class="page-title-content">
                                        <h3>Dashboard</h3>
                                        <p class="mb-2">Welcome <?= $sitename ?> Finance Management</p>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="breadcrumbs">
                                        <a href="<?php echo  $domain ?>/dashboard/ ">Dashboard</a>
                                        <span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="wallet-tab">
                    <div class="row g-0">
                        <div class="col-xl-3">
                            <div class="nav d-block">
                                <div class="row">
                                    <div class="col-xl-12 col-md-6">
                                        <div class="wallet-nav">
                                            <div class="wallet-nav-icon">
                                                <span><i class="fi fi-rr-bank"></i></span>
                                            </div>
                                            <div class="wallet-nav-text">
                                                <h3>Balance</h3>
                                                <p>$<?= money($balance) ?></p>

                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                </div>
                            </div>


                        </div>



                        <div class="col-xl-9">
                            <div class="tab-content wallet-tab-content">

                                <!-- ================= BALANCE TAB ================= -->
                                <div class="tab-pane show active" id="a1">
                                    <div class="wallet-tab-title">
                                        <h3>Zentra Bank</h3>
                                    </div>

                                    <!--  Deposit / Withdraw / Transfer -->
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="row g-3">

                                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                            <a href="<?php echo $domain ?>/deposits/" class="d-block text-decoration-none">
                                                                <div class="stat-widget-1">
                                                                    <h6><i class="fi fi-rr-money-bill-wave me-2"></i>Deposit</h6>
                                                                    <p class="mb-0">Fund your wallet</p>
                                                                </div>
                                                            </a>
                                                        </div>

                                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                            <a href="<?php echo $domain ?>/withdrawal/" class="d-block text-decoration-none">
                                                                <div class="stat-widget-1">
                                                                    <h6> <i class="fi fi-rr-donate"> </i> Withdraw</h6>
                                                                    <p class="mb-0">Cash out funds</p>
                                                                </div>
                                                            </a>
                                                        </div>

                                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                                            <a href="<?php echo $domain ?>/transfer/" class="d-block text-decoration-none">
                                                                <div class="stat-widget-1">
                                                                    <h6><i class="fi fi-rr-exchange me-2"></i>Transfer</h6>
                                                                    <p class="mb-0">Send money</p>
                                                                </div>
                                                            </a>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--  END QUICK ACTIONS -->


                                </div>

                               



                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Transaction History</h4>
                            </div>
                            <div class="card-body">
                                <div class="transaction-table">
                                    <div class="table-responsive">
                                        <table class="table mb-0 table-responsive-sm">
                                            <thead>
                                                <tr>
                                                    <th>Id</th>
                                                    <th>Account Number</th>
                                                    <th>Bank</th>
                                                    <th>Narration</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php

                                                $query = $connection->query("SELECT * FROM deposits WHERE user_id=$id ORDER BY id DESC LIMIT 5");
                                                if ($query->num_rows > 0) {
                                                    while ($transaction = $query->fetch_assoc()) { ?>

                                                        <tr>
                                                            <td>1</td>
                                                            <td><?php echo $transaction['amount'] ?></td>
                                                            
                                                            <td>Payment for services</td>
                                                            <td>$<?php echo $transaction['amount']  ?></td>
                                                            <td>
                                                                <span class="badge text-white <?php
                                                                                                echo ($transaction['status'] == 'pending')
                                                                                                    ? 'bg-warning'
                                                                                                    : (($transaction['status'] == 'completed')
                                                                                                        ? 'bg-success'
                                                                                                        : 'bg-danger'); ?>">
                                                                    <?php echo ucfirst($transaction['status']); ?>
                                                                </span>
                                                            </td>

                                                        </tr>

                                                <?php }
                                                } else {
                                                    echo '<tr><td class="text-danger">No transaction history found.</td></tr>';
                                                }

                                                ?>


                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- <div class="footer">
        <div class="container">
            <div class="row">
                <div class="col-xl-6">
                    <div class="copyright">
                        <p>© Copyright
                            <script>
                                var CurrentYear = new Date().getFullYear()
                                document.write(CurrentYear)
                            </script>
                            <a href="wallets.html#"></a> I All Rights Reserved
                        </p>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="footer-social">
                        <ul>
                            <li><a href="wallets.html#"><i class="fi fi-brands-facebook"></i></a></li>
                            <li><a href="wallets.html#"><i class="fi fi-brands-twitter"></i></a></li>
                            <li><a href="wallets.html#"><i class="fi fi-brands-linkedin"></i></a></li>
                            <li><a href="wallets.html#"><i class="fi fi-brands-youtube"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

    <script src="<?php echo $domain ?>/vendor/jquery/jquery.min.js"></script>
    <script src="<?php echo $domain ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo $domain ?>/vendor/chartjs/chartjs.js"></script>
    <script src="<?php echo $domain ?>/js/plugins/chartjs-line-balance-overtime.js"></script>
    <script src="<?php echo $domain ?>/js/scripts.js"></script>
</body>

</html>