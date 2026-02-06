<?php

include("../../server/connection.php");

$errors = [];
$success = "";

?>


<!DOCTYPE html>



<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $sitename ?> | loan </title>
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
                                        <h3>Loan</h3>
                                        <p class="mb-2">Welcome To <?= $sitename ?> Management</p>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <!-- <div class="breadcrumbs"><a href="settings-api.html#">Home </a>
                                        <span><i class="fi fi-rr-angle-small-right"></i></span>
                                        <a href="settings-api.html#">Api</a>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xxl-12 col-xl-12">

                  

                        <div class="row">
             
                            
                            <?php


                            $user_id = $_SESSION['user_id'];

                          
                            $limit = 10;
                            $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
                            $offset = ($page - 1) * $limit;

                          
                            $count_sql = "
    SELECT COUNT(*) AS total 
    FROM loan_requests 
    WHERE user_id = ?
";
                            $count_stmt = mysqli_prepare($connection, $count_sql);
                            mysqli_stmt_bind_param($count_stmt, "i", $user_id);
                            mysqli_stmt_execute($count_stmt);
                            $count_result = mysqli_stmt_get_result($count_stmt);
                            $total_row = mysqli_fetch_assoc($count_result);
                            $total_records = $total_row['total'];
                            $total_pages = ceil($total_records / $limit);


                            $sql = "
        SELECT 
        loan_requests.id,
        loan_requests.loan_amount,
        loan_requests.status,
        loan_requests.created_at,
        users.fullname
    FROM loan_requests, users
    WHERE loan_requests.user_id = users.id
    AND loan_requests.user_id = ?
    ORDER BY loan_requests.id DESC
    LIMIT ? OFFSET ?
";

                            $stmt = mysqli_prepare($connection, $sql);
                            mysqli_stmt_bind_param($stmt, "iii", $user_id, $limit, $offset);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            ?>


                            <div class="col-xl-12">
                                <h4 class="card-title mb-3">Loan History</h4>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive api-table">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>S/N</th>
                                                        <th>ACCOUNT HOLDER</th>
                                                        <th>AMOUNT</th>
                                                        <th>APPLIED DATE</th>
                                                        <th>STATUS</th>
                                                        <th>ACTION</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    <?php if (mysqli_num_rows($result) > 0): $count = 0 ?>
                                                        <?php while ($row = mysqli_fetch_assoc($result)):  $count++ ?>
                                                            <tr>
                                                                <td><?=  $count?></td>
                                                                <td><?= htmlspecialchars($row['fullname']) ?></td>
                                                                <td>$<?= number_format($row['loan_amount'], 2) ?></td>

                                                                   <td><?= date("Y-m-d", strtotime($row['created_at'])) ?></td>
                                                                <td>
                                                                    <span class="badge 
                                            <?php
                                                            if ($row['status'] === 'approved') echo 'bg-success';
                                                            elseif ($row['status'] === 'rejected') echo 'bg-danger';
                                                            else echo 'bg-warning';
                                            ?>">
                                                                        <?= ucfirst($row['status']) ?>
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <a href="loan_details.php?id=<?php echo $row['id'] ?>"> <span class="badge p-2 bg-info text-white">View Details</span></a>
                                                                </td>
                                                             
                                                            </tr>
                                                        <?php endwhile; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="4" class="text-center">No loan history found</td>
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