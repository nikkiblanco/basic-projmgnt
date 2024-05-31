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

if (isset($_POST['load_dashboard'])) {

    $page = $_POST['page'];
    $res_per_pg = 5;
    $sql_cnt = $db->exec_query("SELECT COUNT(id) AS total FROM m_tasks WHERE delete_flg = 0 AND user_id = $user");
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
    AND MT.user_id = $user";

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
            $res .= "<td> " . $r['proj_name'] . "</td>";
            $res .= "<td> " . $r['task_name'] . "</td>";
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
        "total_project" => $db->exec_query("SELECT COUNT(id) AS cnt FROM m_project WHERE delete_flg = 0"),
        "total_tasks" => $db->exec_query("SELECT COUNT(id) AS cnt FROM m_tasks WHERE delete_flg = 0"),
        "total_users" => $db->exec_query("SELECT COUNT(id) AS cnt FROM m_user WHERE delete_flg = 0"),
    ));
}
