function remplir_tableau(title_cell = null, order = null) {
    let emplacement = document.getElementById("emplacement_tableau");
    // let table_head = document.getElementById("head_tableau");
    // let list_header = [];
    let list = [];
    let str = "";

    /*for (let child of table_head.children) {
        list_header.push(child.innerHTML);
    }*/

    for (let child of emplacement.children) {
        let child_content = [];
        for (let i=0; i < child.children.length; i++) {
            //child_content[list_header[i]]=child.children[i].innerHTML;
            child_content[i]=child.children[i].innerHTML;
        }
        list.push(child_content);
    }

    list.sort(function (a,b) {
        let regex = /[0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{2}:[0-9]{2}:[0-9]{2}(\.[0-9]{1,3})?/i;
        if (regex.test(a[title_cell])) {
            //console.log("date");
            let d1 = new Date(a[title_cell]);
            let d2 = new Date(b[title_cell]);
            if (order == "asc") return (d1 < d2) ? 1 : -1;
            else return (d1 < d2) ? -1 : 1;
        } else if (!Number.isNaN(Number.parseFloat(a[title_cell])) && !Number.isNaN(Number.parseFloat(b[title_cell]))) {
            //console.log("float/int");
            if (order == "asc") return (Number.parseFloat(b[title_cell]) < Number.parseFloat(a[title_cell])) ? 1 : -1;
            else return (Number.parseFloat(b[title_cell]) < Number.parseFloat(a[title_cell])) ? -1 : 1;
        } else {
            //console.log("string");
            if (order == "asc") return (b[title_cell].toLowerCase() < a[title_cell].toLowerCase()) ? 1 : -1;
            else return (b[title_cell].toLowerCase() < a[title_cell].toLowerCase()) ? -1 : 1;
        }
    });

    for (let i = 0; i < list.length; i++ ) {
        str = str+"<tr>";
        for (let j = 0; j < list[i].length; j++) {
            str += "<td>"+list[i][j]+"</td>";
        }
        str = str+"</tr>";
    }

    document.getElementById("emplacement_tableau").innerHTML = str;

    /*$.ajax({
        type: "post",
        data: {ordre: order, champ: list_header[title_cell], report_list: list },
        success: function (response) {
            document.getElementById("emplacement_tableau").innerHTML = response;
        }
    });*/
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
    cell.innerHTML += (order == "asc") ? " ▲" : " ▼";
}