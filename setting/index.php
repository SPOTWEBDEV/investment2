<?php
include("../server/connection.php");

if (!isset($_SESSION['user_id'])) {
 header("location: {$domain}/auth/sign_in/");}
?>

<!DOCTYPE html>



<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $sitename ?> | setting</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $domain ?>/images/favicon.png">
    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="<?php echo $domain ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo $domain ?>/vendor/toastr/toastr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body class="dashboard">
   
    <div id="main-wrapper">
      <!-- nav -->
       <?php  include("../include/header.php") ?>
        
       <!-- sidnav -->
         <?php  include("../include/sidenav.php") ?>

        <div class="content-body">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="page-title">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-xl-4">
                                    <div class="page-title-content">
                                        <h3>Settings</h3>
                                        <p class="mb-2">Welcome <?= $sitename ?> Finance Management</p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xxl-12 col-xl-12">
                        <div class="settings-menu">
                            <a href="settings.html">Account</a>

                        </div>
                        <div class="row">
<?php


$user_id = $_SESSION['user_id'];
$success = "";
$errors  = [];

/* =========================
   HANDLE PROFILE IMAGE UPLOAD
   ========================= */
if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_FILES['profile_image'])) {

    $file = $_FILES['profile_image'];

    if ($file['error'] !== 0) {
        $errors[] = "Error uploading file.";
    } else {

        if ($file['size'] > 2 * 1024 * 1024) {
            $errors[] = "File size must be less than 2MB.";
        }

        $finfo    = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png'
        ];

        if (!array_key_exists($mimeType, $allowedTypes)) {
            $errors[] = "Only JPG and PNG images are allowed.";
        }

        if (empty($errors)) {

            $ext         = $allowedTypes[$mimeType];
            $newFileName = uniqid('profile_', true) . "." . $ext;

            // ✅ Correct filesystem path
            $uploadDir = __DIR__ . "/../images/avatar/";

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $destination = $uploadDir . $newFileName;

            if (move_uploaded_file($file['tmp_name'], $destination)) {

                // ✅ Store RELATIVE path only
                $upload_path = "images/avatar/" . $newFileName;

                $sql  = "UPDATE users SET user_profile = ? WHERE id = ?";
                $stmt = mysqli_prepare($connection, $sql);
                mysqli_stmt_bind_param($stmt, "si", $upload_path, $user_id);

                if (mysqli_stmt_execute($stmt)) {
                    $success = "Profile image uploaded successfully!";
                } else {
                    $errors[] = "Failed to update database.";
                }

                mysqli_stmt_close($stmt);

            } else {
                $errors[] = "Failed to move uploaded file.";
            }
        }
    }
}

/* =========================
   FETCH USER DATA
   ========================= */
$sql2 = "SELECT fullname, user_profile FROM users WHERE id = ?";
$stmt2 = mysqli_prepare($connection, $sql2);
mysqli_stmt_bind_param($stmt2, "i", $user_id);
mysqli_stmt_execute($stmt2);
$result = mysqli_stmt_get_result($stmt2);
$user   = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt2);

$userName    = $user['fullname'];
$userProfile = $user['user_profile'];

// ✅ Default avatar fallback
$defaultAvatar = "images/avatar/avatar.svg";

if (empty($userProfile) || !file_exists(__DIR__ . "/../" . $userProfile)) {
    $userProfile = $defaultAvatar;
}
?>

<!-- =========================
     UPLOAD PROFILE CARD
     ========================= -->
<div class="row">
    <div class="col-xxl-6 col-xl-6 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Upload Profile</h4>
            </div>
            <div class="card-body">

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $err) echo "<p>$err</p>"; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>

                <form action="#" method="post" enctype="multipart/form-data">
                    <div class="row g-3">

                        <div class="col-12 mb-3">
                            <div class="d-flex align-items-center">

                                <!-- Profile Image + Verified -->
                                <div class="position-relative d-inline-block me-3">
                                    <img src="../<?= htmlspecialchars($userProfile) ?>"
                                         class="rounded-circle"
                                         width="55"
                                         height="55"
                                         alt="Profile Image">

                                    <!-- VERIFIED BADGE -->
                                   <!-- <span class="position-absolute bottom-0 end-0 text-success">
                                        <i class="fas fa-check-circle text-white"></i>
                                    </span> -->
                                    <span style="font-weight: 300;" class="position-absolute bottom-2 end-0  badge bg-success">
				        ✔ Verified
				    </span>
                                </div>

                                <div>
                                    <h4 class="mb-0"><?= htmlspecialchars($userName) ?></h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <input type="file" name="profile_image" class="form-control" required>
                        </div>

                        <div class="col-12 mb-3">
                            <button class="btn btn-primary">Upload</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

<?php
/* =========================
   HANDLE PASSWORD CHANGE
   ========================= */
if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['old_password'], $_POST['new_password'])) {

    $old_password = trim($_POST['old_password']);
    $new_password = trim($_POST['new_password']);

    if (empty($old_password) || empty($new_password)) {
        $errors[] = "All fields are required.";
    }

    if (strlen($new_password) < 6) {
        $errors[] = "Minimum of 6 characters required.";
    }

    $sql3 = "SELECT password FROM users WHERE id = ?";
    $stmt3 = mysqli_prepare($connection, $sql3);
    mysqli_stmt_bind_param($stmt3, "i", $user_id);
    mysqli_stmt_execute($stmt3);
    $result = mysqli_stmt_get_result($stmt3);
    $user   = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt3);

    if (!$user || !password_verify($old_password, $user['password'])) {
        $errors[] = "Old password is incorrect.";
    }

    if (empty($errors)) {
        $new_hash = password_hash($new_password, PASSWORD_DEFAULT);

        $sql_update = "UPDATE users SET password = ? WHERE id = ?";
        $stmt_update = mysqli_prepare($connection, $sql_update);
        mysqli_stmt_bind_param($stmt_update, "si", $new_hash, $user_id);

        if (mysqli_stmt_execute($stmt_update)) {
            $success = "Password changed successfully!";
        } else {
            $errors[] = "Failed to update password.";
        }

        mysqli_stmt_close($stmt_update);
    }
}
?>

<!-- =========================
     PASSWORD CHANGE CARD
     ========================= -->
<div class="col-xxl-6 col-xl-6 col-lg-6">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Password Setting</h4>
        </div>
        <div class="card-body">
            <form method="post">
                <div class="row g-3">

                    <div class="col-12 mb-3">
                        <label class="form-label">Old Password</label>
                        <input type="password" name="old_password" class="form-control" required>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>

                    <div class="col-12 mb-3">
                        <button class="btn btn-primary">Change</button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

</div>
</div>

                    </div>
                </div>
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
                                <a href="settings.html#"><?= $sitename ?></a> I All Rights Reserved
                            </p>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="footer-social">
                            <ul>
                                <li><a href="settings.html#"><i class="fi fi-brands-facebook"></i></a></li>
                                <li><a href="settings.html#"><i class="fi fi-brands-twitter"></i></a></li>
                                <li><a href="settings.html#"><i class="fi fi-brands-linkedin"></i></a></li>
                                <li><a href="settings.html#"><i class="fi fi-brands-youtube"></i></a></li>
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