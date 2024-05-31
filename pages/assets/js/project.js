let col_field = "";
let sort_field = "";
let search_field = "";
let proj_id = 0;
let pg = 1;
$(() => {
    $(`#project`).css("background-color", "#8e8e8e");

    loadTable(col_field, sort_field, search_field, pg);

    //#region Add
    $("#click_mainadd").click(function (e) {
        e.preventDefault();
        $("#fl_main:hidden").trigger("click.input");
    });

    $(document).on('change.input', '#fl_main', function () {
        if (this.files && this.files[0]) {
            let reader = new FileReader();
            reader.onload = (e) => {
                e.preventDefault();
                $(`#upload_proj`).attr(`src`, e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        }
    })
    //#endregion

    $(`#btn_save`).click(() => {
        let validated = validateInput(`createNew`);

        if (validated == true) {
            let form_data = new FormData();
            form_data.append("add_proj", "add_proj");
            form_data.append("proj_name", $(`#txt_proj`).val());
            form_data.append("desc", $(`#txt_desc`).val());
            form_data.append("due_date", $(`#txt_due`).val());
            form_data.append("status", $(`#sl_status`).val());

            let img_main = $("#upload_proj").attr("src");
            if (!img_main.includes("/pages/assets/")) form_data.append("img_proj", $("#upload_proj").attr("src"));

            $.ajax({
                method: "post",
                url: "../actions/project.php",
                data: form_data,
                dataType: "text",
                contentType: false,
                processData: false,
                success: (res) => {
                    if (res == 0) {
                        $(`#btn_close`).click();
                        let showMsg = displayMsg(0, "Project");
                        $(`#alert_msg`).removeClass(`d-none`);
                        $(`#show_msg`).empty().append(showMsg);

                        $(`#upload_proj`).attr("src", `/pages/assets/images/upload_image.png`); //Default
                        loadTable(col_field, sort_field, search_field, pg);
                    }
                },
                error: (err) => {
                    console.log("[Error] btn_save()", err.responseText)
                }
            })
        }
    })

    $(`#btn_search`).click(() => {
        search_field = $(`#txt_search`).val();
        loadTable(col_field, sort_field, search_field, pg);
    })

    $(document).on('click', '.page-link', function (e) {
        console.log('h')
        e.preventDefault();
        if (!$(this).parent().hasClass('disabled')) {
            pg = $(this).data('page');
            loadTable(col_field, sort_field, search_field, pg);
        }
    })

    //#region Edit
    $("#click_emainadd").click(function (e) {
        e.preventDefault();
        $("#fl_emain:hidden").trigger("click.input");
    });

    $(document).on('change.input', '#fl_emain', function () {
        if (this.files && this.files[0]) {
            let reader = new FileReader();
            reader.onload = (e) => {
                e.preventDefault();
                $(`#upload_eproj`).attr(`src`, e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        }
    })

    //#endregion

    $(`#btn_update`).click(() => {
        let validated = validateInput(`updateRec`);

        if (validated == true) {
            let form_data = new FormData();
            form_data.append("update_proj", "update_proj");
            form_data.append("proj_name", $(`#txt_eproj`).val());
            form_data.append("desc", $(`#txt_edesc`).val());
            form_data.append("due_date", $(`#txt_edue`).val());
            form_data.append("status", $(`#sl_estatus`).val());
            form_data.append("proj_id", proj_id);

            let img_main = $("#upload_eproj").attr("src");
            if (!img_main.includes("/pages/assets/") && !img_main.includes("/files/projects")) form_data.append("img_proj", $("#upload_eproj").attr("src"));

            $.ajax({
                method: "post",
                url: "../actions/project.php",
                data: form_data,
                dataType: "text",
                contentType: false,
                processData: false,
                success: (res) => {
                    if (res == 0) {
                        $(`#btn_eclose`).click();
                        let showMsg = displayMsg(2, "Project");
                        $(`#alert_msg`).removeClass(`d-none`);
                        $(`#show_msg`).empty().append(showMsg);

                        $(`#upload_eproj`).attr("src", `/pages/assets/images/upload_image.png`); //Default
                        loadTable(col_field, sort_field, search_field, pg);
                    }
                },
                error: (err) => {
                    console.log("[Error] btn_save()", err.responseText)
                }
            })
        }
    })
})

const sortField = (field, sort_type) => {
    col_field = field;
    sort_field = sort_type;
    loadTable(col_field, sort_field, search_field, pg);
}

const loadTable = (field, sort_type, search_field, page) => {
    $.ajax({
        method: "post",
        url: "../actions/project.php",
        data: {
            load_proj: "load_proj",
            field: field,
            sort_type: sort_type,
            search_field: search_field,
            page: page
        },
        success: (data) => {
            let res = JSON.parse(data)
            $(`#proj_cont`).html(res.data);
            $(`#paginate_cont`).html(res.paginate)
        },
        error: (err) => {
            console.log("[Error] loadTable()", err.responseText)
        }
    })
}

const deleteProj = (id) => {
    let msg = confirm("Are you sure you want to delete this record?");
    if (msg) {
        $.ajax({
            method: "post",
            url: "../actions/project.php",
            data: {
                del_proj: `del_proj`,
                proj_id: id
            },
            success: (res) => {
                if (res == 0) {
                    let showMsg = displayMsg(1, "Project");
                    $(`#alert_msg`).removeClass(`d-none`);
                    $(`#show_msg`).empty().append(showMsg);
                    loadTable(col_field, sort_field, search_field, pg);
                } else {
                    console.log(res);
                }
            },
            error: (err) => {
                console.log("[Error] deleteProj()", err)
            }
        })
    }
}

const openEdit = (id) => {
    proj_id = id;
    $.ajax({
        method: "post",
        url: "../actions/project.php",
        data: {
            get_proj: `get_proj`,
            proj_id: id
        },
        dataType: "json",
        success: (res) => {
            if (res != 0) {
                $(`#updateRec`).modal(`show`);
                $(`#edit_proj`).empty().html(`Edit Project <strong>[${res.name}]</strong>`)
                //Append
                $(`#txt_eproj`).val(res.name);
                $(`#txt_edesc`).val(res.desc);
                $(`#txt_edue`).val(res.due);
                $(`#sl_estatus`).val(res.stat);

                $(`#upload_eproj`).attr("src", `/pages/assets/images/upload_image.png`);
                if (res.img != 0) {
                    $(`#upload_eproj`).attr("src", res.img);
                }

            } else {
                console.log(res);
            }
        },
        error: (err) => {
            console.log("[Error] openEdit()", err)
        }
    })
}