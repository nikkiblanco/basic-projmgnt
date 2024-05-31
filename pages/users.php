<?php include 'header.php'; ?>
<?php if ($_SESSION['login']['user_type'] != 0) header("Location: /pages/dashboard.php"); ?>

<section>
    <h4 class="py-2 text-dark">User Management</h4>
    <hr class="border-bottom border-2 border-muted" />

    <div class="container-fluid mt-4">
        <div class="row no-gutters mb-2">
            <div class="col mr-1">
                <input type="text" id="txt_search" class="form form-control form-control-sm" placeholder="Search Username">
            </div>
            <div class="col">
                <button class="btn btn-dark btn-sm" id="btn_search">
                    <i class="fa fa-search text-white" aria-hidden="true"></i>
                </button>
            </div>
            <div class="col text-right">
                <button class="btn btn-dark text-white btn-sm" data-toggle="modal" data-target="#createNew">Add New User</button>
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
                    <th>ID <div class="float-right text-white sort"> <i class="fas fa-angle-down" onClick="return sortField(1, 'desc')"></i><i class="fas fa-angle-up" onClick="return sortField(1, 'asc')"></i></div>
                    </th>
                    <th>User Name <div class="float-right text-white sort"> <i class="fas fa-angle-down" onClick="return sortField(2, 'desc')"></i><i class="fas fa-angle-up" onClick="return sortField(2, 'asc')"></i></div>
                    </th>
                    <th>Full Name <div class="float-right text-white sort"> <i class="fas fa-angle-down" onClick="return sortField(3, 'desc')"></i><i class="fas fa-angle-up" onClick="return sortField(3, 'asc')"></i></div>
                    </th>
                    <th>User Type <div class="float-right text-white sort"> <i class="fas fa-angle-down" onClick="return sortField(4, 'desc')"></i><i class="fas fa-angle-up" onClick="return sortField(4, 'asc')"></i></div>
                    </th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="user_cont" class="bg-white">

            </tbody>
        </table>
        <div id="paginate_cont"></div>

        <div class="modal fade" id="createNew" tabindex="-1" role="dialog" aria-labelledby="createNewLbl" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New User</h5>
                        <button id="btn_close" type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="return resetInput()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Full Name</label>
                                        <input type="text" name="" id="txt_name" class="form form-control req" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Username</label>
                                        <input type="text" name="" id="txt_user" class="form form-control req" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Password</label>
                                        <input type="password" name="" id="txt_pass" class="form form-control req" />
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Confirm Password</label>
                                        <input type="password" name="" id="txt_conpass" class="form form-control req" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>User Type</label>
                                        <select class="req custom-select" id="sl_utype">
                                            <option value="-1">Please select</option>
                                            <option value="0">Admin</option>
                                            <option value="1">User</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" onClick="return resetInput()">Close</button>
                        <button id="btn_save" type="button" class="btn btn-primary">Add</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update -->
        <div class="modal fade" id="updateRec" tabindex="-1" role="dialog" aria-labelledby="updateRecLbl" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 id="edit_user" class="modal-title">Edit User</h5>
                        <button id="btn_eclose" type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="return resetInput()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Full Name</label>
                                        <input type="text" name="" id="txt_ename" class="form form-control req" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Username</label>
                                        <input type="text" name="" id="txt_euser" class="form form-control req" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>User Type</label>
                                        <select class="req custom-select" id="sl_eutype">
                                            <option value="-1">Please select</option>
                                            <option value="0">Admin</option>
                                            <option value="1">User</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <input id="chk_pass" type="checkbox" class="mr-2">
                                        <label for="chk_pass">Change Password?</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row d-none" id="chng_pw">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Current Password</label>
                                        <input type="password" name="" id="txt_epass" class="form form-control pw_req" />
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>New Password</label>
                                        <input type="password" name="" id="txt_enpass" class="form form-control pw_req" />
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Confirm Password</label>
                                        <input type="password" name="" id="txt_econpass" class="form form-control pw_req" />
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
<script src="/pages/assets/js/users.js"></script>