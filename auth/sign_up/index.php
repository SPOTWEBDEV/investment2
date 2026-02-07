<?php
include("../../server/connection.php");

/* ============================
   VARIABLES
============================ */
$success = "";
$fullnameErr = $emailErr = $passwordErr = $confirmPasswordErr = $termsErr = $exist_err = "";

$fullname = $email = $accept_terms = "";

/* ============================
   FETCH GENERAL SETTINGS
============================ */
$settings = mysqli_fetch_assoc(
    mysqli_query($connection, "SELECT welcome_bonus, referral_bonus FROM general_settings LIMIT 1")
);

$WELCOME_BONUS  = (float)$settings['welcome_bonus'];
$REFERRAL_BONUS = (float)$settings['referral_bonus'];

/* ============================
   REFERRAL
============================ */
$referral_code = $_GET['ref'] ?? null;
$referrer_id   = null;

function generateReferralCode($length = 8)
{
    return strtoupper(bin2hex(random_bytes($length / 2)));
}

/* ============================
   FORM SUBMIT
============================ */
if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $fullname = trim($_POST['fullName'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $accept_terms = $_POST['acceptTerms'] ?? '';

    $hasError = false;

    /* ============================
       VALIDATION
    ============================ */
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

    if (strlen($password) < 6) {
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

    /* ============================
       CHECK EMAIL EXISTS
    ============================ */
    $check = mysqli_prepare($connection, "SELECT id FROM users WHERE email = ? LIMIT 1");
    mysqli_stmt_bind_param($check, "s", $email);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) > 0) {
        $exist_err = "This email is already registered!";
        $hasError = true;
    }
    mysqli_stmt_close($check);

    /* ============================
       FIND REFERRER
    ============================ */
    if (!empty($referral_code)) {
        $ref = mysqli_prepare(
            $connection,
            "SELECT id FROM users WHERE referral_code = ? LIMIT 1"
        );
        mysqli_stmt_bind_param($ref, "s", $referral_code);
        mysqli_stmt_execute($ref);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($ref));
        $referrer_id = $row['id'] ?? null;
        mysqli_stmt_close($ref);
    }

    /* ============================
       INSERT USER + BONUSES
    ============================ */
    if (!$hasError) {

        mysqli_begin_transaction($connection);

        try {

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $myReferralCode = generateReferralCode();

            /* INSERT USER */
            $stmt = mysqli_prepare($connection, "
                INSERT INTO users 
                (fullname, email, password, referral_code, referred_by, balance)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            mysqli_stmt_bind_param(
                $stmt,
                "ssssid",
                $fullname,
                $email,
                $hashedPassword,
                $myReferralCode,
                $referrer_id,
                $WELCOME_BONUS
            );
            mysqli_stmt_execute($stmt);
            $newUserId = mysqli_insert_id($connection);
            mysqli_stmt_close($stmt);

            /* ACTIVITY: REGISTER */
            mysqli_query($connection, "
                INSERT INTO activity (user_id, activity_type, description)
                VALUES ($newUserId, 'register', 'User registered')
            ");

            /* ACTIVITY: WELCOME BONUS */
            if ($WELCOME_BONUS > 0) {
                mysqli_query($connection, "
                    INSERT INTO activity (user_id, activity_type, amount, description)
                    VALUES ($newUserId, 'profit', $WELCOME_BONUS, 'Welcome bonus')
                ");
            }

            /* REFERRAL BONUS */
            if (!empty($referrer_id) && $REFERRAL_BONUS > 0) {

                $credit = mysqli_prepare($connection, "
                    UPDATE users 
                    SET referral_balance = referral_balance + ?
                    WHERE id = ?
                ");
                mysqli_stmt_bind_param($credit, "di", $REFERRAL_BONUS, $referrer_id);
                mysqli_stmt_execute($credit);
                mysqli_stmt_close($credit);

                mysqli_query($connection, "
                    INSERT INTO activity (user_id, activity_type, amount, description)
                    VALUES ($referrer_id, 'referral', $REFERRAL_BONUS, 'Referral bonus earned')
                ");

                mysqli_query($connection, "
                    INSERT INTO activity (user_id, activity_type, description)
                    VALUES ($newUserId, 'referral', 'Registered via referral')
                ");
            }

            mysqli_commit($connection);

            $success = "Account created successfully";

            echo "<script>
                setTimeout(() => {
                    window.location.href = '../sign_in/';
                }, 1000);
            </script>";

        } catch (Exception $e) {
            mysqli_rollback($connection);
            $exist_err = "Registration failed. Try again.";
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