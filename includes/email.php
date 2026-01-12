<?php
/**
 * Email Functions using Resend API
 * https://resend.com/docs/api-reference/emails/send-email
 */

/**
 * Send email via Resend API
 *
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $html HTML content
 * @param string|null $text Plain text content (optional)
 * @param string|null $replyTo Reply-to address (optional)
 * @return array ['success' => bool, 'id' => string|null, 'error' => string|null]
 */
function sendEmail($to, $subject, $html, $text = null, $replyTo = null) {
    $apiKey = defined('RESEND_API_KEY') ? RESEND_API_KEY : '';
    $from = defined('EMAIL_FROM') ? EMAIL_FROM : 'Integral Safety <noreply@integralsafetyltd.co.uk>';

    if (empty($apiKey)) {
        return ['success' => false, 'error' => 'Resend API key not configured'];
    }

    $data = [
        'from' => $from,
        'to' => [$to],
        'subject' => $subject,
        'html' => $html
    ];

    if ($text) {
        $data['text'] = $text;
    }

    if ($replyTo) {
        $data['reply_to'] = $replyTo;
    }

    $ch = curl_init('https://api.resend.com/emails');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ],
        CURLOPT_TIMEOUT => 30
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return ['success' => false, 'error' => 'Curl error: ' . $error];
    }

    $result = json_decode($response, true);

    if ($httpCode === 200 && isset($result['id'])) {
        return ['success' => true, 'id' => $result['id']];
    }

    return [
        'success' => false,
        'error' => $result['message'] ?? 'Unknown error',
        'statusCode' => $httpCode
    ];
}

/**
 * Send 2FA verification code email
 */
function send2FAEmail($to, $code, $name = '') {
    $siteName = defined('SITE_NAME') ? SITE_NAME : 'Integral Safety';
    $greeting = $name ? "Hello {$name}," : "Hello,";

    $subject = "Your login code for {$siteName}";

    $html = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
        <div style='background: #132337; padding: 20px; text-align: center;'>
            <h1 style='color: #ffffff; margin: 0; font-size: 24px;'>{$siteName}</h1>
        </div>
        <div style='padding: 30px; background: #ffffff;'>
            <p style='color: #333; font-size: 16px;'>{$greeting}</p>
            <p style='color: #333; font-size: 16px;'>Your verification code is:</p>
            <div style='background: #f5f5f5; padding: 20px; text-align: center; margin: 20px 0; border-radius: 8px;'>
                <span style='font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #e85d04;'>{$code}</span>
            </div>
            <p style='color: #666; font-size: 14px;'>This code will expire in 10 minutes.</p>
            <p style='color: #666; font-size: 14px;'>If you did not request this code, please ignore this email.</p>
        </div>
        <div style='background: #f5f5f5; padding: 15px; text-align: center;'>
            <p style='color: #999; font-size: 12px; margin: 0;'>{$siteName} Admin Portal</p>
        </div>
    </div>
    ";

    $text = "{$greeting}\n\nYour verification code is: {$code}\n\nThis code will expire in 10 minutes.\n\nIf you did not request this code, please ignore this email.\n\n- {$siteName} Admin";

    return sendEmail($to, $subject, $html, $text);
}

/**
 * Send contact form notification email
 */
function sendContactFormEmail($data, $toEmail) {
    $siteName = defined('SITE_NAME') ? SITE_NAME : 'Integral Safety';

    $subject = "New Contact Form Submission - {$siteName}";

    $name = htmlspecialchars($data['name']);
    $email = htmlspecialchars($data['email']);
    $phone = htmlspecialchars($data['phone'] ?? 'Not provided');
    $company = htmlspecialchars($data['company'] ?? 'Not provided');
    $message = nl2br(htmlspecialchars($data['message']));

    $html = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
        <div style='background: #132337; padding: 20px; text-align: center;'>
            <h1 style='color: #ffffff; margin: 0; font-size: 24px;'>New Contact Form Submission</h1>
        </div>
        <div style='padding: 30px; background: #ffffff;'>
            <table style='width: 100%; border-collapse: collapse;'>
                <tr>
                    <td style='padding: 10px 0; border-bottom: 1px solid #eee; color: #666; width: 120px;'><strong>Name:</strong></td>
                    <td style='padding: 10px 0; border-bottom: 1px solid #eee; color: #333;'>{$name}</td>
                </tr>
                <tr>
                    <td style='padding: 10px 0; border-bottom: 1px solid #eee; color: #666;'><strong>Email:</strong></td>
                    <td style='padding: 10px 0; border-bottom: 1px solid #eee; color: #333;'><a href='mailto:{$email}'>{$email}</a></td>
                </tr>
                <tr>
                    <td style='padding: 10px 0; border-bottom: 1px solid #eee; color: #666;'><strong>Phone:</strong></td>
                    <td style='padding: 10px 0; border-bottom: 1px solid #eee; color: #333;'>{$phone}</td>
                </tr>
                <tr>
                    <td style='padding: 10px 0; border-bottom: 1px solid #eee; color: #666;'><strong>Company:</strong></td>
                    <td style='padding: 10px 0; border-bottom: 1px solid #eee; color: #333;'>{$company}</td>
                </tr>
            </table>
            <div style='margin-top: 20px;'>
                <p style='color: #666; margin-bottom: 10px;'><strong>Message:</strong></p>
                <div style='background: #f9f9f9; padding: 15px; border-radius: 8px; color: #333;'>{$message}</div>
            </div>
        </div>
        <div style='background: #f5f5f5; padding: 15px; text-align: center;'>
            <p style='color: #999; font-size: 12px; margin: 0;'>Sent from {$siteName} website contact form</p>
        </div>
    </div>
    ";

    $text = "New Contact Form Submission\n\n";
    $text .= "Name: {$data['name']}\n";
    $text .= "Email: {$data['email']}\n";
    $text .= "Phone: " . ($data['phone'] ?? 'Not provided') . "\n";
    $text .= "Company: " . ($data['company'] ?? 'Not provided') . "\n\n";
    $text .= "Message:\n{$data['message']}\n";

    return sendEmail($toEmail, $subject, $html, $text, $data['email']);
}
