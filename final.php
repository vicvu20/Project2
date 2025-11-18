<?php
require_once 'common.php';

if (!isset($_SESSION['players'])) {
    header('Location: index.php');
    exit;
}

init_game_if_needed();

$players = $_SESSION['players'];
$scores  = $_SESSION['scores'];
$final   = $_SESSION['final_question'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correct = normalize_answer($final['answer']);

    foreach ($players as $i => $name) {
        $wKey = "wager_$i";
        $aKey = "answer_$i";

        if (!isset($_POST[$wKey], $_POST[$aKey])) continue;

        $wager = (int) $_POST[$wKey];
        if ($wager < 0) $wager = 0;
        if ($wager > $scores[$i]) $wager = $scores[$i];

        $userNorm = normalize_answer($_POST[$aKey]);

        if ($userNorm === $correct) {
            $scores[$i] += $wager;
            $_SESSION['correct_count'][$i]++;
        } else {
            $scores[$i] -= $wager;
            $_SESSION['incorrect_count'][$i]++;
        }
    }

    $_SESSION['scores'] = $scores;
    header('Location: results.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Final Jeopardy</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg">
<div class="container">
    <h1 class="title">Final Jeopardy</h1>
    <h2 class="subtitle"><?= sanitize($final['category']) ?></h2>
    <div class="card">
        <p class="question-text"><?= sanitize($final['question']) ?></p>
        <form method="post">
            <?php foreach ($players as $i => $name): ?>
                <fieldset class="final-player">
                    <legend><?= sanitize($name) ?> (Score: <?= $scores[$i] ?>)</legend>
                    <label>Wager (0 to <?= $scores[$i] ?>):
                        <input type="number" name="wager_<?= $i ?>" min="0" max="<?= $scores[$i] ?>" required>
                    </label>
                    <label>Your Final Answer:
                        <input type="text" name="answer_<?= $i ?>" required>
                    </label>
                </fieldset>
            <?php endforeach; ?>

            <button type="submit" class="btn primary">Submit Final Answers</button>
        </form>
    </div>
</div>
</body>
</html>
