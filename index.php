<?php

require_once 'config.php';
require_once 'Todo.php';
require_once 'TaskManager.php';

$database = new Database();
$db = $database->getConnection();
$taskManager = new TaskManager($db);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_todo'])) {
        $taskManager->setTitle($_POST['title']);
        $taskManager->setDescription($_POST['description']);
        if ($taskManager->create()) {
            // echo "<script>alert('Todo created successfully!');</script>";
        }
    }

    if (isset($_POST['update_status'])) {
        $newStatus = isset($_POST['status']) ? $_POST['status'] : 'pending'; 
    
        if ($taskManager->updateStatus($_POST['todo_id'], $newStatus)) {
            // echo "<script>alert('Task status updated!');</script>";
        }
    }

    if (isset($_POST['delete_todo'])) {
        if ($taskManager->delete($_POST['todo_id'])) {
            // echo "<script>alert('Todo deleted successfully!');</script>";
        }
    }

    
    if (isset($_POST['edit_todo'])) {
        $id = $_POST['todo_id'];
        $taskManager->setTitle($_POST['title']);
        $taskManager->setDescription($_POST['description']);
        if ($taskManager->update($id)) {
            // echo "<script>alert('Todo updated successfully!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Todo List</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f5f7fb;
        }

        .card-container {
            max-width: 300px;
            margin: 0 auto;
        }

        .todo-item {
            transition: transform 0.3s ease;
            height: 300px;
        }

        .todo-item:hover {
            transform: translateY(-5px);
        }

        .todo-item-content {
            overflow-y: auto;
            max-height: 180px;
        }

        .completed {
            border-left: 4px solid #2ecc71 !important;
        }

        .pending {
            border-left: 4px solid #cfc355 !important;
        }

        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.5);
        }

        .timestamp {
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="container py-4">
        <!-- Header -->
        <div class="bg-white rounded-3 shadow-sm p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-5">Todo List</h1>
                    <div class="text-muted">
                        <i class="fas fa-user"></i> Gilbert Ozioma<br>
                        <i class="fas fa-clock"></i> Last Updated: <?php echo date('h:i:s A'); ?><br>
                        <i class="fas fa-calendar"></i> <?php echo date('d-m-Y'); ?>
                    </div>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#todoModal">
                    <i class="fas fa-plus"></i> Add Todo
                </button>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="todoModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Todo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                            </div>
                            <button type="submit" name="add_todo" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Todo
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Todo List -->
        <div class="row g-4">
            <?php
            $result = $taskManager->read();
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $statusClass = $row['status'] == 'completed' ? 'completed' : 'pending';
                $statusBadgeClass = $row['status'] == 'completed' ? 'bg-success' : 'bg-warning';
                ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card w-100 todo-item <?php echo $statusClass; ?> h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <div class="text-muted timestamp mb-2">
                                <!-- <i class="fas fa-user"></i> Gilbert Ozioma<br> -->
                                <i class="fas fa-clock"></i> <?= date('h:i: A', strtotime($row['created_at'])); ?>
                                <i class="fas fa-calendar"></i> <?= date('d-m-Y', strtotime($row['created_at'])); ?>
                            </div>
                            <div class="todo-item-content flex-grow-1">
                                <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                            </div>
                            <div class="mt-3 pt-3 border-top">  
                                <form method="POST" class="d-flex align-items-center">
                                    <input type="hidden" name="todo_id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="update_status" value="1">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault<?= $row['id'] ?>" name="status" value="completed" onchange="this.form.submit()" <?php echo $row['status'] == 'completed' ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="flexSwitchCheckDefault<?= $row['id'] ?>">
                                            <?php echo ucfirst($row['status']); ?>
                                        </label>
                                    </div>
                                    <button type="button" class="btn ms-5 p-0" data-bs-toggle="modal" data-bs-target="#editTodoModal<?= $row['id']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn ms-3 p-0" data-bs-toggle="modal" data-bs-target="#deleteTodoModal<?= $row['id']; ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Modal -->
                <div class="modal fade" id="editTodoModal<?= $row['id']; ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Todo</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($row['title']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($row['description']); ?></textarea>
                                    </div>
                                    <input type="hidden" name="todo_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="edit_todo" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Changes
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delete Modal -->
                <div class="modal fade" id="deleteTodoModal<?= $row['id']; ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Delete Todo</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete this todo?</p>
                                <form method="POST">
                                    <input type="hidden" name="todo_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="delete_todo" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>