<?php require 'functions.php'; ?>

<form method="POST">
    <input type="email" name="email" required>
    <button id="submit-email">Submit</button>
</form>

<form method="POST">
    <input type="text" name="verification_code" maxlength="6" required>
    <button id="submit-verification">Verify</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
        $code = generateVerificationCode();
        store_code($email, $code);
        sendVerificationEmail($email, $code);
        echo "Verification code sent to $email";
    } elseif (isset($_POST['verification_code'])) {
        $code = $_POST['verification_code'];
        $email = array_key_first(json_decode(file_get_contents(__DIR__ . '/codes.json'), true)); // crude retrieval
        if (verifyCode($email, $code)) {
            registerEmail($email);
            echo "Email verified and registered!";
        } else {
            echo "Verification failed.";
        }
    }
}
?>
