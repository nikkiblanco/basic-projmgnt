let pg = 1;
$(() => {
    $(`#project`).css("background-color", "#8e8e8e");
    loadTable($("#task_id").val(), pg)
})

const loadTable = (id, page) => {
    console.log(id)
    $.ajax({
        method: "post",
        url: "/actions/project.php",
        data: {
            load_tasks: "load_tasks",
            page: page,
            task_id: id
        },
        cache:false,
        success: (data) => {
            let res = JSON.parse(data)
            $(`#cont_view`).html(res.data);
            $(`#paginate_cont`).html(res.paginate)
        },
        error: (err) => {
            console.log("[Error] loadTable()", err.responseText)
        }
    })
}