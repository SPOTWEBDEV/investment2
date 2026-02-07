<?php
include("../server/connection.php");
include("../server/auth/client.php");


echo $user_id;

// FETCH USER REFERRAL CODE
$user_sql = "SELECT referral_code, referral_balance FROM users WHERE id = ?";
$stmt = mysqli_prepare($connection, $user_sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// TOTAL REFERRALS
$count_sql = "SELECT COUNT(*) AS total FROM users WHERE referred_by = ?";
$stmt = mysqli_prepare($connection, $count_sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$totalReferrals = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];

// FETCH REFERRED USERS
$list_sql = "
    SELECT fullname, email, created_at 
    FROM users 
    WHERE referred_by = ?
    ORDER BY created_at DESC
";
$stmt = mysqli_prepare($connection, $list_sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);



// REFERRAL LINK
$referralLink =  $domain . "/auth/sign_up/?ref=" . $user['referral_code'];
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
        <?php include("../include/header.php") ?>

        <?php include("../include/sidenav.php") ?>

      


   

        <div class="content-body">
            <div class="container">

                <!-- PAGE TITLE -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title">
                            <h3>Referral Dashboard</h3>
                            <p>Invite friends and earn rewards 🎉</p>
                        </div>
                    </div>
                </div>

                <!-- STATS -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="card stat-widget-1">
                            <div class="card-body">
                                <h6>Total Referrals</h6>
                                <h3><?= $totalReferrals ?></h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card stat-widget-1">
                            <div class="card-body">
                                <h6>Referral Earnings</h6>
                                <h3>₦<?= number_format($user['referral_balance'], 2) ?></h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card stat-widget-1">
                            <div class="card-body">
                                <h6>Your Referral Code</h6>
                                <input type="text" class="form-control" value="<?= $user['referral_code'] ?>" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- REFERRAL LINK -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <label>Referral Link</label>
                                <div class="input-group">
                                    <input type="text" id="refLink" class="form-control" value="<?= $referralLink ?>" readonly>
                                    <button class="btn btn-success" onclick="copyLink()">Copy</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TABLE -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Full Name</th>
                                            <th>Email</th>
                                            <th>Date Joined</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (mysqli_num_rows($result) > 0): $sn = 0; ?>
                                            <?php while ($row = mysqli_fetch_assoc($result)): $sn++; ?>
                                                <tr>
                                                    <td><?= $sn ?></td>
                                                    <td><?= htmlspecialchars($row['fullname']) ?></td>
                                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                                    <td><?= date("Y-m-d", strtotime($row['created_at'])) ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center">No referrals yet</td>
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

    <script>
        function copyLink() {
            const input = document.getElementById("refLink");
            input.select();
            document.execCommand("copy");
            alert("Referral link copied!");
        }
    </script>

</body>

</html>