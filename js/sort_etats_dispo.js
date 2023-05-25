
function remplir_tableau(title_cell = null, order = null) {
    if (title_cell != null && order != null) {
        $.ajax({
            url: "../../Controlleur/TabEtatsDispo.php",
            type: "post",
            data: 'ordre=' + order + '&champ=' + title_cell,
        success: function (response) {
            document.getElementById("emplacement_tableau").innerHTML = response;
        }
    });
    } else {
        $.ajax({
            url: "../../Controlleur/TabEtatsDispo.php",
            type: "post",
        success: function (response) {
            document.getElementById("emplacement_tableau").innerHTML = response;
        }
    });
    }
}

window.onload = function () {
    remplir_tableau();
}

function maj_col(title_cell) {
    let order = "desc";
    let cell = document.getElementById("th_cell_" + title_cell);
    let class_name = cell.className;
    if (class_name == "order_asc") cell.className = "order_desc";
    else {
        order = "asc";
        cell.className = "order_asc";
    }

    document.getElementById("th_cell_to_harvest").innerHTML = "À moissonner";
    document.getElementById("th_cell_dispo").innerHTML = "Disponibilité";
    document.getElementById("th_cell_label").innerHTML = "Label";
    document.getElementById("th_cell_code").innerHTML = "Code";

    if (order == "asc") cell.innerHTML += " ▲";
    else cell.innerHTML += " ▼";
    remplir_tableau(title_cell, order);
}