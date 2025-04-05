<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Support – KEFARM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom KEFARM Styles -->
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f5;
            color: #333;
        }

        .kefarm-header {
            background-color: #006400;
            color: white;
            padding: 1rem;
        }

        .support-section {
            background-color: #fff;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-top: 2rem;
        }

        .form-control {
            border-radius: 10px;
        }

        footer {
            background-color: #006400;
            color: #fff;
            text-align: center;
            padding: 1rem 0;
            margin-top: 2rem;
        }

        a.text-success {
            text-decoration: none;
        }

        a.text-success:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="kefarm-header text-center">
    <h1>KEFARM Support</h1>
    <p>We are here to help you. If you have any questions or issues, feel free to reach out to us.</p>
</div>

<!-- Support Section -->
<div class="container">
    <div class="support-section">
        <h3 class="mb-4 text-success fw-bold">Contact Support</h3>
        <form action="support_handler.php" method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Your Message</label>
                <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-success">Submit</button>
        </form>
    </div>
</div>

<!-- Footer -->
<footer>
    <p>&copy; <?php echo date("Y"); ?> KEFARM. All rights reserved.</p>
</footer>

<!-- Bootstrap JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
