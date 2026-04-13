<?php
session_start();
include 'db_connect.php';

$message = '';
$upload_dir = 'uploads/';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uni_id = $_POST['uni_id'];
    // ... (rest of the PHP logic remains the same) ...
    $name = $_POST['name'];
    $password = $_POST['password'];
    $role = 'senior';
    
    $department = $_POST['department'];
    $program = $_POST['program'];
    $semester = (int)$_POST['semester'];
    $card_pic_path = ''; 

    if (isset($_FILES['student_card']) && $_FILES['student_card']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $file_type = $_FILES['student_card']['type'];
        $file_size = $_FILES['student_card']['size'];
        $max_size = 5 * 1024 * 1024; // 5 MB

        if (!in_array($file_type, $allowed_types)) {
            $message = "Error: Only JPG, JPEG, and PNG images are allowed.";
        } elseif ($file_size > $max_size) {
            $message = "Error: File size must be less than 5MB.";
        } else {
            $file_extension = pathinfo($_FILES['student_card']['name'], PATHINFO_EXTENSION);
            $new_file_name = $uni_id . '_' . time() . '.' . $file_extension;
            $target_file = $upload_dir . $new_file_name;

            if (move_uploaded_file($_FILES['student_card']['tmp_name'], $target_file)) {
                $card_pic_path = $target_file;
            } else {
                $message = "Error uploading file. Check folder permissions.";
            }
        }
    } else {
        $message = "Error: Student Card Picture is required.";
    }

    if (empty($message)) {
        $uni_id = $conn->real_escape_string($uni_id);
        $name = $conn->real_escape_string($name);
        $password = $conn->real_escape_string($password);
        $department = $conn->real_escape_string($department);
        $program = $conn->real_escape_string($program);
        $card_pic_path = $conn->real_escape_string($card_pic_path);
        
        $check_sql = "SELECT id FROM users WHERE uni_id = '$uni_id'";
        $check_result = $conn->query($check_sql);
        
        if ($check_result->num_rows > 0) {
            $message = "Error: This University ID is already registered.";
            if (!empty($card_pic_path) && file_exists($card_pic_path)) {
                unlink($card_pic_path);
            }
        } else {
            $sql = "INSERT INTO users (uni_id, name, password, role, department, program, semester, card_pic) 
                    VALUES ('$uni_id', '$name', '$password', '$role', '$department', '$program', $semester, '$card_pic_path')";

            if ($conn->query($sql) === TRUE) {
                $conn->close();
                header("Location: success_page.php");
                exit();
            } else {
                $message = "Error inserting into database: " . $conn->error;
                if (!empty($card_pic_path) && file_exists($card_pic_path)) {
                    unlink($card_pic_path);
                }
            }
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration | University Project Hub</title> <link rel="stylesheet" href="style.css"> 
</head>
<body class="auth-page">
    <div class="auth-box">
        <h2>🧑‍🎓 Student Registration</h2>
        <?php if (!empty($message)) { echo "<p style='color:red;'><strong>$message</strong></p>"; } ?>
        
        <form method="POST" action="" enctype="multipart/form-data">
            <label>University ID (Roll Number):</label>
            <input type="text" name="uni_id" required>
            
            <label>Full Name:</label>
            <input type="text" name="name" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <label>Department Name:</label>
            <select name="department" id="department" required>
                <option value="">-- Select Department --</option>
                <optgroup label="1. Computer Science">
                    <option value="Computer Science">Computer Science</option>
                </optgroup>
                <optgroup label="2. Management Sciences">
                    <option value="Management Sciences">Management Sciences</option>
                </optgroup>
                <optgroup label="3. Social Sciences">
                    <option value="Social Sciences">Social Sciences</option>
                </optgroup>
                <optgroup label="4. Arts & Humanities">
                    <option value="Arts & Humanities">Arts & Humanities</option>
                </optgroup>
            </select>

            <label>Program Name:</label>
            <select name="program" id="program" required disabled> 
                <option value="">-- Select Program --</option>
                
                <optgroup label="Computer Science Programs" data-department="Computer Science" disabled>
                    <option value="BS_SE">Software Engineering</option>
                    <option value="BS_AI">Artifical Intelligence</option>
                    <option value="BS_IT">Information Technology</option>
                    <option value="BS_CS">Computer Science</option>
                </optgroup>
                
                <optgroup label="Management Sciences Programs" data-department="Management Sciences" disabled>
                    <option value="BBA">BBA</option>
                    <option value="Commerce">Commerce</option>
                    <option value="Accounting & Finance">Accounting & Finance</option>
                </optgroup>

                <optgroup label="Social Sciences Programs" data-department="Social Sciences" disabled>
                    <option value="International Relations">International Relations</option>
                    <option value="Education">Education</option>
                    <option value="Psychology">Psychology</option>
                </optgroup>
                
                <optgroup label="Arts & Humanities Programs" data-department="Arts & Humanities" disabled>
                    <option value="English">English</option>
                    <option value="Islamic Thoughts & Culture">Islamic Thoughts and Culture</option>
                </optgroup>
            </select>

            <label>Current Semester:</label>
            <input type="number" name="semester" min="1" max="8" value="1" required>
            
            <label>Upload Student Card Picture (JPG/PNG max 5MB):</label>
            <input type="file" name="student_card" accept="image/jpeg,image/png" required>

            <input type="hidden" name="role" value="senior">
            
            <input type="submit" value="Register User">
        </form>
        
        <p>Already Registered? <a class="login-btn-link" href="login.php">Login here</a></p>
    </div>
    
    <script>
        // ... (JavaScript remains the same) ...
        const departmentSelect = document.getElementById('department');
        const programSelect = document.getElementById('program');
        const programGroups = programSelect.querySelectorAll('optgroup');

        function updatePrograms() {
            const selectedDepartment = departmentSelect.value;
            
            programSelect.disabled = true;
            programSelect.value = ""; 

            programGroups.forEach(group => {
                group.style.display = 'none';
                group.setAttribute('disabled', 'disabled');
            });

            if (selectedDepartment) {
                programGroups.forEach(group => {
                    if (group.dataset.department === selectedDepartment) {
                        group.style.display = 'block';
                        group.removeAttribute('disabled');
                        programSelect.disabled = false;
                    }
                });
            }
        }

        departmentSelect.addEventListener('change', updatePrograms);
        updatePrograms();
    </script>
</body>
</html>