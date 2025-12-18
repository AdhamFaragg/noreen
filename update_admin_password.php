<?php
// Generate password hash for 'admin'
$password = 'admin';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: $password\n";
echo "Hash: $hash\n\n";

// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'clothing_store');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Update admin password
$query = "UPDATE users SET password = '$hash' WHERE username = 'admin'";

if (mysqli_query($conn, $query)) {
    echo "✓ Admin password updated successfully!\n";
    echo "You can now login with:\n";
    echo "Username: admin\n";
    echo "Password: admin\n";
} else {
    echo "Error updating password: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
