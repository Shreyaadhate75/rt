<?php require 'functions.php'; ?>

<form method="POST">
    <input type="email" name="unsubscribe_email" required>
    <button id="submit-unsubscribe">Unsubscribe</button>
</form>

<form method="POST">
    <input type="text" name="verification_code" maxlength="6" required>
    <button id="submit-verification">Verify</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['unsubscribe_email'])) {
        $email = trim($_POST['unsubscribe_email']);
        $code = generateVerificationCode();
        store_code($email, $code);
        $subject = "Confirm Un-subscription";
        $message = "<p>To confirm un-subscription, use this code: <strong>$code</strong></p>";
        $headers = "MIME-Version: 1.0\r\nContent-type: text/html\r\nFrom: no-reply@example.com";
        mail($email, $subject, $message, $headers);
        echo "Unsubscribe code sent.";
    } elseif (isset($_POST['verification_code'])) {
        $code = $_POST['verification_code'];
        $email = array_key_first(json_decode(file_get_contents(__DIR__ . '/codes.json'), true));
        if (verifyCode($email, $code)) {
            unsubscribeEmail($email);
            echo "You have been unsubscribed.";
        } else {
            echo "Verification failed.";
        }
    }
}
?>
