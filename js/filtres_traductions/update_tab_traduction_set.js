function update_tab_cibles(elt, id) {
    let id_category = elt.options[elt.selectedIndex].value;
    $.ajax({
        url: "../../Controlleur/TabTraductionSet.php",
        type: "get",
        data: 'id=' + id + "&id_category=" + id_category,
        success: function (response) {
            document.getElementById("interieur_tableau").innerHTML = response;
        }
    });
}