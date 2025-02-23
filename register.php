<?php

include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
   $user_id = $_COOKIE['user_id'];
} else {
   $user_id = '';
}

$expression = '/^[a-zA-Z0-9._%+-]+@gmail\.com$/';
$message = [];

if (isset($_POST['submit'])) {

   $id = unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);

   if (strpos($name, ' ') === false || count(explode(' ', trim($name))) < 2) {
      $message[] = 'Name must be two words.';
   }


   $email = $_POST['email'];
   if (!preg_match($expression, $email)) {
      $message[] = 'Invalid Email';
   } else {
      $pass_pattern = '/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,32}$/';
      $email = filter_var($email, FILTER_SANITIZE_EMAIL);

      $pass = $_POST['pass'];
      $cpass = $_POST['cpass'];

      if (!preg_match($pass_pattern, $pass)) {
         $message[] = 'Password does not meet the criteria. <br> Password must have one both small and capital letters and one symbol along with one integer';
      } elseif ($pass != $cpass) {
         $message[] = 'Confirm password not matched!';
      }

      $image = $_FILES['image']['name'];
      $image = filter_var($image, FILTER_SANITIZE_STRING);
      $ext = pathinfo($image, PATHINFO_EXTENSION);

      // Only accept jpg, jpeg, and png files
      $allowed_ext = ['jpg', 'jpeg', 'png'];
      if (!in_array(strtolower($ext), $allowed_ext)) {
         $message[] = 'Only JPG, JPEG, and PNG files are allowed.';
      }
      
      // Image size below 10 MB for now
      // if($_FILES['image']['size']>8388608){
      if($_FILES['image']['size']>10485760){
         $message[] = 'Image size must be below 10 MB.';
      }

      if (empty($message)) {
         $pass = sha1($pass);
         $pass = filter_var($pass, FILTER_SANITIZE_STRING);
         $cpass = sha1($cpass);
         $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

         $rename = unique_id() . '.' . $ext;
         $image_size = $_FILES['image']['size'];
         $image_tmp_name = $_FILES['image']['tmp_name'];
         $image_folder = 'uploaded_files/' . $rename;

         $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
         $select_user->execute([$email]);

         if ($select_user->rowCount() > 0) {
            $message[] = 'Email already taken!';
         } else {
            $insert_user = $conn->prepare("INSERT INTO `users`(id, name, email, password, image) VALUES(?,?,?,?,?)");
            $insert_user->execute([$id, $name, $email, $pass, $rename]);
            move_uploaded_file($image_tmp_name, $image_folder);

            $verify_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ? LIMIT 1");
            $verify_user->execute([$email, $pass]);
            $row = $verify_user->fetch(PDO::FETCH_ASSOC);

            if ($verify_user->rowCount() > 0) {
               setcookie('user_id', $row['id'], time() + 60 * 60 * 24 * 30, '/');
               header('location:home.php');
            }
         }
      }
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Home</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <?php include 'components/user_header.php'; ?>

   <section class="form-container">

      <form class="register" action="" method="post" enctype="multipart/form-data">
         <h3>create account</h3>

         <?php
         if (!empty($message)) {
            foreach ((array)$message as $msg) {
               echo '<div class="alert">' . $msg . '</div>';
            }
         }
         ?>

         <div class="flex">
            <div class="col">
               <p>your name <span>*</span></p>
               <input type="text" name="name" placeholder="enter your name" maxlength="50" required class="box">
               <p>your email <span>*</span></p>
               <input type="email" name="email" placeholder="enter your email" maxlength="50" required class="box">
            </div>
            <div class="col">
               <p>your password <span>*</span></p>
               <input type="password" name="pass" placeholder="enter your password" maxlength="32" required class="box">
               <p>confirm password <span>*</span></p>
               <input type="password" name="cpass" placeholder="confirm your password" maxlength="32" required class="box">
            </div>
         </div>
         <p>select pic <span>*</span></p>
         <input type="file" name="image" accept="image/jpeg, image/png, image/jpg" required class="box">
         <p class="link">already have an account? <a href="login.php">Login now</a></p>
         <input type="submit" name="submit" value="register now" class="btn">
      </form>

   </section>

   <?php include 'components/footer.php'; ?>

   <!-- custom js file link  -->
   <script src="js/script.js"></script>

</body>

</html>
