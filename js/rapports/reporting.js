

function onSubmit() {
    let elts = document.querySelectorAll("input[type=hidden]");
    for (let elt of elts ) {
        if ((elt.name).includes("nb_children_operator_") && !(elt.name).includes("nb_children_operator_group_")
            && !(elt.name).includes("nb_children_operator_criteria_") && (elt.name) != "nb_children_operator_" && (elt.value) == "0") {
            alert("Erreur : Il existe un groupe vide.");
            return false;
        }
        if ((elt.name).includes("nb_children_operator_") && (elt.name).includes("nb_children_operator_group_")
            && (elt.value) == "1") {
            alert("Erreur : Il existe un groupe ne contenant qu'un seul groupe.");
            return false;
        }
    }
    return true;
}


// jQuery pour l'accessibilité des liens d'ajout (navigation au clavier)
$("a").on("keypress", function(elt) {
    if (elt.which == 32 || elt.which == 13) {
        $(this).trigger("click");
    }
} );

// jQuery pour déplacer les divs
$("#donnees_affichees").sortable({ tolerance: "pointer" });

// Réinitialise l'état de la combobox d'opération de comparaison
function reset_defaut_cb_operateur(number) {
    $("#cb_operateur_cond_" + number).html(
        '<option value="superior">></option><option value="less"><</option><option value="equals">&equals;</option><option value="not_equals">&ne;</option><option value="superior_equals">&ge;</option><option value="less_equals">&le;</option>'
    );
}

// Retourne le numéro de la section
function getNumber(element) {
    return $(element).attr("id").substr($(element).attr("id").length - 3);
}


// Réinitialise l'état de la combobox / de l'input de valeur
function reset_default_cb_valeur(number) {
    $("#cb_valeur_cond_" + number).html("").removeAttr("required").hide();
    $("#input_valeur_cond_" + number).text("").val("")
        .attr("pattern", "[0-9]*")
        .attr("placeholder", "Valeur de comparaison")
        .attr("type", "text").removeAttr("required").removeAttr("max").hide();
}


// Affiche la combobox de valeur selon la valeur de la combobox de champ
function display_related_operator(element) {
    let number = getNumber(element);
    reset_defaut_cb_operateur(number);
    reset_default_cb_valeur(number);
    if (element.value == "harvest_status" || (element.value).includes("configuration_name")
        || element.value == "notice_type" || (element.value).includes("grabber_type") || (element.value).includes("notice_search_base")) {
        $("#cb_operateur_cond_" + number).html('<option value="equals">&equals;</option> <option value="not_equals">&ne;</option>').show();
        if ((element.value).includes("configuration_name")) {
            // Afficher combobox des noms de configuration au lieu de input_valeur_cond
            // Récupérer grâce à ajax
            $.ajax({
                url: "../../Composant/RapportComposant.php",
                type: "post",
                data: {champs: "id_name"},
                success: function (response) {
                    $("#cb_valeur_cond_" + number).html('<option value="">Sélectionnez une valeur</option>' + response);
                }
            });
        } else if (element.value == "harvest_status") {
            // Afficher combobox des statuts possibles au lieu de input_valeur_cond
            // Récupérer grâce à ajax
            $.ajax({
                url: "../../Composant/RapportComposant.php",
                type: "post",
                data: {champs: "status"},
                success: function (response) {
                    $("#cb_valeur_cond_" + number).html('<option value="">Sélectionnez une valeur</option>' + response);
                }
            });
        } else if (element.value == "notice_type") {
            // Afficher combobox des types / genres de notices possibles au lieu de input_valeur_cond
            // Récupérer grâce à ajax
            $.ajax({
                url: "../../Composant/RapportComposant.php",
                type: "post",
                data: {champs: "resource_type"},
                success: function (response) {
                    $("#cb_valeur_cond_" + number).html('<option value="">Sélectionnez une valeur</option>' + response);
                }
            });
        } else if ((element.value).includes("grabber_type")) {
            // Afficher combobox des connecteurs possibles au lieu de input_valeur_cond
            // Récupérer grâce à ajax
            $.ajax({
                url: "../../Composant/RapportComposant.php",
                type: "post",
                data: {champs: "grabber_type"},
                success: function (response) {
                    $("#cb_valeur_cond_" + number).html('<option value="">Sélectionnez une valeur</option>' + response);
                }
            });
        } else if ((element.value).includes("notice_search_base")) {
            // Afficher combobox des bases de recherches possibles au lieu de input_valeur_cond
            // Récupérer grâce à ajax
            $.ajax({
                url: "../../Composant/RapportComposant.php",
                type: "post",
                data: {champs: "search_base"},
                success: function (response) {
                    $("#cb_valeur_cond_" + number).html('<option value="">Sélectionnez une valeur</option>' + response);
                }
            });
        }
        $("#cb_valeur_cond_" + number).attr("required", true).show();
    } else if (element.value == "harvest_last_task" || (element.value).includes("results_distinct")) {
        $("#cb_operateur_cond_" + number).html('<option value="equals">&equals;</option>').show();
        $("#cb_valeur_cond_" + number).html('<option value="Oui">Oui</option>').attr("required", true).show();
    } else if ((element.value).includes("date") || (element.value).includes("time")) {
        $("#input_valeur_cond_" + number).removeAttr("pattern").attr("type", "datetime-local").attr("max", "9999-12-31T23:59").attr("required", true).show();
    } else if (element.value == "harvest_differences_notices") {
        // Suite à mail du 30/05
        $("#input_valeur_cond_" + number).attr("placeholder", "Si pourcentage, ne pas oublier le %")
            .attr("pattern", "([0-9]*)(%*)")
            .attr("required", true)
            .show();
    } else {
        $("#input_valeur_cond_" + number).attr("pattern", "[0-9]*")
            .attr("required", true)
            .show();
    }
}

// Active ou desactive le bouton d'envoi du formulaire
function disable_input() {
    //console.log(cpt_criteres, cpt_donnees_affs);
    if (cpt_criteres > 0 && cpt_donnees_affs > 0) {
        $("#input_save").removeAttr("disabled");
        $("#input_save").removeClass("submit_disabled");
    } else {
        $("#input_save").attr("disabled", true);
        $("#input_save").addClass("submit_disabled");
    }
}

// Par defaut : la denomination = le label de l'element selectionne
function change_value_input(element) {
    $("#input_name_champ_aff_" + getNumber(element)).val(element.options[element.selectedIndex].innerHTML);
}