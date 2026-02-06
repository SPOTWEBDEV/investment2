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

        <div class="content-body">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="page-title">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-xl-4">
                                    <div class="page-title-content">
                                        <h3>Withdrawal History</h3>
                                        <p class="mb-2">Welcome To <?= $sitename ?> Management</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xxl-12 col-xl-12">

                        <?php
                        $limit  = 10;
                        $page   = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
                        $offset = ($page - 1) * $limit;

                        // COUNT TOTAL WITHDRAWALS
                        $count_sql = "SELECT COUNT(*) AS total FROM withdrawals WHERE user_id = ?";
                        $count_stmt = mysqli_prepare($connection, $count_sql);
                        mysqli_stmt_bind_param($count_stmt, "i", $user_id);
                        mysqli_stmt_execute($count_stmt);
                        $count_result = mysqli_stmt_get_result($count_stmt);
                        $total_row = mysqli_fetch_assoc($count_result);
                        $total_records = (int)($total_row['total'] ?? 0);
                        $total_pages = (int) ceil($total_records / $limit);
                        mysqli_stmt_close($count_stmt);

                        // FETCH WITHDRAWAL HISTORY (using users table too)
                        // NOTE: If your column name is "date" keep it, if it's "created_at" change it.
                        $sql = "
                            SELECT 
                            withdrawals.id,
                                withdrawals.amount,
                                withdrawals.which_account,
                                withdrawals.status,
                                withdrawals.date,
                                users.fullname
                            FROM withdrawals, users
                            WHERE withdrawals.user_id = users.id
                            ORDER BY withdrawals.id DESC
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
                                                <th>ACCOUNT</th>
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
                                                        <td><?= htmlspecialchars($row['which_account']) ?></td>
                                                        <td>$<?= number_format((float)$row['amount'], 2) ?></td>
                                                        <td><?= !empty($row['date']) ? date("Y-m-d", strtotime($row['date'])) : '-' ?></td>
                                                        <td>
                                                            <span class="badge 
                                                                <?php
                                                                    if ($row['status'] === 'approved' || $row['status'] === 'settled') echo 'bg-success';
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
                                                    <td colspan="6" class="text-center">No withdrawal history found</td>
                                                </tr>
                                            <?php endif; ?>

                                        </tbody>
                                    </table>
                                </div>

                                <?php if ($total_pages > 1): ?>
                                    <nav class="mt-3">
                                        <ul class="pagination">
                                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                                </li>
                                            <?php endfor; ?>
                                        </ul>
                                    </nav>
                                <?php endif; ?>

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
