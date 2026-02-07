<?php
include("../server/connection.php");
include("../server/auth/client.php");

$plans = [];

$query = mysqli_query(
    $connection,
    "SELECT * FROM investment_plans WHERE status = 'available'"
);

while ($row = mysqli_fetch_assoc($query)) {
    $plans[$row['id']] = [
        'plan_name' => $row['plan_name'],
        'price' => $row['price'],
        'duration' => $row['duration'],
        'profit_per_day' => $row['profit_per_day'],
        'total_profit' => $row['total_profit']
    ];
}




if (isset($_POST['activate_investment'], $_POST['plan_id'])) {

    $plan_id = (int) $_POST['plan_id'];
    $user_id = $user_id; // from client.php

    /* 1. Validate plan */
    if (!isset($plans[$plan_id])) {
        $error = "Invalid investment plan.";
    } else {

        $plan = $plans[$plan_id];

        try {
            mysqli_begin_transaction($connection);



            if ($plan['price'] > $client['balance']) {
                throw new Exception("Insufficient balance.");
            }

            /* 3. Debit user */
            $new_balance = $client['balance'] - $plan['price'];

            echo $new_balance;

            $stmt = mysqli_prepare(
                $connection,
                "UPDATE users SET balance = ? WHERE id = ?"
            );
            mysqli_stmt_bind_param($stmt, "di", $new_balance, $user_id);
            mysqli_stmt_execute($stmt);

            /* 4. Insert investment (SNAPSHOT) */
            $stmt = mysqli_prepare(
                $connection,
                "INSERT INTO investments 
                (user_id, plan_id, plan_name, amount, profit, profit_per_day , duration, status, created_at)
                VALUES (?, ?, ?, ?, ?,? ,?, 'active', NOW())"
            );
            mysqli_stmt_bind_param(
                $stmt,
                "iisdddi",
                $user_id,
                $plan_id,
                $plan['plan_name'],
                $plan['price'],
                $plan['total_profit'],
                $plan['profit_per_day'],
                $plan['duration']
            );
            mysqli_stmt_execute($stmt);

            mysqli_commit($connection);

            $success = "Investment activated successfully!";

            echo "<script> setTimeout(()=> { window.location.href = './'},1000) </script>"; 
        } catch (Exception $e) {
            mysqli_rollback($connection);
            $error = $e->getMessage();
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
    <title><?= htmlspecialchars($sitename) ?> | Investment</title>

    <link rel="icon" type="image/png" sizes="16x16" href="<?= $domain ?>/images/favicon.png">
    <link rel="stylesheet" href="<?= $domain ?>/css/style.css">
    <link rel="stylesheet" href="<?= $domain ?>/vendor/toastr/toastr.min.css">

    <style>

    </style>
</head>

<body class="dashboard">
    <div id="main-wrapper">

        <!-- HEADER (kept intact) -->
        <?php include("../include/header.php") ?>

        <!-- SIDEBAR (kept intact) -->
        <?php include("../include/sidenav.php") ?>

        <!-- CONTENT -->
        <div class="content-body">
            <div class="container">

                <div class="row">
                    <div class="col-12">
                        <div class="page-title">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-6">
                                    <div class="page-title-content">
                                        <h3>Investment</h3>
                                        <p class="mb-2">Welcome To <?= htmlspecialchars($sitename) ?> Management</p>
                                        <div class="balance-chip mt-2">
                                            <small>Your Balance:</small>
                                            <span>₦<?= number_format($client['balance'], 2) ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto mt-2">
                                    <a href="./investment_history/"><button class="btn btn-primary mr-2">View Investment History</button></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alerts -->
                <?php if (!empty($success)) : ?>
                    <div style="padding:12px; background:#d4edda; color:#155724; border-radius:8px; margin-bottom:15px;">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)) : ?>
                    <div style="padding:12px; background:#f8d7da; color:#721c24; border-radius:8px; margin-bottom:15px;">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>



                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:20px; margin-top:20px;">

                    <?php


                    if (!empty($plans)) :
                        foreach ($plans as $plan_id => $p) :
                    ?>

                            <div style="
                                background:#ffffff;
                                border-radius:12px;
                                padding:20px;
                                box-shadow:0 10px 25px rgba(0,0,0,0.08);
                                border:1px solid #eee;
                            ">

                                <!-- Header -->
                                <div style="display:flex; align-items:center; margin-bottom:15px;">
                                    <div style="
                                        width:45px;
                                        height:45px;
                                        border-radius:50%;
                                        background:#f0f4ff;
                                        display:flex;
                                        align-items:center;
                                        justify-content:center;
                                        margin-right:12px;
                                        font-size:20px;
                                        color:#4a6cf7;
                                    ">
                                        📈
                                    </div>
                                    <div>
                                        <h3 style="margin:0; font-size:18px; color:#222;">
                                            <?= htmlspecialchars($p['plan_name']) ?>
                                        </h3>
                                        <small style="color:#777;">
                                            Investment Plan
                                        </small>
                                    </div>
                                </div>

                                <!-- Details -->
                                <div style="font-size:14px; color:#444;">
                                    <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                                        <span>Duration</span>
                                        <strong><?= $p['duration'] ?> Days</strong>
                                    </div>

                                    <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                                        <span>Daily Profit</span>
                                        <strong>₦<?= number_format($p['profit_per_day'], 2) ?></strong>
                                    </div>

                                    <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                                        <span>Total Profit</span>
                                        <strong>₦<?= number_format($p['total_profit'], 2) ?></strong>
                                    </div>

                                    <div style="display:flex; justify-content:space-between; margin-bottom:15px;">
                                        <span>Investment Amount</span>
                                        <strong>₦<?= number_format($p['price'], 2) ?></strong>
                                    </div>
                                </div>

                                <!-- Button -->
                                <form method="post">
                                    
                                     <input type="hidden" name="plan_id" value="<?= (int)$plan_id ?>">
                                    <button type="submit" name="activate_investment" style="
                width:100%;
                padding:12px;
                background:#4a6cf7;
                color:#fff;
                border:none;
                border-radius:8px;
                font-size:15px;
                cursor:pointer;
                transition:0.3s;
            "
                                        onmouseover="this.style.background='#3a5be0'"
                                        onmouseout="this.style.background='#4a6cf7'">
                                        ACTIVATE PLAN
                                    </button>
                                </form>

                            </div>

                        <?php
                        endforeach;
                    else :
                        ?>
                        <div style="padding:15px; background:#fff3cd; color:#856404; border-radius:8px;">
                            No investment plans found.
                        </div>
                    <?php endif; ?>

                </div>

            </div>



        </div>

        <script src="<?= $domain ?>/vendor/jquery/jquery.min.js"></script>
        <script src="<?= $domain ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="<?= $domain ?>/js/scripts.js"></script>
</body>

</html>