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

if (isset($_POST['load_proj'])) {
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
                $col = "name";
                break;
            case 3:
                $col = "due_date";
                break;
            case 4:
                $col = "status";
                break;
            case 5:
                $col = "total_task";
                break;
        }
    }

    if (isset($_POST['search_field'])) {
        $search = $_POST['search_field'];
        if ($search != "") {
            $search_field = "
            AND name LIKE '%$search%'";
        }
    }

    $res_per_pg = 5;
    $sql_cnt = $db->exec_query("SELECT COUNT(id) AS total FROM m_project WHERE delete_flg = 0");
    $total_res = $sql_cnt[0]['total'];

    $total_pgs = ceil($total_res / $res_per_pg);
    $start_limit = ($page - 1) * $res_per_pg;

    $sql_limit = "LIMIT  " . $start_limit . ", " . $res_per_pg;

    $sql = "
    SELECT
    MP.id, MP.name, date_format(MP.due_date, '%m/%d/%Y') AS due, MP.status,
    CASE MP.status
        WHEN 0 THEN 'Todo'
        WHEN 1 THEN 'In-progress'
        WHEN 2 THEN 'Completed'
    END AS stat,
    MP.image_path,
    (SELECT COUNT(id) FROM m_tasks WHERE delete_flg = 0 AND project_id = MP.id) AS total_task
    FROM m_project MP
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
            $span_color = "";
            switch ($r['status']) {
                case 1:
                    $span_color = "badge badge-pill badge-success";
                    break;
                case 2:
                    $span_color = "badge badge-pill badge-secondary";
                    break;
                default:
                    $span_color = "badge badge-pill badge-primary";
                    break;
            }

            $img = ($r['image_path'] == "" ? "-" : "<img src=" . $r['image_path'] . " width='76' height='68' />");
            $res .= "<tr>";
            $res .= "<td class='align-middle'> " . $r['id'] . "</td>";
            $res .= "<td class='text-center align-middle'> " . $img . "</td>";
            $res .= "<td style='cursor:pointer'><a class='project_move' href='/pages/view_project.php/?id=" . htmlspecialchars($r['id']) . "'> " . $r['name'] . "</a></td>";
            $res .= "<td class='text-center align-middle'> " . $r['due'] . "</td>";
            $res .= "<td class='text-center align-middle'><span class='$span_color'> " . $r['stat'] . "</span></td>";
            $res .= "<td class='text-center align-middle'> " . $r['total_task'] . "</td>";
            $res .= "<td class='text-nowrap align-middle text-center'>";
            $res .= "<button class='btn btn-info btn-sm mr-2' data-toggle='modal' onClick='return openEdit(" . $r['id'] . ")'>";
            $res .= "    <i class='fa fa-wrench' aria-hidden='true'></i>";
            $res .= "</button>";
            $res .= "<button class='btn btn-danger btn-sm' onClick='return deleteProj(" . $r['id'] . ")''>";
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
        $res = "<td colspan='8' class='text-center'>No record(s) found.</td>";
        $paginate = "";
    }

    echo json_encode(array(
        "data" => $res,
        "paginate" => $paginate,
    ));
}

