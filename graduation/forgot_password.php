<?php
require 'db.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Validate the email and check if it exists in the database
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Generate a unique token
        $token = bin2hex(random_bytes(50));
        $expires = date("U") + 3600; // 1 hour expiration
        
        // Store the token in the database
        $stmt = $db->prepare("INSERT INTO password_resets (email, token, expires) VALUES (?, ?, ?)");
        $stmt->execute([$email, $token, $expires]);
        
        // Send the reset email
        $resetLink = "https://ii23004devendra.sunnyday.jp/graduation/reset_password.php?token=" . $token;
        mail($email, 'Password Reset', 'Reset your password by clicking the link: ' . $resetLink);
        
        $successMessage = "メールにリンクうを送りました、確認してください。";
    } else {
        $errorMessage = "No user found with that email address.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Request</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    .btn-primary {
    background-color: green !important;
    border-color: green !important;
}

.btn-primary:hover {
    background-color: darkgreen !important;
    border-color: darkgreen !important;
}
</style>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h3>パスワード変更のリクエスト</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($errorMessage)): ?>
                        <div class="alert alert-danger">
                            <?php echo $errorMessage; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($successMessage)): ?>
                        <div class="alert alert-success">
                            <?php echo $successMessage; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="">
                        <div class="mb-3">
                            <label for="email" class="form-label">メールアドレス</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">パスワードリセット変更する</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
