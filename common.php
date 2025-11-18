<?php
// common.php
// Shared helpers and data for Jeopardy! Battle Arena

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function init_game_if_needed() {
    if (!isset($_SESSION['categories'])) {
        init_categories();
    }
    if (!isset($_SESSION['answered'])) {
        $_SESSION['answered'] = [];
    }
    if (!isset($_SESSION['current_player'])) {
        $_SESSION['current_player'] = 0;
    }
    if (!isset($_SESSION['correct_count'])) {
        $_SESSION['correct_count'] = array_fill(0, count($_SESSION['players']), 0);
    }
    if (!isset($_SESSION['incorrect_count'])) {
        $_SESSION['incorrect_count'] = array_fill(0, count($_SESSION['players']), 0);
    }
}

function init_categories() {
    // Categories with values and Q/A pairs
    $categories = [
        "HTML" => [
            100 => ["q" => "What does HTML stand for?", "a" => "hypertext markup language"],
            200 => ["q" => "Which tag is used to create a link? (just the letter, e.g. a)", "a" => "a"],
            300 => ["q" => "Which tag defines the largest heading level? (e.g. h1)", "a" => "h1"],
        ],
        "CSS" => [
            100 => ["q" => "What does CSS stand for?", "a" => "cascading style sheets"],
            200 => ["q" => "Which property changes text color?", "a" => "color"],
            300 => ["q" => "Which property sets the background color?", "a" => "background-color"],
        ],
        "PHP" => [
            100 => ["q" => "PHP code is executed on which side: client or server?", "a" => "server"],
            200 => ["q" => "Which symbol starts a variable in PHP?", "a" => "$"],
            300 => ["q" => "Which superglobal is used for form data sent with POST?", "a" => "_post"],
        ],
        "General Web" => [
            100 => ["q" => "What does URL stand for?", "a" => "uniform resource locator"],
            200 => ["q" => "Which protocol is used for secure web traffic? (e.g. https)", "a" => "https"],
            300 => ["q" => "Which response code means 'Not Found'?", "a" => "404"],
        ],
    ];

    $_SESSION['categories'] = $categories;

    // Mark all questions as not answered yet
    $_SESSION['answered'] = [];
    foreach ($categories as $cat => $questions) {
        foreach ($questions as $value => $_) {
            $_SESSION['answered'][$cat][$value] = false;
        }
    }

    // Total number of regular questions
    $_SESSION['total_questions'] = count($categories) * count(current($categories));

    // Choose one Daily Double at random
    $allKeys = [];
    foreach ($categories as $cat => $questions) {
        foreach ($questions as $value => $_) {
            $allKeys[] = [$cat, $value];
        }
    }
    $dailyIndex = array_rand($allKeys);
    $_SESSION['daily_double'] = [
        'category' => $allKeys[$dailyIndex][0],
        'value'    => $allKeys[$dailyIndex][1],
    ];

    $_SESSION['final_question'] = [
        'category' => 'Final: Web Fundamentals',
        'question' => 'Name the three core languages of the web front-end (in order: HTML, CSS, ?)',
        'answer'   => 'javascript'
    ];
}

function sanitize($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function normalize_answer($str) {
    $str = trim(strtolower($str));
    // Small normalization to ignore spaces and dollar sign
    $str = str_replace([' ', '$'], '', $str);
    return $str;
}

function is_daily_double($category, $value) {
    if (!isset($_SESSION['daily_double'])) return false;
    $dd = $_SESSION['daily_double'];
    return $dd['category'] === $category && $dd['value'] == $value;
}

function all_questions_answered() {
    $answered = 0;
    foreach ($_SESSION['answered'] as $cat => $vals) {
        foreach ($vals as $v => $flag) {
            if ($flag) $answered++;
        }
    }
    return $answered >= $_SESSION['total_questions'];
}
