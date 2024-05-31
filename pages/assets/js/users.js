let col_field = "";
let sort_field = "";
let search_field = "";
let pg = 1;
let user_id = 0;

let is_change_pw = false;

$(() => {
    $(`#user`).css("background-color", "#8e8e8e");
    loadUsers(col_field, sort_field, search_field, pg);

    $('#chk_pass').on('change', function () {
        if ($(this).is(':checked')) {
            $("#chng_pw").removeClass("d-none");
            is_change_pw = true;
        } else {
            $("#chng_pw").removeClass("row").addClass("row d-none");
            is_change_pw = false;
        }
    });

    $(`#btn_search`).click(() => {
        search_field = $(`#txt_search`).val();
        loadUsers(col_field, sort_field, search_field, pg);
    })

    $(`#btn_save`).click(() => {
        let validated = validateInput(`createNew`);

        if (validated == true) {
            if ($(`#txt_conpass`).val() != $(`#txt_pass`).val()) {
                alert("Password do not match. Please try again.");
                $("#txt_pass").focus();
                return;
            }

            let form_data = new FormData();
            form_data.append("add_user", "add_user");
            form_data.append("utype", $(`#sl_utype`).val());
            form_data.append("uname", $(`#txt_user`).val());
            form_data.append("pass", $(`#txt_pass`).val());
            form_data.append("name", $(`#txt_name`).val());

            $.ajax({
                method: "post",
                url: "../actions/users.php",
                data: form_data,
                dataType: "text",
                contentType: false,
                processData: false,
                success: (res) => {
                    if (res == 0) {
                        $(`#btn_close`).click();
                        let showMsg = displayMsg(0, "User");
                        $(`#alert_msg`).removeClass(`d-none`);
                        $(`#show_msg`).empty().append(showMsg);


                        loadUsers(col_field, sort_field, search_field, pg);
                    } else {
                        alert(res)
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

            if (is_change_pw == true) {
                let check = document.getElementsByClassName("pw_req");
                for (let i = 0; i < check.length; i++) {
                    if (check[i].value === "" || !check[i].value.trim()) {
                        check[i].style.borderColor = "red";
                        check[i].focus();
                        return false;
                    } else {
                        check[i].style.borderColor = "";
                    }
                }

                let c_pw = $("#txt_enpass").val();
                let n_prw = $("#txt_econpass").val();
                if (c_pw != n_prw) {
                    alert("The new password and confirm password do not match.");
                    return false;
                }
            }

            let form_data = new FormData();
            form_data.append("update_user", "update_user");
            form_data.append("utype", $(`#sl_eutype`).val());
            form_data.append("uname", $(`#txt_euser`).val());
            form_data.append("name", $(`#txt_ename`).val());
            form_data.append("user_id", user_id);

            if (is_change_pw == true) {
                form_data.append("old_pw", $(`#txt_epass`).val());
                form_data.append("new_pw", $(`#txt_enpass`).val());
            }

            $.ajax({
                method: "post",
                url: "../actions/users.php",
                data: form_data,
                dataType: "text",
                contentType: false,
                processData: false,
                success: (res) => {
                    if (res == 0) {
                        $(`#btn_eclose`).click();
                        let showMsg = displayMsg(2, "User");
                        $(`#alert_msg`).removeClass(`d-none`);
                        $(`#show_msg`).empty().append(showMsg);

                        $('#chk_pass').prop('checked', false); 
                        $("#chng_pw").removeClass("row").addClass("row d-none");
                        is_change_pw = false;

                        let in_fields = document.querySelectorAll(".pw_req");
                        in_fields.forEach((input) => {
                            input.value = "";
                            input.style.borderColor = "";
                        });

                        loadUsers(col_field, sort_field, search_field, pg);
                    } else {
                        alert(res);
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
    loadUsers(col_field, sort_field, search_field, pg);
}

const loadUsers = (field, sort_type, search_field, page) => {
    $.ajax({
        method: "post",
        url: "../actions/users.php",
        data: {
            load_user: "load_user",
            field: field,
            sort_type: sort_type,
            search_field: search_field,
            page: page
        },
        success: (data) => {
            let res = JSON.parse(data)
            $(`#user_cont`).html(res.data);
            $(`#paginate_cont`).html(res.paginate)
        },
        error: (err) => {
            console.log("[Error] loadUsers()", err.responseText)
        }
    })
}

const openEdit = (id) => {
    user_id = id;
    $.ajax({
        method: "post",
        url: "../actions/users.php",
        data: {
            get_user: `get_user`,
            user_id: id
        },
        dataType: "json",
        success: (res) => {
            if (res != 0) {
                $(`#updateRec`).modal(`show`);
                $(`#edit_user`).empty().html(`Edit User <strong>[${res.uname}]</strong>`)
                //Append
                $(`#sl_eutype`).val(res.utype);
                $(`#txt_euser`).val(res.uname);
                $(`#txt_ename`).val(res.fullname);
            } else {
                console.log(res);
            }
        },
        error: (err) => {
            console.log("[Error] openEdit()", err)
        }
    })
}

const deleteUser = (id) => {
    let msg = confirm("Are you sure you want to delete this record?");
    if (msg) {
        $.ajax({
            method: "post",
            url: "../actions/users.php",
            data: {
                del_user: `del_user`,
                user_id: id
            },
            success: (res) => {
                if (res == 0) {
                    let showMsg = displayMsg(1, "User");
                    $(`#alert_msg`).removeClass(`d-none`);
                    $(`#show_msg`).empty().append(showMsg);
                    loadUsers(col_field, sort_field, search_field, pg);
                } else {
                    console.log(res);
                }
            },
            error: (err) => {
                console.log("[Error] deleteUser()", err)
            }
        })
    }
}
