<?php
session_start();

// Initialize game
if (!isset($_SESSION['board']) || isset($_POST['reset'])) {
    $vehicles = ['🚗', '🚕', '🚙', '🚌', '🚎', '🏎', '🚓', '🚑'];
    $cards = array_merge($vehicles, $vehicles);
    shuffle($cards);
    
    $_SESSION['board'] = $cards;
    $_SESSION['revealed'] = array_fill(0, 16, false);
    $_SESSION['matched'] = array_fill(0, 16, false);
    $_SESSION['first_card'] = null;
    $_SESSION['moves'] = 0;
    $_SESSION['pairs_found'] = 0;
}

// Handle card clicks
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['card'])) {
    $index = (int)$_POST['card'];
    
    if (!$_SESSION['matched'][$index]) {
        $_SESSION['revealed'][$index] = true;
        $_SESSION['moves']++;

        if ($_SESSION['first_card'] === null) {
            $_SESSION['first_card'] = $index;
        } else {
            $first_index = $_SESSION['first_card'];
            
            if ($_SESSION['board'][$first_index] === $_SESSION['board'][$index]) {
                $_SESSION['matched'][$first_index] = true;
                $_SESSION['matched'][$index] = true;
                $_SESSION['pairs_found']++;
            } else {
                // Hide mismatched pair after short delay
                $_SESSION['revealed'][$first_index] = false;
                $_SESSION['revealed'][$index] = false;
            }
            
            $_SESSION['first_card'] = null;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Vehicle Memory Game</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .game-board {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin: 20px;
        }
        .card {
            width: 100px;
            height: 100px;
            background-color: #4CAF50;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            cursor: pointer;
            transition: transform 0.3s;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .hidden {
            background-color: #2196F3;
            color: transparent;
        }
        .matched {
            background-color: #9C27B0;
            cursor: not-allowed;
        }
        .stats {
            font-size: 20px;
            margin: 10px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            margin: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="stats">
        Moves: <?= $_SESSION['moves'] ?> | 
        Pairs Found: <?= $_SESSION['pairs_found'] ?> / 8
    </div>

    <form method="post">
        <div class="game-board">
            <?php foreach ($_SESSION['board'] as $index => $vehicle): ?>
                <button type="submit" name="card" value="<?= $index ?>" 
                    class="card <?= $_SESSION['revealed'][$index] || $_SESSION['matched'][$index] ? '' : 'hidden' ?>
                              <?= $_SESSION['matched'][$index] ? 'matched' : '' ?>"
                    <?= $_SESSION['matched'][$index] ? 'disabled' : '' ?>>
                    <?= $_SESSION['revealed'][$index] || $_SESSION['matched'][$index] 
                        ? htmlspecialchars($vehicle) 
                        : '?' ?>
                </button>
            <?php endforeach; ?>
        </div>
        <button type="submit" name="reset">New Game</button>
    </form>

    <?php if ($_SESSION['pairs_found'] === 8): ?>
        <div style="color: green; font-size: 24px; margin: 20px;">
            🎉 Congratulations! You won in <?= $_SESSION['moves'] ?> moves!
        </div>
    <?php endif; ?>
</body>
</html>