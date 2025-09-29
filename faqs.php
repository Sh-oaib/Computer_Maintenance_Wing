<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>FAQs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1700px;
            margin: 20px auto;
            padding: 0 20px;
            background-color: #f9f9f9;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .faq-item {
            margin-bottom: 20px;
            margin-left: 100px;
        }
        .faq-question {
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        .faq-answer {
            margin-left: 20px;
            color: #555;
        }
    </style>
</head>
<body>
    <h1>Frequently Asked Questions</h1><br>
    <div class="faq-item">
        <div class="faq-question">How do I register on the platform?</div>
        <div class="faq-answer">→ Click on the “Register” button and fill in your details.</div>
    </div>
    <div class="faq-item">
        <div class="faq-question">How do I submit a complaint?</div>
        <div class="faq-answer">→ After logging in, go to your dashboard and click “Job Card” menu.</div>
    </div>
    <div class="faq-item">
        <div class="faq-question">How can I track my complaint status?</div>
        <div class="faq-answer">→ Your dashboard shows the complaint status like Pending, Approved, Processing, or Completed.</div>
    </div>
    <div class="faq-item">
        <div class="faq-question">What if my complaint is rejected?</div>
        <div class="faq-answer">→ You’ll be notified on your dashboard. You can resubmit or contact support.</div>
    </div>
    <div class="faq-item">
        <div class="faq-question">Can I see my complaint history?</div>
        <div class="faq-answer">→ Yes, go to the “Complaints Status & History” tab in your dashboard.</div>
    </div>
<?php include 'footer.php'; ?>
</body>
</html>
