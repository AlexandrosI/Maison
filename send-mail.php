<?php
// =====================================================
// CONTACT FORM BACKEND + SHARED FRONTEND HANDLER (EN / GR / FR)
// =====================================================

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['asset']) && $_GET['asset'] === 'form-handler') {
    header('Content-Type: application/javascript; charset=UTF-8');
    echo <<<'JS'
(function () {
    if (window.__maisonContactFormInit) {
        return;
    }
    window.__maisonContactFormInit = true;

    var form = document.getElementById("contactForm");
    if (!form) {
        return;
    }

    var submitBtn = document.getElementById("submitBtn");
    var messageContainer = document.getElementById("form-message");
    var hideTimer = null;

    function showMessage(type, text) {
        if (!messageContainer || !text) {
            return;
        }

        messageContainer.className = type === "success" ? "success" : "error";
        messageContainer.textContent = text;

        if (hideTimer) {
            clearTimeout(hideTimer);
        }

        hideTimer = setTimeout(function () {
            messageContainer.className = "";
            messageContainer.textContent = "";
        }, 6000);
    }

    var urlParams = new URLSearchParams(window.location.search);
    var successMessage = urlParams.get("success");
    var errorMessage = urlParams.get("error");

    if (successMessage) {
        showMessage("success", successMessage);
    } else if (errorMessage) {
        showMessage("error", errorMessage);
    }

    if (successMessage || errorMessage) {
        window.history.replaceState({}, document.title, window.location.pathname);
    }

    form.addEventListener("submit", function (event) {
        event.preventDefault();

        var defaultButtonText = submitBtn ? submitBtn.textContent : "";
        var loadingText = submitBtn ? (submitBtn.getAttribute("data-loading-text") || defaultButtonText) : "";

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = loadingText;
        }

        var formData = new FormData(form);

        fetch(form.action, {
            method: "POST",
            body: formData,
            headers: {
                "Accept": "application/json",
                "X-Requested-With": "XMLHttpRequest"
            }
        })
            .then(function (response) {
                return response.json().catch(function () {
                    return { error: "Unexpected server response." };
                }).then(function (payload) {
                    if (!response.ok) {
                        throw payload;
                    }
                    return payload;
                });
            })
            .then(function (payload) {
                if (payload.success) {
                    showMessage("success", payload.success);
                    form.reset();
                    return;
                }

                if (payload.error) {
                    showMessage("error", payload.error);
                    return;
                }

                showMessage("error", "Unexpected server response.");
            })
            .catch(function (error) {
                var message = (error && error.error) ? error.error : "Network error. Please try again.";
                showMessage("error", message);
            })
            .finally(function () {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = defaultButtonText;
                }
            });
    });
})();
JS;
    exit;
}

// Configuration
$recipient_email = "alex@rockmedia.gr";
$subject_prefix = "New Maison Contact Submission:";

