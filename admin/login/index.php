<?php
include("../../server/connection.php");
include("../controllers/login.php");


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($sitename) ?> | Sign In</title>
    <link rel="icon" type="image/png" sizes="16x16" href="<?php  echo $domain ?>/images/favicon.png">
    <link rel="stylesheet" href="<?php  echo $domain ?>/css/style.css">
    <link rel="stylesheet" href="<?php  echo $domain ?>/vendor/toastr/toastr.min.css">
</head>

<body class="dashboard">
    
    <div class="authincation">
        <div class="container">
            <div class="row justify-content-center align-items-center g-0">
                <div class="col-xl-8">
                    <div class="row g-0">
                        <div class="col-lg-6">
                            <div class="welcome-content">
                                <div class="welcome-title">
                                    <div class="mini-logo">
                                        <a href="index.html"><img src="<?php  echo $domain ?>/images/logo-white.png" alt="" width="30" /></a>
                                    </div>
                                    <h3>Welcome to <?= htmlspecialchars($sitename) ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="auth-form">
                                <h4>Sign In</h4>
                                <form action="" method="POST">
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <label class="form-label">Email</label>
                                            <input name="email" type="text" class="form-control"/>
                                            
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="form-label">Password</label>
                                            <input name="password" type="password" class="form-control" />
                                           
                                        </div>                                        
                                    </div>
                                    <div class="mt-3 d-grid gap-2">
                                        <button type="submit" name="login" class="btn btn-primary me-8 text-white">Sign In</button>
                                    </div>
                                </form>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php  echo $domain ?>/vendor/jquery/jquery.min.js"></script>
    <script src="<?php  echo $domain ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?php  echo $domain ?>/js/scripts.js"></script>
</body>

</html>