<?php
// Retrieve branch code from query parameter
$branchCode = $_GET['branch'];

// Connect to database
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "sales_database";

$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Construct SQL query to retrieve sales data
$sql = "SELECT quote_no, salesman_name, rep_code, branch_code, created_date, created_time, invoice_no FROM sales WHERE branch_code = '$branchCode' AND created_date = CURDATE()";

$result = mysqli_query($conn, $sql);

if (!$result) {
  die("Error: " . $sql . "<br>" . mysqli_error($conn));
}

// Convert result set to array
$salesData = array();
while ($row = mysqli_fetch_assoc($result)) {
  $salesData[] = $row;
}

// Return sales data as JSON
header('Content-Type: application/json');
echo json_encode($salesData);

mysqli_close($conn);
?>
