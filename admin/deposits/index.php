<?php
include("../../server/connection.php");

// if (!isset($_SESSION['user_id'])) {
//     header("location: ./signin.php");
//     exit;
// }

$user_id = $_SESSION['user_id'];
?>


<!DOCTYPE html>



<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $sitename ?> | Deposite-History </title>
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

        <!-- Side Nav -->
        <?php include("../include/sidenav.php") ?>


        <div class="content-body">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="page-title">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-xl-4">
                                    <div class="page-title-content">
                                        <h3>All Deposits</h3>
                                        <p class="mb-2">Welcome To <?= $sitename ?> Management</p>
                                    </div>
                                </div>
                                <div class="col-auto">
                                     <a href="./details/"><button class="btn btn-primary mr-2">View Deposit History</button></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-xxl-12 col-xl-12">

                        <?php
                        /* FETCH DEPOSIT HISTORY  */
                        $sql = "
                            SELECT 
                            deposits.id,
                                deposits.amount,
                                deposits.type_id,
                                deposits.status,
                                deposits.date,
                                users.fullname,
                                payment_account.type,
                                payment_account.account_number,
                                payment_account.bank_name,
                                payment_account.wallet_address
                            FROM deposits
                            INNER JOIN users ON deposits.user_id = users.id
                            INNER JOIN payment_account ON payment_account.id = deposits.type_id
                            ORDER BY deposits.id DESC
                        ";

                        $stmt = mysqli_prepare($connection, $sql);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        ?>

                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>S/N</th>
                                                <th>ACCOUNT HOLDER</th>
                                                <th>TYPE</th>
                                                <th>AMOUNT</th>
                                                <th>DATE</th>
                                                <th>STATUS</th>
                                                <th>ACTION</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php if (mysqli_num_rows($result) > 0): $count = 0; ?>
                                                <?php while ($row = mysqli_fetch_assoc($result)): $count++; ?>
                                                    <tr>
                                                        <td><?= $count ?></td>
                                                        <td><?= htmlspecialchars($row['fullname']) ?></td>
                                                        <td><?= htmlspecialchars($row['type']) ?></td>
                                                        <td>$<?= number_format($row['amount'], 2) ?></td>
                                                        <td><?= date("Y-m-d", strtotime($row['date'])) ?></td>
                                                        <td>
                                                            <span class="badge 
        <?php
                                                    if ($row['status'] === 'approved') echo 'bg-success';
                                                    elseif ($row['status'] === 'failed') echo 'bg-danger';
                                                    else echo 'bg-warning';
        ?>">
                                                                <?= ucfirst($row['status']) ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                           <a href="./details/?id=<?php echo $row['id'] ?>"> <span class="badge p-2 bg-info text-white">View Details</span></a>

                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="7" class="text-center">No deposit history found</td>
                                                </tr>
                                            <?php endif; ?>

                                        </tbody>
                                    </table>
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
                    <!--  -->
                    <!--  -->
                    <script src="<?php echo $domain ?>/js/scripts.js"></script>
</body>

</html>