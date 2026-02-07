<?php
include("../../server/connection.php");

$result = mysqli_query($connection, "SELECT * FROM general_settings LIMIT 1");
$settings = mysqli_fetch_assoc($result);


if (isset($_POST['save'])) {

    $currency = $_POST['currency'];
    $deposit_limit = $_POST['deposit_limit'];
    $min_deposit = $_POST['min_deposit'];
    $min_withdrawal = $_POST['min_withdrawal'];
    $welcome_bonus = $_POST['welcome_bonus'];
    $referral_bonus = $_POST['referral_bonus'];
    $withdrawal_status = $_POST['withdrawal_status'];

    mysqli_query($connection, "
        UPDATE general_settings SET
            currency='$currency',
            deposit_limit='$deposit_limit',
            min_deposit='$min_deposit',
            min_withdrawal='$min_withdrawal',
            welcome_bonus='$welcome_bonus',
            referral_bonus='$referral_bonus',
            withdrawal_status='$withdrawal_status'
        WHERE id=1
    ");

    echo "<script>

    alert('Settings updated successfully');
    setTimeout(()=>{
      window.location.href = './'
     },100)
    
    </script>";

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
                                        <h3>Account Page</h3>
                                        <p class="mb-2">Welcome <?php echo $sitename ?></p>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="breadcrumbs"><a href="settings-api.html#">Home </a>
                                        <span><i class="fi fi-rr-angle-small-right"></i></span>
                                        <a href="<?php echo $domain ?>/admin/settings">Account</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xxl-12 col-xl-12">
                        <div class="settings-menu">
                            <a class="text-primary" href="<?php echo $domain ?>/admin/settings/">General</a>
                            <a href="<?php echo $domain ?>/admin/bank/">Add Bank</a>
                            <a href="<?php echo $domain ?>/admin/investment_plan/">Investment Plan</a>
                        </div>
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Preferences</h4>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST">
                                            <div class="row">

                                                <div class="mb-3 col-6">
                                                    <label class="form-label">Primary Currency</label>
                                                    <select name="currency" class="form-select">
                                                        <option value="NGN" <?= $settings['currency'] == 'NGN' ? 'selected' : '' ?>>
                                                            NAIRA (₦)
                                                        </option>
                                                    </select>
                                                </div>

                                                <div class="mb-3 col-6">
                                                    <label class="form-label">Deposit Limit</label>
                                                    <input type="number" name="deposit_limit" class="form-control"
                                                        value="<?= $settings['deposit_limit'] ?>">
                                                </div>

                                                <div class="mb-3 col-6">
                                                    <label class="form-label">Minimum Deposit</label>
                                                    <input type="number" name="min_deposit" class="form-control"
                                                        value="<?= $settings['min_deposit'] ?>">
                                                </div>

                                                <div class="mb-3 col-6">
                                                    <label class="form-label">Minimum Withdrawal</label>
                                                    <input type="number" name="min_withdrawal" class="form-control"
                                                        value="<?= $settings['min_withdrawal'] ?>">
                                                </div>

                                                <div class="mb-3 col-6">
                                                    <label class="form-label">Welcome Bonus (₦)</label>
                                                    <input type="number" name="welcome_bonus" class="form-control"
                                                        value="<?= $settings['welcome_bonus'] ?>">
                                                </div>

                                                <div class="mb-3 col-6">
                                                    <label class="form-label">Referral Bonus (%)</label>
                                                    <input type="number" name="referral_bonus" class="form-control"
                                                        value="<?= $settings['referral_bonus'] ?>">
                                                </div>

                                                <div class="mb-3 col-6">
                                                    <label class="form-label">Withdrawal Status</label>
                                                    <select name="withdrawal_status" class="form-select">
                                                        <option value="enabled" <?= $settings['withdrawal_status'] == 'enabled' ? 'selected' : '' ?>>Enabled</option>
                                                        <option value="disabled" <?= $settings['withdrawal_status'] == 'disabled' ? 'selected' : '' ?>>Disabled</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3 col-12">
                                                    <button type="submit" style="background:#3A5BE0" name="save" class="btn  px-5">
                                                        Save Settings
                                                    </button>
                                                </div>

                                            </div>
                                        </form>

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
                                <a href="settings-api.html#"><?php echo $sitename ?></a> I All Rights Reserved
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
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!--  -->
    <!--  -->
    <script src="js/scripts.js"></script>
</body>

</html>