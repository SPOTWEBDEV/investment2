<?php
include("../server/connection.php");
include("../server/auth/client.php");





$errors = [];
$success = "";


$currentBalance = $client['balance'];



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['withdraw'])) {

    $amount_raw     = trim($_POST['amount'] ?? "");
    $bank_name      = trim($_POST['bank_name'] ?? "");
    $account_number = trim($_POST['account_number'] ?? "");
    $account_name   = trim($_POST['account_name'] ?? "");

    

    
    if ($amount_raw === "" || !is_numeric($amount_raw)) {
        $errors[] = "Enter a valid amount.";
    } else {
        $amount = (float) $amount_raw;

        if ($amount <= 0) {
            $errors[] = "Amount must be greater than zero.";
        } elseif ($amount < 1) {
            $errors[] = "Minimum withdrawal is $1.00.";
        } elseif ($amount > 100000000) {
            $errors[] = "Amount is too large.";
        }
    }


    if ($bank_name === "" || strlen($bank_name) < 2 || strlen($bank_name) > 80) {
        $errors[] = "Enter a valid bank name.";
    }


    $account_number_digits = preg_replace('/\D+/', '', $account_number);
    if ($account_number === "" || $account_number_digits === "") {
        $errors[] = "Enter a valid account number.";
    } elseif (strlen($account_number_digits) < 8 || strlen($account_number_digits) > 20) {
        $errors[] = "Account number must be 8 to 20 digits.";
    }


    if ($account_name === "" || strlen($account_name) < 3 || strlen($account_name) > 80) {
        $errors[] = "Enter a valid account name.";
    }


    if (!empty($errors)) {
        $errors = [$errors[0]];
    }

    if (empty($errors)) {
        mysqli_begin_transaction($connection);

        try {

        

            if ($amount > $currentBalance) {
                throw new Exception("Insufficient balance");
            }


            $ins_sql = "
                INSERT INTO withdrawals
                    (user_id, amount,  bank_name, account_number, account_name)
                VALUES
                    (?,?, ?, ?,  ?)
            ";
            $ins_stmt = mysqli_prepare($connection, $ins_sql);
            if (!$ins_stmt) {
                throw new Exception("System error: unable to submit request.");
            }

            mysqli_stmt_bind_param(
                $ins_stmt,
                "idsss",
                $user_id,
                $amount,
                $bank_name,
                $account_number_digits,
                $account_name
            );

            if (!mysqli_stmt_execute($ins_stmt)) {
                $err = mysqli_stmt_error($ins_stmt);
                mysqli_stmt_close($ins_stmt);
                throw new Exception("Withdrawal failed.");
            }
            mysqli_stmt_close($ins_stmt);

            mysqli_commit($connection);

            $success = "Withdrawal request submitted successfully. Processing...";

            echo "<script>
    setTimeout(() => {
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
        location.reload();
    }, 1500);
</script>";
        } catch (Exception $e) {
            mysqli_rollback($connection);
            $errors = [$e->getMessage()];
        }
    }
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= htmlspecialchars($sitename) ?> | Withdrawal </title>

    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $domain ?>/images/favicon.png">
    <link rel="stylesheet" href="<?php echo $domain ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo $domain ?>/vendor/toastr/toastr.min.css">
</head>

<body class="dashboard">
    <div id="main-wrapper">

        <!-- nav -->
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
                                        <h3>Withdrawal</h3>
                                        <p class="mb-2">Welcome To <?= htmlspecialchars($sitename) ?> Management</p>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <a href="./withdrawal_history/"><button class="btn btn-primary mr-2">View Withdrawal History</button></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alerts -->
                <?php if (!empty($errors)) { ?>
                    <div class="alert alert-danger mt-3">
                        <p><?= htmlspecialchars($errors[0]) ?></p>
                    </div>
                <?php } ?>

                <?php if (!empty($success)) { ?>
                    <div class="alert alert-success mt-3">
                        <p><?= htmlspecialchars($success) ?></p>
                    </div>
                <?php } ?>

                <div class="row">
                    <div class="col-xxl-12 col-xl-12">
                        <div class="row g-4">

                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h4 class="card-title">Withdrawal Details</h4>

                                       
                                    </div>

                                    <div class="card-body">
                                        <form method="post">

                                            

                                            <div class="mb-3">
                                                <label class="form-label">Amount</label>
                                                <input name="amount" type="number" step="0.01" min="1" class="form-control" placeholder="Amount" required>
                                                <small class="text-muted">Available: $<?= number_format($currentBalance, 2) ?></small>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Bank Name</label>
                                                <input name="bank_name" type="text" class="form-control" placeholder="Bank Name" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Account Number</label>
                                                <input name="account_number" type="text" class="form-control" placeholder="Account Number" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Account Name</label>
                                                <input name="account_name" type="text" class="form-control" placeholder="Account Name" required>
                                            </div>

                                            <button type="submit" name="withdraw" class="btn btn-primary w-100">PLACE WITHDRAWAL</button>

                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- End -->

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