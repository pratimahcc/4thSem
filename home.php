<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

$select_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ?");
$select_likes->execute([$user_id]);
$total_likes = $select_likes->rowCount();

$select_comments = $conn->prepare("SELECT * FROM `comments` WHERE user_id = ?");
$select_comments->execute([$user_id]);
$total_comments = $select_comments->rowCount();

$select_bookmark = $conn->prepare("SELECT * FROM `bookmark` WHERE user_id = ?");
$select_bookmark->execute([$user_id]);
$total_bookmarked = $select_bookmark->rowCount();

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
<style>
   .calander{
      background-color: #fff;
      border:2px solid #333;
      margin-top: 2rem;
      padding: 20px;
      border-radius: 10px;
   }
   .calander input{
      font-size: 20px;
      background : transparent;
      padding:10px;
      color:#333;
      border: 1px solid #fff;
      border-radius:10px;
   }
</style>
<body>

<?php include 'components/user_header.php'; ?>

<!-- quick  section stselectarts  -->

<section class="quick-select">

   <h1 class="heading">options</h1>

   <div class="box-container">

    <?php
         if($user_id != ''){
      ?>
      <div class="box">
         <h3 class="title">likes and comments</h3>
         <p>total likes : <span><?= $total_likes; ?></span></p>
         <a href="likes.php" class="inline-btn">view likes</a>
         <p>total comments : <span><?= $total_comments; ?></span></p>
         <a href="comments.php" class="inline-btn">view comments</a>
         <p>saved playlist : <span><?= $total_bookmarked; ?></span></p>
         <a href="bookmark.php" class="inline-btn">view bookmark</a>
      </div>
      */<?php
         }else{ }
      ?>
      <div class="box" style="text-align: center;">
         <h3 class="title">Please Login or Register</h3>
          <div class="flex-btn" style="padding-top: .5rem;">
            <a href="login.php" class="option-btn">Login</a>
            <a href="register.php" class="option-btn">Register</a>
         </div>
      </div>
     
    

      <div class="box">
         <h3 class="title">Subjects Available</h3>
         <div class="flex">
            <a href="search_course.php?"><i class="fas fa-user"></i><span>Science</span></a>
            <a href="search_course.php?"><i class="fas fa-chart-simple"></i><span>Mathamatics</span></a>
            <a href="search_course.php?"><i class="fas fa-computer"></i><span>computer</span></a>
         <a href="search_course.php?"><i class="fas fa-pen"></i><span>Accounting</span></a>
           
      
      </div>
      <form action="" class="calander">
         <input type="date">
      </form>
      </div>

      
      </div>

      <div class="box tutor">
         <h1 class="title">Become a Educator</h1>
         <h3>Choose an online tuition platform that offers a comprehensive curriculum aligned with your academic goals and syllabus requirement</h3>
         <a href="admin/register.php" class="inline-btn">get started</a>
      </div>
    

   </div>

</section>

<!-- quick select section ends -->

<!-- courses section starts  -->

<section class="courses">

   <h1 class="heading">latest courses</h1>

   <div class="box-container">

      <?php
         $select_courses = $conn->prepare("SELECT * FROM `playlist` WHERE status = ? ORDER BY date DESC LIMIT 6");
         $select_courses->execute(['active']);
         if($select_courses->rowCount() > 0){
            while($fetch_course = $select_courses->fetch(PDO::FETCH_ASSOC)){
               $course_id = $fetch_course['id'];

               $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ?");
               $select_tutor->execute([$fetch_course['tutor_id']]);
               $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="box">
         <div class="tutor">
            <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
            <div>
               <h3><?= $fetch_tutor['name']; ?></h3>
               <span><?= $fetch_course['date']; ?></span>
            </div>
         </div>
         <img src="uploaded_files/<?= $fetch_course['thumb']; ?>" class="thumb" alt="">
         <h3 class="title"><?= $fetch_course['title']; ?></h3>
         <a href="playlist.php?get_id=<?= $course_id; ?>" class="inline-btn">view playlist</a>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty">no courses added yet!</p>';
      }
      ?>

   </div>

   <div class="more-btn">
      <a href="courses.php" class="inline-option-btn">view more</a>
   </div>

</section>

<!-- courses section ends -->












<!-- footer section starts  -->
<?php include 'components/footer.php'; ?>
<!-- footer section ends -->

<!-- custom js file link  -->
<script src="js/script.js"></script>
   
</body>
</html>