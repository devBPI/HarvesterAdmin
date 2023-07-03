function change_label_text(element, indice) {
    if (element.checked)
        document.getElementById("label_"+indice).innerHTML = "Activée";
    else
        document.getElementById("label_"+indice).innerHTML = "Désactivée";
}
$("input[type=checkbox]").on( "focus", function() {
    let nb =  $(this).attr("id").substring("input_is_enabled_".length,$(this).attr("id").length );
    $("#slider_"+nb).css("transition", "0s");
    $("#slider_"+nb).css("border", "2px double black");
});
$("input[type=checkbox]").on( "focusout", function() {
    $(".slider").css("border", "none");
});