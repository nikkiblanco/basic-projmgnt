let col_field = "";
let sort_field = "";
let search_field = "";
let pg = 1;
let task_id = 0;

$(() => {
    $(`#task`).css("background-color", "#8e8e8e");

    loadTasks(col_field, sort_field, search_field, pg);

    $(`#btn_search`).click(() => {
        search_field = $(`#txt_search`).val();
        loadTasks(col_field, sort_field, search_field, pg);
    })

    $(`#btn_save`).click(() => {
        let validated = validateInput(`createNew`);

        if (validated == true) {
            let form_data = new FormData();
            form_data.append("add_task", "add_task");
            form_data.append("proj", $(`#sl_proj`).val());
            form_data.append("task", $(`#txt_task`).val());
            form_data.append("desc", $(`#txt_desc`).val());
            form_data.append("due_date", $(`#txt_due`).val());
            form_data.append("assign_user", $(`#sl_assigned`).val());
            form_data.append("prio", $(`#sl_prio`).val());

            $.ajax({
                method: "post",
                url: "../actions/task.php",
                data: form_data,
                dataType: "text",
                contentType: false,
                processData: false,
                success: (res) => {
                    if (res == 0) {
                        $(`#btn_close`).click();
                        let showMsg = displayMsg(0, "Task");
                        $(`#alert_msg`).removeClass(`d-none`);
                        $(`#show_msg`).empty().append(showMsg);

                        loadTasks(col_field, sort_field, search_field, pg);
                    }
                },
                error: (err) => {
                    console.log("[Error] btn_save()", err.responseText)
                }
            })
        }
    })

    $(`#btn_update`).click(() => {
        let validated = validateInput(`updateRec`);

        if (validated == true) {
            let form_data = new FormData();
            form_data.append("update_task", "update_task");
            form_data.append("proj", $(`#sl_eproj`).val());
            form_data.append("task", $(`#txt_etask`).val());
            form_data.append("desc", $(`#txt_edesc`).val());
            form_data.append("due_date", $(`#txt_edue`).val());
            form_data.append("prio", $(`#sl_eprio`).val());
            form_data.append("assign_user", $(`#sl_eassigned`).val());
            form_data.append("task_id", task_id);

            $.ajax({
                method: "post",
                url: "../actions/task.php",
                data: form_data,
                dataType: "text",
                contentType: false,
                processData: false,
                success: (res) => {
                    if (res == 0) {
                        $(`#btn_eclose`).click();
                        let showMsg = displayMsg(2, "Task");
                        $(`#alert_msg`).removeClass(`d-none`);
                        $(`#show_msg`).empty().append(showMsg);

                        loadTasks(col_field, sort_field, search_field, pg);
                    }
                },
                error: (err) => {
                    console.log("[Error] btn_update()", err.responseText)
                }
            })
        }
    })
})

const sortField = (field, sort_type) => {
    col_field = field;
    sort_field = sort_type;
    loadTasks(col_field, sort_field, search_field, pg);
}

const loadTasks = (field, sort_type, search_field, page) => {
    $.ajax({
        method: "post",
        url: "../actions/task.php",
        data: {
            load_task: "load_task",
            field: field,
            sort_type: sort_type,
            search_field: search_field,
            page: page
        },
        success: (data) => {
            let res = JSON.parse(data)
            $(`#task_cont`).html(res.data);
            $(`#paginate_cont`).html(res.paginate)
        },
        error: (err) => {
            console.log("[Error] loadTasks()", err.responseText)
        }
    })
}

const openEdit = (id) => {
    task_id = id;
    $.ajax({
        method: "post",
        url: "../actions/task.php",
        data: {
            get_task: `get_task`,
            task_id: id
        },
        dataType: "json",
        success: (res) => {
            if (res != 0) {
                $(`#updateRec`).modal(`show`);
                $(`#edit_task`).empty().html(`Edit Task <strong>[${res.name}]</strong>`)
                //Append
                $(`#sl_eproj`).val(res.proj_id);
                $(`#txt_etask`).val(res.name);
                $(`#txt_edesc`).val(res.desc);
                $(`#txt_edue`).val(res.due);
                $(`#sl_eprio`).val(res.prio);
                $(`#sl_eassigned`).val(res.user_id);
            } else {
                console.log(res);
            }
        },
        error: (err) => {
            console.log("[Error] openEdit()", err)
        }
    })
}

const deleteTask = (id) => {
    let msg = confirm("Are you sure you want to delete this record?");
    if (msg) {
        $.ajax({
            method: "post",
            url: "../actions/task.php",
            data: {
                del_task: `del_task`,
                task_id: id
            },
            success: (res) => {
                if (res == 0) {
                    let showMsg = displayMsg(1, "Task");
                    $(`#alert_msg`).removeClass(`d-none`);
                    $(`#show_msg`).empty().append(showMsg);
                    loadTasks(col_field, sort_field, search_field, pg);
                } else {
                    console.log(res);
                }
            },
            error: (err) => {
                console.log("[Error] deleteTask()", err)
            }
        })
    }
}

