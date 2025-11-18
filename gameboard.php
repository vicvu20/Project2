<?php
require_once 'common.php';

if (!isset($_SESSION['players'])) {
    header('Location: index.php');
    exit;
}

init_game_if_needed();

$categories = $_SESSION['categories'];
$answered   = $_SESSION['answered'];
$players    = $_SESSION['players'];
$scores     = $_SESSION['scores'];
$current    = $_SESSION['current_player'];

if (all_questions_answered()) {
    header('Location: final.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jeopardy! Battle Arena - Board</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg">
<div class="container">
    <header class="top-bar">
        <div>
            <h1 class="title small">Jeopardy! Battle Arena</h1>
            <p>Current Player: <strong><?= sanitize($players[$current]) ?></strong></p>
        </div>
        <div class="scoreboard">
            <?php foreach ($players as $i => $name): ?>
                <div class="score-card">
                    <div class="score-name"><?= sanitize($name) ?></div>
                    <div class="score-value"><?= $scores[$i] ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </header>

    <main>
        <div class="board">
            <?php foreach ($categories as $catName => $questions): ?>
                <div class="column">
                    <div class="category"><?= sanitize($catName) ?></div>
                    <?php foreach ($questions as $value => $qa): ?>
                        <?php $isUsed = $answered[$catName][$value]; ?>
                        <?php if ($isUsed): ?>
                            <div class="cell used"><?= $value ?></div>
                        <?php else: ?>
                            <a class="cell" href="question.php?category=<?= urlencode($catName) ?>&value=<?= $value ?>">
                                <?= $value ?>
                                <?php if (is_daily_double($catName, $value)): ?>
                                    <span class="dd-dot">*</span>
                                <?php endif; ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer class="footer">
        <p>No more questions? Board will automatically send you to Final Jeopardy.</p>
    </footer>
</div>
</body>
</html>
