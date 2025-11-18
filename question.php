<?php
require_once 'common.php';

if (!isset($_SESSION['players'])) {
    header('Location: index.php');
    exit;
}

init_game_if_needed();

$players = $_SESSION['players'];
$current = $_SESSION['current_player'];
$scores  = &$_SESSION['scores'];

$categories = $_SESSION['categories'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['category'], $_GET['value'])) {
        header('Location: gameboard.php');
        exit;
    }

    $category = $_GET['category'];
    $value    = (int) $_GET['value'];

    if (!isset($categories[$category][$value])) {
        header('Location: gameboard.php');
        exit;
    }

    $qa = $categories[$category][$value];
    $isDD = is_daily_double($category, $value);

    $_SESSION['current_question'] = [
        'category' => $category,
        'value'    => $value,
        'is_dd'    => $isDD
    ];
    $feedback = null;

} else { // POST -> process answer
    if (!isset($_SESSION['current_question'])) {
        header('Location: gameboard.php');
        exit;
    }

    $qInfo    = $_SESSION['current_question'];
    $category = $qInfo['category'];
    $value    = $qInfo['value'];
    $isDD     = $qInfo['is_dd'];

    $qa = $categories[$category][$value];

    $userAnswer = isset($_POST['answer']) ? $_POST['answer'] : '';
    $correct    = normalize_answer($qa['a']);
    $userNorm   = normalize_answer($userAnswer);

    $wager = $value;
    if ($isDD) {
        $wagerInput = isset($_POST['wager']) ? (int) $_POST['wager'] : $value;
        $maxWager   = max($value, $scores[$current]);
        if ($wagerInput < 0) $wagerInput = 0;
        if ($wagerInput > $maxWager) $wagerInput = $maxWager;
        $wager = $wagerInput;
    }

    $feedback = '';
    if ($userNorm === $correct) {
        $scores[$current] += $wager;
        $_SESSION['correct_count'][$current]++;
        $feedback = "Correct! You earned {$wager} points.";
    } else {
        $scores[$current] -= $wager;
        $_SESSION['incorrect_count'][$current]++;
        $feedback = "Incorrect. The correct answer was: \"" . sanitize($qa['a']) . "\". You lost {$wager} points.";
    }

    $_SESSION['scores'] = $scores;
    $_SESSION['answered'][$category][$value] = true;

    // Move to next player
    $numPlayers = count($players);
    $_SESSION['current_player'] = ($current + 1) % $numPlayers;

    $showResultOnly = true;
}

// Reload category/value if needed
if (!isset($category)) {
    $qInfo    = $_SESSION['current_question'];
    $category = $qInfo['category'];
    $value    = $qInfo['value'];
    $isDD     = $qInfo['is_dd'];
    $qa       = $categories[$category][$value];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Question - <?= sanitize($category) ?> <?= $value ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg">
<div class="container">
    <h1 class="title small">Category: <?= sanitize($category) ?> – <?= $value ?></h1>

    <?php if (!empty($showResultOnly)): ?>
        <div class="card">
            <p class="question-text"><?= sanitize($qa['q']) ?></p>
            <p class="feedback"><?= sanitize($feedback) ?></p>
            <a href="gameboard.php" class="btn primary">Back to Board</a>
        </div>
    <?php else: ?>
        <div class="card">
            <?php if ($isDD): ?>
                <div class="daily-double">DAILY DOUBLE!</div>
                <p>You may wager up to your current score or the question value, whichever is higher.</p>
            <?php endif; ?>

            <p class="question-text"><?= sanitize($qa['q']) ?></p>

            <form method="post">
                <?php if ($isDD): ?>
                    <label>Wager:
                        <input type="number" name="wager" min="0" step="10" required>
                    </label>
                <?php endif; ?>
                <label>Your Answer:
                    <input type="text" name="answer" required>
                </label>
                <button type="submit" class="btn primary">Submit Answer</button>
            </form>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
