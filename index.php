<?php

$name = $email = "";
$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Collect form data
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    
    if (empty($name)) {
        $errors['name'] = "Name is required";
    }

    if (empty($email)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }

    if (empty($password)) {
        $errors['password'] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters";
    } elseif (!preg_match('/[!@#$%^&*()_\-+=?]/', $password)) {
        $errors['password'] = "Password must contain at least one special character (!@#$%^&*)";
    }

    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match";
    }

    
    if (count($errors) === 0) {

        $file = "users.json";

        // Read existing users
        $existing_users = json_decode(file_get_contents($file), true);

        if (!is_array($existing_users)) {
            $existing_users = [];
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // New user data
        $new_user = [
            "name" => $name,
            "email" => $email,
            "password" => $hashed_password
        ];

        // Add new user to array
        $existing_users[] = $new_user;

        // Save back to JSON
        if (file_put_contents($file, json_encode($existing_users, JSON_PRETTY_PRINT))) {
            $success = "Registration successful!";
            $name = $email = ""; // clear form after success
        } else {
            $errors['file'] = "Error writing to users.json";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #ffffff;
            padding: 20px;
            display: flex;
            justify-content: center;
        }

        .container {
            width: 350px;
            padding: 20px;
            border: 1px solid #ccc;
            background: #fff;
            border-radius: 5px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: normal;
        }

        label {
            display: block;
            margin-top: 10px;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #bbb;
            border-radius: 3px;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 10px;
            margin-top: 15px;
            border: none;
            background: #333;
            color: white;
            border-radius: 3px;
            cursor: pointer;
            font-size: 15px;
        }

        button:hover {
            background: #555;
        }

        .error {
            color: red;
            font-size: 12px;
            margin-top: 3px;
        }

        .success {
            padding: 10px;
            background: #e7ffe7;
            border: 1px solid #9adf9a;
            color: #2e7d32;
            border-radius: 3px;
            margin-bottom: 10px;
            text-align: center;
            font-size: 14px;
        }
    </style>

</head>
<body>

<div class="container">

<h2>User Registration</h2>

<?php if ($success): ?>
    <div class="success"><?= $success ?></div>
<?php endif; ?>

<form method="POST">
    <label>Name:</label>
    <input type="text" name="name" value="<?= $name ?>">
    <div class="error"><?= $errors['name'] ?? "" ?></div>

    <label>Email:</label>
    <input type="text" name="email" value="<?= $email ?>">
    <div class="error"><?= $errors['email'] ?? "" ?></div>

    <label>Password:</label>
    <input type="password" name="password">
    <div class="error"><?= $errors['password'] ?? "" ?></div>

    <label>Confirm Password:</label>
    <input type="password" name="confirm_password">
    <div class="error"><?= $errors['confirm_password'] ?? "" ?></div>

    <button type="submit">Register</button>
</form>

</div>

</body>
</html>
