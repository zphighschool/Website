<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "database";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$sql = "SELECT full_name, dob, gender, contact_number, email, admission_class, address FROM form";
$result = $conn->query($sql);

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"admission data.xlsx\"");
header("Pragma: no-cache");
header("Expires: 0");

echo "Full Name\tDOB\tGender\tContact Number\tEmail\tAdmission Class\tAddress\n";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo $row['full_name'] . "\t" .
             $row['dob'] . "\t" .
             $row['gender'] . "\t" .
             $row['contact_number'] . "\t" .
             $row['email'] . "\t" .
             $row['admission_class'] . "\t" .
             $row['address'] . "\n";
    }
}
$conn->close();
exit;
?>
