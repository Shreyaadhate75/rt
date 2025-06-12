<?php

function generateVerificationCode() {
    return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

function registerEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    if (!is_email_registered($email)) {
        file_put_contents($file, $email . PHP_EOL, FILE_APPEND);
    }
}

function unsubscribeEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $emails = array_filter($emails, fn($e) => trim($e) !== $email);
    file_put_contents($file, implode(PHP_EOL, $emails) . PHP_EOL);
}

function sendVerificationEmail($email, $code) {
    $subject = 'Your Verification Code';
    $message = "<p>Your verification code is: <strong>$code</strong></p>";
    $headers = "MIME-Version: 1.0\r\nContent-type: text/html\r\nFrom: no-reply@example.com";
    mail($email, $subject, $message, $headers);
}

function verifyCode($email, $code) {
    $codes = json_decode(file_get_contents(__DIR__ . '/codes.json'), true) ?? [];
    return isset($codes[$email]) && $codes[$email] === $code;
}

function fetchAndFormatXKCDData() {
    $id = random_int(1, 2800); // max known XKCD id as of 2024
    $url = "https://xkcd.com/$id/info.0.json";
    $data = json_decode(file_get_contents($url), true);
    $img = $data['img'];
    return "<h2>XKCD Comic</h2><img src=\"$img\" alt=\"XKCD Comic\"><p><a href=\"#\" id=\"unsubscribe-button\">Unsubscribe</a></p>";
}

function sendXKCDUpdatesToSubscribers() {
    $file = __DIR__ . '/registered_emails.txt';
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $message = fetchAndFormatXKCDData();
    $subject = "Your XKCD Comic";
    $headers = "MIME-Version: 1.0\r\nContent-type: text/html\r\nFrom: no-reply@example.com";

    foreach ($emails as $email) {
        $unsubscribe_url = "http://yourdomain.com/src/unsubscribe.php?email=" . urlencode($email);
        $finalMessage = str_replace('#', $unsubscribe_url, $message);
        mail($email, $subject, $finalMessage, $headers);
    }
}

function is_email_registered($email) {
    $emails = file(__DIR__ . '/registered_emails.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return in_array(trim($email), $emails);
}

function store_code($email, $code) {
    $codesFile = __DIR__ . '/codes.json';
    $codes = json_decode(file_get_contents($codesFile), true) ?? [];
    $codes[$email] = $code;
    file_put_contents($codesFile, json_encode($codes));
}
