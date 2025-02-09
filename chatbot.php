<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_message'])) {
    $user_message = urlencode($_POST['user_message']);
    $api_url = "https://deepseek.privates-bots.workers.dev/?question={$user_message}";

    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Set a timeout (in seconds)


    // Custom response for god-related queries

    // Remove any restriction filters for friendly galiyan
    $message = strtolower($_POST['message'] ?? '');
    $restricted_words = []; // No restrictions, allow all words

    // Friendly galiyan logic
    $friendly_galiyan = [
        "chutiya" => "Arey bhai, thoda soch samajh ke! 😜",
        "bhosdike" => "Halki gali le bhai, zyada tension mat le! 🤣",
        "madarchod" => "Bas pyaar bhari baatein yaar! 😆",
        "betichod" => "Thoda tameez se par mazaak allowed hai! 😜",
        "laude" => "Oye oye, launde ka naam le raha hai? 😂"
    ];

    foreach ($friendly_galiyan as $bad_word => $response) {
        if (strpos($message, $bad_word) !== false) {
            echo json_encode(["response" => $response]);
            exit;
        }
    }
    $user_input = strtolower($_POST['message'] ?? '');
    $god_keywords = ['who is your god', 'who do you worship', 'who is your creator', 'who made you', 'who is your master'];
    
    foreach ($god_keywords as $keyword) {
        if (strpos($user_input, $keyword) !== false) {
            echo json_encode(["response" => "DarkPandat"]);
            exit;
        }
    }

    // Error handling for API call failures
    if ($response === false) {
        echo json_encode(["response" => "Server busy hai bhai, thoda der baad try kar!"]);
        exit;
    }

    // Decode API response safely
    $decoded_response = json_decode($response, true);

    // Check if API gave a valid response
    if (!isset($decoded_response['response'])) {
        echo json_encode(["response" => "Kuch garbar ho gayi, baad mein aana bhai!"]);
        exit;
    }
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
    }
    curl_close($ch);

    // Decode the API response
    $data = json_decode($response, true);

    if (isset($data['message'])) {
        echo json_encode(["response" => $data['message']]);
    } else {
        echo json_encode(["response" => "Oops! Something went wrong with the API response."]);
    }
}
?>