<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../conn/db_manage.php';
//Open connection
$db = new db_manage();
if ($db == null) echo $db->error;

$user = 0;
if (isset($_SESSION['login'])) {
    $user = $_SESSION['login']['user_id'];
}

if (isset($_POST['load_user'])) {
    $col = "id";
    $order = "DESC";
    $search_field = "";
    $page = 1;

    if (isset($_POST['page'])) {
        $page = $_POST['page'];
    }

    if (isset($_POST['field'], $_POST['sort_type'])) {
        $order = $_POST['sort_type'] == "" ? 'DESC' : $_POST['sort_type'];

        switch ($_POST['field']) {
            case 1:
                $col = "id";
                break;
            case 2:
                $col = "username";
                break;
            case 3:
                $col = "full_name";
                break;
            case 4:
                $col = "user_type";
                break;
        }
    }

    if (isset($_POST['search_field'])) {
        $search = $_POST['search_field'];
        if ($search != "") {
            $search_field = "
            AND username LIKE '%$search%'";
        }
    }

    $res_per_pg = 5;
    $sql_cnt = $db->exec_query("SELECT COUNT(id) AS total FROM m_user WHERE delete_flg = 0");
    $total_res = $sql_cnt[0]['total'];

    $total_pgs = ceil($total_res / $res_per_pg);
    $start_limit = ($page - 1) * $res_per_pg;

    $sql_limit = "LIMIT  " . $start_limit . ", " . $res_per_pg;

    $sql = "
    SELECT
        id,
        username,
        full_name,
        user_type,
        CASE user_type
            WHEN 0 THEN 'Admin'
            WHEN 1 THEN 'User'
        END AS u_type
    FROM m_user
    WHERE delete_flg = 0
    $search_field
    ORDER BY $col $order
    $sql_limit";

    $res = "";
    $paginate = "";
    $sql_exec = $db->exec_query($sql);

    if ($sql_exec != NULL) {
        $cnt = 0;
        foreach ($sql_exec as $r) {
            $res .= "<tr>";
            $res .= "<td> " . $r['id'] . "</td>";
            $res .= "<td> " . $r['username'] . "</td>";
            $res .= "<td> " . $r['full_name'] . "</td>";
            $res .= "<td> " . $r['u_type'] . "</td>";
            $res .= "<td class='text-center text-nowrap'>";
            $res .= "<button class='btn btn-info btn-sm mr-2' data-toggle='modal' onClick='return openEdit(" . $r['id'] . ")'>";
            $res .= "    <i class='fa fa-wrench' aria-hidden='true'></i>";
            $res .= "</button>";
            $res .= "<button class='btn btn-danger btn-sm' onClick='return deleteUser(" . $r['id'] . ")''>";
            $res .= "     <i class='fa fa-trash' aria-hidden='true'></i>";
            $res .= "</button>";
            $res .= "</td>";
            $res .= "</tr>";
            $cnt++;
        }

        $paginate .= '<nav aria-label="Page navigation"><ul class="pagination justify-content-end">';
        if ($page > 1) {
            $paginate .= '<li class="page-item"><a class="page-link" href="#" data-page="' . ($page - 1) . '">&laquo;</a></li>';
        } else {
            $paginate .= '<li class="page-item disabled"><a class="page-link" href="#" tabindex="-1" aria-disabled="true">&laquo;</a></li>';
        }

        for ($i = 1; $i <= $total_pgs; $i++) {
            $active = ($page == $i) ? 'active' : '';
            $paginate .= '<li class="page-item ' . $active . '"><a class="page-link" href="#" data-page="' . $i . '">' . $i . '</a></li>';
        }

        if ($page < $total_pgs) {
            $paginate .= '<li class="page-item"><a class="page-link" href="#" data-page="' . ($page + 1) . '">&raquo;</a></li>';
        } else {
            $paginate .= '<li class="page-item disabled"><a class="page-link" href="#" tabindex="-1" aria-disabled="true">&raquo;</a></li>';
        }

        $paginate .= '</ul></nav>';
    } else {
        $res = "<td colspan='6' class='text-center'>No record(s) found.</td>";
        $paginate = "";
    }

    echo json_encode(array(
        "data" => $res,
        "paginate" => $paginate,
    ));
}

if (isset($_POST['add_user'])) {

    $pass = $_POST['pass'];
    $utype = $_POST['utype'];
    $uname = $_POST['uname'];
    $name = $_POST['name'];
    $enc_pass = password_hash($pass, PASSWORD_BCRYPT);

    //Check if already exists
    $chk = strtoupper($uname);
    $sql = $db->exec_query("SELECT username FROM m_user WHERE upper(username) = '$chk' AND delete_flg = 0");
    if ($sql != NULL) {
        echo "Username already exists. Please try again.";
        die();
    }

    $sql_in = $db->exec_cmd("INSERT INTO m_user (username, password, user_type, update_user, create_user, full_name) VALUES ('$uname', '$enc_pass', $utype, $user, $user, '$name')");
    echo $sql_in;
}

//Fetch
if (isset($_POST['get_user'])) {
    $user_id = $_POST['user_id'];

    $sql = $db->exec_query("SELECT id, username, full_name, user_type, password FROM m_user WHERE id = $user_id AND delete_flg = 0");
    if ($sql != NULL) {
        echo json_encode(array(
            'uname' => $sql[0]['username'],
            'utype' => $sql[0]['user_type'],
            'fullname' => $sql[0]['full_name']
        ), JSON_UNESCAPED_UNICODE);
    } else {
        echo 0;
    }
}

// Add
if (isset($_POST['update_user'])) {

    $sql_up = "UPDATE m_user SET ";
    $user_id = $_POST['user_id'];
    $utype = $_POST['utype'];
    $uname = $_POST['uname'];
    $name = $_POST['name'];

    // Check username
    $sql_chk = $db->exec_query("SELECT username, password FROM m_user WHERE username = '$uname' AND id <> $user_id AND delete_flg = 0");
    if ($sql_chk != NULL) {
        die("Username already exists");
    }

    if (isset($_POST['pass'], $_POST['new_pw'])) {
        $old_pass = $_POST['pass'];
        $new_pass = $_POST['new_pw'];

        $sql_chk = $db->exec_query("SELECT password FROM m_user WHERE id = $user_id AND delete_flg = 0");
        if ($sql_chk != NULL) {
            if (password_verify($old_pass, $sql_chk[0]['password'])) {

                $enc_pw = password_hash($new_pass, PASSWORD_BCRYPT);
                $sql_update = $db->exec_cmd("UPDATE m_admin SET password = '$enc_pw' WHERE id = $user_id");
                if ($sql_update != 0) {
                    die("Old password typed is incorrect. Please try again.");
                }
            } else {
                die("Old password typed is incorrect. Please try again.");
            }
        }
    }

    $sql_up = $db->exec_cmd($sql_up .= " full_name = '$name', username = '$uname', user_type = '$utype' WHERE id = $user_id");
    echo $sql_up;
}



//Delete
if (isset($_POST['del_user'])) {
    $user_id = $_POST['user_id'];

    $sql_del = $db->exec_cmd("UPDATE m_user SET delete_flg = 1 WHERE id = $user_id");
    echo $sql_del;
}
