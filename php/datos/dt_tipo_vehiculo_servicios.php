<?php
class dt_tipo_vehiculo_servicios extends toba_datos_tabla
{
	function get_listado()
	{
		$sql = "SELECT
			t_tvs.tvs_tipo_vehiculo,
			t_tvs.tvs_servicio,
			t_tvs.tvs_valor,
			t_tvs.tvs_vigente
		FROM
			tipo_vehiculo_servicios as t_tvs";
		return toba::db('lavautos')->consultar($sql);
	}

}

?>