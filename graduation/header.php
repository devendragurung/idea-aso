<?php

if(isset($message)){
   foreach($message as $msg){ 
      echo '
      <div class="alert alert-info alert-dismissible fade show" role="alert">
         '.htmlspecialchars($msg).'
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      ';
   }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>header</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


<header class="header bg-light py-3 shadow-sm">

   <div class="container d-flex justify-content-between align-items-center">

      <!-- Logo -->
      <a href="admin_page.php" class="logo text-success fw-bold">Groco<span class="text-primary">.</span></a>

      <!-- Navigation -->
      <nav class="navbar">
         <ul class="nav">
            <li class="nav-item">
               <a href="home.php" class="nav-link text-dark">Home</a>
            </li>
            <li class="nav-item">
               <a href="shop.php" class="nav-link text-dark">Shop</a>
            </li>
            <li class="nav-item">
               <a href="orders.php" class="nav-link text-dark">Orders</a>
            </li>
            <li class="nav-item">
               <a href="about.php" class="nav-link text-dark">About</a>
            </li>
            <li class="nav-item">
               <a href="contact.php" class="nav-link text-dark">Contact</a>
            </li>
         </ul>
      </nav>

      <!-- Icons and Cart -->
      <div class="d-flex align-items-center">
         <div class="d-flex me-4">
            <div id="menu-btn" class="fas fa-bars me-3 fs-4 cursor-pointer"></div>
            <div id="user-btn" class="fas fa-user fs-4 cursor-pointer"></div>
            <a href="search_page.php" class="fas fa-search fs-4 me-3 text-decoration-none text-dark"></a>
         </div>

         <?php
            if(isset($user_id)) {
               $count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
               $count_cart_items->execute([$user_id]);
               $count_wishlist_items = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ?");
               $count_wishlist_items->execute([$user_id]);
         ?>
         <a href="wishlist.php" class="btn btn-outline-danger position-relative me-3">
            <i class="fas fa-heart"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
               <?= $count_wishlist_items->rowCount(); ?>
            </span>
         </a>
         <a href="cart.php" class="btn btn-outline-success position-relative">
            <i class="fas fa-shopping-cart"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success">
               <?= $count_cart_items->rowCount(); ?>
            </span>
         </a>
         <?php } ?>
      </div>

      <!-- User Profile -->
      <div class="profile ms-4">
         <?php
            if(isset($user_id)){
               $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
               $select_profile->execute([$user_id]);
               if($fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC)){
         ?>
         <img src="uploaded_img/<?= htmlspecialchars($fetch_profile['image']); ?>" alt="Profile Picture" class="rounded-circle" style="width:40px; height:40px;">
         <p class="d-inline-block ms-2"><?= htmlspecialchars($fetch_profile['name']); ?></p>
         <a href="user_profile_update.php" class="btn btn-outline-primary btn-sm ms-2">Update Profile</a>
         <a href="logout.php" class="btn btn-outline-danger btn-sm ms-2">Logout</a>
         <?php } } else { ?>
         <div class="d-flex">
            <a href="login.php" class="btn btn-outline-primary btn-sm me-2">Login</a>
            <a href="register.php" class="btn btn-outline-secondary btn-sm">Register</a>
         </div>
         <?php } ?>
      </div>

   </div>

</header>
