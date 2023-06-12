// -------------------------------------------------------------------------------------------------------------- OK

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
        .attr("type", "text").removeAttr("required").hide();
}

// -------------------------------------------------------------------------------------------------------------- OK

// Affiche la combobox de valeur selon la valeur de la combobox de champ
function display_related_operator(element) {
    let number = getNumber(element);
    reset_defaut_cb_operateur(number);
    reset_default_cb_valeur(number);

    if (element.value == "harvest_status" || element.value == "harvest_configuration_name") {
        $("#cb_operateur_cond_" + number).html('<option value="equals">&equals;</option> <option value="not_equals">&ne;</option>').show();
        if (element.value == "harvest_configuration_name") {
            // Afficher combobox des noms de configuration au lieu de input_valeur_cond
            // Récupérer grâce à ajax
            var request = $.ajax({
                url: "../Composant/HarvestTaskRapport.php",
                type: "post",
                data: "champs=id_name",
                success: function (response) {
                    $("#cb_valeur_cond_" + number).html('<option value="">Sélectionnez une valeur</option>' + response);
                }
            });
        } else if (element.value == "harvest_status") {
            // Afficher combobox des statuts possibles au lieu de input_valeur_cond
            // Récupérer grâce à ajax
            var request = $.ajax({
                url: "../Composant/HarvestTaskRapport.php",
                type: "post",
                data: "champs=status",
                success: function (response) {
                    $("#cb_valeur_cond_" + number).html('<option value="">Sélectionnez une valeur</option>' + response);
                }
            });
        }
        $("#cb_valeur_cond_" + number).attr("required", true).show();
    } else if (element.value == "harvest_last_task") {
        $("#cb_operateur_cond_" + number).html('<option value="equals">&equals;</option>').show();
        $("#cb_valeur_cond_" + number).html('<option value="Oui">Oui</option>').attr("required", true).show();
    } else if (element.value == "harvest_creation_date" || element.value == "harvest_modification_date" || element.value == "harvest_start_time" || element.value == "harvest_end_time") {
        $("#input_valeur_cond_" + number).attr("type", "datetime-local").attr("required", true).show();
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

// -------------------------------------------------------------------------------------------------------------- OK

// Rajoute une ligne de critères / données à afficher et incrémente les compteurs correspondants
function add_critere_or_donnee(parent, type) {
    let inserted_child = $(parent.children[1]).clone();
    // Calcul du nouvel indice pour l'enfant a inserer
    let nb = 0;
    if (type == 'critere') { nb = nb_criteres; }
    else { nb = nb_donnees_affs; }
    if (nb < 10) { nb = "00" + nb; }
    else if (nb < 100) { nb = "0" + nb; }
    $(inserted_child).attr("id", $(inserted_child).attr("id") + nb);
    // Ajout de l'indice a chaque element que contient l'enfant
    for (let i=0; i < inserted_child.children().length; i++) {
        if (inserted_child.children()[i].type == "select-one" || inserted_child.children()[i].type=="text")
            $(inserted_child.children()[i]).attr("required", true);
        $(inserted_child.children()[i]).attr("id", $(inserted_child.children()[i]).attr("id") + nb);
        $(inserted_child.children()[i]).attr("name", $(inserted_child.children()[i]).attr("name") + nb);
    }
    // Ajout de l'enfant dans le parent
    $(parent).append(inserted_child);
    $(inserted_child).show();
    // Incrementation de l'indice et du compteur
    if (type == 'critere') { nb_criteres++; cpt_criteres++; }
    else { nb_donnees_affs++; cpt_donnees_affs++; }
    disable_input();
}

// Supprime une ligne de critères / données à afficher et décrémente les compteurs correspondants
function delete_critere_or_donnee(parent, type) {
    if (type == 'critere') cpt_criteres--;
    else cpt_donnees_affs--;
    parent.remove();
    disable_input();
}

// Active ou desactive le bouton d'envoi du formulaire
function disable_input() {
    console.log(cpt_criteres, cpt_donnees_affs);
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