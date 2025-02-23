<?php

include '../components/connect.php';

$message = [];  // Initialize $message as an array
$expression = '/^[a-zA-Z0-9._%+-]+@gmail\.com$/';

if (isset($_POST['submit'])) {

   $id = unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $profession = $_POST['profession'];
   $profession = filter_var($profession, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_EMAIL);
   $pass = $_POST['pass'];
   $cpass = $_POST['cpass'];
   $aboutteacher = isset($_POST['aboutteacher']) ? $_POST['aboutteacher'] : '';

   // Name validation
   if (strpos($name, ' ') === false || count(explode(' ', trim($name))) < 2) {
      $message[] = 'Name must be two words.';
   }

   // Email validation
   if (!preg_match($expression, $email)) {
      $message[] = 'Invalid Email';
   }

   // Password validation
   $pass_pattern = '/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,32}$/';
   if (!preg_match($pass_pattern, $pass)) {
      $message[] = 'Password must be 8-32 characters long, contain at least one number, one lowercase letter, one uppercase letter, and one special character.';
   } elseif ($pass != $cpass) {
      $message[] = 'Confirm password not matched!';
   }

   // Image validation
   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $ext = pathinfo($image, PATHINFO_EXTENSION);
   $allowed_ext = ['jpg', 'jpeg', 'png'];
   if (!in_array(strtolower($ext), $allowed_ext)) {
      $message[] = 'Only JPG, JPEG, and PNG files are allowed.';
   }

   // Image size below 10 MB for now
   if ($_FILES['image']['size'] > 10485760) {
      $message[] = 'Image size must be below 10 MB.';
   }

   if (empty($message)) {
      $pass = sha1($pass);
      $cpass = sha1($cpass);

      $rename = unique_id() . '.' . $ext;
      $image_size = $_FILES['image']['size'];
      $image_tmp_name = $_FILES['image']['tmp_name'];
      $image_folder = '../uploaded_files/' . $rename;

      $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE email = ?");
      $select_tutor->execute([$email]);

      if ($select_tutor->rowCount() > 0) {
         $message[] = 'Email already taken!';
      } else {
         $insert_tutor = $conn->prepare("INSERT INTO `tutors`(id, name, profession, email, password, image, aboutteacher) VALUES(?,?,?,?,?,?,?)");
         $insert_tutor->execute([$id, $name, $profession, $email, $cpass, $rename, $aboutteacher]);
         move_uploaded_file($image_tmp_name, $image_folder);
         $message[] = 'New tutor registered! Please login now';
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
   <title>register</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body style="padding-left: 0;">

   <?php
   if (isset($message)) {
      foreach ($message as $msg) {
         echo '
      <div class="message form">
         <span>' . $msg . '</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
      }
   }
   ?>

   <!-- register section starts  -->

   <section class="form-container">
      <style>
         .aboutteacher {
            background-color: #fff;
            color: #333;
            border: 1px solid #333;
            border-radius: 10px;
            width: 100%;
            height: 130px;
            padding: 10px;
         }
      </style>

      <form class="register" action="" method="post" enctype="multipart/form-data">
         <h3>register new</h3>
         <div class="flex">
            <div class="col">
               <p>your name <span>*</span></p>
               <input type="text" name="name" placeholder="enter your name" maxlength="50" required class="box">
               <p>your profession <span>*</span></p>
               <select name="profession" class="box" required>
                  <option value="" disabled selected>-- select your profession</option>
                  <option value="developer">developer</option>
                  <option value="designer">designer</option>
                  <option value="biologist">biologist</option>
                  <option value="teacher">teacher</option>
                  <option value="engineer">engineer</option>
                  <option value="accountant">accountant</option>
                  <option value="doctor">doctor</option>
               </select>
               <p>your email <span>*</span></p>
               <input type="email" name="email" placeholder="enter your email" maxlength="50" required class="box">
            </div>
            <div class="col">
               <p>your password <span>*</span></p>
               <input type="password" name="pass" placeholder="enter your password" maxlength="32" required class="box">
               <p>confirm password <span>*</span></p>
               <input type="password" name="cpass" placeholder="confirm your password" maxlength="32" required class="box">
               <p>select pic <span>*</span></p>
               <input type="file" name="image" accept="image/jpeg, image/png, image/jpg" required class="box">
            </div>
         </div>

         <p>About You<span>*</span></p>
         <textarea name="aboutteacher" id="aboutteacher" class="aboutteacher"></textarea>
         <p class="link">already have an account? <a href="login.php">login now</a></p>
         <input type="submit" name="submit" value="register now" class="btn">
      </form>

   </section>

   <!-- register section ends -->

   <script>
      let darkMode = localStorage.getItem('dark-mode');
      let body = document.body;

      const enableDarkMode = () => {
         body.classList.add('dark');
         localStorage.setItem('dark-mode', 'enabled');
      }

      const disableDarkMode = () => {
         body.classList.remove('dark');
         localStorage.setItem('dark-mode', 'disabled');
      }

      if (darkMode === 'enabled') {
         enableDarkMode();
      } else {
         disableDarkMode();
      }
   </script>

</body>

</html>