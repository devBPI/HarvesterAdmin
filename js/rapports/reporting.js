var seuil = 5;

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
                url: "../Composant/RapportComposant.php",
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
                url: "../Composant/RapportComposant.php",
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
                url: "../Composant/RapportComposant.php",
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
                url: "../Composant/RapportComposant.php",
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
                url: "../Composant/RapportComposant.php",
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


function add_group(parent, profondeur=0) {
    add_group_(parent, profondeur);
    add_group_(parent, profondeur);
}

// Code à part pour refactoring futur (mettre en commun avec code de filter_rule.js)
function add_group_(parent, profondeur=0) {
    console.log("Dans add_group, profondeur vaut " + profondeur);
    var inserted_child = $("#operation_").clone();
    let nb = nb_groupes;
    if (nb < 10) { nb = "00" + nb; }
    else if (nb < 100) { nb = "0" + nb; }
    $(inserted_child).attr("id", $(inserted_child).attr("id") + nb);
    if ((profondeur+1) % 2 == 0)
        $(inserted_child).addClass("operation_even");
    // Ajout de l'indice pour chaque element que contient l'enfant
    for (let i=0; i < inserted_child.children().length; i++) {
        maj_id_and_name_group(inserted_child.children()[i], nb, profondeur);
    }
    // Ajout de l'enfant dans le grandparent
    let nb_parent = ($(parent).attr("id")).slice(-3);
    ($("#div_operation_sub_int_"+nb_parent)).append(inserted_child);
    $(inserted_child).show();
    // Désactivation de l'autre bouton / lien
    deactivation_add_critere(nb_parent);
    // Incrémentation du nombre d'enfants du parent
    $("#nb_children_operator_group_"+nb_parent).val(parseInt($("#nb_children_operator_group_"+nb_parent).val()) + 1);
    // Incrémentation de l'indice et du compteur
    nb_groupes++; cpt_groupes++;
}

// Rajoute une ligne de critères / données à afficher et incrémente les compteurs correspondants
function add_critere_or_donnee(parent, type) {
    if (type == "critere") {
        var inserted_child = $("#critere_rapport_").clone();
    } else if (type == "donnee") {
        var inserted_child = $("#donnee_affichee_").clone();
    }
    // Calcul du nouvel indice pour l'enfant a inserer
    let nb = 0;
    if (type == 'critere') { nb = nb_criteres; }
    else { nb = nb_donnees_affs; }
    if (nb < 10) { nb = "00" + nb; }
    else if (nb < 100) { nb = "0" + nb; }
    $(inserted_child).attr("id", $(inserted_child).attr("id") + nb);
    // Ajout de l'indice a chaque element que contient l'enfant
    for (let i=0; i < inserted_child.children().length; i++) {
        maj_id_and_name(inserted_child.children()[i], nb);
    }
    if (type == "critere") {
        // Ajout de l'enfant dans le grandparent
        var nb_parent = ($(parent).parent().parent()).attr("id").slice(-3);
        $("#div_operation_sub_int_" + nb_parent).append(inserted_child);
    } else {
        $(parent).append(inserted_child);
    }
    $(inserted_child).show();
    // Désactivation de l'autre bouton / lien
    if (type == "critere") {
        deactivation_add_group(nb_parent);
    }
    // Incrementation de l'indice et du compteur
    if (type == "critere") { nb_criteres++; cpt_criteres++; }
    else if (type == "donnee"){ nb_donnees_affs++; cpt_donnees_affs++; }
    disable_input();
}

function deactivation_add_critere(nb_parent) {
    $("#a_add_critere_"+nb_parent).attr("title", "Ce groupe n'accepte que des groupes de critères");
    $("#a_add_critere_"+nb_parent).addClass("a_disabled");
    $("#a_add_critere_"+nb_parent).removeAttr("onclick");
    $("#a_add_critere_" + nb_parent).off( "click");
}

function reactivation_add_critere(nb_parent) {
    $("#a_add_critere_"+nb_parent).removeAttr("title");
    $("#a_add_critere_"+nb_parent).removeClass("a_disabled");
    $("#a_add_critere_" + nb_parent).off( "click");
    $("#a_add_critere_" + nb_parent).on("click", function () {
        return add_critere_or_donnee(this.parentElement.parentElement, "critere");
    });
}

function deactivation_add_group(nb_parent) {
    $("#a_add_group_" + nb_parent).attr("title", "Ce groupe n'accepte que des critères simples");
    $("#a_add_group_" + nb_parent).addClass("a_disabled");
    $("#a_add_group_"+nb_parent).removeAttr("onclick");
    $("#a_add_group_" + nb_parent).off( "click");
}

