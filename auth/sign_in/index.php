<?php
include("../../server/connection.php");

$success = "";
$emailErr = "";
$passwordErr = "";


$email = "";

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $email    = $_POST['email'];
    $password = $_POST['password'];
    $hasError = false;

    if (empty($email)) {
        $emailErr = "Email is required";
        $hasError = true;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format";
        $hasError = true;
    }

 
    if (empty($password)) {
        $passwordErr = "Password is required";
        $hasError = true;
    }


    if (!$hasError) {
        $sql = "SELECT id, fullname, email, password FROM users WHERE email = ?";
        $stmt = mysqli_prepare($connection, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) > 0) {
                mysqli_stmt_bind_result($stmt, $id, $fullname, $db_email, $db_password);
                mysqli_stmt_fetch($stmt);

                if (password_verify($password, $db_password ?? "")) {
                    $success = "Login successful. Welcome, $fullname!";

                    
                    $_SESSION['user_id'] = $id;
                    $_SESSION['fullname'] = $fullname;
                    echo "
                    <script>
                     setTimeout(() => {
          window.location.href = '../../dashboard/';
                 }, 1000);
                  </script>
                    ";
                } else {
                    $passwordErr = "invalid credential";
                }
            } else {
                $emailErr = "No account found ";
            }

            mysqli_stmt_close($stmt);
        } else {
            $success = "Database error: " . mysqli_error($connection);
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($sitename) ?> | Sign In</title>
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $domain ?>/images/favicon.png">
    <link rel="stylesheet" href="<?php echo $domain ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo $domain ?>/vendor/toastr/toastr.min.css">
</head>

<body class="dashboard">
   
    <div class="authincation">
        <div class="container">
            <div class="row justify-content-center align-items-center g-0">
                <div class="col-xl-8">
                    <div class="row g-0">
                        
                        <div class="col-lg-6">
                            <div class="auth-form">
                                <h4>Sign In</h4>

                                <?php if (!empty($success)) { ?>
                                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                                <?php } ?>

                                <form action="" method="POST">
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <label class="form-label">Email</label>
                                            <input name="email" type="text" class="form-control" value="<?= htmlspecialchars($email) ?>" />
                                            <small style="color:red"><?= $emailErr ?></small>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="form-label">Password</label>
                                            <input name="password" type="password" class="form-control" />
                                            <small style="color:red"><?= $passwordErr ?></small>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input name="remember" id="remember" type="checkbox" class="form-check-input" />
                                                <label class="form-check-label" for="remember">Remember me</label>
                                            </div>
                                        </div>
                                        <div class="col-6 text-end"><a href="reset.html">Forgot Password?</a></div>
                                    </div>
                                    <div class="mt-3 d-grid gap-2">
                                        <button type="submit" class="btn btn-primary me-8 text-white">Sign In</button>
                                    </div>
                                </form>
                                <p class="mt-3 mb-0 undefined">Don't have an account?<a class="text-primary" href="../sign_up/"> Sign up</a></p>
                            </div>
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