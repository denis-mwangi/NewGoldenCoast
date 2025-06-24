<?php
$db_name = 'mysql:host=localhost;dbname=hotel_db'; // Change .sql to just the database name
$db_user_name = 'root'; // Default XAMPP MySQL username
$db_user_pass = '';     // Default XAMPP MySQL password (usually empty)

try {
    $conn = new PDO($db_name, $db_user_name, $db_user_pass);
    // Set the PDO error mode to exception for better error handling
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection successful!"; // Optional: Confirmation message
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}

   function create_unique_id(){
      $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
      $rand = array();
      $length = strlen($str) - 1;

      for($i = 0; $i < 20; $i++){
         $n = mt_rand(0, $length);
         $rand[] = $str[$n];
      }
      return implode($rand);
   }

?>