<?php

include_once("../PDO/Gateway.php");

class Logs
{
	static function getLogs($niv,$start)
	{
		if($niv!="")
		{
			$query = pg_query(Gateway::getConnexion(),"SELECT * FROM logging.logs WHERE level='".$niv."' 
			AND (date >= date_trunc('week', CURRENT_TIMESTAMP - interval '1 week')) ORDER BY date DESC LIMIT 15 OFFSET ".$start*15);
		}
		else
		{
			$query = pg_query(Gateway::getConnexion(),"SELECT * FROM logging.logs WHERE level!='INFO'
			AND (date >= date_trunc('week', CURRENT_TIMESTAMP - interval '1 week')) ORDER BY date DESC LIMIT 15 OFFSET ".$start*15);
		}
		return @pg_fetch_all($query);
	}

	static function countLogs($niv)
	{
		if($niv!="")
		{
			$query = pg_query(Gateway::getConnexion(),"SELECT count(*) FROM logging.logs WHERE level='".$niv."' 
			AND (date >= date_trunc('week', CURRENT_TIMESTAMP - interval '1 week'))");
		}
		else
		{
			$query = pg_query(Gateway::getConnexion(),"SELECT count(*) FROM logging.logs WHERE level!='INFO'
			AND (date >= date_trunc('week', CURRENT_TIMESTAMP - interval '1 week'))");
		}
		return @pg_fetch_all($query)[0]['count'];
	}

}