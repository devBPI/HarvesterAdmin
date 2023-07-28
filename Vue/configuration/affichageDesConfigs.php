<table class="table-config">
    <thead>
        <tr>
    <?php

    $url = "Accueil.php?name=" . $name . "&connecteur=" . $grabber . "&order=";
    $arrow = "";
    $sens = "";
    if ($order == "HGC.id") {
        $arrow = "▼";
        $sens = "DESC";
    } else if ($order == "HGC.id DESC") {
        $arrow = "▲";
    }
    echo "<th scope=\"col\" onclick = 'location.href=\"" . $url . "HGC.id " . $sens . "\"' class='button primairy-color full-width' style='height:35px'>ID" . $arrow . "</th>";

    $arrow = "";
    $sens = "";
    if ($order == "HC.code") {
        $arrow = "▼";
        $sens = "DESC";
    } else if ($order == "HC.code DESC") {
        $arrow = "▲";
    }
    echo "<th scope=\"col\" onclick = 'location.href=\"" . $url . "HC.code " . $sens . "\"' class='button primairy-color full-width' style='height:35px'>Code" . $arrow . "</th>";

    $arrow = "";
    $sens = "";
    if ($order == "HC.name") {
        $arrow = "▼";
        $sens = "DESC";
    } else if ($order == "HC.name DESC") {
        $arrow = "▲";
    }
    echo "<th scope=\"col\" onclick = 'location.href=\"" . $url . "HC.name " . $sens . "\"' class='button primairy-color full-width' style='height:35px'>Nom abrégé" . $arrow . "</th>";

    $arrow = "";
    $sens = "";
    if ($order == "public_name") {
        $arrow = "▼";
        $sens = "DESC";
    } else if ($order == "public_name DESC") {
        $arrow = "▲";
    }
    echo "<th scope=\"col\" onclick = 'location.href=\"" . $url . "public_name " . $sens . "\"' class='button primairy-color full-width' style='height:35px'>Base de recherche" . $arrow . "</th>";

    $arrow = "";
    $sens = "";
    if ($order == "G.name") {
        $arrow = "▼";
        $sens = "DESC";
    } else if ($order == "G.name DESC") {
        $arrow = "▲";
    }
    echo "<th scope=\"col\" onclick = 'location.href=\"" . $url . "G.name " . $sens . "\"' class='button primairy-color full-width' style='height:35px'>Type de connecteur" . $arrow . "</th>";

    $arrow = "";
    $sens = "";
    if ($order == "date") {
        $arrow = "▼";
        $sens = "DESC";
    } else if ($order == "date DESC") {
        $arrow = "▲";
    }
    echo "<th scope=\"col\" class='button primairy-color full-width' style='height:35px'>Dernière moisson" . $arrow . "</th>";
    echo "</tr></thead>";
    foreach ($conf as $var) {
        ?>
        
        
        <tr>
            <td data-label="ID"><?= $var['id'] ?></td>
            <td data-label="Code"><?= str_replace("_", "_<wbr>", $var['code']) ?></td>
            <td data-label="Nom Abrégé"><a
                href="../Vue/FicheIndividuelle.php?param=<?= $var['id'] ?>"><?= str_replace("_", "_<wbr>", $var['name']) ?></a></td>
            <td data-label="Base de recherche"><?= $var['public_name'] ?></a></td>
            <td data-label="Type de connecteur"><?= $var['grabber'] ?></a></td>
            <td data-label="Dernière moisson"><?php
                if (!isset($var['date']) || $var['date'] == null) {
                    echo 'Jamais ou réinitialisé';
                } else {
                    $modificationDateSyst = date('Y-m-d H:i:s', strtotime($var['date'])) . " ";
                    $creationDateSyst = date("Y-m-d H:i:s");
                    $date1 = date_create($creationDateSyst);
                    $date2 = date_create($modificationDateSyst);
                    $diff = date_diff($date1, $date2);
                    echo $diff->format('Il y a %d jours ');
                } ?></td>
        </tr>
    <?php } ?>
</table>
