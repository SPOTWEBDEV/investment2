<?php
include("../../../server/connection.php");


$type = $_GET['type'] ?? 'bank'; // default

include("../../controllers/paymentaccount.php");


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
        <?php include("../../include/nav.php") ?>
        <?php include("../../include/sidenav.php") ?>
        <div class="content-body">
            <div class="verification">
                <div class="container">
                    <div class="row justify-content-center h-100 align-items-center">
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title">
                                    <div class="row align-items-center justify-content-between">
                                        <div class="col-xl-4">
                                            <div class="page-title-content">
                                                <h3>Add Payment Account</h3>
                                                <p class="mb-2">Welcome To <?= $sitename ?></p>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <a href="../"><button class="btn btn-primary mr-2">View Account</button></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        <?php echo ($type === "crypto") ? "Link a crypto wallet" : "Link a bank account"; ?>
                                    </h4>
                                </div>


                                <div class="card-body">
                                    <form action="" method="POST">

                                        <?php if ($type === "bank"): ?>

                                            <!-- BANK ACCOUNT FORM -->
                                            <div class="form-row">
                                                <div class="mb-3 col-xl-12">
                                                    <label>Routing Number</label>
                                                    <input type="text" name="routing_number" class="form-control" placeholder="25487">
                                                </div>
                                                <div class="mb-3 col-xl-12">
                                                    <label>Bank Name</label>
                                                    <input type="text" name="bank_name" class="form-control" placeholder="36475">
                                                </div>

                                                <div class="mb-3 col-xl-12">
                                                    <label>Account Number</label>
                                                    <input type="text" name="account_number" class="form-control" placeholder="36475">
                                                </div>

                                                <div class="mb-3 col-xl-12">
                                                    <label>Full Name</label>
                                                    <input type="text" name="fullname" class="form-control" placeholder="John Doe">
                                                </div>
                                            </div>

                                        <?php else: ?>

                                            <!-- CRYPTO WALLET FORM -->
                                            <div class="form-row">
                                                <div class="mb-3 col-xl-12">
                                                    <label>Network</label>
                                                    <select name="network" class="form-control">
                                                        <option value="BTC">Bitcoin</option>
                                                        <option value="ETH">Ethereum</option>
                                                        <option value="USDT">USDT (TRC20 / ERC20)</option>
                                                        <option value="BNB">BNB Smart Chain</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3 col-xl-12">
                                                    <label>Wallet Address</label>
                                                    <input type="text" name="wallet_address" class="form-control" placeholder="0xA2B...">
                                                </div>

                                                <div class="mb-3 col-xl-12">
                                                    <label>Label (Optional)</label>
                                                    <input type="text" name="label" class="form-control" placeholder="My ETH Wallet">
                                                </div>
                                            </div>

                                        <?php endif; ?>

                                        <div class="col-12 mt-4">
                                            <button name="bank" type="submit" class="btn btn-success w-100">Save</button>
                                        </div>

                                    </form>
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