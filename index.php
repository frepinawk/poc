<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple PHP Greeting</title>
</head>
<body>
    <h1>Enter Your Name</h1>
    <form method="POST" action="">
        <input type="text" name="name" placeholder="Your Name" required>
        <input type="submit" value="Submit">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = htmlspecialchars($_POST['name']); // Sanitize user input
        echo "<h2>Hey, $name!</h2>";
    }
    ?>
</body>
</html>