// Dictionary for UI Messages (AJAX and Redirect Responses)
$messages = [
    'en' => [
        'error_name_required' => "Name is required.",
        'error_email_invalid' => "A valid email address is required.",
        'error_phone_invalid' => "Phone can contain numbers only.",
        'error_guests_required' => "Number of guests is required.",
        'error_guests_invalid' => "Number of guests must contain numbers only.",
        'error_arrival_required' => "Arrival date is required.",
        'error_departure_required' => "Departure date is required.",
        'error_arrival_invalid' => "Arrival date is invalid.",
        'error_departure_invalid' => "Departure date is invalid.",
        'error_departure_before_arrival' => "Departure date must be after arrival date.",
        'error_subject_required' => "Subject is required.",
        'error_message_required' => "Message is required.",
        'success_sent' => "Your message has been sent successfully.",
        'fail_server' => "The server could not send the email. Please try again later.",
        'invalid_method' => "Invalid request method."
    ],
    'gr' => [
        'error_name_required' => "Το όνομα είναι υποχρεωτικό.",
        'error_email_invalid' => "Απαιτείται έγκυρη διεύθυνση email.",
        'error_phone_invalid' => "Το τηλέφωνο πρέπει να περιέχει μόνο αριθμούς.",
        'error_guests_required' => "Ο αριθμός επισκεπτών είναι υποχρεωτικός.",
        'error_guests_invalid' => "Ο αριθμός επισκεπτών πρέπει να περιέχει μόνο αριθμούς.",
        'error_arrival_required' => "Η ημερομηνία άφιξης είναι υποχρεωτική.",
        'error_departure_required' => "Η ημερομηνία αναχώρησης είναι υποχρεωτική.",
        'error_arrival_invalid' => "Η ημερομηνία άφιξης δεν είναι έγκυρη.",
        'error_departure_invalid' => "Η ημερομηνία αναχώρησης δεν είναι έγκυρη.",
        'error_departure_before_arrival' => "Η αναχώρηση πρέπει να είναι μετά την άφιξη.",
        'error_subject_required' => "Το θέμα είναι υποχρεωτικό.",
        'error_message_required' => "Το μήνυμα είναι υποχρεωτικό.",
        'success_sent' => "Το μήνυμά σας αποστάλθηκε επιτυχώς.",
        'fail_server' => "Ο διακομιστής δεν μπόρεσε να στείλει το email. Δοκιμάστε ξανά αργότερα.",
        'invalid_method' => "Μη έγκυρη μέθοδος αιτήματος."
    ],
    'fr' => [
        'error_name_required' => "Le nom est obligatoire.",
        'error_email_invalid' => "Une adresse email valide est obligatoire.",
        'error_phone_invalid' => "Le telephone ne peut contenir que des chiffres.",
        'error_guests_required' => "Le nombre de personnes est obligatoire.",
        'error_guests_invalid' => "Le nombre de personnes doit contenir uniquement des chiffres.",
        'error_arrival_required' => "La date d'arrivee est obligatoire.",
        'error_departure_required' => "La date de depart est obligatoire.",
        'error_arrival_invalid' => "La date d'arrivee est invalide.",
        'error_departure_invalid' => "La date de depart est invalide.",
        'error_departure_before_arrival' => "La date de depart doit etre apres la date d'arrivee.",
        'error_subject_required' => "Le sujet est obligatoire.",
        'error_message_required' => "Le message est obligatoire.",
        'success_sent' => "Votre message a ete envoye avec succes.",
        'fail_server' => "Le serveur n'a pas pu envoyer l'email. Veuillez reessayer plus tard.",
        'invalid_method' => "Methode de requete invalide."
    ]
];

$lang = isset($_POST['lang']) ? strtolower(trim((string) $_POST['lang'])) : 'en';
if (!array_key_exists($lang, $messages)) {
    $lang = 'en';
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond_json(405, ['error' => t('invalid_method', $lang, $messages)]);
}

$redirect_path = normalized_redirect_path($_POST['redirect'] ?? 'contact.html');

// Sanitize and Validate Input
$name = isset($_POST['name']) ? trim(strip_tags((string) $_POST['name'])) : '';
$email = isset($_POST['email']) ? trim((string) $_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim((string) $_POST['phone']) : '';
$guests = isset($_POST['guests']) ? trim((string) $_POST['guests']) : '';
$arrival_input = isset($_POST['arrival']) ? trim((string) $_POST['arrival']) : '';
$departure_input = isset($_POST['departure']) ? trim((string) $_POST['departure']) : '';
$subject = isset($_POST['subject']) ? trim(strip_tags((string) $_POST['subject'])) : '';
$message = isset($_POST['message']) ? trim(strip_tags((string) $_POST['message'])) : '';

$arrival = parse_date_string($arrival_input);
$departure = parse_date_string($departure_input);

$errors = [];

if ($name === '') {
    $errors[] = t('error_name_required', $lang, $messages);
}

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = t('error_email_invalid', $lang, $messages);
}

if ($phone !== '' && !ctype_digit($phone)) {
    $errors[] = t('error_phone_invalid', $lang, $messages);
}

if ($guests === '') {
    $errors[] = t('error_guests_required', $lang, $messages);
} elseif (!ctype_digit($guests) || (int) $guests < 1) {
    $errors[] = t('error_guests_invalid', $lang, $messages);
}

if ($arrival_input === '') {
    $errors[] = t('error_arrival_required', $lang, $messages);
} elseif ($arrival === null) {
    $errors[] = t('error_arrival_invalid', $lang, $messages);
}

