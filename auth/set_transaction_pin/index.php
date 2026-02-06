<?php
include("../../server/connection.php");

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../sign_in/");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";
$has_pin = false;

// Fetch user PIN status
$sql = "SELECT transaction_pin FROM users WHERE id = ?";
$stmt = mysqli_prepare($connection, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $db_pin);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

$has_pin = !empty($db_pin);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $entered_pin = $_POST['pin'] ?? "";

    if (!$has_pin) {
        // User is creating a PIN
        $confirm_pin = $_POST['confirm_pin'] ?? "";

        if ($entered_pin === $confirm_pin && strlen($entered_pin) === 4) {
            $hashed_pin = password_hash($entered_pin, PASSWORD_DEFAULT);

            $sql = "UPDATE users SET transaction_pin = ? WHERE id = ?";
            $stmt = mysqli_prepare($connection, $sql);
            mysqli_stmt_bind_param($stmt, "si", $hashed_pin, $user_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            $message = "PIN successfully created!";
            echo "<script>
                setTimeout(() => {
                    window.location.href = '../../dashboard/';
                }, 1500);
            </script>";
        } else {
            $message = "PINs do not match!";
        }
    } else {
        // User verifies PIN
        if ($db_pin !== null && password_verify($entered_pin, $db_pin)) {

            echo "<script>
                window.location.href = '../../dashboard/';
            </script>";
            exit;
        } else {
            $message = "Incorrect PIN";
        }
    }
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Set Transaction PIN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $domain ?>/images/favicon.png">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="<?php echo $domain ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo $domain ?>/vendor/toastr/toastr.min.css">

    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        body {

            background: #3A5BE0;

        }

        .pin-box {
            width: 380px;
            margin: 90px auto;
            background: #fff;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
            text-align: center;
        }

        /* PIN dots */
        .pin-dots {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin: 25px 0;
        }

        .dot {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 2px solid #2C9497;
        }

        .dot.filled {
            background: #2C9497;
        }

        /* keypad */
        .keypad button {
            width: 100%;
            padding: 18px;
            font-size: 22px;
            background: #2C9497;
            border: none;
            color: #fff;
            border-radius: 10px;
            transition: .2s;
        }

        .keypad button:active {
            transform: scale(.96);
        }

        .delete-btn {
            background: #dc3545 !important;
        }

        .ok-btn {
            background: #198754 !important;
        }

        @media(max-width:400px) {
            .pin-box {
                width: 95%;
                padding: 15px;
            }

            .keypad button {
                padding: 12px;
                font-size: 17px;
                border-radius: 7px;

            }

        }
    </style>
</head>

<body>

    <div class="pin-box">
        <h3 class="mb-3">
            <?php echo $has_pin ? "Enter Transaction PIN" : "Create Transaction PIN"; ?>
        </h3>

        <?php if (!empty($message)) { ?>
            <div class="alert alert-warning py-2">
                <?= $message ?>
            </div>
        <?php } ?>

        <form method="POST" id="pinForm">

            <input type="hidden" name="pin" id="pinInput">
            <input type="hidden" name="confirm_pin" id="confirmPinInput">

            <!-- PIN DOTS -->
            <div class="pin-dots">
                <div class="dot" id="d1"></div>
                <div class="dot" id="d2"></div>
                <div class="dot" id="d3"></div>
                <div class="dot" id="d4"></div>
            </div>

            <!-- NUM PAD -->
            <div class="row keypad g-2 mt-2">
                <?php
                $keys = [1, 2, 3, 4, 5, 6, 7, 8, 9, "del", 0, "ok"];
                foreach ($keys as $key) {

                    if ($key === "del") {
                        echo '
                        <div class="col-4">
                            <button type="button" class="delete-btn" id="deleteBtn">⌫</button>
                        </div>';
                    } elseif ($key === "ok") {
                        echo '
                        <div class="col-4">
                            <button type="submit" class="ok-btn">OK</button>
                        </div>';
                    } else {
                        echo '
                        <div class="col-4">
                            <button type="button" class="num-btn" data-num="' . $key . '">' . $key . '</button>
                        </div>';
                    }
                }
                ?>
            </div>

        </form>
    </div>

    <script>
        let pin = "";
        let confirmPin = "";
        let stage = "<?php echo $has_pin ? 'verify' : 'create'; ?>";

        const dots = [
            document.getElementById("d1"),
            document.getElementById("d2"),
            document.getElementById("d3"),
            document.getElementById("d4")
        ];

        function refreshDots() {
            dots.forEach((dot, i) => {
                if (i < pin.length) dot.classList.add("filled");
                else dot.classList.remove("filled");
            });
        }

        document.querySelectorAll(".num-btn").forEach(btn => {
            btn.addEventListener("click", () => {
                if (pin.length < 4) {
                    pin += btn.dataset.num;
                    refreshDots();
                }
            });
        });

        // Delete
        document.getElementById("deleteBtn").onclick = () => {
            pin = pin.slice(0, -1);
            refreshDots();
        };

        // Submit
        document.getElementById("pinForm").onsubmit = function(e) {

            if (pin.length !== 4) {
                e.preventDefault();
                return;
            }

            if (stage === "create") {

                if (confirmPin === "") {
                    confirmPin = pin;
                    pin = "";
                    refreshDots();

                    // Show bootstrap alert
                    document.querySelector(".alert")?.remove();
                    document.querySelector(".pin-box")
                        .insertAdjacentHTML("afterbegin",
                            `<div class="alert alert-info py-2">Confirm PIN</div>`
                        );

                    e.preventDefault();
                    return;
                }

                if (confirmPin !== pin) {
                    e.preventDefault();
                    pin = "";
                    confirmPin = "";
                    refreshDots();

                    document.querySelector(".alert")?.remove();
                    document.querySelector(".pin-box")
                        .insertAdjacentHTML("afterbegin",
                            `<div class="alert alert-danger py-2">PINs do not match!</div>`
                        );

                    return;
                }

                document.getElementById("pinInput").value = confirmPin;
                document.getElementById("confirmPinInput").value = pin;
            } else {
                document.getElementById("pinInput").value = pin;
            }
        };
    </script>

    <script src="<?php echo $domain ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>

</html>