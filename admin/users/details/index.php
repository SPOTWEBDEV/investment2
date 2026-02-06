<?php
include("../../../server/connection.php");


$id = mysqli_real_escape_string($connection, $_GET['id']);

$sql = "
    SELECT 
        *
    FROM users
    WHERE id = '$id'
";

$query = $connection->query($sql);





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
                                    <img src="<?php echo $domain ?>/uploads/profile/<?php echo $user['user_profile']; ?>"
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
                                                <td>$<?php echo number_format($user['balance'], 2); ?></td>
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
                                                    <?php

                                                    if( $user['status'] !== 'active' ){ ?>
                                                        <a href="./?activate_user=<?= $user['id'] ?>">
                                                            <button class="btn btn-success btn-sm activate-user">Activate User</button>
                                                        </a>    
                                                    <?php } else { ?>
                                                       
                                                       <form method="POST">
                                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                            <input type="text" name="deactivation_reason" class="form-control mb-2" placeholder="Reason for deactivation" required>
                                                            <button type="submit" name="deactivate_user" class="btn btn-danger btn-sm deactivate-user">Deactivate User</button>
                                                       </form>
                                                        
                                                    <?php }
                                                    ?>
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