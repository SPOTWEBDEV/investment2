<?php
include("../../server/connection.php");
include("../../server/auth/client.php");


?>


<!DOCTYPE html>



<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $sitename ?> | Investment-History </title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $domain ?>/images/favicon.png">
    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="<?php echo $domain ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo $domain ?>/vendor/toastr/toastr.min.css">
</head>

<body class="dashboard">

    <div id="main-wrapper">
        <!-- nav -->
        <?php include("../../include/header.php") ?>

        <!-- sidenav -->
        <?php include("../../include/sidenav.php") ?>
        <div class="content-body">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="page-title">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-xl-4">
                                    <div class="page-title-content">
                                        <h3>Investment History</h3>
                                        <p class="mb-2">Welcome To <?= $sitename ?> Management</p>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <a href="../"><button class="btn btn-primary mr-2">Make Investment</button></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-xxl-12 col-xl-12">

                        <?php



                        $sql = "
    SELECT 
        plan_name,
        duration,
        amount,
        profit_per_day,
        profit,
        created_at,
        status
    FROM investments
    WHERE user_id = ?
    ORDER BY id DESC
";

                        $stmt = mysqli_prepare($connection, $sql);
                        mysqli_stmt_bind_param($stmt, "i", $user_id);
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
                                                <th>PLAN NAME</th>
                                                <th>PLAN DURNATION (DAYS)</th>
                                                <th>AMOUNT</th>
                                                <th>PROFIT PER DAY</th>
                                                <th>TOTAL PROFIT</th>
                                                <th>DATE</th>
                                                <th>STATUS</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php if (mysqli_num_rows($result) > 0): $count = 0; ?>
                                                <?php while ($row = mysqli_fetch_assoc($result)): $count++; ?>
                                                    <tr>
                                                        <td><?= $count ?></td>
                                                        <td><?= htmlspecialchars($row['plan_name']) ?></td>
                                                        <td><?= htmlspecialchars($row['duration']) ?></td>
                                                        <td>₦<?= number_format($row['amount'], 2) ?></td>
                                                        <td>₦<?= htmlspecialchars(number_format($row['profit_per_day'],2)) ?></td>
                                                        <td>₦<?= htmlspecialchars(number_format($row['profit'],2)) ?></td>

                                                        <td><?= date("Y-m-d", strtotime($row['created_at'])) ?></td>
                                                        <td>
                                                            <span class="badge 
                                                    <?php
                                                    if ($row['status'] === 'approved') echo 'bg-success';
                                                    elseif ($row['status'] === 'declined') echo 'bg-danger';
                                                    else echo 'bg-warning';
                                                    ?>">
                                                                <?= ucfirst($row['status']) ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="7" class="text-center">No Investment history found</td>
                                                </tr>
                                            <?php endif; ?>

                                        </tbody>
                                    </table>
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
                        </div> -->

                    </div>
                    <script src="<?php echo $domain ?>/vendor/jquery/jquery.min.js"></script>
                    <script src="<?php echo $domain ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
                    <!--  -->
                    <!--  -->
                    <script src="<?php echo $domain ?>/js/scripts.js"></script>
</body>

</html>