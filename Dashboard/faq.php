<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FAQs – KEFARM</title>
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

        .faq-section {
            background-color: #fff;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-top: 2rem;
        }

        .accordion-button {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .accordion-button:not(.collapsed) {
            background-color: #c8e6c9;
        }

        .accordion-body {
            background-color: #f9f9f9;
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
    <h1>KEFARM Frequently Asked Questions</h1>
</div>

<!-- FAQ Section -->
<div class="container">
    <div class="faq-section">
        <h3 class="mb-4 text-success fw-bold">General FAQs</h3>
        <div class="accordion" id="faqAccordion">

            <!-- FAQ 1 -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        What is KEFARM?
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show"
                     aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        KEFARM is a digital platform that promotes smart agriculture, food security, and sustainability in Kenya by connecting farmers, suppliers, and buyers.
                    </div>
                </div>
            </div>

            <!-- FAQ 2 -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Who can use KEFARM?
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse"
                     aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        KEFARM is open to farmers, agricultural experts, suppliers, NGOs, government agencies, and anyone interested in improving food systems in Kenya.
                    </div>
                </div>
            </div>

            <!-- FAQ 3 -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        How do I register on the platform?
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse"
                     aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Click the <a href="/Kefarm/register.html" class="text-success fw-bold">Register</a> button at the top right of the homepage, fill in your details, and submit. You’ll receive login access once your account is verified.
                    </div>
                </div>
            </div>

            <!-- FAQ 4 -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFour">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        Is KEFARM free to use?
                    </button>
                </h2>
                <div id="collapseFour" class="accordion-collapse collapse"
                     aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Yes, KEFARM provides free access to basic services. However, premium features may be available for institutions or enterprise users.
                    </div>
                </div>
            </div>

            <!-- FAQ 5 -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFive">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                        How secure is my information on KEFARM?
                    </button>
                </h2>
                <div id="collapseFive" class="accordion-collapse collapse"
                     aria-labelledby="headingFive" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        We use modern security standards, including data encryption and secure logins, to ensure your information is protected.
                    </div>
                </div>
            </div>

        </div>
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
