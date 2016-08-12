<?php
require_once('libreria/combos_parametros.php');
class navegacion {
   static function get_servicio_encabezado($filtro=null)    {
         $where = array();
         if(isset($filtro['serenc_id'])) {
           $where[] = "serenc_id = ".quote("{$filtro['serenc_id']}");
         }
         if(isset($filtro['serenc_tipo_vehiculo'])) {
           $where[] = "serenc_tipo_vehiculo = ".quote("{$filtro['serenc_tipo_vehiculo']}");
         }
         if(isset($filtro['serenc_vehiculo'])) {
           $where[] = "serenc_vehiculo = ".quote("{$filtro['serenc_vehiculo']}");
         }
         if(isset($filtro['serenc_fecha_actual'])) {
           $where[] = "substr(serenc_fecha::text, 0, 11) = ".quote("{$filtro['serenc_fecha_actual']}");
         }
         if(isset($filtro['serenc_fecha'])) {
           $where[] = "substr(serenc_fecha::text, 0, 11) >= ".quote("{$filtro['serenc_fecha']}");
         }
         if(isset($filtro['fecha_hasta'])) {
           $where[] = "substr(serenc_fecha::text, 0, 11) <= ".quote("{$filtro['fecha_hasta']}");
         }
         if(isset($filtro['veh_placa'])) {
           $where[] = "serenc_vehiculo IN (SELECT veh_id FROM vehiculos WHERE veh_placa ILIKE ".quote("{$filtro['veh_placa']}").")";
         }
         if(isset($filtro['estser_id'])) {
           $where[] = "serenc_estado = ".quote("{$filtro['estser_id']}");
         }
         if(isset($filtro['per_id'])) {
           $where[] = "serenc_empleado = ".quote("{$filtro['per_id']}");
         }
         $sql = " SELECT servicio_encabezado.*, vehiculos.*, tipo_vehiculos.*, estado_servicios.*,
                         personas.*
                    FROM servicio_encabezado, vehiculos, tipo_vehiculos, estado_servicios, personas
                   WHERE serenc_vehiculo = veh_id
                     AND serenc_tipo_vehiculo = tipveh_id
                     AND serenc_estado = estser_id
                     AND serenc_empleado = per_id
                ORDER BY serenc_id;";
        if(count($where)>0) {
           $sql = sql_concatenar_where($sql, $where);
        } //print_r($sql);
        $res = consultar_fuente($sql);
        $fila = 0;
        foreach ($res as $encabezados){
           foreach ($encabezados as $encabezado => $value){
              if ($encabezado == 'serenc_id')            $encabezado_id    = $value;
              if ($encabezado == 'serenc_vehiculo')      $vehiculo_id      = $value;
              if ($encabezado == 'serenc_tipo_vehiculo') $tipo_vehiculo_id = $value;
           }
           $datos_totales = combos_parametros::get_totales_encabezado ($encabezados);
           $res[$fila]['total_servicios']    = $datos_totales[0]['total_servicios'];
           $res[$fila]['total_compensacion'] = $datos_totales[0]['total_compensacion'];
           $res[$fila]['total_suministros']  = $datos_totales[0]['total_suministros'];
           $fila++;
        }
        return $res;
   }

   static function get_tipo_vehiculos($filtro=null)    {
         $where = array();
         if(isset($filtro['tipveh_id'])) {
           $where[] = "tipveh_id = ".quote("{$filtro['tipveh_id']}");
         }
         $sql = "SELECT tipo_vehiculos.*
                   FROM tipo_vehiculos
               ORDER BY tipveh_nombre;";
        if(count($where)>0) {
           $sql = sql_concatenar_where($sql, $where);
        } //print_r($sql);
        $res = consultar_fuente($sql);
        return $res;
	}

   static function get_vehiculos($filtro=null)    {
         $where = array();
         if(isset($filtro['veh_id'])) {
           $where[] = "veh_id = ".quote("{$filtro['veh_id']}");
         }
         if(isset($filtro['veh_tipo_vehiculo'])) {
           $where[] = "veh_tipo_vehiculo = ".quote("{$filtro['veh_tipo_vehiculo']}");
         }
         if(isset($filtro['veh_placa'])) {
           $where[] = "veh_placa ILIKE ".quote("{$filtro['veh_placa']}");
         }
         $sql = "SELECT vehiculos.*
                   FROM vehiculos
               ORDER BY veh_placa;";
        if(count($where)>0) {
           $sql = sql_concatenar_where($sql, $where);
        } //print_r($sql);
        $res = consultar_fuente($sql);
        return $res;
	}

   static function get_servicios($filtro=null)    {
         $where = array();
         if(isset($filtro['ser_id'])) {
           $where[] = "ser_id = ".quote("{$filtro['ser_id']}");
         }
         $sql = "SELECT ser_id.*
                   FROM ser_id
               ORDER BY ser_id;";
        if(count($where)>0) {
           $sql = sql_concatenar_where($sql, $where);
        } //print_r($sql);
        $res = consultar_fuente($sql);
        return $res;
	}

   static function get_personas($filtro=null)    {
         $where = array();
         if(isset($filtro['per_id'])) {
           $where[] = "per_id = ".quote("{$filtro['per_id']}");
         }
         if(isset($filtro['per_nombres'])) {
               $where[] = "per_nombres ILIKE ".quote("%{$filtro['per_nombres']}%");
         }
         if(isset($filtro['per_identificacion'])) {
              $where[] = "per_identificacion = ".quote("{$filtro['per_identificacion']}");
         }
         if(isset($filtro['no_definido'])) {
             if ($filtro['no_definido']) $where[] = "per_id >= 0";
             else $where[] = "per_id > 0";
         }
         $sql = "SELECT personas.*
                   FROM personas
               ORDER BY per_nombres;";
        if(count($where)>0) {
           $sql = sql_concatenar_where($sql, $where);
        } //print_r($sql);
        $res = consultar_fuente($sql);
        return $res;
	}
} //fin clase navegacion
?>
