<?php
include '../components/connect.php';

// Ensure admin is logged in
if (isset($_COOKIE['admin_id'])) {
    $admin_id = filter_var($_COOKIE['admin_id'], FILTER_SANITIZE_STRING);
} else {
    $admin_id = '';
    header('location:login.php');
    exit;
}

// Handle booking deletion
if (isset($_POST['delete'])) {
    $delete_id = filter_var($_POST['delete_id'], FILTER_SANITIZE_STRING);

    $verify_delete = $conn->prepare("SELECT * FROM `bookings` WHERE booking_id = ?");
    $verify_delete->execute([$delete_id]);

    if ($verify_delete->rowCount() > 0) {
        $delete_bookings = $conn->prepare("DELETE FROM `bookings` WHERE booking_id = ?");
        $delete_bookings->execute([$delete_id]);
        $success_msg[] = 'Booking deleted!';
    } else {
        $warning_msg[] = 'Booking deleted already!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings</title>

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link -->
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<!-- header section starts -->
<?php include '../components/admin_header.php'; ?>
<!-- header section ends -->

<!-- bookings section starts -->
<section class="grid">
    <h1 class="heading">Bookings</h1>

    <div class="box-container">
        <?php
        // Fetch all bookings
        $select_bookings = $conn->prepare("SELECT booking_id, name, email, number, check_in, check_out, rooms, adults, childs, total_price FROM `bookings`");
        $select_bookings->execute();

        if ($select_bookings->rowCount() > 0) {
            while ($fetch_bookings = $select_bookings->fetch(PDO::FETCH_ASSOC)) {
        ?>
        <div class="box">
            <p>Booking ID: <span><?= htmlspecialchars($fetch_bookings['booking_id']); ?></span></p>
            <p>Name: <span><?= htmlspecialchars($fetch_bookings['name']); ?></span></p>
            <p>Email: <span><?= htmlspecialchars($fetch_bookings['email']); ?></span></p>
            <p>Number: <span><?= htmlspecialchars($fetch_bookings['number']); ?></span></p>
            <p>Check-in: <span><?= htmlspecialchars($fetch_bookings['check_in']); ?></span></p>
            <p>Check-out: <span><?= htmlspecialchars($fetch_bookings['check_out']); ?></span></p>
            <p>Rooms: <span><?= htmlspecialchars($fetch_bookings['rooms']); ?></span></p>
            <p>Adults: <span><?= htmlspecialchars($fetch_bookings['adults']); ?></span></p>
            <p>Children: <span><?= htmlspecialchars($fetch_bookings['childs']); ?></span></p>
            <p>Total Price: <span>
                <?php
                if ($fetch_bookings['total_price'] > 0) {
                    echo '$' . number_format($fetch_bookings['total_price'], 2);
                } else {
                    echo 'N/A (Contact support)';
                }
                ?>
            </span></p>
            <form action="" method="POST">
                <input type="hidden" name="delete_id" value="<?= htmlspecialchars($fetch_bookings['booking_id']); ?>">
                <input type="submit" value="Delete Booking" onclick="return confirm('Delete this booking?');" name="delete" class="btn">
            </form>
        </div>
        <?php
            }
        } else {
        ?>
        <div class="box" style="text-align: center;">
            <p>No bookings found!</p>
            <a href="dashboard.php" class="btn">Go to Home</a>
        </div>
        <?php
        }
        ?>
    </div>
</section>
<!-- bookings section ends -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<!-- custom js file link -->
<script src="../js/admin_script.js"></script>

<?php include '../components/message.php'; ?>

</body>
</html>