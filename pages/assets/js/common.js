const hamBurger = document.querySelector(".toggle-btn");
hamBurger.addEventListener("click", () => {
    document.querySelector("#sidebar").classList.toggle("expand");
});

const validateInput = (active_field) => {
    let cur_form = document.getElementById(active_field);
    let infield = cur_form.getElementsByClassName("req");
    for (let i = 0; i < infield.length; i++) {
        if (infield[i].value === "" || !infield[i].value.trim() || infield[i].value === "-1") {
            infield[i].style.borderColor = "red";
            infield[i].focus();
            return false;
        } else {
            infield[i].style.borderColor = "";
        }
    }
    return true
}

const resetInput = () => {
    let in_fields = document.querySelectorAll(['input[type="text"]', 'select', 'input[type="date"]', 'textarea']);
    in_fields.forEach((input) => {
        input.value = "";
        input.style.borderColor = "";

        //For select tag
        if (input.type.includes(`select`)) input.value = "-1";
    });
    $(`#upload_proj`).attr("src", `/pages/assets/images/upload_image.png`); //Default
}

const displayMsg = (type, module_name) => {
    let msg = "";
    switch (type) {
        case 0:
            msg = `<strong>Success!</strong> New ${module_name} was added. ` +
                `<button type="button" class="close" data-dismiss="alert" aria-label="Close">` +
                `	<span aria-hidden="true">&times;</span> ` +
                `</button>`
            break;
        case 1:
            msg = `<strong>Success!</strong> ${module_name} was deleted. ` +
                `<button type="button" class="close" data-dismiss="alert" aria-label="Close">` +
                `	<span aria-hidden="true">&times;</span> ` +
                `</button>`
            break;
        case 2:
            msg = `<strong>Success!</strong> ${module_name} was updated. ` +
                `<button type="button" class="close" data-dismiss="alert" aria-label="Close">` +
                `	<span aria-hidden="true">&times;</span> ` +
                `</button>`
            break;
    }

    return msg;
}

const tablePaginate = () => {
    return (
        `<nav aria-label="Page navigation example">
       <ul class="pagination">
           <li class="page-item">
               <a class="page-link" href="#" aria-label="Previous">
                   <span aria-hidden="true">&laquo;</span>
                   <span class="sr-only">Previous</span>
               </a>
           </li>
           <li class="page-item"><a class="page-link" href="#">1</a></li>
           <li class="page-item"><a class="page-link" href="#">2</a></li>
           <li class="page-item"><a class="page-link" href="#">3</a></li>
           <li class="page-item">
               <a class="page-link" href="#" aria-label="Next">
                   <span aria-hidden="true">&raquo;</span>
                   <span class="sr-only">Next</span>
               </a>
           </li>
       </ul>
     </nav>`
    )
}