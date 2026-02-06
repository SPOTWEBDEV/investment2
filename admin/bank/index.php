<?php
include("../../server/connection.php");

$sql = "SELECT * FROM payment_account";
$stmt = mysqli_prepare($connection, $sql);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);


if(isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $delete_sql = "DELETE FROM payment_account WHERE id = ?";
    $delete_stmt = mysqli_prepare($connection, $delete_sql);
    mysqli_stmt_bind_param($delete_stmt, "i", $id);
    mysqli_stmt_execute($delete_stmt);

    echo "<script>alert('Payment account deleted successfully.'); window.location.href='index.php';</script>";
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ekash : Personal Finance Management Admin Dashboard HTML Template</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $domain ?>/images/favicon.png">
    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="<?php echo $domain ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo $domain ?>/vendor/toastr/toastr.min.css">
</head>

<body class="dashboard">
    <div id="main-wrapper">
        <?php include("../include/nav.php") ?>
        <?php include("../include/sidenav.php") ?>
        <div class="content-body">
            <div class="verification">
                <div class="container">
                    <div class="row justify-content-center h-100 align-items-center">
                        <div class="col-12">

                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Your Payment Accounts</h4>
                                </div>

                               
                                <div class="card-body gap-2">

                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>

                                        <?php if ($row['type'] === 'bank'): ?>
                                            <!-- BANK ACCOUNT CARD -->
                                            <div class="verify-content mb-3">
                                                <div class="d-flex align-items-center">
                                                    <span class="me-3 icon-circle bg-primary text-white">
                                                        <i class="fi fi-rs-bank"></i>
                                                    </span>

                                                    <div class="primary-number">
                                                        <h5 class="mb-0"><?php echo htmlspecialchars($row['fullname']); ?></h5>
                                                        <small>Account ****<?php echo substr($row['account_number'], -4); ?></small>
                                                        <br>
                                                        <span class="text-success">Verified</span>
                                                    </div>
                                                </div>

                                                <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger">
                                                    Delete
                                                </a>
                                            </div>

                                            <hr class="border opacity-1">

                                        <?php elseif ($row['type'] === 'crypto'): ?>
                                            <!-- CRYPTO WALLET CARD -->
                                            <div class="verify-content mb-3">
                                                <div class="d-flex align-items-center">
                                                    <span class="me-3 icon-circle bg-primary text-white">
                                                        <i class="fi fi-rr-credit-card"></i>
                                                    </span>

                                                    <div class="primary-number">
                                                        <h5 class="mb-0"><?php echo htmlspecialchars($row['network']); ?> Wallet</h5>
                                                        <small>Address: ****<?php echo substr($row['wallet_address'], -6); ?></small>
                                                        <br>
                                                        <span class="text-success">Verified</span>
                                                    </div>
                                                </div>

                                                <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger">
                                                    Delete
                                                </a>
                                            </div>

                                            <hr class="border opacity-1">

                                        <?php endif; ?>

                                    <?php endwhile; ?>


                                    <!-- Add New Buttons -->
                                    <div class="mt-5">
                                        <a href="./add?type=bank" class="btn btn-primary m-2">Add New Bank</a>
                                        <a href="./add/?type=crypto" class="btn btn-primary m-2">Add New Crypto</a>
                                    </div>

                                </div>
                                

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
                                <a href="add-bank.html#">Ekash</a> I All Rights Reserved
                            </p>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="footer-social">
                            <ul>
                                <li><a href="add-bank.html#"><i class="fi fi-brands-facebook"></i></a></li>
                                <li><a href="add-bank.html#"><i class="fi fi-brands-twitter"></i></a></li>
                                <li><a href="add-bank.html#"><i class="fi fi-brands-linkedin"></i></a></li>
                                <li><a href="add-bank.html#"><i class="fi fi-brands-youtube"></i></a></li>
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