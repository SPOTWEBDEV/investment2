<?php
include("../../server/connection.php");




$success = "";
$fullnameErr = "";
$emailErr = "";
$passwordErr = "";
$confirmPasswordErr = "";
$termsErr = "";
$exist_err = "";

$fullname = "";
$email = "";
$accept_terms = "";



if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $fullname = $_POST['fullName'] ?? '';
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $accept_terms = isset($_POST['acceptTerms']) ? $_POST['acceptTerms'] : "";
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    $hasError = false;

    // VALIDATION
    if (empty($fullname)) {
        $fullnameErr = "Full name is required";
        $hasError = true;
    }

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
    } elseif (strlen($password) < 6) {
        $passwordErr = "Password must be at least 6 characters";
        $hasError = true;
    }

    if ($password !== $confirmPassword) {
        $confirmPasswordErr = "Passwords do not match";
        $hasError = true;
    }

    if (empty($accept_terms)) {
        $termsErr = "Please accept terms and conditions";
        $hasError = true;
    }

    // EMAIL EXISTS CHECK
    $sql_exist = "SELECT id FROM users WHERE email = ? LIMIT 1";
    $stmt_exist = mysqli_prepare($connection, $sql_exist);
    mysqli_stmt_bind_param($stmt_exist, "s", $email);
    mysqli_stmt_execute($stmt_exist);
    mysqli_stmt_store_result($stmt_exist);

    if (mysqli_stmt_num_rows($stmt_exist) > 0) {
        $exist_err = "This email is already registered!";
        $hasError = true;
    }
    mysqli_stmt_close($stmt_exist);

    // If NO ERRORS → insert user
    if (!$hasError) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

       

        $virtualCardExpiry = (new DateTime())->modify('+4 years')->format('Y-m-d');

        if (!$hasError) {
            $sql = "INSERT INTO users (fullname, email, password)
                    VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($connection, $sql);

            if ($stmt) {
                mysqli_stmt_bind_param(
                    $stmt,
                    "sss",
                    $fullname,
                    $email,
                    $hashedPassword,
                );

                mysqli_stmt_execute($stmt);

                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    $success = "User registered successfully";

                    // clear form values
                    $fullname = "";
                    $email = "";
                    $accept_terms = "";

                    echo "<script>
                        setTimeout(() => {
                            window.location.href = '../sign_in/'
                        }, 2500);
                    </script>";
                } else {
                    $success = "Registration failed";
                }

                mysqli_stmt_close($stmt);
            } else {
                $success = "Database error: " . mysqli_error($connection);
            }
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $sitename ?>| Sign Up </title>
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
                        

                        <div class="col-lg-12">
                            <div class="auth-form">
                                <h4>Sign Up</h4>

                                <?php if (!empty($success)) { ?>
                                    <div class="alert alert-success"><?= $success ?></div>
                                <?php } ?>

                                <?php if (!empty($exist_err)) { ?>
                                    <div class="alert alert-danger"><?= $exist_err ?></div>
                                <?php } ?>

                                <form action="" method="POST">
                                    <div class="row">

                                        <div class="col-12 mb-3">
                                            <label class="form-label">Full Name</label>
                                            <input name="fullName" type="text" class="form-control" value="<?= htmlspecialchars($fullname) ?>" />
                                            <small style="color:red"><?= $fullnameErr ?></small>
                                        </div>

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

                                        <div class="col-12 mb-3">
                                            <label class="form-label">Confirm Password</label>
                                            <input name="confirmPassword" type="password" class="form-control" />
                                            <small style="color:red"><?= $confirmPasswordErr ?></small>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-check">
                                                <input name="acceptTerms" type="checkbox" class="form-check-input" id="acceptTerms" <?= !empty($accept_terms) ? 'checked' : '' ?> />
                                                <label class="form-check-label" for="acceptTerms">I certify that I am 18 years of age or older, and agree to the <a href="#" class="text-primary">User Agreement</a> and <a href="#" class="text-primary">Privacy Policy</a>.</label>
                                                <br><small style="color:red"><?= $termsErr ?></small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-3 d-grid gap-2">
                                        <button type="submit" class="btn btn-primary me-8 text-white">Sign Up</button>
                                    </div>
                                </form>

                                <p class="mt-3 mb-0">Already have an account?<a class="text-primary" href="../sign_in/"> Sign In</a></p>

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
