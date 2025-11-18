<?php
require_once 'common.php';

if (!isset($_SESSION['players'])) {
    header('Location: index.php');
    exit;
}

$players  = $_SESSION['players'];
$scores   = $_SESSION['scores'];
$corrects = $_SESSION['correct_count'];
$wrongs   = $_SESSION['incorrect_count'];

$winnerIndex = array_keys($scores, max($scores))[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Results - Jeopardy! Battle Arena</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg">
<div class="container">
    <h1 class="title">Game Results</h1>

    <div class="winner-card">
        <p class="winner-label">Winner:</p>
        <p class="winner-name"><?= sanitize($players[$winnerIndex]) ?></p>
        <p class="winner-score"><?= $scores[$winnerIndex] ?> points</p>
    </div>

    <h2 class="subtitle">Player Statistics</h2>
    <table class="stats-table">
        <thead>
        <tr>
            <th>Player</th>
            <th>Score</th>
            <th>Correct</th>
            <th>Incorrect</th>
            <th>Accuracy</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($players as $i => $name):
            $total = $corrects[$i] + $wrongs[$i];
            $acc = $total > 0 ? round($corrects[$i] / $total * 100, 1) : 0;
            ?>
            <tr>
                <td><?= sanitize($name) ?></td>
                <td><?= $scores[$i] ?></td>
                <td><?= $corrects[$i] ?></td>
                <td><?= $wrongs[$i] ?></td>
                <td><?= $acc ?>%</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="btn-row">
        <a href="index.php" class="btn">Play Again</a>
    </div>
</div>
</body>
</html>
