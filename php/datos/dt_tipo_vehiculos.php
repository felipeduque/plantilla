<?php
class dt_tipo_vehiculos extends toba_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT tipveh_id, tipveh_nombre FROM tipo_vehiculos ORDER BY tipveh_nombre";
		return toba::db('lavautos')->consultar($sql);
	}

}

?>