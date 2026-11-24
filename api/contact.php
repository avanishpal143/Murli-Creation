<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/functions.php';

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$subject = trim($_POST['subject'] ?? 'General Inquiry');
$message = trim($_POST['message'] ?? '');

if (empty($name) || (empty($phone) && empty($email)) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in your name, contact phone or email, and message.']);
    exit;
}

$inquiryData = [
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'subject' => $subject,
    'message' => $message
];

// Save to Contact Inquiries & CRM Leads (MySQL & JSON)
$saveResult = function_exists('db_save_contact_inquiry') 
    ? db_save_contact_inquiry($inquiryData)
    : ['success' => true];

// Send email alert to Studio Admin if configured
$settings = get_site_settings();
$studioEmail = $settings['studio']['email'] ?? 'contact@murlicreations.com';
if (!empty($studioEmail) && function_exists('db_send_system_email')) {
    $mailSub = "New Inquiry from {$name}: {$subject}";
    $mailBody = "
        <div style='font-family:sans-serif; color:#1c1815;'>
            <h3 style='color:#c5a059;'>New Website Contact Form Inquiry Received</h3>
            <p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>
            <p><strong>Phone:</strong> " . htmlspecialchars($phone) . "</p>
            <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
            <p><strong>Subject:</strong> " . htmlspecialchars($subject) . "</p>
            <div style='background:#fcfaf7; padding:15px; border-left:4px solid #c5a059; margin:15px 0;'>
                <strong>Message:</strong><br/>" . nl2br(htmlspecialchars($message)) . "
            </div>
            <p><a href='https://murlicreations.com/admin.php#contact-form' style='color:#c5a059;'>Open Studio Admin to Reply &rarr;</a></p>
        </div>
    ";
    db_send_system_email($studioEmail, $mailSub, $mailBody, 'contact_notification');
}

echo json_encode([
    'success' => true,
    'message' => "Thank you, {$name}! Master artisan Murli Prasad's studio has received your inquiry. We will contact you shortly."
]);
