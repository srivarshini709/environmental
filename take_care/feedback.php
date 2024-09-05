<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Feedback</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Submit Your Feedback</h1>
    <form action="submit_feedback.php" method="POST">
        <label for="feedback_text">Your Feedback:</label><br>
        <textarea id="feedback_text" name="feedback_text" rows="5" cols="50" required></textarea><br><br>
        <input type="submit" value="Submit Feedback">
    </form>
</body>
</html>