// Add
if (isset($_POST['add_proj'])) {

    $proj_name = $_POST['proj_name'];
    $desc = $_POST['desc'];
    $due_date = $_POST['due_date'];
    $status = ($_POST['status'] == "-1" ? 0 : $_POST['status']);

    $sql = $db->exec_cmd("
    INSERT INTO m_project (name, description, due_date, status, create_user, update_user)
    VALUES ('$proj_name', '$desc', '$due_date', '$status', $user, $user)");

    if ($sql == "0") {
        $proj_id = $db->get_last_id();

        //Image
        $file_path = realpath("../files");
        $folder_name = "projects";
        if (isset($_POST['img_proj'])) {
            if (!file_exists($file_path . "/" . $folder_name)) mkdir($file_path . "/" . $folder_name);

            $sub_folder = $file_path . "/$folder_name/" . $proj_id;
            if (!file_exists($sub_folder))  mkdir($sub_folder);

            $ext_file = explode('/', mime_content_type($_POST['img_proj']))[1];
            $file_format = $proj_id . "_" . date("dmY") . "_" . time() . "." . $ext_file;
            $img_content = file_get_contents($_POST['img_proj']);
            file_put_contents($sub_folder . "/" . $file_format, $img_content);

            $file =  "../files" . "/$folder_name/$proj_id/" . $file_format;
            $sql_update = $db->exec_cmd("UPDATE m_project SET image_path = '$file' WHERE id = $proj_id");
            echo $sql_update;
        }
    } else {
        echo $sql;
    }
}

//Delete
if (isset($_POST['del_proj'])) {
    $proj_id = $_POST['proj_id'];

    $sql_del = $db->exec_cmd("UPDATE m_project SET delete_flg = 1 WHERE id = $proj_id");
    echo $sql_del;
}

//Fetch
if (isset($_POST['get_proj'])) {
    $proj_id = $_POST['proj_id'];

    $sql = $db->exec_query("SELECT id, name, description, due_date, status, image_path FROM m_project WHERE id = $proj_id AND delete_flg = 0");
    if ($sql != NULL) {
        echo json_encode(array(
            'name' => $sql[0]['name'],
            'desc' => $sql[0]['description'],
            'due' => $sql[0]['due_date'],
            'stat' => $sql[0]['status'],
            'img' => ($sql[0]['image_path'] == "" ? 0 : $sql[0]['image_path'])
        ), JSON_UNESCAPED_UNICODE);
    } else {
        echo 0;
    }
}

// Add
if (isset($_POST['update_proj'])) {

    $sql_up = "UPDATE m_project SET ";

    $proj_name = $_POST['proj_name'];
    $desc = $_POST['desc'];
    $due_date = $_POST['due_date'];
    $status = ($_POST['status'] == "-1" ? 0 : $_POST['status']);

    $proj_id = $_POST['proj_id'];

    if ($proj_id == 0) die("No selected Project");

    $sql_up .= " name = '$proj_name', description = '$desc', due_date = '$due_date', status = $status";

    if (isset($_POST['img_proj'])) {

        //Check if there is available data in db, if col is not empty remove it
        $chk = $db->exec_query("SELECT image_path FROM m_project WHERE id = $proj_id AND delete_flg = 0");
        if ($chk != NULL) {
            if ($chk[0]['image_path'] != "") {
                if (file_exists($chk[0]['image_path'])) unlink($chk[0]['image_path']);
            }
        }

        $file_path = realpath("../files");
        $folder_name = "projects";

        if (!file_exists($file_path . "/" . $folder_name)) mkdir($file_path . "/" . $folder_name);
        $sub_folder = $file_path . "/$folder_name/" . $proj_id;
        if (!file_exists($sub_folder))  mkdir($sub_folder);

        $ext_file = explode('/', mime_content_type($_POST['img_proj']))[1];
        $file_format = $proj_id . "_" . date("dmY") . "_" . time() . "." . $ext_file;
        $img_content = file_get_contents($_POST['img_proj']);
        file_put_contents($sub_folder . "/" . $file_format, $img_content);

        $file =  "../files" . "/$folder_name/$proj_id/" . $file_format;
        $sql_up .= ", image_path = '$file'";
    }

    $sql_exec = $db->exec_cmd($sql_up .= ", update_user = $user WHERE id = $proj_id");
    echo $sql_exec;
}

if (isset($_POST['load_tasks'])) {
    $page = 1;
    $task_id = $_POST['task_id'];
    $page = $_POST['page'];

    $res_per_pg = 5;
    $sql_cnt = $db->exec_query("SELECT COUNT(id) AS total FROM m_tasks WHERE delete_flg = 0 AND id = $task_id");
    $total_res = $sql_cnt[0]['total'];

    $total_pgs = ceil($total_res / $res_per_pg);
    $start_limit = ($page - 1) * $res_per_pg;

    $sql_limit = "LIMIT  " . $start_limit . ", " . $res_per_pg;

    $sql = "
    SELECT
        MT.id, MT.task_name, DATE_FORMAT(MT.due_date, '%m/%d/%Y') AS due, MT.priority,
        IF(LENGTH(MT.description) > 0, MT.description,'-') AS 'desc',
        CASE MT.priority
            WHEN 0 THEN 'High'
            WHEN 1 THEN 'Medium'
            WHEN 2 THEN 'Low'
        END AS prio,
        (SELECT MP.name FROM m_project MP WHERE MP.id = MT.project_id AND MP.delete_flg = 0) AS proj_name,
        (SELECT MU.full_name FROM m_user MU WHERE MT.user_id = MU.id AND MU.delete_flg = 0) AS full_name
    FROM m_tasks MT
    WHERE MT.delete_flg = 0
    ORDER BY id DESC
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
            $res .= "<td> " . $r['task_name'] . "</td>";
            $res .= "<td> " . $r['desc'] . "</td>";
            $res .= "<td class='text-center'><span class='$span_color'> " . $r['prio'] . "</span></td>";
            $res .= "<td class='text-center'> " . $r['due'] . "</td>";
            $res .= "<td class='text-center'> " . $r['full_name'] . "</td>";
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
