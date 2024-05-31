<?php include 'header.php'; ?>
<section>
    <div class="row">
        <div class="col">
            <h4 class="text-dark">Dashboard</h4>
        </div>
        <div class="col text-right">
            <h4 class=" text-dark">
                <?php if (isset($_SESSION['login'])) { ?>
                    <p for="">Welcome, <?= $_SESSION['login']['full_name'] . "!" ?></p>
                <?php } ?>
            </h4>
        </div>
    </div>
    <hr class="border-bottom border-2 border-muted" />

    <div class="jumbotron">
        <div class="row">
            <div class="col-4">
                <div class="card bg-success py-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-7 text-left text-nowrap">
                                <p class="card-title">Total Projects</p>
                            </div>
                            <div class="col-sm-5 text-right">
                                <span id="total_proj" class="card-title ">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-4">
                <div class="card bg-warning py-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-7 text-left text-nowrap">
                                <p class="card-title">Total Tasks</p>
                            </div>
                            <div class="col-sm-5 text-right">
                                <span id="total_task"class="card-title ">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-4">
                <div class="card bg-info  py-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-7 text-left text-nowrap">
                                <p class="card-title">Total Users</p>
                            </div>
                            <div class="col-sm-5 text-right">
                                <span id="total_user" class="card-title ">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card px-2 mt-4">
            <h5 class="mt-4">My Tasks</h5>
            <table class="table table-bordered table-sm">
                <thead>
                    <th>Project Name</th>
                    <th>Task Name</th>
                    <th>Priority</th>
                    <th>Due Date</th>
                    <th>Assigned User</th>
                </thead>
                <tbody id="cont_tasks">

                </tbody>
            </table>
            <div id="paginate_cont"></div>
        </div>

    </div>
</section>

<script>
    $(() => {
        let page = 1;
        $(`#dashboard`).css("background-color", "#8e8e8e");
        loadTable(page);
    })

    const loadTable = (page) => {
        $.ajax({
            method: "post",
            url: "/actions/dashboard.php",
            data: {
                load_dashboard: "load_dashboard",
                page: page
            },
            success: (data) => {
                let res = JSON.parse(data)
                $(`#cont_tasks`).html(res.data);
                $(`#paginate_cont`).html(res.paginate)

                $(`#total_proj`).text(res.total_project[0].cnt)
                $(`#total_task`).text(res.total_tasks[0].cnt)
                $(`#total_user`).text(res.total_users[0].cnt)

            },
            error: (err) => {
                console.log("[Error] loadTasks()", err.responseText)
            }
        })
    }
</script>
<?php include 'footer.php'; ?>