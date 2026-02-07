<?php
include("../../../server/connection.php");



/* =========================
   FETCH USER
========================= */
$id = mysqli_real_escape_string($connection, $_GET['id']);

$sql = "SELECT * FROM users WHERE id = '$id'";
$query = $connection->query($sql);

/* =========================
   ACTIVATE USER
========================= */
if (isset($_GET['activate_user'])) {
    $uid = (int) $_GET['activate_user'];

    mysqli_query($connection, "
        UPDATE users 
        SET status='active' 
        WHERE id='$uid'
    ");

    header("Location: ./?id=$uid");
    exit;
}

/* =========================
   DEACTIVATE USER
========================= */
if (isset($_POST['deactivate_user'])) {
    $uid = (int) $_POST['user_id'];
    $reason = mysqli_real_escape_string($connection, $_POST['deactivation_reason']);

    mysqli_query($connection, "
        UPDATE users 
        SET status='suspended'
        WHERE id='$uid'
    ");

    header("Location: ./?id=$uid");
    exit;
}

/* =========================
   DELETE USER (ONLY IF NOT ACTIVE)
========================= */
if (isset($_POST['delete_user'])) {
    $uid =  $_POST['user_id'];

    // Ensure user is not active
    $check = mysqli_query($connection, "
        SELECT status FROM users WHERE id='$uid'
    ");
    $row = mysqli_fetch_assoc($check);

    

        // OPTIONAL: delete related data first
        mysqli_query($connection, "DELETE FROM withdrawals WHERE user_id='$uid'");
        mysqli_query($connection, "DELETE FROM deposits WHERE user_id='$uid'");
        mysqli_query($connection, "DELETE FROM investments WHERE user_id='$uid'");

        // Delete user
        mysqli_query($connection, "DELETE FROM users WHERE id='$uid'");

        header("Location: ../"); 
        
}
?>



<!DOCTYPE html>



<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $sitename ?> | Transfer-History </title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $domain ?>/images/favicon.png">
    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="<?php echo $domain ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo $domain ?>/vendor/toastr/toastr.min.css">
</head>

<body class="dashboard">

    <div id="main-wrapper">
        <!-- header -->
        <?php include("../../include/nav.php") ?>

        <!-- side nav -->

        <?php include("../../include/sidenav.php") ?>
        <div class="content-body">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="page-title">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-xl-4">
                                    <div class="page-title-content">
                                        <h3>User Details</h3>
                                        <p class="mb-2">Welcome To <?= $sitename ?> Management</p>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="breadcrumbs"><a href="<?php echo $domain  ?>/admin/dashboard/">Home </a>
                                        <span><i class="fi fi-rr-angle-small-right"></i></span>
                                        <a href="<?php echo $domain  ?>/admin/users/details">User Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <?php
                if ($query->num_rows > 0) {
                    $user = $query->fetch_assoc();
                    // Status color logic
                    $status_color = match ($user['status']) {
                        'active'     => 'bg-success',
                        'pending'    => 'bg-warning',
                        'suspended'  => 'bg-danger',
                        'banned'     => 'bg-danger',
                        default      => 'bg-secondary'
                    };
                ?>
                    <div class="col-12">

                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    User Details — <?php echo $user['fullname']; ?>
                                </h4>
                            </div>

                            <div class="card-body">



                                <!-- Profile Image -->
                                <div class="text-center mb-4">

                                    <img src=" <?php echo $user['user_profile'] ==  '' ? $domain . '/images/avatar/avatar.svg' : $domain . 'images/avatar/' .  $user['user_profile'] ?>"
                                        width="90" height="90"
                                        class="rounded-circle border"
                                        alt="User Profile">
                                </div>

                                <div class="table-responsive">
                                    <table class="table">
                                        <tbody>

                                            <tr>
                                                <td><strong>Full Name:</strong></td>
                                                <td><?php echo $user['fullname']; ?></td>
                                            </tr>

                                            <tr>
                                                <td><strong>Email:</strong></td>
                                                <td><?php echo $user['email']; ?></td>
                                            </tr>

                                            <tr>
                                                <td><strong>Account Created:</strong></td>
                                                <td><?php echo $user['created_at']; ?></td>
                                            </tr>

                                            <tr>
                                                <td><strong>Main Balance:</strong></td>
                                                <td>₦<?php echo number_format($user['balance'], 2); ?></td>
                                            </tr>


                                            <tr>
                                                <td><strong>Account Status:</strong></td>
                                                <td>
                                                    <span class="badge text-white <?php echo $status_color; ?>">
                                                        <?php echo ucfirst($user['status']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong>Action</strong>
                                                </td>
                                               

                                                <td>

                                                    <?php if ($user['status'] !== 'active') { ?>

                                                        <!-- ACTIVATE -->
                                                        <a href="./?id=<?= $user['id'] ?>&activate_user=<?= $user['id'] ?>">
                                                            <button class="btn btn-success btn-sm mb-2">
                                                                Activate User
                                                            </button>
                                                        </a>

                                                        <!-- DELETE -->


                                                    <?php } else { ?>

                                                        <!-- DEACTIVATE -->
                                                        <form method="POST" class="flex flex-wrap">
                                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                            <input type="text"
                                                                name="deactivation_reason"
                                                                class="form-control mb-2"
                                                                placeholder="Reason for deactivation"
                                                                required>
                                                            <button type="submit" name="deactivate_user" class="btn btn-warning btn-sm">
                                                                Deactivate User
                                                            </button>
                                                        </form>

                                                    <?php } ?>

                                                    <form method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this user?');">
                                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                        <button type="submit" name="delete_user" class="btn btn-danger btn-sm">
                                                            Delete User
                                                        </button>
                                                    </form>

                                                </td>


                                            </tr>

                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>

                <?php } else { ?>
                    <div class="alert alert-danger">User not found</div>
                <?php } ?>

            </div>
        </div>
    </div>
    <script src="<?php echo $domain ?>/vendor/jquery/jquery.min.js"></script>
    <script src="<?php echo $domain ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!--  -->
    <!--  -->
    <script src="<?php echo $domain ?>/js/scripts.js"></script>
</body>

</html>