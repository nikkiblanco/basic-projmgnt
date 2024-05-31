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

if (isset($_POST['load_task'])) {
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
                $col = "proj_name";
                break;
            case 3:
                $col = "task_name";
                break;
            case 4:
                $col = "due_date";
                break;
            case 5:
                $col = "priority";
                break;
            case 6:
                $col = "full_name";
                break;
        }
    }

    if (isset($_POST['search_field'])) {
        $search = $_POST['search_field'];
        if ($search != "") {
            $search_field = "
            AND task_name LIKE '%$search%'";
        }
    }

    $res_per_pg = 5;
    $sql_cnt = $db->exec_query("SELECT COUNT(id) AS total FROM m_tasks WHERE delete_flg = 0");
    $total_res = $sql_cnt[0]['total'];

    $total_pgs = ceil($total_res / $res_per_pg);
    $start_limit = ($page - 1) * $res_per_pg;

    $sql_limit = "LIMIT  " . $start_limit . ", " . $res_per_pg;

    $sql = "
    SELECT
        MT.id, MT.task_name, DATE_FORMAT(MT.due_date, '%m/%d/%Y') AS due, MT.priority,
        CASE MT.priority
            WHEN 0 THEN 'High'
            WHEN 1 THEN 'Medium'
            WHEN 2 THEN 'Low'
        END AS prio,
        (SELECT MP.name FROM m_project MP WHERE MP.id = MT.project_id AND MP.delete_flg = 0) AS proj_name,
        (SELECT MU.full_name FROM m_user MU WHERE MT.user_id = MU.id AND MU.delete_flg = 0) AS full_name
    FROM m_tasks MT
    WHERE MT.delete_flg = 0
    $search_field
    ORDER BY $col $order
    $sql_limit";

    $res = "";
    $paginate = "";
    $sql_exec = $db->exec_query($sql);

    if ($sql_exec != NULL) {
        $cnt = 0;
        foreach ($sql_exec as $r) {
            $span_color = "";
            switch ($r['priority']) {
                case 1:
                    $span_color = "badge badge-pill badge-warning";
                    break;
                case 2:
                    $span_color = "badge badge-pill badge-secondary";
                    break;
                default:
                    $span_color = "badge badge-pill badge-danger";
                    break;
            }
            
            $res .= "<tr>";
            $res .= "<td> " . $r['id'] . "</td>";
            $res .= "<td> " . $r['proj_name'] . "</td>";
            $res .= "<td> " . $r['task_name'] . "</td>";
            $res .= "<td class='text-center'> " . $r['due'] . "</td>";
            $res .= "<td class='text-center'><span class='$span_color'> " . $r['prio'] . "</span></td>";
            $res .= "<td class='text-center'> " . $r['full_name'] . "</td>";
            $res .= "<td class='text-center text-nowrap'>";
            $res .= "<button class='btn btn-info btn-sm mr-2' data-toggle='modal' onClick='return openEdit(" . $r['id'] . ")'>";
            $res .= "    <i class='fa fa-wrench' aria-hidden='true'></i>";
            $res .= "</button>";
            $res .= "<button class='btn btn-danger btn-sm' onClick='return deleteTask(" . $r['id'] . ")''>";
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
        $res = "<td colspan='7' class='text-center'>No record(s) found.</td>";
        $paginate = "";
    }

    echo json_encode(array(
        "data" => $res,
        "paginate" => $paginate,
    ));
}

// Add
if (isset($_POST['add_task'])) {

    $proj_id = $_POST['proj'];
    $task = $_POST['task'];
    $desc = $_POST['desc'];
    $due_date = $_POST['due_date'];
    $assigned = $_POST['assign_user'];
    $prio = ($_POST['prio'] == "-1" ? 0 : $_POST['prio']);

    $sql = $db->exec_cmd("
    INSERT INTO m_tasks (project_id, task_name, description, due_date, priority, create_user, update_user, user_id)
    VALUES ('$proj_id', '$task', '$desc', '$due_date', $prio, $user, $user, $assigned)");

    echo $sql;
}

//Fetch
if (isset($_POST['get_task'])) {
    $task_id = $_POST['task_id'];

    $sql = $db->exec_query("SELECT id, task_name, project_id, user_id, description, due_date, priority FROM m_tasks WHERE id = $task_id AND delete_flg = 0");
    if ($sql != NULL) {
        echo json_encode(array(
            'proj_id' => $sql[0]['project_id'],
            'name' => $sql[0]['task_name'],
            'desc' => $sql[0]['description'],
            'due' => $sql[0]['due_date'],
            'prio' => $sql[0]['priority'],
            'user_id' => $sql[0]['user_id']
        ), JSON_UNESCAPED_UNICODE);
    } else {
        echo 0;
    }
}

// Add
if (isset($_POST['update_task'])) {

    $sql_up = "UPDATE m_tasks SET ";

    $task_id = $_POST['task_id'];
    $proj_id = $_POST['proj'];
    $task = $_POST['task'];
    $desc = $_POST['desc'];
    $due_date = $_POST['due_date'];
    $prio = ($_POST['prio'] == "-1" ? 0 : $_POST['prio']);
    $assigned = $_POST['assign_user'];

    $sql_up .= " task_name = '$task', due_date = '$due_date', priority = '$prio', user_id = $assigned,
    description = '$desc', project_id = '$proj_id', update_user = '$user' WHERE id = $task_id";

    $sql = $db->exec_cmd($sql_up);
    echo $sql;
}

//Delete
if (isset($_POST['del_task'])) {
    $task_id = $_POST['task_id'];

    $sql_del = $db->exec_cmd("UPDATE m_tasks SET delete_flg = 1 WHERE id = $task_id");
    echo $sql_del;
}
