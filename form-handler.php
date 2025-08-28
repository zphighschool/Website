<?php
// Database connection
$host = "localhost";
$username = "root";
$password = "";
$dbname = "feedback";

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo '<div style="padding:20px; background:#ff4d4d; color:white; font-family:sans-serif; border-radius:10px; text-align:center;">
            ❌ Oops! Database connection failed: ' . $conn->connect_error . '
          </div>';
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = htmlspecialchars(trim($_POST['firstname']));
    $lastname  = htmlspecialchars(trim($_POST['lastname']));
    $email     = htmlspecialchars(trim($_POST['email']));
    $phone     = htmlspecialchars(trim($_POST['phone']));
    $subject   = htmlspecialchars(trim($_POST['subject']));
    $message   = htmlspecialchars(trim($_POST['message']));

    // Basic validation
    if (empty($firstname) || empty($lastname) || empty($email) || empty($subject) || empty($message)) {
        echo '<div style="padding:20px; background:#ff6666; color:white; font-family:sans-serif; border-radius:10px; text-align:center;">
                ⚠️ Whoops! All required fields must be filled.
              </div>';
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo '<div style="padding:20px; background:#ff6666; color:white; font-family:sans-serif; border-radius:10px; text-align:center;">
                ⚠️ Hmm… that email doesn’t look valid. Please check it!
              </div>';
        exit;
    }

    // Save to database
    $stmt = $conn->prepare("INSERT INTO contact_messages (firstname, lastname, email, phone, subject, message) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $firstname, $lastname, $email, $phone, $subject, $message);

    if ($stmt->execute()) {
        echo '<div style="padding:20px; background:#4CAF50; color:white; font-family:sans-serif; border-radius:10px; text-align:center;">
                ✅ Done! Your message has been sent successfully. We will reach out to you soon, ' . htmlspecialchars($firstname) . ' 🌟
              </div>';
    } else {
        echo '<div style="padding:20px; background:#ff4d4d; color:white; font-family:sans-serif; border-radius:10px; text-align:center;">
                ❌ Oops! Something went wrong while saving your message: ' . $stmt->error . '
              </div>';
    }

    $stmt->close();

    // (Optional) Send email
    $to = "aadityapawar5622@gmail.com";
    $email_subject = "New Contact Form Message: " . $subject;
    $email_body = "
        📩 New Message:\n
        👤 $firstname $lastname\n
        📧 $email\n
        📱 $phone\n
        📝 $subject\n
        💬 $message
    ";
    $headers = "From: $email\r\nReply-To: $email\r\n";

    @mail($to, $email_subject, $email_body, $headers);
}

$conn->close();
?>
