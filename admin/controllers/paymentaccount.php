<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="<?php echo $domain ?>/js/jquery-3.6.0.min.js"></script>
    <script src="<?php echo $domain ?>/js/sweetalert2.all.min.js"></script>
</head>

<body>
    <?php

    $url = $domain . '/admin/bank/add';

    


    // Run only on POST
    if (isset($_POST['bank'])) {
        
        if ($type === "bank") {

            // BANK INPUTS
            $routing_number = $_POST['routing_number'] ?? null;
            $account_number = $_POST['account_number'] ?? null;
            $fullname       = $_POST['fullname'] ?? null;
            $bank_name = $_POST['bank_name'] ?? null;

            // INSERT BANK INTO payment_account
            $sql = "INSERT INTO payment_account 
                (type, routing_number, account_number, fullname , bank_name) 
                VALUES ('bank', ?, ?, ? , ?)";

            $stmt = mysqli_prepare($connection, $sql);
            mysqli_stmt_bind_param(
                $stmt,
                "ssss",
                $routing_number,
                $account_number,
                $fullname,
                $bank_name
            );
        } else if ($type === "crypto") {

            // CRYPTO INPUTS
            $network        = $_POST['network'] ?? null;
            $wallet_address = $_POST['wallet_address'] ?? null;
            $label          = $_POST['label'] ?? null;

            // INSERT CRYPTO INTO payment_account
            $sql = "INSERT INTO payment_account 
                (type, network, wallet_address, label) 
                VALUES ('crypto', ?, ?, ?)";

            $stmt = mysqli_prepare($connection, $sql);
            mysqli_stmt_bind_param(
                $stmt,
                "sss",
                $network,
                $wallet_address,
                $label
            );
        }

        // EXECUTE AND REDIRECT
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>Swal.fire('Bank Request','Successful added payment account','success')</script>";
            echo "<script> setTimeout(()=> { window.location.href = '$url'},1000) </script>";
        } else {
            echo "<script>Swal.fire('Bank Request','Something went wrong when adding payment account','error')</script>";
            echo "<script> setTimeout(()=> { window.location.href = '$url'},1000) </script>";
        }

        mysqli_stmt_close($stmt);
    }
    ?>

</body>

</html>