<?php
$name = $_POST['taskname'];
$param = $_POST['taskparameter'];
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
require_once ("../PDO/Gateway.php");
Gateway::connection();

switch ($action) {
    case 'maintenant':
        {
            if ($name != null) {
                if($name == "PURGE"){
                    $param = Gateway::getIdFromCode($param);
                } else {
                    $param = null;
                }
                $ins = Gateway::insertSideTask($name, $param);
                if ($ins) {
                    echo "<script type='text/javascript'>document.location.replace('../Controlleur/HistoriqueTachesAnnexes.php');</script>";
                }
            } else {
                ?>
 <div id="divAccepter" class="avertissement">
    <p>Veuillez remplir tous les champs.</p>
</div>
<?php
            }
            break;
        }
    case 'quotidienne':
        {
            $heure = $_POST['heureQuot'];
            if ($name != null && $heure != "null") {
                if($name == "PURGE"){
                    $param = Gateway::getIdFromCode($param);
                } else {
                    $param = null;
                }
                $hExplode = explode(':', $heure);
                $h = $hExplode[0];
                $m = $hExplode[1];
                $ins = Gateway::insertSideTaskDate($m, $h, "NULL", "NULL", $name, $param);
                if ($ins) {
                    echo "<script type='text/javascript'>document.location.replace('../Controlleur/PlanningTachesAnnexes.php');</script>";
                }
            } else {
                ?>
<div id="divAccepter" class="avertissement">
    <p>Veuillez remplir tous les champs.</p>
</div>
<?php
            }
            break;
        }
    case 'hebdomadaire':
        {
            $heure = $_POST['heureHebdo'];
            $jour = $_POST['jourHebdo'];
            if ($name != null && $heure != "null" && $jour != 0) {
                if($name == "PURGE"){
                    $param = Gateway::getIdFromCode($param);
                } else {
                    $param = null;
                }
                $hExplode = explode(':', $heure);
                $h = $hExplode[0];
                $m = $hExplode[1];
                $joursemaine = ($jour+1)%7;
                if($joursemaine == 0){
                    $joursemaine = 7;
                }
                $ins = Gateway::insertSideTaskDate($m, $h, "NULL", $joursemaine, $name, $param);
                if ($ins) {
                    echo "<script type='text/javascript'>document.location.replace('../Controlleur/PlanningTachesAnnexes.php');</script>";
                }
            } else {
                ?>
<div id="divAccepter" class="avertissement">
    <p>Veuillez remplir tous les champs.</p>
</div>
<?php
            }

            break;
        }
    case 'mensuelle':
        {
            $heure = $_POST['heureMonth'];
            $jour = $_POST['jourMonth'];
            $day = DayOfTheMonth($semaine);
            if ($name != null && $jour != 0 && $day != 0 && $semaine != 0 && $heure != "null") {
                if($name == "PURGE"){
                    $param = Gateway::getIdFromCode($param);
                } else {
                    $param = null;
                }
                $hExplode = explode(':', $heure);
                $h = $hExplode[0];
                $m = $hExplode[1];
                $ins = Gateway::insertSideTaskDate($m, $h, $day, "NULL", $name, $param);
                if ($ins) {
                    echo "<script type='text/javascript'>document.location.replace('../Controlleur/PlanningTachesAnnexes.php');</script>";
                }
            } else {
                ?>
<div id="divAccepter" class="avertissement">
    <p>Veuillez remplir tous les champs.</p>
</div>
<?php
            }
            break;
        }
}

function DayOfTheMonth($semaineParam)
{
    $day = 0;
    switch ($semaineParam) {
        case '1':
            {
                $day = 0 + $jour;
                break;
            }
        case '2':
            {
                $day = 6 + $jour;
                break;
            }
        case '3':
            {
                $day = 13 + $jour;
                break;
            }
        case '4':
            {
                $day = 20 + $jour;
                break;
            }
        case '5':
            {
                $day = 27 + $jour;
                break;
            }
    }
    return $day;
}

?>