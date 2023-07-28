<?php
include_once("../PDO/Gateway.php");
class TacheAnnexe
{
	static function getSideTaskPlanifForEveryDayOfWeek($dow)
	{
		$query = pg_query (Gateway::getConnexion(), "SELECT cron.name, cron.parameter,cron.id,h,m,dow,dom FROM configuration.side_task_cron_line cron
			WHERE ((dow IS NULL AND dom IS NULL)  OR dow=".$dow.") ORDER BY h,m ASC");
		if (!$query)
		{
			echo "Erreur durant la requête de getSideTaskPlanif .\n";
			exit;
		}
		return pg_fetch_all($query);
	}

	/** Supprime la tâche annexe
	 * @param $id integer id de la tâche annexe a supprimer
	 * @return false si la requête a échoué | resource sinon
	 */
	static function deleteSideTaskPlanif($id)
	{
		return pg_query (Gateway::getConnexion(), "DELETE FROM configuration.side_task_cron_line WHERE id=".$id);
	}

	static function insertSideTask($name, $parameter)
	{
		return pg_query(Gateway::getConnexion(), "INSERT into configuration.side_task(name, parameter, status,creation_date, modification_date) values ('".$name."','".$parameter."','TO_PROCESS', NOW(),NOW());")or die ('Erreur insertSideTask'. pg_last_error(Gateway::getConnexion()));
	}

	static function insertSideTaskDate($m, $h, $day, $jour, $name, $parameter)
	{
		return pg_query (Gateway::getConnexion(), "INSERT INTO configuration.side_task_cron_line(m,h,dom,mon,dow,name, parameter) VALUES (".$m.",".$h.",".$day.",NULL,".$jour.",'".$name."','".$parameter."') RETURNING id")or die ('Erreur insertSideTaskDate'. pg_last_error(Gateway::getConnexion()));

	}

	/** Compte les tâches annexes
	 * @return int le nombre de tâches trouvées | void si erreur dans la requête
	 */
	static function countSideTasks()
	{
		$query = "SELECT COUNT(*) FROM configuration.side_task";
		$sql = pg_query(Gateway::getConnexion(), $query);
		if (!$sql)
		{
			echo "Erreur durant la requête de countSideTasksConfiguration .\n";
			exit;
		}
		return pg_fetch_all($sql)[0]['count'];
	}

	/** Retourne une page des tâches annexes
	 * @param $order int ordre souhaité pour l'affichage des données
	 * @param $size int nombre de résultats par page
	 * @param $page int page à afficher (sert à calculer l'offset)
	 * @return array|false|void selon les tâches annexes trouvées
	 */
	static function getSideTasksPagined($order,$size,$page)
	{
		$offset = ($size*($page-1));
		$query = pg_query (Gateway::getConnexion(), "SELECT status,t.id, name, parameter, creation_date, modification_date, message, start_time,end_time, total_effective_duration_sec FROM configuration.side_task t
		ORDER BY ".$order." LIMIT ".$size." OFFSET ".$offset);
		if (!$query)
		{
			echo "Erreur durant la requête de getSideTasks .\n";
			exit;
		}
		return pg_fetch_all($query);
	}
}