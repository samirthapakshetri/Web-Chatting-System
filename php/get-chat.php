<?php 
// Set timezone to Nepal time
date_default_timezone_set('Asia/Kathmandu');

session_start();
if (isset($_SESSION['unique_id'])) {
    include_once "config.php";
    $outgoing_id = $_SESSION['unique_id'];
    $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
    $output = "";
    
    $sql = "SELECT * FROM messages LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
            WHERE (outgoing_msg_id = {$outgoing_id} AND incoming_msg_id = {$incoming_id})
            OR (outgoing_msg_id = {$incoming_id} AND incoming_msg_id = {$outgoing_id}) ORDER BY msg_id";
    $query = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_assoc($query)) {
            // Format time using the correct column name 'date_time' (not 'created_at')
            $time_display = '';
            if (isset($row['date_time']) && !empty($row['date_time'])) {
                $time_display = date('h:i A', strtotime($row['date_time']));
            }
            
            if ($row['outgoing_msg_id'] === $outgoing_id) {
                // Outgoing message (sent by current user)
                $output .= '<div class="flex justify-end mb-4">
                            <div class="mr-2 py-3 px-4 bg-blue-400 rounded-bl-3xl rounded-tl-3xl rounded-tr-xl text-white max-w-xs">';
                
                // Show message if not empty
                if (!empty($row['msg'])) {
                    $output .= '<p>'.nl2br($row['msg']).'</p>';
                }
                
                // Show file if exists
                if (!empty($row['file_path'])) {
                    $file_ext = pathinfo($row['file_path'], PATHINFO_EXTENSION);
                    
                    // Display images inline
                    if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $output .= '<img src="'.$row['file_path'].'" class="mt-2 rounded max-w-full" alt="Uploaded image">';
                    } else {
                        // Show download link for other files
                        $file_name = basename($row['file_path']);
                        $output .= '<div class="file-message mt-2">
                                    <a href="'.$row['file_path'].'" download class="flex items-center bg-blue-500 hover:bg-blue-600 text-white text-sm py-1 px-2 rounded">
                                        <i class="fas fa-download mr-2"></i> '.$file_name.'
                                    </a>
                                </div>';
                    }
                }
                
                $output .= '<span class="text-xs text-gray-200 block text-right mt-1">'.$time_display.'</span>
                            </div>
                        </div>';
            } else {
                // Incoming message (received from other user)
                $output .= '<div class="flex justify-start mb-4">
                            <img src="php/images/pfp/'.$row['img'].'" class="h-8 w-8 rounded-full self-end mr-2">
                            <div class="ml-2 py-3 px-4 bg-gray-400 rounded-br-3xl rounded-tr-3xl rounded-tl-xl text-white max-w-xs">';
                
                // Show message if not empty
                if (!empty($row['msg'])) {
                    $output .= '<p>'.nl2br($row['msg']).'</p>';
                }
                
                // Show file if exists
                if (!empty($row['file_path'])) {
                    $file_ext = pathinfo($row['file_path'], PATHINFO_EXTENSION);
                    
                    // Display images inline
                    if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $output .= '<img src="'.$row['file_path'].'" class="mt-2 rounded max-w-full" alt="Uploaded image">';
                    } else {
                        // Show download link for other files
                        $file_name = basename($row['file_path']);
                        $output .= '<div class="file-message mt-2">
                                    <a href="'.$row['file_path'].'" download class="flex items-center bg-gray-500 hover:bg-gray-600 text-white text-sm py-1 px-2 rounded">
                                        <i class="fas fa-download mr-2"></i> '.$file_name.'
                                    </a>
                                </div>';
                    }
                }
                
                $output .= '<span class="text-xs text-gray-200 block mt-1">'.$time_display.'</span>
                            </div>
                        </div>';
            }
        }
    } else {
        $output .= '<div class="text-center text-gray-400 py-4">No messages yet. Start a conversation!</div>';
    }
    echo $output;
} else {
    header("location: ../login.php");
}
?>