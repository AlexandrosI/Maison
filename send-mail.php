<?php
// =====================================================
// CONTACT FORM BACKEND - MULTILINGUAL AJAX VERSION (EN / GR)
// =====================================================

// Configuration
$recipient_email = "accounting@faexecutive.gr";
$subject_prefix = "New Contact Form Submission:";

// Set response header for JSON
header('Content-Type: application/json');

// Only process POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- 1. DETECT LANGUAGE ---
    // Default to 'en' unless the form explicitly sends 'gr'
    $lang = isset($_POST['lang']) ? strtolower(trim($_POST['lang'])) : 'en';

    // Dictionary for UI Messages (AJAX Responses)
    $messages = [
        'en' => [
            'error_name_required'      => "Name is required.",
            'error_email_invalid'      => "A valid email address is required.",
            'error_subject_required'   => "Subject is required.",
            'error_message_required'   => "Message is required.",
            'success_sent'             => "Your message has been sent successfully.",
            'fail_server'              => "The server couldn't send the email. Please try again later."
        ],
        'gr' => [
            'error_name_required'      => "Το όνομα είναι υποχρεωτικό.",
            'error_email_invalid'      => "Απαιτείται μια έγκυρη διεύθυνση email.",
            'error_subject_required'   => "Το θέμα είναι υποχρεωτικό.",
            'error_message_required'   => "Το μήνυμα είναι υποχρεωτικό.",
            'success_sent'             => "Το μήνυμά σας αποστάλθηκε επιτυχώς.",
            'fail_server'              => "Ο διακομιστής δεν μπόρεσε να στείλει το email. Παρακαλώ δοκιμάστε ξανά αργότερα."
        ]
    ];

    // Helper function to get translation
    function t($key, $lang, $dict) {
        return $dict[$lang][$key] ?? $dict['en'][$key]; // Fallback to English if lang missing
    }

    // 2. Sanitize and Validate Input
    $name = isset($_POST['name']) ? trim(htmlspecialchars($_POST['name'])) : '';
    $email = isset($_POST['email']) ? trim(htmlspecialchars($_POST['email'])) : '';
    $subject = isset($_POST['subject']) ? trim(htmlspecialchars($_POST['subject'])) : '';
    $message = isset($_POST['message']) ? trim(htmlspecialchars($_POST['message'])) : '';

    $errors = [];

    if (empty($name)) {
        $errors[] = t('error_name_required', $lang, $messages);
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = t('error_email_invalid', $lang, $messages);
    }

    if (empty($subject)) {
        $errors[] = t('error_subject_required', $lang, $messages);
    }

    if (empty($message)) {
        $errors[] = t('error_message_required', $lang, $messages);
    }

    // If there are validation errors, return JSON and stop
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(['error' => implode(" ", $errors)]);
        exit();
    }

    // 3. Prepare Email Content (Sent TO YOU)
    // Note: You might want to keep this in English, or translate it too. 
    // Currently, it stays in English for consistency with your internal system.
    $email_subject = "$subject_prefix " . $subject;
    
    $email_body = "You have received a new message from your website contact form.\n\n"
                . "Name: $name\n"
                . "Email: $email\n"
                . "Subject: $subject\n\n"
                . "Message:\n$message";

    // Set headers
    $headers  = "From: no-reply@" . $_SERVER['HTTP_HOST'] . "\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // 4. Send the Email
    if (mail($recipient_email, $email_subject, $email_body, $headers)) {
        // Success - Return localized JSON
        echo json_encode(['success' => t('success_sent', $lang, $messages)]);
    } else {
        // Server failed to send - Return localized JSON error
        http_response_code(500);
        echo json_encode(['error' => t('fail_server', $lang, $messages)]);
    }

} else {
    // Direct access attempt
    http_response_code(403);
    // Fallback message for direct access (default to EN since no POST data)
    echo json_encode(['error' => 'Invalid request method.']);
}
?>