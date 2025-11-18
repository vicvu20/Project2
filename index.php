<?php
require_once 'common.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $players = [];
    foreach (['player1', 'player2', 'player3', 'player4'] as $field) {
        if (!empty($_POST[$field])) {
            $players[] = trim($_POST[$field]);
        }
    }

    // Need at least 2 players
    if (count($players) >= 2) {
        $_SESSION['players'] = $players;
        $_SESSION['scores'] = array_fill(0, count($players), 0);
        $_SESSION['current_player'] = 0;
        unset($_SESSION['categories']); // force re-init
        init_game_if_needed();
        header('Location: gameboard.php');
        exit;
    } else {
        $error = "Please enter at least 2 player names.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jeopardy! Battle Arena - Start</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg">
<div class="container">
    <h1 class="title">Jeopardy! Battle Arena</h1>
    <p class="subtitle">Enter 2–4 players to begin.</p>

    <?php if (!empty($error)): ?>
        <div class="error"><?= sanitize($error) ?></div>
    <?php endif; ?>

    <form method="post" class="card">
        <label>Player 1:
            <input type="text" name="player1" required>
        </label>
        <label>Player 2:
            <input type="text" name="player2" required>
        </label>
        <label>Player 3 (optional):
            <input type="text" name="player3">
        </label>
        <label>Player 4 (optional):
            <input type="text" name="player4">
        </label>
        <button type="submit" class="btn primary">Start Game</button>
    </form>
</div>
</body>
</html>
