<?php
class dt_estado_turnos extends toba_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT esttur_id, esttur_nombre FROM estado_turnos ORDER BY esttur_nombre";
		return toba::db('lavautos')->consultar($sql);
	}

}

?>