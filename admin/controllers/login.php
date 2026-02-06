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


    if (isset($_POST['login'])) {
        $email = mysqli_real_escape_string($connection, $_POST['email']);
        $pass = mysqli_real_escape_string($connection, $_POST['password']);

        if (!empty($email) && !empty($pass)) {

            $query = mysqli_query($connection, "SELECT * FROM `admin` WHERE  `email` = '$email' AND `password` = '$pass'");
            if (mysqli_num_rows($query) > 0) {
                $getDetails = mysqli_fetch_assoc($query);
                $_SESSION['logged_in'] = true;
                $_SESSION['id'] = $getDetails['id'];

                echo "<script> Swal.fire('Authenticated','Account Login Successfull','success')</script>";

                echo "<script>setTimeout( ()=> { window.open('../../profile/test.php','_self')}, 1000)</script>";
            } else {
                echo "<script>Swal.fire('Warning','Login Error','warning')</script>";
                echo "<script> setTimeout(()=> { window.location.href = '../login'},1000) </script>";
            }
        } else {
            echo "<script>Swal.fire('You have an input error','Make sure to fill all input','warning')</script>";
            echo "<script> setTimeout(()=> { window.location.href = '../login'},1000) </script>";
        }
    }


    ?>
</body>

</html>