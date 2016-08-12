<?php
class combos_parametros {
  static function get_saldo ($prestamo, $pago)    {
    return ($prestamo - $pago);
  }

  static function set_estado_turno ($filtro=null)    {
     $where = array();
     if(isset($filtro['tur_empleado'])) {
        $where[] = "tur_empleado = ".quote("{$filtro['tur_empleado']}");
     }
     $turno_neutro = 0;
     $where[] = "tur_empleado <> ".$turno_neutro;
     $sql = " UPDATE turnos SET tur_estado_turno = {$filtro['estado_turno']};";
     if(count($where)>0) {
       $sql = sql_concatenar_where($sql, $where);
     } //print_r($sql);
     $res = consultar_fuente($sql);
     return $res;
  }

  static function get_totales_encabezado($filtro=null)    {
         $where = array();
         if(isset($filtro['serenc_id'])) {
           $where[] = "serdet_encabezado = ".quote("{$filtro['serenc_id']}");
         }
         if(isset($filtro['serenc_tipo_vehiculo'])) {
           $where[] = "serdet_tipo_vehiculo = ".quote("{$filtro['serenc_tipo_vehiculo']}");
         }
         if(isset($filtro['serenc_vehiculo'])) {
           $where[] = "serdet_vehiculo = ".quote("{$filtro['serenc_vehiculo']}");
         }
         $sql = " SELECT SUM(serdet_valor) AS total_servicios,
                         SUM(serdet_descuento_compens) AS total_compensacion,
                         SUM(serdet_descuento_insumos) AS total_suministros
                    FROM servicio_detalles
                GROUP BY serdet_encabezado, serdet_vehiculo, serdet_tipo_vehiculo;";
        if(count($where)>0) {
           $sql = sql_concatenar_where($sql, $where);
        } //print_r($sql);
        $res = consultar_fuente($sql);
        return $res;
  }

  static function get_servicios_tipo_vehiculo($tipo_vehiculo)    {
       $sql = "SELECT ser_id, tipo_vehiculo_servicios.*,
                    (case when ser_descripcion is not null then ('[ ' || ser_nombre || '] ' || ser_descripcion || ' [' || tvs_valor || ']') else (ser_nombre || ' [' || tvs_valor || ']') end) As ser_nombre
               FROM servicios, tipo_vehiculo_servicios
              WHERE tvs_servicio = ser_id
                AND tvs_tipo_vehiculo = {$tipo_vehiculo};";    //print_r($sql);
       return consultar_fuente($sql);
  }

  static function get_valor_tipo_vehiculo_servicio ($tipo_vehiculo, $servicio)    {
       $sql = "SELECT tipo_vehiculo_servicios.*
                 FROM tipo_vehiculo_servicios
                WHERE tvs_tipo_vehiculo = {$tipo_vehiculo} AND tvs_servicio = {$servicio};";
       $res = consultar_fuente($sql);
       return $res[0]['tvs_valor'];
  }

  static function get_descuento_compensacion ($tipo_vehiculo, $servicio)    {
       $sql = "SELECT tipo_vehiculo_servicios.*
                 FROM tipo_vehiculo_servicios
                WHERE tvs_tipo_vehiculo = {$tipo_vehiculo} AND tvs_servicio = {$servicio};";
       $res = consultar_fuente($sql);
       $compensacion = 1;
       $datos_descuento = combos_parametros::get_descuentos_tipo ($compensacion);
       if (! $datos_descuento[0]['des_porcentaje']) $datos_descuento[0]['des_porcentaje'] = 0;
       return $res[0]['tvs_valor'] * ($datos_descuento[0]['des_porcentaje'] / 100);
  }

  static function get_descuento_suministros ($tipo_vehiculo, $servicio)    {
       $sql = "SELECT tipo_vehiculo_servicios.*
                 FROM tipo_vehiculo_servicios
                WHERE tvs_tipo_vehiculo = {$tipo_vehiculo} AND tvs_servicio = {$servicio};";
       $res = consultar_fuente($sql);
       $suministros = 2;
       $datos_descuento = combos_parametros::get_descuentos_tipo ($suministros);
       if (! $datos_descuento[0]['des_porcentaje']) $datos_descuento[0]['des_porcentaje'] = 0;
       return $res[0]['tvs_valor'] * ($datos_descuento[0]['des_porcentaje'] / 100);
  }

  static function get_descuentos_tipo ($tipo_descuento)    {
         $where = array();
         $where[] = "des_id = {$tipo_descuento}";
         $sql = "SELECT descuentos.*
                   FROM descuentos;";
        if(count($where)>0) {
           $sql = sql_concatenar_where($sql, $where);
        } //print_r($sql);
        $res = consultar_fuente($sql);
        return $res;
  }

