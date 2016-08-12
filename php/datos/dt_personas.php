<?php
class dt_personas extends toba_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT per_id, per_identificacion FROM personas ORDER BY per_identificacion";
		return toba::db('lavautos')->consultar($sql);
	}

}

?>