if ($departure_input === '') {
    $errors[] = t('error_departure_required', $lang, $messages);
} elseif ($departure === null) {
    $errors[] = t('error_departure_invalid', $lang, $messages);
}

if ($arrival !== null && $departure !== null && $departure <= $arrival) {
    $errors[] = t('error_departure_before_arrival', $lang, $messages);
}

if ($subject === '') {
    $errors[] = t('error_subject_required', $lang, $messages);
}

if ($message === '') {
    $errors[] = t('error_message_required', $lang, $messages);
}

if (!empty($errors)) {
    $error_text = implode(' ', $errors);
    if (is_ajax_request()) {
        respond_json(400, ['error' => $error_text]);
    }
    redirect_with_message('error', $error_text, $redirect_path);
}

// Prepare Email Content
$email_subject = $subject_prefix . ' ' . $subject;

$phone_for_email = $phone === '' ? 'Not provided' : $phone;
$arrival_for_email = $arrival !== null ? $arrival->format('Y-m-d') : $arrival_input;
$departure_for_email = $departure !== null ? $departure->format('Y-m-d') : $departure_input;
$language_for_email = strtoupper($lang);
$submitted_at_utc = gmdate('Y-m-d H:i:s') . ' UTC';

$email_body = "You have received a new message from your website contact form.\n\n"
    . "Language: $language_for_email\n"
    . "Form page: $redirect_path\n"
    . "Submitted at: $submitted_at_utc\n\n"
    . "Name: $name\n"
    . "Email: $email\n"
    . "Phone: $phone_for_email\n"
    . "Number of guests: $guests\n"
    . "Arrival: $arrival_for_email\n"
    . "Departure: $departure_for_email\n"
    . "Subject: $subject\n\n"
    . "Message:\n$message";

$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$host = preg_replace('/[^a-z0-9.\-]/i', '', $host);
if ($host === '') {
    $host = 'localhost';
}

$headers = "From: no-reply@" . $host . "\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

if (mail($recipient_email, $email_subject, $email_body, $headers)) {
    $success_text = t('success_sent', $lang, $messages);
    if (is_ajax_request()) {
        respond_json(200, ['success' => $success_text]);
    }
    redirect_with_message('success', $success_text, $redirect_path);
}

$server_error_text = t('fail_server', $lang, $messages);
if (is_ajax_request()) {
    respond_json(500, ['error' => $server_error_text]);
}
redirect_with_message('error', $server_error_text, $redirect_path);

function t($key, $lang, $dict)
{
    return $dict[$lang][$key] ?? $dict['en'][$key] ?? $key;
}

function is_ajax_request()
{
    $x_requested_with = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '');
    $accept = strtolower($_SERVER['HTTP_ACCEPT'] ?? '');

    return $x_requested_with === 'xmlhttprequest' || strpos($accept, 'application/json') !== false;
}

function respond_json($status_code, $payload)
{
    http_response_code($status_code);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function parse_date_string($value)
{
    $candidate = trim((string) $value);
    if ($candidate === '') {
        return null;
    }

    $formats = ['Y-m-d', 'm/d/Y'];
    foreach ($formats as $format) {
        $date = DateTimeImmutable::createFromFormat('!' . $format, $candidate);
        $errors = DateTimeImmutable::getLastErrors();
        $is_valid = $errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0);
        if ($date !== false && $is_valid) {
            return $date;
        }
    }

    return null;
}

function normalized_redirect_path($path)
{
    $candidate = trim((string) $path);

    if ($candidate === '') {
        return 'contact.html';
    }

    if (preg_match('/^(?:[a-z]+:)?\/\//i', $candidate)) {
        return 'contact.html';
    }

    $candidate = ltrim($candidate, '/');

    if (strpos($candidate, '..') !== false) {
        return 'contact.html';
    }

    if (!preg_match('/^[a-z0-9_\-\/\.]+$/i', $candidate)) {
        return 'contact.html';
    }

    if (strtolower(substr($candidate, -5)) !== '.html') {
        return 'contact.html';
    }

    return $candidate;
}

function redirect_with_message($type, $message, $redirect_path)
{
    $query = http_build_query([$type => $message], '', '&', PHP_QUERY_RFC3986);
    header('Location: /' . $redirect_path . '?' . $query, true, 303);
    exit;
}
?>