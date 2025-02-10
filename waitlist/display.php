<?php
include 'db.php';

// Handle form submissions for Create and Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $conn->real_escape_string($_POST['full-name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $role = $conn->real_escape_string($_POST['role']);
    $industry = $conn->real_escape_string($_POST['industry']);
    $work_history = $conn->real_escape_string($_POST['work-history']);
    $origin_country = $conn->real_escape_string($_POST['origin-country']);
    $age_confirmation = $conn->real_escape_string($_POST['age-confirmation']);
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $filePath = '';

    // Handle file upload
    if (isset($_FILES['id-photo']) && $_FILES['id-photo']['error'] == 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['id-photo']['type'];

        if (in_array($fileType, $allowedTypes) && $_FILES['id-photo']['size'] <= 5 * 1024 * 1024) {
            $uploadDir = 'uploads/';
            $filePath = $uploadDir . basename($_FILES['id-photo']['name']);

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            move_uploaded_file($_FILES['id-photo']['tmp_name'], $filePath);
        }
    }

    // Insert or Update record
    if ($id) {
        $sql = "UPDATE waitlist SET full_name='$full_name', email='$email', phone='$phone', role='$role', industry='$industry', work_history='$work_history', origin_country='$origin_country', age_confirmation='$age_confirmation', id_photo='$filePath' WHERE id=$id";
    } else {
        $sql = "INSERT INTO waitlist (full_name, email, phone, role, industry, work_history, origin_country, age_confirmation, id_photo) VALUES ('$full_name', '$email', '$phone', '$role', '$industry', '$work_history', '$origin_country', '$age_confirmation', '$filePath')";
    }

    if ($conn->query($sql) === TRUE) {
        echo "Record saved successfully.";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Handle record deletion
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM waitlist WHERE id=$id");
}

// Fetch all records
$sql = "SELECT * FROM waitlist";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waitlist Records</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; }
        th { background-color: #20294a; }
        form { margin-bottom: 20px; }
        input[type="text"], input[type="email"], input[type="file"], select { width: 100%; padding: 8px; margin: 5px 0; }
        input[type="submit"] { background-color: #4CAF50; color: white; border: none; padding: 10px 15px; cursor: pointer; }
        input[type="submit"]:hover { background-color: #45a049; }
        .action-links a { margin-right: 10px; }
    </style>
</head>
<body>

<h1 style="text-align: center;">Waitlist Records</h1>

<!-- Form for Adding/Editing Records -->
<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo isset($_GET['edit']) ? (int)$_GET['edit'] : ''; ?>">
    <input type="text" name="full-name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="text" name="phone" placeholder="Phone" required>
    <input type="text" name="role" placeholder="Role" required>
    <input type="text" name="industry" placeholder="Industry" required>
    <textarea name="work-history" placeholder="Work History" required></textarea>
    <input type="text" name="origin-country" placeholder="Origin Country" required>
    <select name="age-confirmation" required>
        <option value="">Age Confirmation</option>
        <option value="Yes">Yes</option>
        <option value="No">No</option>
    </select>
    <input type="file" name="id-photo" accept="image/*">
    <input type="submit" value="Save Record">
</form>

<!-- Display Records -->
<?php if ($result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Industry</th>
                <th>Work History</th>
                <th>Origin Country</th>
                <th>Age Confirmation</th>
                <th>ID Photo</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td><?php echo htmlspecialchars($row['role']); ?></td>
                    <td><?php echo htmlspecialchars($row['industry']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($row['work_history'])); ?></td>
                    <td><?php echo htmlspecialchars($row['origin_country']); ?></td>
                    <td><?php echo htmlspecialchars($row['age_confirmation']); ?></td>
                    <td>
                        <?php if (!empty($row['id_photo'])): ?>
                            <a href="<?php echo htmlspecialchars($row['id_photo']); ?>" download>Download</a>
                        <?php else: ?>
                            No Image
                        <?php endif; ?>
                    </td>
                    <td class="action-links">
                        <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No records found.</p>
<?php endif; ?>

<?php $conn->close(); ?>
</body>
</html>