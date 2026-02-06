<?php
include("../server/connection.php");

if (!isset($_SESSION['user_id'])) {
    header("location: {$domain}/auth/sign_in/");
}

$user_id = (int) $_SESSION['user_id'];
$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deposit'])) {

    $type   = trim($_POST['type'] ?? "");
    $amount = trim($_POST['amount'] ?? "");



    if (empty($type)) {
        $errors[] = "Deposit type is required.";
    } elseif (empty($amount)) {
        $errors[] = "amount is required.";
    }

    if (empty($errors)) {

        $sql = "INSERT INTO deposits (user_id,  type_id, amount)
                VALUES (?, ?,  ?)";

        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param($stmt, "isd", $user_id, $type, $amount);

        if (mysqli_stmt_execute($stmt)) {
            $success = "Deposit submitted successfully. Awaiting confirmation.";
        } else {
            $errors[] = "Deposit failed. Please try again.";
        }

        mysqli_stmt_close($stmt);
    }
}






?>


<!DOCTYPE html>



<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $sitename ?> | Deposit </title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $domain ?>/images/favicon.png">
    <!-- Custom Stylesheet -->
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
                                        <h3>Deposit</h3>
                                        <p class="mb-2">Welcome To <?= $sitename ?> Management</p>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <a href="./deposits_history/"><button class="btn btn-primary mr-2">View Deposit History</button></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xxl-12 col-xl-12">

                        <div class="row">
                            <div class="row g-4">

                                <!-- ERROR -->
                                <div class="col-xxl-6 col-xl-6 col-lg-6">
                                    <?php if (!empty($errors)): ?>
                                        <div class="alert alert-danger">
                                            <?php foreach ($errors as $error): ?>
                                                <p><?= htmlspecialchars($error) ?></p>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($success): ?>
                                        <div class="alert alert-success">
                                            <?= htmlspecialchars($success) ?>
                                        </div>
                                    <?php endif; ?>


                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title">Select Deposit Method</h4>
                                        </div>
                                        <div class="card-body">
                                            <form method="post" id="depositForm">

                                                <?php
                                                // Fetch all payment types
                                                $sql_type = "SELECT * FROM payment_account ORDER BY type ASC";
                                                $stm_query = mysqli_query($connection, $sql_type);
                                                ?>

                                                <div class="mb-3">
                                                    <label class="form-label">Type</label>
                                                    <select name="type" class="form-select" id="type">
                                                        <option disabled selected hidden value="">Select Type</option>

                                                        <?php while ($row = mysqli_fetch_assoc($stm_query)): ?>

                                                            <option
                                                                value="<?= $row['id']; ?>"
                                                                data-type="<?= $row['type']; ?>"
                                                                data-network="<?= $row['network']; ?>"
                                                                data-wallet="<?= $row['wallet_address']; ?>"
                                                                data-bankname="<?= $row['bank_name']; ?>"
                                                                data-accountnumber="<?= $row['account_number']; ?>"
                                                                data-fullname="<?= $row['fullname']; ?>"
                                                                data-label="<?= $row['label']; ?>">
                                                                <?= ucfirst($row['type']) ?> - <?= $row['label'] ?>
                                                            </option>

                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>

                                        </div>
                                    </div>
                                </div>

                                <!-- RIGHT SIDE: Payment Address -->
                                <div class="col-xxl-6 col-xl-6 col-lg-6">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h4 class="card-title">Payment Address / Account Details</h4>
                                            <button type="button" class="btn btn-sm btn-primary" onclick="copyWallet()">Copy</button>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Type:</strong> <span id="cryptoType">---</span></p>
                                            <p id="walletAddress" class="text-break text-muted"><strong>Details:</strong> Select a type</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- SUBMIT AMOUNT -->
                                <div class="col-xxl-6 col-xl-6 col-lg-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title">Submit Payment</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label class="form-label">Amount Sent</label>
                                                <input name="amount" type="number" class="form-control" placeholder="Enter amount">
                                            </div>
                                            <button type="submit" name="deposit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </div>
                                </div>

                                </form>

                            </div>
                        </div>

                    </div>
                </div>

                <script>
                    const typeSelect = document.getElementById("type");
                    const walletAddress = document.getElementById("walletAddress");
                    const cryptoType = document.getElementById("cryptoType");

                    function updateWallet() {
                        const selected = typeSelect.options[typeSelect.selectedIndex];
                        if (!selected) return;

                        const type = selected.getAttribute("data-type");
                        const network = selected.getAttribute("data-network");
                        const wallet = selected.getAttribute("data-wallet");
                        const bankName = selected.getAttribute("data-bankname");
                        const accountNumber = selected.getAttribute("data-accountnumber");
                        const fullname = selected.getAttribute("data-fullname");
                        const label = selected.getAttribute("data-label");

                        // === CRYPTO ===
                        if (type === "crypto") {
                            cryptoType.textContent = network;

                            walletAddress.innerHTML = `
                <strong>Wallet Address:</strong><br>${wallet}<br><br>
                <strong>Network:</strong> ${network}<br>
                <strong>Label:</strong> ${label}
            `;
                            return;
                        }

                        // === BANK ===
                        if (type === "bank") {
                            cryptoType.textContent = bankName;

                            walletAddress.innerHTML = `
                <strong>Bank Name:</strong> ${bankName}<br>
                <strong>Account Number:</strong> ${accountNumber}<br>
                <strong>Account Name:</strong> ${fullname}
            `;
                            return;
                        }

                        cryptoType.textContent = "---";
                        walletAddress.textContent = "Select a method";
                    }

                    typeSelect.addEventListener("change", updateWallet);

                    function copyWallet() {
                        navigator.clipboard.writeText(walletAddress.textContent);
                        alert("Copied!");
                    }
                </script>



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