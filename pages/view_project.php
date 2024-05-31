<?php include 'header.php'; ?>

<section>
    <h4 class="py-2 text-dark">View Project</h4>
    <hr class="border-bottom border-2 border-muted" />

    <?php if (isset($_REQUEST['id'])) {
        $id = $_REQUEST['id'];
    ?>
        <input id="task_id" type="hidden" value="<?= $id ?>" />
    <?php } else {
        header('Location: /pages/dashboard.php');
    } ?>

    <div class="jumbotron">
        <?php
        $sql = $db->exec_query("
            SELECT name, description, DATE_FORMAT(due_date, '%m/%d/%Y') AS due,
            CASE status
                WHEN 0 THEN 'Todo'
                WHEN 1 THEN 'In-progress'
                WHEN 2 THEN 'Completed'
            END AS stat,
            status,
            image_path
            FROM m_project
            WHERE delete_flg = 0");

        if ($sql == NULL) header('Location: /pages/project.php');
        $img = "/pages/assets/images/upload_image.png";
        if ($sql[0]['image_path'] != "") $img = $sql[0]['image_path'];

        $span_color = "";
        switch ($sql[0]['status']) {
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
        ?>
        <div class="row">
            <div class="col-sm-8 text-center py-2">
                <div class="row mt-2 mb-2">
                    <div class="col-6 text-left">
                        <div class="form-group">
                            <label for=""><strong>Project Name</strong></label>
                            <p><?= $sql[0]['name'] ?></p>
                        </div>
                    </div>
                    <div class="col-6 ">
                        <div class="form-group">
                            <label for=""><strong>Due date:</strong></label>
                            <p><?= $sql[0]['due'] ?></p>
                        </div>
                    </div>

                </div>
                <div class="row mt-2 mb-2">
                    <div class="col-6 text-left">
                        <div class="form-group">
                            <label for=""><strong>Description</strong></label>
                            <p><?= $sql[0]['description'] ?></p>
                        </div>
                    </div>
                    <div class="col-6 ">
                        <div class="form-group">
                            <label for=""><strong>Status</strong></label>
                            <p>
                                <span class="<?= $span_color ?>">
                                    <?= $sql[0]['stat'] ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3 text-center border border-secondary p-2 ">
                <img src="<?= str_replace("..", "", $img) ?>" style="width:150px; height: 150px" />
            </div>
        </div>

        <hr>
        <h4>Tasks</h4>

        <table class="table table-bordered table-hover mt-2 table-sm">
            <thead class="bg-dark text-white">
                <tr>
                    <th>ID</th>
                    <th>Task Name</th>
                    <th>Description</th>
                    <th>Priority</th>
                    <th>Due date</th>
                    <th>Assigned User</th>
                </tr>
            </thead>
            <tbody id="cont_view" class="bg-white">

            </tbody>
        </table>
        <div id="paginate_cont"></div>
    </div>


</section>
<?php include 'footer.php'; ?>
<script src="/pages/assets/js/view_project.js"></script>