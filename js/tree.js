var seuil = 5;
var page_type = "filter";

function onSubmit() {
    let elts = document.querySelectorAll("input[type=hidden]");
    for (let elt of elts ) {
        if ((elt.name).includes("nb_children_operator_") && !(elt.name).includes("nb_children_operator_group_")
            && !(elt.name).includes("nb_children_operator_criteria_") && (elt.name) != "nb_children_operator_" && (elt.value) == "0") {
            alert("Erreur : Il existe un groupe de critères vide.");
            return false;
        }
        if ((elt.name).includes("nb_children_operator_") && (elt.name).includes("nb_children_operator_group_")
            && (elt.value) == "1") {
            alert("Erreur : Il existe un groupe ne contenant qu'un seul groupe de critères.");
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


// ----------------------- UTILITAIRES ------------------------
// Retourne le numéro de la section
function getNumber(element) {
    return $(element).attr("id").slice(-3);
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

function count_children_in_parent(parent) {
    let cpt_children_in_parent = 0;
    if ($(parent).hasClass("div_predicate") || $(parent).hasClass("div_operation")) {
        return 1;
    } else if ($(parent).children().length > 0) {
        for (let i = 0; i < $(parent).children().length; i++) {
            cpt_children_in_parent += count_children_in_parent($($(parent).children()[i]));
        }
        return cpt_children_in_parent;
    } else {
        return 0;
    }
}

function is_group_empty(grandparent, parent) {
    for (let some_parent of $(grandparent).children()) {
        if (($(some_parent).hasClass("critere_rapport") || $(some_parent).hasClass("div_operation")) && some_parent != parent) {
            return 0;
        }
    }
    return 1;
}

// ----------------------- FIN UTILITAIRES ------------------------

// ----------------------- (DES)ACTIVATION DES LIENS ------
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
    if (page_type == "filter") {
        $("#a_add_critere_" + nb_parent).on("click", function () {
            return add_critere_or_donnee(this.parentElement.parentElement, "filter");
        });
    } else {
        $("#a_add_critere_" + nb_parent).on("click", function () {
            return add_critere_or_donnee(this.parentElement.parentElement, "critere");
        });
    }
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
// ----------------------- FIN (DES)ACTIVATION DES LIENS ------


function add_group(parent, profondeur=0) {
    let nb_children_parent = $("#nb_children_operator_"+($(parent).attr("id")).slice(-3)).val();
    // Ajout de deux groupes enfants si le parent est vide
    if (page_type == "filter" && nb_children_parent >= 2) {
        alert("Nombre de sous-groupes maximal (2) atteint");
    } else if (nb_children_parent == 0) {
        add_group_(parent, profondeur);
        add_group_(parent, profondeur);
    } else {
        add_group_(parent, profondeur);
    }
}

// Code à part pour refactoring futur (mettre en commun avec code de filter_rule.js)
function add_group_(parent, profondeur=0) {
    // console.log("Dans add_group, profondeur vaut " + profondeur);
    var inserted_child = $("#operation_").clone();
    let nb = nb_groupes;
    if (nb < 10) { nb = "00" + nb; }
    else if (nb < 100) { nb = "0" + nb; }
    $(inserted_child).attr("id", $(inserted_child).attr("id") + nb);
    // Ajout de l'indice pour chaque element que contient l'enfant
    for (let i=0; i < inserted_child.children().length; i++) {
        maj_id_and_name_group(inserted_child.children()[i], nb, profondeur);
    }
    if ((profondeur+1) % 2 == 0) {
        $(inserted_child).find(".div_operation_int_int").addClass("operation_even");
    }
    // Ajout de l'enfant dans le grandparent
    let nb_parent = getNumber(parent);
    ($("#div_operation_sub_int_"+nb_parent)).append(inserted_child);
    $(inserted_child).show();
    // Désactivation de l'autre bouton / lien
    if (page_type != "filter") {
        deactivation_add_critere(nb_parent);
    } else if ($("#nb_children_operator_"+getNumber($(parent))).val() == 1 || nb_parent == "-01") {
        deactivation_add_critere(nb_parent);
        deactivation_add_group(nb_parent);
    }
    // Incrémentation du nombre d'enfants du parent
    if (page_type == "filter") {
        let child_op_cr = $("#nb_children_operator_criteria_" + nb_parent);
        let child_op_gp = $("#nb_children_operator_group_" + nb_parent);
        let child_op = $("#nb_children_operator_" + nb_parent);
        child_op_cr.val(parseInt(child_op_cr.val()) + 1);
        child_op_gp.val(parseInt(child_op_gp.val()) + 1);
        child_op.val(parseInt(child_op.val()) + 1);
    } else {
        let child_op_gp = $("#nb_children_operator_group_" + nb_parent);
        let child_op = $("#nb_children_operator_" + nb_parent);
        child_op_gp.val(parseInt(child_op_gp.val()) + 1);
        child_op.val(parseInt(child_op.val()) + 1);
    }
    // Incrémentation de l'indice et du compteur
    nb_groupes++; cpt_groupes++;
}

// Supprime une ligne de critères / données à afficher et décrémente les compteurs correspondants
function delete_critere_or_donnee(parent, type) {
    // Décrémentation des compteurs
    if (type == "critere" || type == "filter") { var nb_grandparent = getNumber($(parent).parent()); cpt_criteres--; }
    else cpt_donnees_affs--;
    // Réactivation de l'élément d'ajout des groupes si besoin
    let profondeur = parseInt($(parent).parent().attr("class").slice(-1));
    if (page_type != "filter" && is_group_empty($(parent).parent(), parent)) {
        reactivation_add_group(nb_grandparent, profondeur);
    }
    // Suppression de l'élément
    if (type == "filter") {
        // console.log("profondeur : " + profondeur + " vs seuil : " + seuil);
        if (profondeur+1 < seuil) {
            reactivation_add_group(nb_grandparent, profondeur);
            reactivation_add_critere(nb_grandparent, profondeur);
        } else {
            reactivation_add_critere(nb_grandparent, profondeur);
        }
        let child_op_cr = $("#nb_children_operator_criteria_" + nb_grandparent);
        let child_op_gp = $("#nb_children_operator_group_" + nb_grandparent);
        let child_op = $("#nb_children_operator_" + nb_grandparent);
        child_op_cr.val(parseInt(child_op_cr.val()) - 1);
        child_op_gp.val(parseInt(child_op_gp.val()) - 1);
        child_op.val(parseInt(child_op.val()) - 1);
    } else if (type == "critere") {
        let child_op_cr = $("#nb_children_operator_criteria_" + nb_grandparent);
        let child_op = $("#nb_children_operator_" + nb_grandparent);
        child_op_cr.val(parseInt(child_op_cr.val()) - 1);
        child_op.val(parseInt(child_op.val()) - 1);
    }
    parent.remove();
    if (page_type != "filter")
        disable_input();
}

// Supprime un groupe et son contenu
function delete_group(parent, profondeur_grandparent) {
    let nb_grandparent = ($(parent).parent().attr("id")).slice(-3);
    // Décrémentation du compteur
    if (page_type != "filter")
        cpt_criteres = cpt_criteres - count_criteres_in_parent($(parent));
    // Réactivation de l'élément d'ajout des critères si besoin
    if (page_type != "filter" && is_group_empty($(parent).parent(), parent)) {
        reactivation_add_critere(nb_grandparent, profondeur_grandparent);
    }
    // Suppression de l'élément
    if (page_type == "filter") {
        if (profondeur_grandparent < seuil) {
            reactivation_add_critere(nb_grandparent, profondeur_grandparent);
            reactivation_add_group(nb_grandparent, profondeur_grandparent);
        }
        let child_op_cr = $("#nb_children_operator_criteria_" + nb_grandparent);
        let child_op_gp = $("#nb_children_operator_group_" + nb_grandparent);
        let child_op = $("#nb_children_operator_" + nb_grandparent);
        child_op_cr.val(parseInt(child_op_cr.val()) - 1);
        child_op_gp.val(parseInt(child_op_gp.val()) - 1);
        child_op.val(parseInt(child_op.val()) - 1);
    } else {
        let child_op_gp = $("#nb_children_operator_group_" + nb_grandparent);
        let child_op = $("#nb_children_operator_" + nb_grandparent);
        child_op_gp.val(parseInt(child_op_gp.val()) - 1);
        child_op.val(parseInt(child_op.val()) - 1);
    }
    parent.remove();
    if (page_type != "filter")
        disable_input();
}

function add_critere_or_donnee(parent, type) {
    var nb_grandparent = getNumber($(parent).parent().parent());
    if (nb_grandparent != "-01" && type == "filter" && $("#nb_children_operator_" + nb_grandparent).val() == 0) {
        add_critere_or_donnee_(parent, type);
        add_critere_or_donnee_(parent, type);
    } else {
        add_critere_or_donnee_(parent, type);
    }
}

// Rajoute une ligne de critères / données à afficher et incrémente les compteurs correspondants
function add_critere_or_donnee_(parent, type) {
    if (type == "critere") {
        var inserted_child = $("#critere_rapport_").clone();
    } else if (type == "donnee") {
        var inserted_child = $("#donnee_affichee_").clone();
    } else if (type == "filter") {
        var inserted_child = $("#predicat_").clone();
    }
    // Calcul du nouvel indice pour l'enfant a inserer
    let nb = 0;
    if (type == "critere" || type == "filter") { var nb_grandparent = getNumber($(parent).parent().parent()); nb = nb_criteres; }
    else if (type == "donnee") { nb = nb_donnees_affs; }
    if (nb < 10) { nb = "00" + nb; }
    else if (nb < 100) { nb = "0" + nb; }
    $(inserted_child).attr("id", $(inserted_child).attr("id") + nb);
    // Ajout de l'indice a chaque element que contient l'enfant
    for (let i=0; i < inserted_child.children().length; i++) {
        maj_id_and_name(inserted_child.children()[i], nb);
    }
    if (type == "critere") {
        // Ajout de l'enfant dans le grandparent
        let child_op_cr = $("#nb_children_operator_criteria_" + nb_grandparent);
        let child_op = $("#nb_children_operator_" + nb_grandparent);
        child_op_cr.val(parseInt(child_op_cr.val()) + 1);
        child_op.val(parseInt(child_op.val()) + 1);
        $("#div_operation_sub_int_" + nb_grandparent).append(inserted_child);
    } else if (type == "donnee") {
        $(parent).append(inserted_child);
    } else if (type == "filter") {
        let child_op_cr = $("#nb_children_operator_criteria_" + nb_grandparent);
        let child_op_gp = $("#nb_children_operator_group_" + nb_grandparent);
        let child_op = $("#nb_children_operator_" + nb_grandparent);
        child_op_cr.val(parseInt(child_op_cr.val()) + 1);
        child_op_gp.val(parseInt(child_op_gp.val()) + 1);
        child_op.val(parseInt(child_op.val()) + 1);
        $("#div_operation_sub_int_" + nb_grandparent).append(inserted_child);
    }
    $(inserted_child).show();
    // Désactivation de l'autre bouton / lien
    if (type == "critere") {
        deactivation_add_group(nb_grandparent);
    } else if (type == "filter" && ($("#nb_children_operator_" + nb_grandparent).val() == 2 || nb_grandparent == "-01")) {
        deactivation_add_critere(nb_grandparent);
        deactivation_add_group(nb_grandparent);
    }
    // Incrementation de l'indice et du compteur
    if (type == "critere" || type == "filter") { nb_criteres++; cpt_criteres++; }
    else if (type == "donnee"){ nb_donnees_affs++; cpt_donnees_affs++; }
    if (page_type != "filter")
        disable_input();
}