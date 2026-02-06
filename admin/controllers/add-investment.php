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


  $url = $domain . '/admin/investment/add';
   
    // Only allow POST requests
    if (isset($_POST['investment'])) {

        $plan_name      = $_POST['plan_name'] ?? null;
        $duration       = $_POST['duration'] ?? null;
        $profit_per_day = $_POST['profit_per_day'] ?? null;
        $total_profit   = $_POST['total_profit'] ?? null;

        // Simple validation
        if (!$plan_name || !$duration || !$profit_per_day || !$total_profit) {
            
            echo "<script>Swal.fire('You have an input error','Make sure to fill all input','warning')</script>";
            echo "<script> setTimeout(()=> { window.location.href = '$url'},1000) </script>";
        }

        $sql = "INSERT INTO investment_plans (plan_name, duration, profit_per_day, total_profit)
            VALUES (?, ?, ?, ?)";

        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param(
            $stmt,
            "sidd",
            $plan_name,
            $duration,
            $profit_per_day,
            $total_profit
        );

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>Swal.fire('Investment Plan Added Successfully','','success')</script>";
            echo "<script> setTimeout(()=> { window.location.href = '$domain/admin/investment/'},1000) </script>";
        } else {
           
            echo "<script>Swal.fire('Add Investment Request','','warning')</script>";
            echo "<script> setTimeout(()=> { window.location.href = '$url'},1000) </script>";
        }

        mysqli_stmt_close($stmt);
    }
    ?>

</body>

</html>