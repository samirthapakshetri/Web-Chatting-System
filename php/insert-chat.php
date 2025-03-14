<?php
session_start();
if (isset($_SESSION['unique_id'])) {
    include_once "config.php";
    $outgoing_id = $_SESSION['unique_id'];
    $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    // Initialize file_path as null
    $file_path = null;
    
    // Check if a file was uploaded
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $file = $_FILES['file'];
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_size = $file['size'];
        $file_error = $file['error'];
        
        // File extension
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Allowed file extensions - add or remove as needed
        $allowed_ext = array('jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt', 'zip');
        
        // Max file size (5MB)
        $max_size = 5 * 1024 * 1024;
        
        // Check file extension and size
        if (in_array($file_ext, $allowed_ext) && $file_size <= $max_size) {
            // Create unique filename to prevent overwriting
            $new_file_name = uniqid('file_') . '.' . $file_ext;
            
            // Upload directory
            $upload_dir = '../uploads/';
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Full path to file
            $upload_path = $upload_dir . $new_file_name;
            
            // Move file to uploads directory
            if (move_uploaded_file($file_tmp, $upload_path)) {
                // Store only the path relative to the root
                $file_path = 'uploads/' . $new_file_name;
            }
        }
    }
    
    if (!empty($message) || $file_path !== null) {
        $sql = mysqli_query($conn, "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg, file_path)
                            VALUES ({$incoming_id}, {$outgoing_id}, '{$message}', " . 
                            ($file_path ? "'{$file_path}'" : "NULL") . ")");
    }
} else {
    header("location: ../login.php");
}
?>