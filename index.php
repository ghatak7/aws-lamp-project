<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
if (isset($_POST['submit'])) {
  $host = "testdb.cjkmuei8gq9v.us-west-2.rds.amazonaws.com"; // RDS endpoint
  $user = "admin";       // your RDS master username
  $pass = "Speedrun123"; // your RDS password
  $db   = "testdb";       // your database name

  $conn = new mysqli($host, $user, $pass, $db);

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
  $name = $_POST['name'];

  // Prepare the query to insert data
  $stmt = $conn->prepare("INSERT INTO users (name) VALUES (?)");
  $stmt->bind_param("s", $name);
  $stmt->execute();  // Execute the query

  echo "Saved!";  // Confirmation message

  $stmt->close();  // Close the statement
  $conn->close();  // Close the connection
}
?>

<!DOCTYPE html>
<html>
  <body>
    <form method="post">
      Name: <input type="text" name="name"/>
      <input type="submit" name="submit"/>
    </form>
  </body>
</html>