  static function get_lavadores_disponibles_turno ()    {
         $where = array(); $estado_libre = 1; $estado_asignado = 2;
         $datos_encabezado = toba::memoria()->get_dato_instancia('datos_encabezado');
         //if ($estado_servicio == $estado_recepcion || $estado_servicio == $estado_asignado)
         if ($datos_encabezado[0]['serenc_id']){
           $where[] = "(tur_estado_turno = {$estado_libre} OR tur_empleado = ".$datos_encabezado[0]['serenc_empleado'].")";
           $sql = "SELECT per_id, ('[' || tur_orden || '] ' || per_nombres) AS per_nombres
                     FROM turnos, personas
                    WHERE tur_empleado = per_id
                 ORDER BY tur_orden;";
           if(count($where)>0) {
             $sql = sql_concatenar_where($sql, $where);
           } //print_r($sql);
           $res = consultar_fuente($sql);
           return $res;
        }else{
           $where[] = "tur_estado_turno = {$estado_libre}";
           $sql = "SELECT per_id, ('[' || tur_orden || '] ' || per_nombres) AS per_nombres
                     FROM turnos, personas
                    WHERE tur_empleado = per_id
                 ORDER BY tur_orden;";
           if(count($where)>0) {
             $sql = sql_concatenar_where($sql, $where);
           } //print_r($sql);
           $res = consultar_fuente($sql);
           return $res;
        }
	}

    static function get_empleados_lavadores($filtro=null)    {
         $where = array();
         if ($filtro['no_definido']) $where[] = "per_id > 0";
         $lavadores = 'lavador';
         //$where[] = "per_id > 0";
         $where[] = "per_perfil = ".quote("{$lavadores}");
         $sql = "SELECT personas.*
                   FROM personas
               ORDER BY per_nombres;";
        if(count($where)>0) {
           $sql = sql_concatenar_where($sql, $where);
        } //print_r($sql);
        $res = consultar_fuente($sql);
        return $res;
	}

    static function get_empleados_prestamos($filtro=null)    {
         $where = array();
         if(isset($filtro['emppre_empleado'])) {
           $where[] = "emppre_empleado = ".quote("{$filtro['emppre_empleado']}");
         }
         $sql = " SELECT SUM(emppre_valor_prestamo) AS total_prestamos
                    FROM empleado_prestamos
                GROUP BY emppre_empleado;";
        if(count($where)>0) {
           $sql = sql_concatenar_where($sql, $where);
        }
        $res = consultar_fuente($sql);
        return $res;
	}

    static function get_liquidar_empleado ($filtro=null)    {
         $where = array();
         if(isset($filtro['serenc_empleado'])) {
           $where[] = "serenc_empleado = ".quote("{$filtro['serenc_empleado']}");
         }
         if(isset($filtro['serenc_fecha'])) {
           $where[] = "substr(serenc_fecha::text, 0, 11) >= ".quote("{$filtro['serenc_fecha']}");
         }
         if(isset($filtro['serenc_estado'])) {
           $where[] = "serenc_estado = ".quote("{$filtro['serenc_estado']}");
         }
         $sql = " SELECT per_id, per_nombres, per_identificacion,
                         (SUM(serdet_valor) - SUM(serdet_descuento_compens) -SUM(serdet_descuento_insumos)) AS total_servicios
                    FROM servicio_detalles, servicio_encabezado, personas
                   WHERE serdet_encabezado = serenc_id
                     AND serenc_empleado = per_id
                  GROUP BY per_id, per_nombres, per_identificacion;";
        if(count($where)>0) {
           $sql = sql_concatenar_where($sql, $where);
        }  // print_r($sql);
        $res = consultar_fuente($sql);
        $fila = 0;
        foreach ($res as $encabezados){
           foreach ($encabezados as $encabezado => $value){
              if ($encabezado == 'per_id')          $empleado_id    = $value;
              if ($encabezado == 'total_servicios') $total_servicios    = $value;
           }
           $filtro_empleado['emppre_empleado'] = $empleado_id;
           $datos_totales = combos_parametros::get_empleados_prestamos ($filtro_empleado);
           $res[$fila]['total_prestamos'] = $datos_totales[0]['total_prestamos'];
           $res[$fila]['saldo'] = $total_servicios - $datos_totales[0]['total_prestamos'];
           $fila++;
        }
        return $res;
	}

    static function get_vehiculos($filtro=null, $locale=null)    {
        if (! isset($filtro) || trim($filtro) == '') {
            return array();
        }
        $where = '';
        if (isset($locale)) {
            $where = " AND (veh_placa LIKE ".quote($locale).")";
        }
        $sql = "SELECT veh_id, veh_placa
                  FROM vehiculos
                 WHERE veh_placa ILIKE '%{$filtro}%'
                $where;"; //print_r($sql);
        return toba::db()->consultar($sql);
    }

    function get_vehiculo($id = null)     {
        if (! isset($id)) {
            return array();
        }
        $sql = "SELECT veh_id, veh_placa
                  FROM vehiculos
                 WHERE veh_id = '$id'
              ORDER BY veh_placa";
         $result = toba::db()->consultar($sql);

        if (! empty($result)) {
            return $result[0]['veh_placa'];
        }
	}
}
?>
