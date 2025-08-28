<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "database";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$submitted = false;
$file = __DIR__ . "/Admission Data.xls"; // Your existing Excel file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'] . '@gmail.com';
    $admission_class = $_POST['admission_class'];
    $address = $_POST['address'];

    // Insert into database
    $sql = "INSERT INTO form (full_name, dob, gender, contact_number, email, admission_class, address)
            VALUES ('$full_name', '$dob', '$gender', '$contact_number', '$email', '$admission_class', '$address')";
    if ($conn->query($sql) === TRUE) { $submitted = true; }
    else { $error = "Error: " . $conn->error; }

    // Fetch all data from DB to include previous entries
    $sql_all = "SELECT full_name, dob, gender, contact_number, email, admission_class, address FROM form";
    $result_all = $conn->query($sql_all);

    if ($result_all) {
        $handle = fopen($file, "w"); // Overwrite existing file with all entries
        if ($handle) {
            // Add headers
            fputcsv($handle, ['Full Name','DOB','Gender','Contact Number','Email','Class','Address'], "\t");

            // Add all rows
            while ($row = $result_all->fetch_assoc()) {
                fputcsv($handle, [
                    $row['full_name'],
                    $row['dob'],
                    $row['gender'],
                    $row['contact_number'],
                    $row['email'],
                    $row['admission_class'],
                    $row['address']
                ], "\t");
            }

            fclose($handle);
        } else {
            $error = "Cannot open Excel file for writing!";
        }
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Z.P. High School Admission Form</title>
<style>
body { font-family: 'Segoe UI', sans-serif; margin: 0; padding: 0; background: linear-gradient(to right,#83a4d4,#b6fbff); display:flex; align-items:center; justify-content:center; min-height:100vh; }
.form-container { background-color:white; padding:40px; border-radius:20px; box-shadow:0 10px 25px rgba(0,0,0,0.2); width:100%; max-width:500px; }
h2 { text-align:center; color:#2c3e50; margin-bottom:20px; }
input, select { width:100%; padding:12px; margin:10px 0; border-radius:8px; border:1px solid #ccc; font-size:16px; }
input:focus, select:focus { outline:none; border-color:#3498db; }
.submit-btn { background:linear-gradient(to right,#00b09b,#96c93d); color:white; border:none; padding:12px; font-size:14px; border-radius:8px; cursor:pointer; transition:all 0.3s; width:100%; }
.submit-btn:hover { background:linear-gradient(to right,#fc466b,#3f5efb); transform:scale(1.05); }
.success-message { text-align:center; padding:30px; background:#f0fff0; border:2px solid #28a745; border-radius:15px; box-shadow:0 5px 20px rgba(0,0,0,0.1); animation:zoomIn 0.5s ease-in-out; }
.success-message h2 { color:#28a745; margin-bottom:10px; }
.success-message a { display:inline-block; margin-top:15px; text-decoration:none; color:#fff; background:#28a745; padding:10px 20px; border-radius:6px; transition:0.3s; }
.success-message a:hover { background:#218838; }
@keyframes zoomIn { from { transform:scale(0.8); opacity:0; } to { transform:scale(1); opacity:1; } }
.email-hint { font-size:14px; color:#555; margin-top:-8px; margin-bottom:10px; }
</style>
</head>
<body>

<?php if ($submitted): ?>
<div class="form-container success-message">
    <h2>🎉 Admission Successful!</h2>
    <p>Your admission form has been submitted successfully.</p>
    <a href="admission_form.php">Fill Another Form</a>
</div>
<script>
// Automatically download updated Excel file
window.onload = function() {
    let iframe = document.createElement('iframe');
    iframe.style.display = "none";
    iframe.src = "Admission Data.xls";
    document.body.appendChild(iframe);
};
</script>
<?php else: ?>
<form method="POST" action="" class="form-container">
<h2>Admission Form - Z.P. High School</h2>

<input type="text" name="full_name" placeholder="Full Name" required>
<input type="date" name="dob" required>

<select name="gender" required>
<option value="" disabled selected>Select Gender</option>
<option value="Male">Male</option>
<option value="Female">Female</option>
<option value="Other">Other</option>
</select>

<input type="text" name="contact_number" placeholder="Contact Number" pattern="[0-9]{10}" required>

<input type="text" name="email" id="email" placeholder="Your Email ID (without @gmail.com)" required>
<div class="email-hint">📧 Your email will be saved as <strong>youremail@gmail.com</strong></div>

<select name="admission_class" required>
<option value="" disabled selected>Admission For Class</option>
<option value="1st">1st</option>
<option value="2nd">2nd</option>
<option value="3rd">3rd</option>
<option value="4th">4th</option>
<option value="5th">5th</option>
<option value="6th">6th</option>
<option value="7th">7th</option>
<option value="8th">8th</option>
<option value="9th">9th</option>
<option value="10th">10th</option>
</select>

<input type="text" name="address" placeholder="Full Address" required>
<button type="submit" class="submit-btn">Submit Form</button>
</form>
<?php endif; ?>

<script>
document.getElementById('email').addEventListener('input', function() {
    let val = this.value;
    if (!val.includes("@")) { this.value = val; }
});
</script>

</body>
</html>
