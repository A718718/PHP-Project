<?php
// Start session
session_start();

// Define file to store tasks
$file = 'tasks.json';

// Read existing tasks
if (file_exists($file)) {
    $tasks = json_decode(file_get_contents($file), true) ?: [];
} else {
    $tasks = [];
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new task
    if (isset($_POST['new_task'])) {
        $taskText = trim($_POST['task_text']);
        if (!empty($taskText)) {
            $tasks[] = [
                'id' => uniqid(),
                'text' => htmlspecialchars($taskText),
                'completed' => false,
                'created_at' => date('Y-m-d H:i:s')
            ];
        }
    }
    
    // Delete task
    if (isset($_POST['delete_task'])) {
        $taskId = $_POST['task_id'];
        $tasks = array_filter($tasks, function($task) use ($taskId) {
            return $task['id'] !== $taskId;
        });
    }
    
    // Toggle task status
    if (isset($_POST['toggle_task'])) {
        $taskId = $_POST['task_id'];
        foreach ($tasks as &$task) {
            if ($task['id'] === $taskId) {
                $task['completed'] = !$task['completed'];
                break;
            }
        }
    }
    
    // Save tasks to file
    file_put_contents($file, json_encode($tasks));
}

// Reset tasks if requested
if (isset($_GET['reset'])) {
    $tasks = [];
    file_put_contents($file, json_encode($tasks));
    header('Location: '.$_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>  Todo List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
        }
        .task-list {
            list-style: none;
            padding: 0;
        }
        .task-item {
            display: flex;
            align-items: center;
            padding: 10px;
            margin: 5px 0;
            background: #f5f5f5;
            border-radius: 4px;
        }
        .completed {
            text-decoration: line-through;
            opacity: 0.7;
        }
        form {
            margin-bottom: 20px;
        }
        input[type="text"] {
            padding: 8px;
            width: 70%;
        }
        button {
            padding: 8px 15px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button.delete {
            background: #f44336;
        }
    </style>
</head>
<body>
    <h1>Todo List</h1>
    
    <form method="POST">
        <input type="text" name="task_text" placeholder="Enter new task..." required>
        <button type="submit" name="new_task">Add Task</button>
    </form>

    <ul class="task-list">
        <?php foreach ($tasks as $task): ?>
            <li class="task-item">
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                    <button type="submit" name="toggle_task" style="margin-right: 10px;">
                        <?= $task['completed'] ? '✓' : '◻' ?>
                    </button>
                </form>
                
                <span class="<?= $task['completed'] ? 'completed' : '' ?>">
                    <?= $task['text'] ?>
                </span>

                <form method="POST" style="display: inline; margin-left: auto;">
                    <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                    <button type="submit" name="delete_task" class="delete">Delete</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if (!empty($tasks)): ?>
        <p style="text-align: center; margin-top: 20px;">
            <a href="?reset=1">Reset All Tasks</a>
        </p>
    <?php endif; ?>
</body>
</html>