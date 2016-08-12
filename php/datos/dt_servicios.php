<?php
class dt_servicios extends toba_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT ser_id, ser_nombre FROM servicios ORDER BY ser_nombre";
		return toba::db('lavautos')->consultar($sql);
	}

}

?>