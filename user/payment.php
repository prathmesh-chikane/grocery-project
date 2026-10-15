<?php
include('../includes/connection.php'); 
include('../functions/common_function.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php
$user_ip=getIPAddress();
$get_user="Select * from `user' where user_ip=$user_ip";
$result=mysqli_query($con, $get_user);
$run_query=mysqli_fetch_array($result);
$user_id=$run_query['user_id'];

?>

<div class="container">
    <h2 class="text-center text-info my-5">Payment Options</h2>
    <div class="row d-flex justify-content-center align-items-center my-5">
        <!-- PayPal Payment -->
        <div class="col-md-6 text-center">
            <a href="https://www.paypal.com" target="_blank">
                <img src="../images/payment.jpg" alt="PayPal Payment" class="img-fluid mb-3">
            </a>
            <p>Pay securely using PayPal</p>
        </div>
        <!-- Cash on Delivery -->
        <div class="col-md-6 text-center">
            <a href="../order.php ?user_id=<?php echo $user_id ?>"></a>
                <h2 class="text-center my-5">Pay Offline</h2>
            </a>
            <p>Choose Cash on Delivery for offline payment</p>
        </div>
    </div>
</div>

</body>
</html>