<?php include 'header.php'; ?>

<section>
    <h4 class="py-2 text-dark">Task Management</h4>
    <hr class="border-bottom border-2 border-muted" />

    <div class="container-fluid mt-4">
        <div class="row no-gutters mb-2">
            <div class="col mr-1">
                <input type="text" id="txt_search" class="form form-control form-control-sm" placeholder="Search Task Name">
            </div>
            <div class="col">
                <button class="btn btn-dark btn-sm" id="btn_search">
                    <i class="fa fa-search text-white" aria-hidden="true"></i>
                </button>
            </div>
            <div class="col text-right">
                <button class="btn btn-dark text-white btn-sm" data-toggle="modal" data-target="#createNew">Create New</button>
            </div>
        </div>

        <div class="d-none" id="alert_msg">
            <div id="show_msg" class="alert alert-success alert-dismissible fade show my-2" role="alert">
                <strong>Holy guacamole!</strong> You should check in on some of those fields below.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>

        <table class="table table-bordered table-hover">
            <thead class="bg-dark text-white">
                <tr>
                    <th style="width: 6%;">ID <div class="float-right text-white sort"> <i class="fas fa-angle-down" onClick="return sortField(1, 'desc')"></i><i class="fas fa-angle-up" onClick="return sortField(1, 'asc')"></i></div>
                    </th>
                    <th>Project Name <div class="float-right text-white sort"> <i class="fas fa-angle-down" onClick="return sortField(2, 'desc')"></i><i class="fas fa-angle-up" onClick="return sortField(2, 'asc')"></i></div>
                    </th>
                    <th>Task Name <div class="float-right text-white sort"> <i class="fas fa-angle-down" onClick="return sortField(3, 'desc')"></i><i class="fas fa-angle-up" onClick="return sortField(3, 'asc')"></i></div>
                    </th>
                    <th style="width: 10%;">Due Date <div class="float-right text-white sort"> <i class="fas fa-angle-down" onClick="return sortField(4, 'desc')"></i><i class="fas fa-angle-up" onClick="return sortField(4, 'asc')"></i></div>
                    </th>
                    <th style="width: 9%;">Priority <div class="float-right text-white sort"> <i class="fas fa-angle-down" onClick="return sortField(5, 'desc')"></i><i class="fas fa-angle-up" onClick="return sortField(5, 'asc')"></i></div>
                    </th>
                    <th style="width: 13%;">Assigned User <div class="float-right text-white sort"> <i class="fas fa-angle-down" onClick="return sortField(6, 'desc')"></i><i class="fas fa-angle-up" onClick="return sortField(6, 'asc')"></i></div>
                    </th>
                    <th class="text-center" style="width: 7%;">Actions</th>
                </tr>
            </thead>
            <tbody id="task_cont" class="bg-white">
            </tbody>
        </table>
        <div id="paginate_cont"></div>

        <!-- Create New -->
        <?php
        require_once __DIR__ . '/../conn/db_manage.php';
        //Open connection
        $db = new db_manage();
        if ($db == null) echo $db->error;
        ?>

        <div class="modal fade" id="createNew" tabindex="-1" role="dialog" aria-labelledby="createNewLbl" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Task</h5>
                        <button id="btn_close" type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="return resetInput()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post">

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Project Name</label>
                                        <?php
                                        $sql = $db->exec_query("SELECT id, name FROM m_project WHERE delete_flg = 0 ORDER BY name ASC");
                                        ?>
                                        <div class="form-group">
                                            <select class="req custom-select" id="sl_proj">
                                                <?php if ($sql != "") { ?>
                                                    <option value="-1">Please select</option>
                                                    <?php foreach ($sql as $r) : ?>
                                                        <option value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
                                                    <?php endforeach; ?>
                                                <?php } else { ?>
                                                    <option value="-1">NO RECORD FOUND. PLEASE ADD PROJECTS TO CONTINUE</option>
                                                <?php   }
                                                ?>
                                            </select>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Task Name</label>
                                        <input type="text" name="task" id="txt_task" class="form form-control req" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="txt_desc">Description</label>
                                        <textarea id="txt_desc" class="form-control" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Assign User</label>
                                        <?php
                                        $sql = $db->exec_query("SELECT id, full_name FROM m_user WHERE delete_flg = 0 ORDER BY full_name ASC");
                                        ?>
                                        <div class="form-group">
                                            <select class="req custom-select" id="sl_assigned">
                                                <?php if ($sql != "") { ?>
                                                    <option value="-1">Please assign user</option>
                                                    <?php foreach ($sql as $r) : ?>
                                                        <option value="<?= $r['id'] ?>"><?= $r['full_name'] ?></option>
                                                    <?php endforeach; ?>
                                                <?php } else { ?>
                                                    <option value="-1">NO RECORD FOUND. PLEASE ADD PROJECTS TO CONTINUE</option>
                                                <?php   }
                                                ?>
                                            </select>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Due Date</label>
                                        <input type="date" name="due" id="txt_due" class="form form-control req" />
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label>Priority</label>
                                    <div class="form-group">
                                        <select class="req custom-select" id="sl_prio">
                                            <option value="-1">Please select</option>
                                            <option value="0">High</option>
                                            <option value="1">Medium</option>
                                            <option value="2">Low</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" onClick="return resetInput()">Close</button>
                        <button id="btn_save" type="button" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update -->
        <div class="modal fade" id="updateRec" tabindex="-1" role="dialog" aria-labelledby="updateRecLbl" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 id="edit_task" class="modal-title">Edit Task</h5>
                        <button id="btn_eclose" type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="return resetInput()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Project Name</label>
                                        <?php
                                        $sql = $db->exec_query("SELECT id, name FROM m_project WHERE delete_flg = 0 ORDER BY name ASC");
                                        ?>
                                        <div class="form-group">
                                            <select class="req custom-select" id="sl_eproj">
                                                <?php if ($sql != "") { ?>
                                                    <option value="-1">Please select</option>
                                                    <?php foreach ($sql as $r) : ?>
                                                        <option value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
                                                    <?php endforeach; ?>
                                                <?php } else { ?>
                                                    <option value="-1">NO RECORD FOUND. PLEASE ADD PROJECTS TO CONTINUE</option>
                                                <?php   }
                                                ?>
                                            </select>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Task Name</label>
                                        <input type="text" name="etask" id="txt_etask" class="form form-control req" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="txt_edesc">Description</label>
                                        <textarea id="txt_edesc" class="form-control" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Assign User</label>
                                        <?php
                                        $sql = $db->exec_query("SELECT id, full_name FROM m_user WHERE delete_flg = 0 ORDER BY full_name ASC");
                                        ?>
                                        <div class="form-group">
                                            <select class="req custom-select" id="sl_eassigned">
                                                <?php if ($sql != "") { ?>
                                                    <option value="-1">Please assign user</option>
                                                    <?php foreach ($sql as $r) : ?>
                                                        <option value="<?= $r['id'] ?>"><?= $r['full_name'] ?></option>
                                                    <?php endforeach; ?>
                                                <?php } else { ?>
                                                    <option value="-1">NO RECORD FOUND. PLEASE ADD PROJECTS TO CONTINUE</option>
                                                <?php   }
                                                ?>
                                            </select>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Due Date</label>
                                        <input type="date" name="due" id="txt_edue" class="form form-control req" />
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label>Priority</label>
                                    <div class="form-group">
                                        <select class="req custom-select" id="sl_eprio">
                                            <option value="-1">Please select</option>
                                            <option value="0">High</option>
                                            <option value="1">Medium</option>
                                            <option value="2">Low</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" onClick="return resetInput()">Close</button>
                        <button id="btn_update" type="button" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
<?php include 'footer.php'; ?>
<script src="/pages/assets/js/task.js"></script>