function reactivation_add_group(nb_parent, profondeur) {
    $("#a_add_group_" + nb_parent).removeAttr("title");
    $("#a_add_group_" + nb_parent).removeClass("a_disabled");
    $("#a_add_group_" + nb_parent).off( "click");
    $("#a_add_group_" + nb_parent).on("click", function () {
        return add_group(this.parentElement, profondeur + 1);
    });
}

function maj_id_and_name_group(child_of_inserted_child, nb, profondeur) {
    if (child_of_inserted_child.className == "div_add_group") {
        if (profondeur < (seuil - 1))
            $(child_of_inserted_child).on("click", function () {
                return add_group(this.parentElement, profondeur + 1);
            });
        else {
            $(child_of_inserted_child).attr("title", "Seuil de profondeur de l'arbre atteint");
            $(child_of_inserted_child).addClass("a_disabled");
        }
    } else if (child_of_inserted_child.className.includes("delete")) {
        console.log("delete_group(this.parentElement, "+profondeur+")");
        $(child_of_inserted_child).on("click", function () {
            return delete_group(this.parentElement.parentElement.parentElement.parentElement, profondeur);
        });
    } else if (child_of_inserted_child.className.includes("prof_")) {
        $(child_of_inserted_child).removeClass("prof_");
        $(child_of_inserted_child).addClass("prof_"+profondeur);
    }
    maj_id_and_name(child_of_inserted_child, nb, profondeur, true);
}

function maj_id_and_name(child_of_inserted_child, nb, profondeur=0, is_group=false) {
    if ((child_of_inserted_child.type == "select-one" || child_of_inserted_child.type == "text")
        && !(child_of_inserted_child.id).includes("cb_valeur_cond"))
        $(child_of_inserted_child).attr("required", true);
    if ($(child_of_inserted_child).attr("id"))
        $(child_of_inserted_child).attr("id", $(child_of_inserted_child).attr("id") + nb);
    if ($(child_of_inserted_child).attr("name"))
        $(child_of_inserted_child).attr("name", $(child_of_inserted_child).attr("name") + nb);
    for (let i = 0; i < $(child_of_inserted_child).children().length; i++) {
       if (is_group)
            maj_id_and_name_group($(child_of_inserted_child).children()[i], nb, profondeur);
        else
            maj_id_and_name($(child_of_inserted_child).children()[i], nb);
    }
}

// Supprime une ligne de critères / données à afficher et décrémente les compteurs correspondants
function delete_critere_or_donnee(parent, type) {
    // Décrémentation des compteurs
    if (type == 'critere') cpt_criteres--;
    else cpt_donnees_affs--;
    // Réactivation de l'élément d'ajout des groupes si besoin
    if (is_group_empty($(parent).parent(), parent)) {
        let nb_grandparent = ($(parent).parent().attr("id")).slice(-3);
        let profondeur = parseInt($(parent).parent().attr("class").slice(-1));
        reactivation_add_group(nb_grandparent, profondeur);
    }
    // Suppression de l'élément
    parent.remove();
    disable_input();
}

// Supprime un groupe et son contenu
function delete_group(parent, profondeur_grandparent) {
    let nb_grandparent = ($(parent).parent().attr("id")).slice(-3);
    // Décrémentation du compteur
    cpt_criteres = cpt_criteres - count_criteres_in_parent($(parent));
    // Réactivation de l'élément d'ajout des critères si besoin
    if (is_group_empty($(parent).parent(), parent)) {
        reactivation_add_critere(nb_grandparent, profondeur_grandparent);
    }
    // Suppression de l'élément
    $("#nb_children_operator_group_"+nb_grandparent).val(parseInt($("#nb_children_operator_group_"+nb_grandparent).val()) - 1);
    console.log
    parent.remove();
    disable_input();
}

function is_group_empty(grandparent, parent) {
    for (let some_parent of $(grandparent).children()) {
        if (($(some_parent).hasClass("critere_rapport") || $(some_parent).hasClass("div_operation")) && some_parent != parent) {
            return 0;
        }
    }
    return 1;
}

function count_criteres_in_parent(parent) {
    let cpt_criteres_in_parent = 0;
    if (parent.hasClass("critere_rapport")) {
        return 1;
    } else if($(parent).children().length > 0) {
        for (let i = 0; i < $(parent).children().length; i++) {
            cpt_criteres_in_parent += count_criteres_in_parent($($(parent).children()[i]));
        }
        return cpt_criteres_in_parent;
    } else {
        return 0;
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