<?php
session_start();

// Include the database connection file
include 'db0.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not authenticated
    exit();
}

// Fetch user data from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Landing Page with Container Image</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.9.1/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
.dropdown-menu {
    position: absolute; /* Positioning the dropdown */
    top: 100%; /* Position it below the dropdown toggle */
    left: 50%; /* Center it horizontally */
    transform: translateX(-50%); /* Adjust for centering */
    z-index: 1000; /* Ensure it appears above other elements */
}

/* Optional: Card Customization */
.card {
    border: none; /* Rounded corners for the card */
}

/* Add hover effect to the card */
.card:hover {
    box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.25); /* Stronger shadow on hover */
    transition: box-shadow 0.3s ease; /* Smooth transition */
}

</style>
</head>



<body>
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <a class="navbar-brand" href="#">IICA.JP</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#">講師を追加</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">科目を追加</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">請求書</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">ログイン</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <div class="dropdown-item">
                                <div class="card shadow-sm" style="width: 200px;">
                                    <div class="text-center mt-3">
                                        <img src="user.png" alt="User Avatar" class="rounded-circle" style="width: 80px; height: 80px;">
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title text-center"><?php echo htmlspecialchars($user['name']); ?></h5>
                                        <p class="card-text text-center">Email: <?php echo htmlspecialchars($user['email']); ?></p>
                                        <div class="d-grid gap-2">
                                            <a href="logout.php" class="btn btn-danger">Logout</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>


<div class="container">
    <!-- Date Filter Form -->
    <form method="POST" action="">
        <div class="row mb-3">
            <label for="monthYear" class="col-sm-2 col-form-label">Select Year and Month</label>
            <div class="col-sm-4">
                <input type="month" class="form-control" id="monthYear" name="monthYear">
            </div>
            <div class="col-sm-4">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>

    <?php
    header("Content-Type: text/html; charset=UTF-8");
    require("p0.php");

    $limit = 10; 
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
    $offset = ($page - 1) * $limit; 

    // Get selected table (Lecture in this case)
    $sel1 = isset($_POST['sellist1']) ? htmlspecialchars($_POST['sellist1']) : "Lecture";

    // Initialize the SQL query without filters
    $baseQuery = "SELECT lecture_id, subject as 科目, lecture_date as 日付, comasuu as コーマ数, bikou as 備考 FROM $sel1";
    $whereClause = "";
    $params = [];  // Parameters for the SQL query

    // Capture the selected year and month
    if (isset($_POST['monthYear']) && !empty($_POST['monthYear'])) {
        $monthYear = $_POST['monthYear']; // in "YYYY-MM" format
        $year = substr($monthYear, 0, 4);  // Get the year part
        $month = substr($monthYear, 5, 2); // Get the month part

        // Add the filter to the SQL query
        $whereClause = " WHERE YEAR(lecture_date) = :year AND MONTH(lecture_date) = :month";
        $params[':year'] = $year;
        $params[':month'] = $month;
    }

    try {
        // Count total rows with or without filters
        $countQuery = "SELECT COUNT(*) FROM $sel1" . $whereClause;
        $stmt = $dbhost->prepare($countQuery);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $totalRows = $stmt->fetchColumn();

        // Fetch filtered or all data based on the filter
        $finalQuery = $baseQuery . $whereClause . " LIMIT :limit OFFSET :offset";
        $sth = $dbhost->prepare($finalQuery);
        foreach ($params as $key => $value) {
            $sth->bindValue($key, $value);
        }
        $sth->bindValue(':limit', $limit, PDO::PARAM_INT);
        $sth->bindValue(':offset', $offset, PDO::PARAM_INT);
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);

        // Display the table
        echo "<table class='table table-striped table-bordered table-hover caption-top'>";
        echo "<caption class='text-center display-6 mb-4'>" . htmlspecialchars($sel1) . " List</caption>";
        echo "<thead class='table-dark'><tr>";
        if (!empty($result)) {
            foreach (array_keys($result[0]) as $columnHeader) {
                echo "<th>" . htmlspecialchars($columnHeader) . "</th>";
            }
            echo "<th> 編集</th>"; 
        }
        echo "</tr></thead><tbody>";

        foreach ($result as $row) {
            echo "<tr>";
            foreach ($row as $columnValue) {
                echo "<td>" . htmlspecialchars($columnValue) . "</td>";
            }
            echo "<td>";
            echo "<a href='edit_lecture.php?lecture_id=" . $row['lecture_id'] . "' class='btn btn-warning btn-sm me-2'><i class='bi bi-pencil'></i> 変更</a>";
            echo "<a href='delete_lecture.php?lecture_id=" . $row['lecture_id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this lecture?\");'><i class='bi bi-trash'></i> 削除</a>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";

        // Pagination
        $totalPages = ceil($totalRows / $limit);

        echo "<nav aria-label='Page navigation'>";
        echo "<ul class='pagination justify-content-center'>";
        
        // Previous page button
        if ($page > 1) {
            echo "<li class='page-item'><a class='page-link' href='?page=" . ($page - 1) . "'>Previous</a></li>";
        } else {
            echo "<li class='page-item disabled'><a class='page-link'>Previous</a></li>";
        }

        // Pagination numbers
        for ($i = 1; $i <= $totalPages; $i++) {
            if ($i == $page) {
                echo "<li class='page-item active'><a class='page-link' href='?page=$i'>$i</a></li>";
            } else {
                echo "<li class='page-item'><a class='page-link' href='?page=$i'>$i</a></li>";
            }
        }

        // Next page button
        if ($page < $totalPages) {
            echo "<li class='page-item'><a class='page-link' href='?page=" . ($page + 1) . "'>Next</a></li>";
        } else {
            echo "<li class='page-item disabled'><a class='page-link'>Next</a></li>";
        }

        echo "</ul>";
        echo "</nav>";

        $dbhost = null;
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
    ?>
</div>






  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

  <script>

  document.addEventListener('DOMContentLoaded', function() {
    fetch('get_user_info.php')
      .then(response => response.json())
      .then(data => {
        if (data.error) {
          console.error(data.error);
        } else {
          document.getElementById('username').innerText = data.username;
          document.getElementById('userEmail').innerText = data.email;
        }
      })
      .catch(error => console.error('Error fetching user data:', error));
  });

function toggleCard(event) {
            const userCard = document.getElementById('userCard');
            userCard.classList.toggle('show');
            // Close the card if clicked outside
            document.addEventListener('click', function (e) {
                if (!event.target.closest('.dropdown-toggle') && !userCard.contains(e.target)) {
                    userCard.classList.remove('show');
                }
            });
        }
</script>
</body>

</html>
