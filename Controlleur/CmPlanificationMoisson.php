<?php
$id = $_POST['template'];
$semaine = $_POST['semaine'];

if (isset($_POST['now'])) {
    $_REQUEST['action'] = 'maintenant';
} else {
    if (isset($_POST["quot"])) {
        $_REQUEST['action'] = 'quotidienne';
    } else {
        if (isset($_POST['hebdo'])) {
            $_REQUEST['action'] = 'hebdomadaire';
        } else {
            if (isset($_POST['month'])) {
                $_REQUEST['action'] = 'mensuelle';
            }
        }
    }
}

$action = $_REQUEST['action'];
require_once ("../Gateway.php");
Gateway::connection();

switch ($action) {
    case 'maintenant':
        {
            if ($id != 0) {
                $ins = Gateway::insertMoisson($id);
                if ($ins) {
                    echo "<script type='text/javascript'>document.location.replace('../Controlleur/HistoriqueMoisson.php');</script>";
                }
            } else {
                ?>
<div id="divAccepter" style="width: 100%;">
	<font color="red">Veuillez remplir tous les champs.</font>
</div>
<?php
            }
            break;
        }
    case 'quotidienne':
        {
            $heure = $_POST['heureQuot'];
            if ($id != 0 && $heure != "null") {
                $hExplode = explode(':', $heure);
                $h = $hExplode[0];
                $m = $hExplode[1];
                $ins = Gateway::insertDate($m, $h, "NULL", "NULL", $id);
                if ($ins) {
                    echo "<script type='text/javascript'>document.location.replace('../Controlleur/PlanningMoisson.php');</script>";
                }
            } else {
                ?>
<div id="divAccepter" style="top: 0%; width: 100%;">
	<font color="red">Veuillez remplir tous les champs.</font>
</div>
<?php
            }
            break;
        }
    case 'hebdomadaire':
        {
            $heure = $_POST['heureHebdo'];
            $jour = $_POST['jourHebdo'];
            if ($id != 0 && $heure != "null" && $jour != 0) {
                $hExplode = explode(':', $heure);
                $h = $hExplode[0];
                $m = $hExplode[1];
                $joursemaine = ($jour+1)%7;
                if($joursemaine == 0){
                    $joursemaine = 7;
                }
                $ins = Gateway::insertDate($m, $h, "NULL", $joursemaine, $id);
                if ($ins) {
                    echo "<script type='text/javascript'>document.location.replace('../Controlleur/PlanningMoisson.php');</script>";
                }
            } else {
                ?>
<div id="divAccepter" style="width: 100%;">
	<font color="red">Veuillez remplir tous les champs.</font>
</div>
<?php
            }

            break;
        }
    case 'mensuelle':
        {
            $heure = $_POST['heureMonth'];
            $jour = $_POST['jourMonth'];
            if ($id != 0 && $jour != 0 && $semaine != 0 && $heure != "null") {
                $hExplode = explode(':', $heure);
                $h = $hExplode[0];
                $m = $hExplode[1];
                $joursemaine = ($jour+1)%7;
                if($joursemaine == 0){
                    $joursemaine = 7;
                }
                $ins = Gateway::insertDate($m, $h, $semaine, $joursemaine, $id);
                if ($ins) {
                    echo "<script type='text/javascript'>document.location.replace('../Controlleur/PlanningMoisson.php');</script>";
                }
            } else {
                ?>
                    <div id="divAccepter" style="width: 100%;">
                        <font color="red">Veuillez remplir tous les champs.</font>
                    </div>
                <?php
            }
            break;
        }
}

?>