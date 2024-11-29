<?php
// Include the database connection file
include 'database.php';

$normalizedData = []; // Array to hold normalized data

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if a file is uploaded
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        // Check if the file is a CSV
        $fileType = mime_content_type($_FILES['file']['tmp_name']);
        if ($fileType !== 'text/plain' && $fileType !== 'text/csv') {
            die("Please upload a valid CSV file.");
        }

        // Temporary file path
        $filePath = $_FILES['file']['tmp_name'];

        // Open the file and read the content
        if (($handle = fopen($filePath, "r")) !== false) {
            // Get the first row as headers (column headers in cross-tab)
            $headers = fgetcsv($handle);

            // Read each row in the CSV and normalize it
            while (($row = fgetcsv($handle, 1000, ",")) !== false) {
                $rowHeader = $row[0]; // Row header (first column in each row)
                for ($i = 1; $i < count($row); $i++) {
                    $columnHeader = $headers[$i];
                    $value = $row[$i];
                    // Store normalized data for preview and insertion
                    $normalizedData[] = [$rowHeader, $columnHeader, $value];
                }
            }
            fclose($handle);
        } else {
            echo "Error opening the file.";
        }
    } else {
        echo "No file uploaded or there was an upload error.";
    }
}

// Handle data insertion after file upload
if (isset($_POST['insert_data']) && !empty($_POST['normalized_data'])) {
    // Decode the normalized data from the POST request
    $normalizedData = json_decode($_POST['normalized_data'], true);

    // Prepare the SQL statement for normalized data
    $sql = "INSERT INTO normalized_table (row_header, column_header, value) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Insert each row into the database
        foreach ($normalizedData as $row) {
            $stmt->bind_param("sss", $row[0], $row[1], $row[2]);
            if (!$stmt->execute()) {
                echo "Error inserting row: " . $stmt->error;
            }
        }
        echo "CSV data successfully normalized and inserted into the database.";
        $stmt->close();
    } else {
        echo "Error preparing the statement: " . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload CSV File</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <h2 class="text-center mb-4 mt-5">Upload CSV File</h2>

    <!-- Upload Form -->
    <div class="col-sm-6 col-md-4 d-flex justify-content-center mx-auto">
        <form method="post" enctype="multipart/form-data" class="border p-4 rounded shadow-sm bg-light w-100">
            <div class="mb-3">
                <label for="file" class="form-label">Select CSV file:</label>
                <input type="file" name="file" id="file" class="form-control" accept=".csv" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload CSV</button>
        </form>
    </div>

    <?php if (!empty($normalizedData)): ?>
    <!-- Data Preview Section -->
    <div class="mt-5">
        <h3>Data Preview</h3>
        <table class="table table-bordered table-striped mt-3">
            <thead class="table-dark">
                <tr>
                    <th>Row Header</th>
                    <th>Column Header</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($normalizedData as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row[0]); ?></td>
                    <td><?php echo htmlspecialchars($row[1]); ?></td>
                    <td><?php echo htmlspecialchars($row[2]); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Insert Data Button -->
    <form method="post" class="mt-3">
        <input type="hidden" name="normalized_data" value='<?php echo htmlspecialchars(json_encode($normalizedData)); ?>'>
        <button type="submit" name="insert_data" class="btn btn-success">Insert Data into Database</button>
    </form>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
