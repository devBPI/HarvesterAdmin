function remplir_tableau(title_cell = null, order = null) {
    let emplacement = document.getElementById("emplacement_tableau");
    let table_head = document.getElementById("head_tableau");
    let list_header = [];
    var list = [];

    for (let child of table_head.children) {
        list_header.push(child.innerHTML);
    }
var j = 0;
    for (let child of emplacement.children) {
        let child_content = {};
        for (i=0; i < child.children.length; i++) {
            child_content[list_header[i]]=child.children[i].innerHTML;
        }
        list.push(child_content);
	j++;
    }
	console.log(j);

    $.ajax({
        type: "post",
        data: {ordre: order, champ: list_header[title_cell], report_list: list },
        success: function (response) {
            document.getElementById("emplacement_tableau").innerHTML = response;
        }
    });
}

function maj_col(nb) {
    let order = "desc";
    let cell = document.getElementById("th_cell_" + nb);
    let class_name = cell.className;
    if (class_name == "order_asc") cell.className = "order_desc";
    else {
        order = "asc";
        cell.className = "order_asc";
    }

    for (let i=0; i<nb_col; i++) {
        let th_label = document.getElementById("th_cell_"+i).innerHTML;
        if (th_label.charAt(th_label.length-1) == '▲' || th_label.charAt(th_label.length-1) == '▼') {
            document.getElementById("th_cell_"+i).innerHTML = th_label.substring(0, th_label.length-2);
        }
    }

    remplir_tableau(nb, order);
    if (order == "asc") cell.innerHTML += " ▲";
    else cell.innerHTML += " ▼";
}