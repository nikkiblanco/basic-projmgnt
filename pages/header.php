<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['login'])) {
    header('Location: /pages/login.php');
    exit();
}
require_once __DIR__ . '/../conn/db_manage.php';
//Open connection
$db = new db_manage();
if ($db == null) echo $db->error;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Management</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="/pages/assets/css/styles.css">
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/solid.js" integrity="sha384-tzzSw1/Vo+0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ" crossorigin="anonymous"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js" integrity="sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</head>

<body>
    <main class="main">
        <div class="wrapper">
            <aside id="sidebar">
                <div class="d-flex">
                    <button class="toggle-btn" type="button">
                        <i class="fa fa-bars text-secondary"></i>
                    </button>
                    <div class="sidebar-logo">
                        <a href="/" class="text-secondary">
                        </a>
                    </div>
                </div>


                <ul class="sidebar-nav">
                    <li id="dashboard" class="sidebar-item">
                        <a href="/" class="sidebar-link text-secondary">
                            <i class="fas fa-chart-line text-secondary"></i>
                            <span class="ml-2">Dashboard</span>
                        </a>
                    </li>
                    <?php if ($_SESSION['login']['user_type'] == 0) { ?>
                        <li id="project" class="sidebar-item">
                            <a href="/pages/project.php" class="sidebar-link text-secondary">
                                <i class="fa fa-file text-secondary"></i>
                                <span class="ml-2">Projects</span>
                            </a>
                        </li>
                    <?php } ?>
                    <li id="task" class="sidebar-item">
                        <a href="/pages/task.php" class="sidebar-link text-secondary">
                            <i class="fa fa-tasks text-secondary"></i>
                            <span class="ml-2">Tasks</span>
                        </a>
                    </li>
                    <?php if ($_SESSION['login']['user_type'] == 0) { ?>
                        <li id="user" class="sidebar-item">
                            <a href="/pages/users.php" class="sidebar-link text-secondary">
                                <i class="fas fa-users-cog text-secondary"></i>
                                <span class="ml-2">Users</span>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
                <div class="sidebar-footer">
                    <a href="../actions/logout.php" class="sidebar-link text-secondary">
                        <i class="fas fa-sign-out-alt text-secondary"></i>
                        <span class="ml-2">Logout</span>
                    </a>
                </div>
            </aside>


            <div class="main p-3">