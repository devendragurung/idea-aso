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
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Teacher Data</title>
    <style>
        .flex-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .flex-container .form-select,
        .flex-container .form-control,
        .flex-container button {
            flex: 1;
            margin: 0 5px;
        }
        .salary-button {
            text-align: right;
            margin-top: 15px;
        }
    </style>
</head>
<body>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar with User Info</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
</head>
<body>

    <div class="container mt-3">
        <?php
        // Include the database connection file
        include('database.php');

        // Fetch unique teacher IDs from the database
        $sql = "SELECT DISTINCT teacher_id FROM Lecture"; 
        $result = $conn->query($sql);
        ?>
        <div class="row ">
            <div class="col-sm-12 mt-3">
                <!-- Form to select the teacher ID, year, and month -->
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="flex-container">
                        <!-- Dropdown selection for teacher IDs -->
                        <select class="form-select form-select-lg" id="sel1" name="sellist1">
                            <option value="">講師IDを選んでください</option>
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='" . htmlspecialchars($row['teacher_id']) . "'>" . htmlspecialchars($row['teacher_id']) . "</option>";
                                }
                            } else {
                                echo "<option value=''>データがありません</option>";
                            }
                            ?>
                        </select>

                        <!-- Calendar picker for selecting year and month -->
                        <input type="month" class="form-control form-control-lg" id="month" name="month">

                        <!-- Submit button -->
                        <button type="submit" class="btn btn-success btn-lg" id="submit" name="submit">確認する</button>
                    </div>
                </form>
            </div>

            <div class="row mt-2 text-center">
    <?php
    $sel1 = $_POST['sellist1'] ?? null;
    $selectedMonth = $_POST['month'] ?? null;

    // Set the number of results per page
    $results_per_page = 10;

    // Get the current page number from the URL (default is page 1)
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

    // Calculate the starting row for the query
    $start_from = ($page - 1) * $results_per_page;

    // Check if form is submitted and filters are applied
    if (isset($_POST['submit']) && ($sel1 || $selectedMonth)) {
        $query = "SELECT * FROM Lecture WHERE 1=1";

        if ($sel1) {
            $query .= " AND teacher_id = ?";
        }
        if ($selectedMonth) {
            $year = date("Y", strtotime($selectedMonth));
            $month = date("m", strtotime($selectedMonth));
            $query .= " AND YEAR(lecture_date) = ? AND MONTH(lecture_date) = ?";
        }

        // Add LIMIT clause for pagination
        $query .= " LIMIT ?, ?";

        // Prepare the SQL statement
        $stmt = $conn->prepare($query);

        // Bind parameters based on selected filters
        if ($sel1 && $selectedMonth) {
            $stmt->bind_param("ssiii", $sel1, $year, $month, $start_from, $results_per_page);
        } elseif ($sel1) {
            $stmt->bind_param("sii", $sel1, $start_from, $results_per_page);
        } elseif ($selectedMonth) {
            $stmt->bind_param("ssii", $year, $month, $start_from, $results_per_page);
        }

        $stmt->execute();
        $result_teacher = $stmt->get_result();
    } else {
        // Fetch all data with pagination by default
        $query = "SELECT * FROM Lecture LIMIT ?, ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $start_from, $results_per_page);
        $stmt->execute();
        $result_teacher = $stmt->get_result();
    }

    // Count the total number of rows for pagination
    $total_rows_query = "SELECT COUNT(*) AS total FROM Lecture";
    if (isset($sel1) || isset($selectedMonth)) {
        $total_rows_query .= " WHERE 1=1";
        if ($sel1) {
            $total_rows_query .= " AND teacher_id = '$sel1'";
        }
        if ($selectedMonth) {
            $year = date("Y", strtotime($selectedMonth));
            $month = date("m", strtotime($selectedMonth));
            $total_rows_query .= " AND YEAR(lecture_date) = '$year' AND MONTH(lecture_date) = '$month'";
        }
    }
    $total_rows_result = $conn->query($total_rows_query);
    $total_rows = $total_rows_result->fetch_assoc()['total'];
    $total_pages = ceil($total_rows / $results_per_page);

    // Display the fetched data
    if ($result_teacher->num_rows > 0) {
        echo "<h5 class='mb-3 mt-3'>講師実績一覧:</h5>";
        echo "<div class='table-responsive'>";
        echo "<table class='table table-striped table-bordered'>";
        echo "<thead class='table-dark'><tr>";
        echo "<th>講師ID</th>";
        echo "<th>科目</th>";
        echo "<th>日付</th>";
        echo "<th>コーマ数</th>";
        echo "<th>備考</th>";
        echo "<th>編集</th>";
        echo "</tr></thead>";
        echo "<tbody>";
        while ($row_teacher = $result_teacher->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row_teacher['teacher_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row_teacher['subject']) . "</td>";
            echo "<td>" . htmlspecialchars($row_teacher['lecture_date']) . "</td>";
            echo "<td>" . htmlspecialchars($row_teacher['comasuu']) . "</td>";
            echo "<td>" . htmlspecialchars($row_teacher['bikou']) . "</td>";
            echo "<td>";
            echo "<a href='edit_lecture.php?id=" . htmlspecialchars($row_teacher['id']) . "' class='btn btn-warning btn-sm me-2'><i class='bi bi-pencil'></i> 変更</a>";
            echo "<a href='delete_lecture.php?id=" . htmlspecialchars($row_teacher['id']) . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this lecture?\");'><i class='bi bi-trash'></i> 削除</a>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
        echo "</div>";

        // Pagination
echo "<nav aria-label='Page navigation'>";
echo "<ul class='pagination justify-content-center'>";

// Previous button - disabled if on the first page
if ($page > 1) {
    echo "<li class='page-item'><a class='page-link' href='?page=" . ($page - 1) . "'>Previous</a></li>";
} else {
    echo "<li class='page-item disabled'><a class='page-link'>Previous</a></li>";
}

// Display page numbers
for ($i = 1; $i <= $total_pages; $i++) {
    if ($i == $page) {
        echo "<li class='page-item active'><a class='page-link' href='?page=$i'>$i</a></li>";
    } else {
        echo "<li class='page-item'><a class='page-link' href='?page=$i'>$i</a></li>";
    }
}

// Next button - disabled if on the last page
if ($page < $total_pages) {
    echo "<li class='page-item'><a class='page-link' href='?page=" . ($page + 1) . "'>Next</a></li>";
} else {
    echo "<li class='page-item disabled'><a class='page-link'>Next</a></li>";
}

echo "</ul>";
echo "</nav>";

        // Only show Salary Calculation button if filtered results are displayed
        if (isset($_POST['submit']) && ($sel1 || $selectedMonth)) {
            echo "<div class='salary-button'>";
            echo "<form action='salary_calculation.php' method='post'>";
            echo "<input type='hidden' name='teacher_id' value='" . htmlspecialchars($sel1) . "'>";
            echo "<input type='hidden' name='month' value='" . htmlspecialchars($selectedMonth) . "'>";
            echo "<button type='submit' class='btn btn-primary'>給与計算</button>";
            echo "</form>";
            echo "</div>";
        }
    } else {
        echo "<h5 class='text-danger'>データが見つかりません。</h5>";
    }

    if (isset($stmt)) {
        $stmt->close();
    }
    ?>
</div>


    <?php
    // Close the database connection
    $conn->close();
    ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
