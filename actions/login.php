<?php
session_start();
require_once __DIR__ . '/../conn/db_manage.php';
//Open connection
$db = new db_manage();
if ($db == null) echo $db->error;

// echo password_hash(123456, PASSWORD_BCRYPT);

if (isset($_POST['logId'], $_POST['password'])) {
    if ($_POST['logId'] == "" || $_POST['password'] == "") return false;

    $log_id = $_POST['logId'];
    $password = $_POST['password'];
    $res = "";

    $sqlQuery = $db->exec_query("
    SELECT
        id,
        username,
        full_name,
        password,
        user_type
    FROM m_user
    WHERE username = '$log_id'
    AND delete_flg = 0");

    if ($sqlQuery != NULL) {
        if (password_verify($password, $sqlQuery[0]["password"])) {
            $cur_id = $sqlQuery[0]['id'];
            $res = 1;
            // set session mode
            $sqlQuery[0]["user_id"] = $sqlQuery[0]['id'];
            unset($sqlQuery[0]['id']);
            unset($sqlQuery[0][0]);
            unset($sqlQuery[0]['password']);
            unset($sqlQuery[0][2]);
            $user_cred = $sqlQuery[0];
            $_SESSION['login'] = $user_cred;
        } else {
            $res = 2;
            header("Location: /index.php");
            exit();
        }
    } else {
        $res = 0;
    }
    echo $res;
}