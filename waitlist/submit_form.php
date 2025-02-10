<?php
// Include database connection
include 'db.php';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $full_name = $conn->real_escape_string($_POST['full-name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $role = $conn->real_escape_string($_POST['role']);
    $industry = $conn->real_escape_string($_POST['industry']);
    $work_history = $conn->real_escape_string($_POST['work-history']);
    $origin_country = $conn->real_escape_string($_POST['origin-country']);
    $age_confirmation = $conn->real_escape_string($_POST['age-confirmation']);
    $filePath = '';

    if (isset($_FILES['id-photo']) && $_FILES['id-photo']['error'] == 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['id-photo']['type'];
        
        if (in_array($fileType, $allowedTypes) && $_FILES['id-photo']['size'] <= 5 * 1024 * 1024) {
            $uploadDir = 'uploads/';
            $filePath = $uploadDir . basename($_FILES['id-photo']['name']);
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (!move_uploaded_file($_FILES['id-photo']['tmp_name'], $filePath)) {
                echo "Error uploading the file.";
                exit;
            }
        } else {
            echo "Invalid file type or size exceeded.";
            exit;
        }
    }

    $sql = "INSERT INTO waitlist (full_name, email, role, industry, work_history, origin_country, phone, age_confirmation, id_photo)
            VALUES ('$full_name', '$email', '$role', '$industry', '$work_history', '$origin_country', '$phone', '$age_confirmation', '$filePath')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>