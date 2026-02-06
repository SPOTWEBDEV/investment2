<?php
include("../../../server/connection.php");




include("../../controllers/management.php");


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $sitename ?></title>
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
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title">
                                <div class="row align-items-center justify-content-between">
                                    <div class="col-xl-4">
                                        <div class="page-title-content">
                                            <h3>All Users</h3>
                                            <p class="mb-2">Welcome To <?= $sitename ?> Management</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center h-100 align-items-center">
                        <div class="col-12">

                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Add User Investment</h4>
                                </div>

                                <div class="card-body">
                                    <form method="POST">

                                        <!-- USER SELECT -->
                                        <div class="form-row">
                                            <div class="mb-3 col-xl-12">
                                                <label class="mr-sm-2">Select User</label>
                                                <select name="user_id" class="form-control">
                                                    <option selected>Select User</option>
                                                    <?php
                                                    $users = mysqli_query($connection, "SELECT id, fullname FROM users ORDER BY fullname ASC");
                                                    while ($u = mysqli_fetch_assoc($users)) {
                                                        echo '<option value="' . $u['id'] . '">' . $u['fullname'] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- PLAN NAME -->
                                        <div class="form-row">
                                            <div class="mb-3 col-xl-12">
                                                <label class="mr-sm-2">Plan Name</label>
                                                <select name="plan_name" class="form-control">
                                                    <option selected>Select Plan</option>
                                                    <?php
                                                    $users = mysqli_query($connection, "SELECT plan_name FROM investment_plans ORDER BY id ASC");
                                                    while ($u = mysqli_fetch_assoc($users)) {
                                                        echo '<option value="' . $u['id'] . '">' . $u['plan_name'] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- AMOUNT INVESTED -->
                                        <div class="form-row">
                                            <div class="mb-3 col-xl-12">
                                                <label class="mr-sm-2">Amount Invested</label>
                                                <input type="number" name="amount_invested" class="form-control" placeholder="5000">
                                            </div>
                                        </div>

                                        <!-- DAILY PROFIT -->
                                        <div class="form-row">
                                            <div class="mb-3 col-xl-12">
                                                <label class="mr-sm-2">Daily Profit (%)</label>
                                                <input type="number" step="0.01" name="daily_profit" class="form-control" placeholder="2.5">
                                            </div>
                                        </div>

                                        <!-- TOTAL PROFIT -->
                                        <div class="form-row">
                                            <div class="mb-3 col-xl-12">
                                                <label class="mr-sm-2">Total Profit (%)</label>
                                                <input type="number" step="0.01" name="total_profit" class="form-control" placeholder="75">
                                            </div>
                                        </div>

                                        <!-- START DATE -->
                                        <div class="form-row">
                                            <div class="mb-3 col-xl-12">
                                                <label class="mr-sm-2">Start Date</label>
                                                <input type="date" name="start_date" class="form-control">
                                            </div>
                                        </div>

                                        <!-- END DATE -->
                                        <div class="form-row">
                                            <div class="mb-3 col-xl-12">
                                                <label class="mr-sm-2">End Date</label>
                                                <input type="date" name="end_date" class="form-control">
                                            </div>
                                        </div>

                                        <!-- SAVE BUTTON -->
                                        <div class="col-12 mt-4">
                                            <button name="add_user_investment" class="btn btn-success w-100">Save Investment</button>
                                        </div>

                                    </form>
                                </div>
                            </div>

                            <div class="card">


                                <div class="card-header">
                                    <h4 class="card-title">Add Loan Request</h4>
                                </div>

                                <div class="card-body">

                                    <form method="POST" class="form-style">

                                        <label>User</label>
                                        <select name="user_id" class="form-control">
                                            <option selected>Select User</option>
                                            <?php
                                            $users = mysqli_query($connection, "SELECT id, fullname FROM users ORDER BY fullname ASC");
                                            while ($u = mysqli_fetch_assoc($users)) {
                                                echo '<option value="' . $u['id'] . '">' . $u['fullname'] . '</option>';
                                            }
                                            ?>
                                        </select>

                                        <label>Loan Amount</label>
                                        <input type="number" name="loan_amount" class="form-control" required>

                                        <label>Loan Duration</label>
                                        <input type="text" name="loan_duration" class="form-control" placeholder="e.g. 12 months" required>

                                        <label>Loan Reason</label>
                                        <textarea name="loan_reason" class="form-control" required></textarea>

                                        <label>Monthly Income</label>
                                        <input type="number" name="monthly_income" class="form-control" required>

                                        <label>Employment Status</label>
                                        <input type="text" name="employment_status" class="form-control" required>

                                        <label>Bank Name</label>
                                        <input type="text" name="bank_name" class="form-control" required>

                                        <label>Account Number</label>
                                        <input type="text" name="account_number" class="form-control" required>

                                        <label>Interest Rate (%)</label>
                                        <input type="number" step="0.01" name="interest_rate" class="form-control" required>

                                        <label>Total Payable</label>
                                        <input type="number" step="0.01" name="total_payable" class="form-control" required>

                                        <button type="submit" name="submit" class="btn-submit mt-3 btn btn-success w-100">Submit Loan Request</button>

                                    </form>


                                </div>




                            </div>

                            <div class="card">


                                <div class="card-header">
                                    <h4 class="card-title">Add Deposit To User</h4>
                                </div>

                                <div class="card-body">

                                    <form method="POST" class="form-style">

                                        <label>User</label>
                                        <select name="user_id" class="form-control">
                                            <option selected>Select User</option>
                                            <?php
                                            $users = mysqli_query($connection, "SELECT id, fullname FROM users ORDER BY fullname ASC");
                                            while ($u = mysqli_fetch_assoc($users)) {
                                                echo '<option value="' . $u['id'] . '">' . $u['fullname'] . '</option>';
                                            }
                                            ?>
                                        </select>

                                        <label class="mt-3">Method (e.g. Bank Transfer, Crypto, Paystack)</label>
                                        <select name="type" class="form-control">
                                            <option selected>Select Method</option>
                                            <?php
                                            $users = mysqli_query($connection, "SELECT id, type FROM payment_account");
                                            while ($u = mysqli_fetch_assoc($users)) {
                                                echo '<option value="' . $u['id'] . '">' . $u['type'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                        


                                        <label>Amount</label>
                                        <input type="number" step="0.01" name="amount" class="form-control" required>

                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option value="pending">Pending</option>
                                            <option value="approved">Approved</option>
                                            <option value="failed">Failed</option>
                                        </select>

                                        <label>Date</label>
                                        <input type="date"  name="date" class="form-control" required>

                                        <button type="submit" name="add_deposit" class="btn-submit mt-3 btn btn-success w-100">Submit Deposit</button>

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