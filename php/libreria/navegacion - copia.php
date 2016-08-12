<?php
require_once('libreria/combos_total.php');
require_once('libreria/combos_parametros.php');
class navegacion {
  static function get_historial_orden ($filtro=null)    {
         navegacion::set_alertas_ordenes_trabajo ();
         $where = array();
         if(isset($filtro['ordtra_id'])) {
           $where[] = "ordtra_id = ".quote("{$filtro['ordtra_id']}");
         }
         if(isset($filtro['ordtra_en_alerta'])) {
           $where[] = "ordtra_en_alerta = ".quote("{$filtro['ordtra_en_alerta']}");
         }
         if(isset($filtro['estord_id'])) {
           $where[] = "ordtra_estado_orden = ".quote("{$filtro['estord_id']}");
         }
         if(isset($filtro['cli_id'])) {
           $where[] = "ordtra_cliente = ".quote("{$filtro['cli_id']}");
         }
         if(isset($filtro['ordtra_servicio'])) {
           $where[] = "ordtra_servicio = ".quote("{$filtro['ordtra_servicio']}");
         }
         if(isset($filtro['sed_id'])) {
           $where[] = "ordtra_sede = ".quote("{$filtro['sed_id']}");
         }
         if(isset($filtro['solcan_solicitud'])) {
           $where[] = "ordtra_solicitud = ".quote("{$filtro['solcan_solicitud']}");
         }
         if(isset($filtro['solcan_candidato_id'])) {
           $where[] = "ordtra_candidato = ".quote("{$filtro['solcan_candidato_id']}");
         }
         if(isset($filtro['dep_id'])) {
           $where[] = "solcan_departamento = ".quote("{$filtro['dep_id']}");
         }
         if(isset($filtro['ciu_id'])) {
           $where[] = "solcan_ciudad = ".quote("{$filtro['ciu_id']}");
         }
         if(isset($filtro['cli_nombre'])) {
               $where[] = "cli_nombre ILIKE ".quote("%{$filtro['cli_nombre']}%");
         }
         if(isset($filtro['cli_nit'])) {
              $where[] = "cli_nit = ".quote("{$filtro['cli_nit']}");
         }
         if(isset($filtro['fecha_desde'])) {
              $where[] = "sedsol_fecha_recepcion >= ".quote("{$filtro['fecha_desde']}");
         }
         if(isset($filtro['fecha_hasta'])) {
              $where[] = "sedsol_fecha_recepcion <= ".quote("{$filtro['fecha_hasta']}");
         }
         if(isset($filtro['solcan_candidato'])) {
               $where[] = "solcan_candidato ILIKE ".quote("%{$filtro['solcan_candidato']}%");
         }
         if(isset($filtro['solcan_identificacion'])) {
              $where[] = "solcan_identificacion = ".quote("{$filtro['solcan_identificacion']}");
         }

         $sql = "SELECT ordtra_id, ordtra_cliente, ordtra_sede, ordtra_solicitud, ordtra_candidato,
                        ordtra_servicio, ordtra_fecha_registro, ordtra_observaciones, ordtra_estado_orden,
                        (case when ordtra_en_alerta = TRUE then 'SI' else 'NO' end) as ordtra_en_alerta,
                        solcan_candidato_id, solcan_cliente, solcan_sede, solcan_solicitud,
                        solcan_departamento, solcan_ciudad, solcan_servicios,
                        solcan_identificacion, solcan_candidato, solcan_cargo_texto,
                        sed_nombre, cli_nombre, sedsol_fecha_recepcion, sedsol_hora_recepcion,
                        dep_nombre, ciu_nombre, sed_departamento, sed_ciudad,
                        sedsol_cliente, cli_nit,
                        ser_nombre, estord_nombre,
                        oth_id, oth_cliente, oth_sede, oth_solicitud, oth_candidato,
                        oth_servicio, oth_orden, oth_estado_orden, oth_profesional, oth_revisor,
                        oth_fecha_movimiento, oth_hora_movimiento, oth_observaciones,
                        oth_usuario, oth_fecha_registro, oth_valor_facturado
                   FROM ordenes_trabajo, solicitud_candidatos, sede_solicitudes, sedes, clientes,
                        departamentos, ciudades, servicios, estado_ordenes, ordenes_trabajo_historial
                  WHERE ordtra_solicitud = solcan_solicitud AND solcan_solicitud = sedsol_id
                    AND ordtra_cliente = sedsol_cliente AND sedsol_cliente = cli_id
                    AND ordtra_sede = sedsol_sede AND sedsol_sede = sed_id
                    AND ordtra_candidato = solcan_candidato_id
                    AND ordtra_servicio = ser_id
                    AND oth_estado_orden = estord_id
                    AND (solcan_departamento = dep_id AND solcan_ciudad = ciu_id)
                    AND ordtra_id = oth_orden
               ORDER BY oth_sede, oth_id, oth_orden, oth_estado_orden;";
        if(count($where)>0) {
           $sql = sql_concatenar_where($sql, $where);
        } //print_r($sql);
        $res = toba::db()->consultar($sql);

        $fila = 0;  $re_asignado = 4;
        foreach ($res as $ordenes) {
           $filtro_facturacion = null; $filtro_persona = null;
           foreach ($ordenes as $orden => $value) {
             if ($orden == 'ordtra_id')           $orden_id  = $value;
             if ($orden == 'oth_estado_orden')    $estado_id = $value;
           }
           $datos_ciudad_sede = combos_parametros::get_ciudad_sede ($ordenes['ordtra_sede']);
           $res[$fila]['nombre_ciudad_sede']       = $datos_ciudad_sede[0]['ciu_nombre'];
           $res[$fila]['nombre_departamento_sede'] = $datos_ciudad_sede[0]['dep_nombre'];

           $datos_historial = combos_parametros::get_historial_orden_estado ($ordenes);
           $res[$fila]['oth_profesional'] = 0; $res[$fila]['oth_revisor'] = 0;
           if ($datos_historial) $res[$fila]['oth_profesional']    = $datos_historial[0]['oth_profesional'];
           if ($datos_historial) $res[$fila]['oth_revisor']        = $datos_historial[0]['oth_revisor'];

           $filtro_persona['no_definido'] = TRUE;
           if (! $datos_historial) $filtro_persona['per_id'] = 0;
           else $filtro_persona['per_id'] = $datos_historial[0]['oth_profesional'];
           $datos_personales = navegacion::get_personas ($filtro_persona);
           $res[$fila]['nombre_profesional'] = $datos_personales[0]['per_nombres'];
           $res[$fila]['per_identificacion'] = $datos_personales[0]['per_identificacion'];

           if (! $datos_historial) $filtro_persona['per_id'] = 0;
           else $filtro_persona['per_id'] = $datos_historial[0]['oth_revisor'];
           $datos_personales = navegacion::get_personas ($filtro_persona);
           $res[$fila]['nombre_revisor']     = $datos_personales[0]['per_nombres'];

           //facturacion
           $facturado = 6;
           $filtro_facturacion['ordtra_id']           = $ordenes['ordtra_id'];
           $filtro_facturacion['ordtra_estado_orden'] = $facturado;
           $datos_facturacion  = combos_parametros::get_historial_orden_estado ($filtro_facturacion);
           if (! $datos_facturacion[0]['oth_valor_facturado']) $valor_facturado = 0;
           else $valor_facturado = $datos_facturacion[0]['oth_valor_facturado'];
           $res[$fila]['oth_valor_facturado'] = $valor_facturado;

           if ($estado_id == $re_asignado) {
             $filtro_persona['no_definido'] = TRUE;
             if (! $datos_historial) $filtro_persona['per_id'] = 0;
             else $filtro_persona['per_id'] = $datos_historial[0]['oth_profesional'];
             $datos_personales = navegacion::get_personas ($filtro_persona);
             $res[$fila]['nombre_re_profesional'] = $datos_personales[0]['per_nombres'];
             //$res[$fila]['per_identificacion'] = $datos_personales[0]['per_identificacion'];

             if (! $datos_historial) $filtro_persona['per_id'] = 0;
             else $filtro_persona['per_id'] = $datos_historial[0]['oth_revisor'];
             $datos_personales = navegacion::get_personas ($filtro_persona);
             $res[$fila]['nombre_re_revisor']     = $datos_personales[0]['per_nombres'];
           }
           $fila++;
        }
        return $res;
	}

  static function set_alertas_ordenes_trabajo ()    {
         $sin_profesional = 0; $sin_asignar = 1; $asignado = 2; $revision_informe = 3;
         $re_asignado = 4; $re_revision_informe = 5; $facturado = 6; $anulado = 7;  $finalizado = 8;
         $sin_alerta = 0; $en_alerta = 1;
         $fecha_actual = date('Y-m-d');
         $sql = "SELECT ordtra_id, ordtra_cliente, ordtra_sede, ordtra_solicitud, ordtra_candidato,
                        ordtra_servicio, ordtra_fecha_registro, ordtra_observaciones, ordtra_estado_orden,
                        ordtra_en_alerta,
                        sedsol_fecha_recepcion
                   FROM ordenes_trabajo, sede_solicitudes
                  WHERE ordtra_solicitud = sedsol_id
                    AND ordtra_estado_orden <> {$finalizado}
                    AND ordtra_estado_orden <> {$anulado};";
        $res = toba::db()->consultar($sql);
        $fila = 0;
        foreach ($res as $ordenes) {
           foreach ($ordenes as $orden => $value) {
             if ($orden == 'ordtra_id')              $orden_id        = $value;
             if ($orden == 'ordtra_servicio')        $servicio_id     = $value;
             if ($orden == 'ordtra_estado_orden')    $estado_orden    = $value;
             if ($orden == 'ordtra_solicitud')       $solicitud_id    = $value;
             if ($orden == 'sedsol_fecha_recepcion') $fecha_recepcion = $value;
           }
           $filtro_orden['ordtra_id']           = $orden_id;
           $filtro_orden['ordtra_estado_orden'] = $estado_orden;
           $fechaInicial = $fecha_recepcion;
           if ($estado_orden > $sin_asignar) {
             $datos_historial = combos_parametros::get_historial_orden_estado ($filtro_orden);
             $fechaInicial = $datos_historial[0]['oth_fecha_movimiento'];
           }
          //$tiene_historial = combos_parametros::get_total_ordenes_estado ($solicitud_id, $estado_orden);
           $datos_tiempo_limite = combos_parametros::get_limite_servicio_estado($servicio_id, $estado_orden);
           $fecha_limite_finalizacion = $fecha_actual;  $sw_alerta = $sin_alerta;
  //comparr fechas   if(mktime(0,0,0,$mes_pg,$dia_pg,$anyo_pg) <  mktime(0,0,0,$mes_pc, $dia_pc, $anyo_pc)) {
  list($anyo_pd,$mes_pd,$dia_pd)=split("-",$fechaInicial);
  $fecha_final  = $dia_pd.'-'.$mes_pd.'-'.$anyo_pd;
  if ($datos_tiempo_limite) $fecha_limite_finalizacion =  combos_parametros::get_incrementar_tiempo ($fecha_final, $dd=$datos_tiempo_limite[0]['set_tiempo_limite'], $mm= 0, $yy=0, $hh=0, $mn=0, $ss=0);
           if ($fecha_actual > $fecha_limite_finalizacion) $sw_alerta = $en_alerta;
           $sw_alerta = quote($sw_alerta);
           $sql = "UPDATE ordenes_trabajo SET ordtra_en_alerta = {$sw_alerta}
                    WHERE ordtra_id       = {$orden_id}
                      AND ordtra_servicio = {$servicio_id};";
           $res = toba::db()->consultar($sql);
           $fila++;
        }
  }

  static function get_ordenes_trabajo ($filtro=null)    {
         navegacion::set_alertas_ordenes_trabajo ();
         $where = array();
         if(isset($filtro['ordtra_id'])) {
           $where[] = "ordtra_id = ".quote("{$filtro['ordtra_id']}");
         }
         if(isset($filtro['ordtra_en_alerta'])) {
           $where[] = "ordtra_en_alerta = ".quote("{$filtro['ordtra_en_alerta']}");
         }
         if(isset($filtro['estord_id'])) {
           $where[] = "ordtra_estado_orden = ".quote("{$filtro['estord_id']}");
         }
         if(isset($filtro['cli_id'])) {
           $where[] = "ordtra_cliente = ".quote("{$filtro['cli_id']}");
         }
         if(isset($filtro['ordtra_servicio'])) {
           $where[] = "ordtra_servicio = ".quote("{$filtro['ordtra_servicio']}");
         }
         if(isset($filtro['sed_id'])) {
           $where[] = "ordtra_sede = ".quote("{$filtro['sed_id']}");
         }
         if(isset($filtro['solcan_solicitud'])) {
           $where[] = "ordtra_solicitud = ".quote("{$filtro['solcan_solicitud']}");
         }
         if(isset($filtro['solcan_candidato_id'])) {
           $where[] = "ordtra_candidato = ".quote("{$filtro['solcan_candidato_id']}");
         }
         if(isset($filtro['dep_id'])) {
           $where[] = "solcan_departamento = ".quote("{$filtro['dep_id']}");
         }
         if(isset($filtro['ciu_id'])) {
           $where[] = "solcan_ciudad = ".quote("{$filtro['ciu_id']}");
         }
         if(isset($filtro['cli_nombre'])) {
               $where[] = "cli_nombre ILIKE ".quote("%{$filtro['cli_nombre']}%");
         }
         if(isset($filtro['cli_nit'])) {
              $where[] = "cli_nit = ".quote("{$filtro['cli_nit']}");
         }
         if(isset($filtro['fecha_desde'])) {
              $where[] = "sedsol_fecha_recepcion >= ".quote("{$filtro['fecha_desde']}");
         }
         if(isset($filtro['fecha_hasta'])) {
              $where[] = "sedsol_fecha_recepcion <= ".quote("{$filtro['fecha_hasta']}");
         }
         if(isset($filtro['solcan_candidato'])) {
               $where[] = "solcan_candidato ILIKE ".quote("%{$filtro['solcan_candidato']}%");
         }
         if(isset($filtro['solcan_identificacion'])) {
              $where[] = "solcan_identificacion = ".quote("{$filtro['solcan_identificacion']}");
         }
         $sql = "SELECT ordtra_id, ordtra_cliente, ordtra_sede, ordtra_solicitud, ordtra_candidato,
                        ordtra_servicio, ordtra_fecha_registro, ordtra_observaciones, ordtra_estado_orden,
                        ordtra_en_alerta,
                        solcan_candidato_id, solcan_cliente, solcan_sede, solcan_solicitud,
                        solcan_departamento, solcan_ciudad, solcan_servicios,
                        solcan_identificacion, solcan_candidato, solcan_cargo_texto,
                        sed_nombre, cli_nombre,
                        dep_nombre, ciu_nombre, sed_departamento, sed_ciudad,
                        sedsol_cliente, cli_nit, sedsol_fecha_recepcion,
                        ser_nombre, estord_nombre
                   FROM ordenes_trabajo, solicitud_candidatos, sede_solicitudes, sedes, clientes,
                        departamentos, ciudades, servicios, estado_ordenes
                  WHERE ordtra_solicitud = solcan_solicitud AND solcan_solicitud = sedsol_id
                    AND ordtra_cliente = sedsol_cliente AND sedsol_cliente = cli_id
                    AND ordtra_sede = sedsol_sede AND sedsol_sede = sed_id
                    AND ordtra_candidato = solcan_candidato_id
                    AND ordtra_servicio = ser_id
                    AND ordtra_estado_orden = estord_id
                    AND (solcan_departamento = dep_id AND solcan_ciudad = ciu_id)
               ORDER BY sedsol_fecha_recepcion DESC, sedsol_cliente;";
        if(count($where)>0) {
           $sql = sql_concatenar_where($sql, $where);
        } //print_r($sql);
        $res = toba::db()->consultar($sql);

        $fila = 0;
        foreach ($res as $ordenes) {
           $filtro_facturacion = null; $filtro_persona = null;
           foreach ($ordenes as $orden => $value) {
             if ($orden == 'ordtra_id')    $orden_id = $value;
           }
           $datos_ciudad_sede = combos_parametros::get_ciudad_sede ($ordenes['ordtra_sede']);
           $res[$fila]['nombre_ciudad_sede']       = $datos_ciudad_sede[0]['ciu_nombre'];
           $res[$fila]['nombre_departamento_sede'] = $datos_ciudad_sede[0]['dep_nombre'];

           $datos_historial = combos_parametros::get_historial_orden_estado ($ordenes);
           $res[$fila]['oth_profesional'] = 0; $res[$fila]['oth_revisor'] = 0;
           if ($datos_historial) $res[$fila]['oth_profesional']    = $datos_historial[0]['oth_profesional'];
           if ($datos_historial) $res[$fila]['oth_revisor']        = $datos_historial[0]['oth_revisor'];

           $filtro_persona['no_definido'] = TRUE;
           if (! $datos_historial) $filtro_persona['per_id'] = 0;
           else $filtro_persona['per_id'] = $datos_historial[0]['oth_profesional'];
           $datos_personales = navegacion::get_personas ($filtro_persona);
           $res[$fila]['nombre_profesional'] = $datos_personales[0]['per_nombres'];
           $res[$fila]['per_identificacion'] = $datos_personales[0]['per_identificacion'];

           if (! $datos_historial) $filtro_persona['per_id'] = 0;
           else $filtro_persona['per_id'] = $datos_historial[0]['oth_revisor'];
           $datos_personales = navegacion::get_personas ($filtro_persona);
           $res[$fila]['nombre_revisor']     = $datos_personales[0]['per_nombres'];

           //facturacion
           $facturado = 6;
           $filtro_facturacion['ordtra_id']           = $ordenes['ordtra_id'];
           $filtro_facturacion['ordtra_estado_orden'] = $facturado;
           $datos_facturacion  = combos_parametros::get_historial_orden_estado ($filtro_facturacion);
           if (! $datos_facturacion[0]['oth_valor_facturado']) $valor_facturado = 0;
           else $valor_facturado = $datos_facturacion[0]['oth_valor_facturado'];
           $res[$fila]['oth_valor_facturado'] = $valor_facturado;
           $fila++;
        }
        return $res;
	}

  static function get_procentaje_solicitud_candidato ($filtro=null)    {
         $cancelado = 4;
         $where = array();
        // $where[] = "ordtra_estado_orden <> {$cancelado} ";
         if(isset($filtro['solcan_solicitud'])) {
           $where[] = "ordtra_solicitud = ".quote("{$filtro['solcan_solicitud']}");
         }
         if(isset($filtro['solcan_candidato_id'])) {
           $where[] = "ordtra_candidato = ".quote("{$filtro['solcan_candidato_id']}");
         }
         $sql = "SELECT AVG(0) AS porcentaje_finalizado
                   FROM ordenes_trabajo;";
        if(count($where)>0) {
           $sql = sql_concatenar_where($sql, $where);
        }   //print_r($sql);
        $res = toba::db()->consultar($sql);
        return $res;
	}

  static function get_solicitud_candidatos ($filtro=null)    {
         $where = array();
         if(isset($filtro['solcan_candidato_id'])) {
           $where[] = "solcan_candidato_id = ".quote("{$filtro['solcan_candidato_id']}");
         }
         if(isset($filtro['sedsol_id'])) {
           $where[] = "sedsol_id = ".quote("{$filtro['sedsol_id']}");
         }
         if(isset($filtro['estsol_id'])) {
           $where[] = "sedsol_estado = ".quote("{$filtro['estsol_id']}");
         }
         if(isset($filtro['solcan_servicios'])) {
               $where[] = "solcan_servicios ILIKE ".quote("%{$filtro['solcan_servicios']}%");
         }
         if(isset($filtro['cli_id'])) {
           $where[] = "sedsol_cliente = ".quote("{$filtro['cli_id']}");
         }
         if(isset($filtro['sed_id'])) {
           $where[] = "sedsol_sede = ".quote("{$filtro['sed_id']}");
         }
         if(isset($filtro['dep_id'])) {
           $where[] = "solcan_departamento = ".quote("{$filtro['dep_id']}");
         }
         if(isset($filtro['ciu_id'])) {
           $where[] = "solcan_ciudad = ".quote("{$filtro['ciu_id']}");
         }
         if(isset($filtro['cli_nombre'])) {
               $where[] = "cli_nombre ILIKE ".quote("%{$filtro['cli_nombre']}%");
         }
         if(isset($filtro['cli_nit'])) {
              $where[] = "cli_nit = ".quote("{$filtro['cli_nit']}");
         }
         if(isset($filtro['fecha_desde'])) {
              $where[] = "sedsol_fecha_recepcion >= ".quote("{$filtro['fecha_desde']}");
         }
         if(isset($filtro['fecha_hasta'])) {
              $where[] = "sedsol_fecha_recepcion <= ".quote("{$filtro['fecha_hasta']}");
         }
         if(isset($filtro['solcan_candidato'])) {
               $where[] = "solcan_candidato ILIKE ".quote("%{$filtro['solcan_candidato']}%");
         }
         if(isset($filtro['solcan_identificacion'])) {
              $where[] = "solcan_identificacion = ".quote("{$filtro['solcan_identificacion']}");
         }
         $sql = "SELECT solcan_candidato_id, solcan_cliente, solcan_sede, solcan_solicitud,
                        solcan_departamento, solcan_ciudad, solcan_servicios, solcan_estado,
                        solcan_identificacion, solcan_candidato, solcan_cargo_texto,
                        sed_ciudad, sed_departamento, sed_nombre, cli_nombre,
                        estsol_nombre, estsol_archivo,
                        dep_nombre, ciu_nombre, sed_departamento, sed_ciudad,
                        sedsol_cliente, cli_nit
                   FROM solicitud_candidatos, sede_solicitudes, sedes, clientes, estado_solicitudes,
                        departamentos, ciudades
                  WHERE solcan_solicitud = sedsol_id
                    AND sedsol_sede = sed_id AND sedsol_cliente = cli_id
                    AND solcan_estado = estsol_id
                    AND (solcan_departamento = dep_id AND solcan_ciudad = ciu_id)
               ORDER BY sedsol_fecha_recepcion DESC, sedsol_cliente;";
        if(count($where)>0) {
           $sql = sql_concatenar_where($sql, $where);
        } //print_r($sql);
        $res = toba::db()->consultar($sql);
        if ($res) {
          $datos_ciudad_sede = combos_parametros::get_ciudad_sede ($res[0]['solcan_sede']);
          $fila = 0;
          foreach ($res as $candidatos) {
             foreach ($candidatos as $candidato => $value) {
               if ($candidato == 'solcan_servicios')    $servicios = $value;
               if ($candidato == 'solcan_candidato_id') $candidato_id = $value;
               if ($candidato == 'solcan_solicitud')    $solicitud = $value;
             }
             $descripcion_servicios = combos_parametros::get_descripcion_servicios($servicios);
             $res[$fila]['ser_nombre']         = $descripcion_servicios['ser_nombre'];
             $filtro_orden = null;
             $filtro_orden['solcan_candidato_id'] = $candidato_id;
             $filtro_orden['solcan_solicitud']    = $solicitud;
             $cantidad_ordenes     = navegacion::get_ordenes_trabajo($filtro_orden);
             $filtro_orden['per_id']  = 0;
             $cantidad_sin_asignados = navegacion::get_ordenes_trabajo($filtro_orden);
             $res[$fila]['cantidad_asignados']    = (count($cantidad_ordenes) - count($cantidad_sin_asignados)).' de '.count($cantidad_ordenes);

             $filtro_solicitud['solcan_solicitud']    = $solicitud;
             $filtro_solicitud['solcan_candidato_id'] = $candidato_id;
             //$datos_solicitud_candidato = navegacion::get_datos_solicitud_candidatos($filtro_solicitud);
             //$cantidad_servicios = navegacion::get_cantidad_servicios_solicitud($datos_solicitud_candidato);
             //$porcentaje_avances = navegacion::get_procentaje_solicitud_candidato ($filtro_solicitud);
             //$res[$fila]['porcentaje_finalizado'] = $porcentaje_avances[0]['porcentaje_finalizado'];
             $res[$fila]['nombre_ciudad_sede'] = $datos_ciudad_sede[0]['ciu_nombre'];
             $res[$fila]['nombre_departamento_sede'] = $datos_ciudad_sede[0]['dep_nombre'];
             $fila++;
           }
        }
        return $res;
	}

  static function get_datos_solicitud_candidatos ($filtro=null)    {
         $cancelado = 4;
         $where = array();
         $where[] = "solcan_estado <> {$cancelado} ";
         if(isset($filtro['solcan_candidato_id'])) {
           $where[] = "solcan_candidato_id = ".quote("{$filtro['solcan_candidato_id']}");
         }
         if(isset($filtro['sedsol_id'])) {
           $where[] = "sedsol_id = ".quote("{$filtro['sedsol_id']}");
         }
         if(isset($filtro['estsol_id'])) {
           $where[] = "sedsol_estado = ".quote("{$filtro['estsol_id']}");
         }
         if(isset($filtro['cli_id'])) {
           $where[] = "sedsol_cliente = ".quote("{$filtro['cli_id']}");
         }
         if(isset($filtro['sed_id'])) {
           $where[] = "sedsol_sede = ".quote("{$filtro['sed_id']}");
         }
         if(isset($filtro['dep_id'])) {
           $where[] = "solcan_departamento = ".quote("{$filtro['dep_id']}");
         }
         if(isset($filtro['ciu_id'])) {
           $where[] = "solcan_ciudad = ".quote("{$filtro['ciu_id']}");
         }
         if(isset($filtro['cli_nombre'])) {
               $where[] = "cli_nombre ILIKE ".quote("%{$filtro['cli_nombre']}%");
         }
         if(isset($filtro['cli_nit'])) {
              $where[] = "cli_nit = ".quote("{$filtro['cli_nit']}");
         }
         if(isset($filtro['fecha_desde'])) {
              $where[] = "sedsol_fecha_recepcion >= ".quote("{$filtro['fecha_desde']}");
         }
         if(isset($filtro['fecha_hasta'])) {
              $where[] = "sedsol_fecha_recepcion <= ".quote("{$filtro['fecha_hasta']}");
         }
         if(isset($filtro['solcan_candidato'])) {
               $where[] = "solcan_candidato ILIKE ".quote("%{$filtro['solcan_candidato']}%");
         }
         if(isset($filtro['solcan_identificacion'])) {
              $where[] = "solcan_identificacion = ".quote("{$filtro['solcan_identificacion']}");
         }
         $sql = "SELECT solcan_candidato_id, solcan_cliente, solcan_sede, solcan_solicitud,
                        solcan_departamento, solcan_ciudad, solcan_servicios, solcan_estado,
                        solcan_identificacion, solcan_candidato,
                        sed_ciudad, sed_departamento, sed_nombre, cli_nombre,
                        estsol_nombre, estsol_archivo,
                        dep_nombre, ciu_nombre, sed_departamento, sed_ciudad,
                        sedsol_cliente, cli_nit
                   FROM solicitud_candidatos, sede_solicitudes, sedes, clientes, estado_solicitudes,
                        departamentos, ciudades
                  WHERE solcan_solicitud = sedsol_id
                    AND sedsol_sede = sed_id AND sedsol_cliente = cli_id
                    AND solcan_estado = estsol_id
                    AND (solcan_departamento = dep_id AND solcan_ciudad = ciu_id)
               ORDER BY sedsol_fecha_recepcion DESC, sedsol_cliente;";
        if(count($where)>0) {
           $sql = sql_concatenar_where($sql, $where);
        } //print_r($sql);
        $res = toba::db()->consultar($sql);
        return $res;
	}

  static function get_solicitudes ($filtro=null)    {
         $where = array();
         if(isset($filtro['sedsol_id'])) {
           $where[] = "sedsol_id = ".quote("{$filtro['sedsol_id']}");
         }
         if(isset($filtro['cli_id'])) {
           $where[] = "sedsol_cliente = ".quote("{$filtro['cli_id']}");
         }
         if(isset($filtro['sed_id'])) {
           $where[] = "sedsol_sede = ".quote("{$filtro['sed_id']}");
         }
         if(isset($filtro['cli_nombre'])) {
               $where[] = "cli_nombre ILIKE ".quote("%{$filtro['cli_nombre']}%");
         }
         if(isset($filtro['cli_nit'])) {
              $where[] = "cli_nit = ".quote("{$filtro['cli_nit']}");
         }
         if(isset($filtro['fecha_desde'])) {
              $where[] = "sedsol_fecha_recepcion >= ".quote("{$filtro['fecha_desde']}");
         }
         if(isset($filtro['fecha_hasta'])) {
              $where[] = "sedsol_fecha_recepcion <= ".quote("{$filtro['fecha_hasta']}");
         }
         $sql = "SELECT sedsol_id, sedsol_cliente, sedsol_sede, sedsol_fecha_recepcion,
                        sedsol_hora_recepcion, sedsol_fecha_registro, sedsol_descripcion,
                        sedsol_estado,
                        sed_nombre, cli_nombre, estsol_nombre, estsol_archivo,
                        dep_nombre, ciu_nombre, sed_departamento, sed_ciudad
                   FROM sede_solicitudes, sedes, clientes, estado_solicitudes,
                        departamentos, ciudades
                  WHERE sedsol_sede = sed_id AND sedsol_cliente = cli_id
                    AND sedsol_estado = estsol_id
                    AND (sed_departamento = dep_id AND sed_ciudad = ciu_id
                    AND sedsol_cliente = cli_id AND sedsol_sede = sed_id)
               ORDER BY sedsol_id DESC, sedsol_fecha_recepcion DESC, sedsol_cliente;";
        if(count($where)>0) {
           $sql = sql_concatenar_where($sql, $where);
        } //print_r($sql);
        $res = toba::db()->consultar($sql);
        $fila = 0;
        foreach ($res as $candidatos) {
           foreach ($candidatos as $candidato => $value) {
             if ($candidato == 'sedsol_id')    $solicitud = $value;
           }
           $filtro_orden['solcan_solicitud']    = $solicitud;
           $filtro_solicitud['sedsol_id']    = $solicitud;
           $datos_solicitud_candidato = navegacion::get_datos_solicitud_candidatos($filtro_solicitud);
           $cantidad_servicios = navegacion::get_cantidad_servicios_solicitud($datos_solicitud_candidato);
           $filtro_orden = null;
           $filtro_orden['solcan_solicitud']    = $solicitud;
           $cantidad_ordenes     = navegacion::get_ordenes_trabajo($filtro_orden);
           $filtro_orden['per_id']  = 0;
           $cantidad_sin_asignados = navegacion::get_ordenes_trabajo($filtro_orden);
           $res[$fila]['cantidad_ordenes'] = count($cantidad_ordenes).' de '.$cantidad_servicios;
           //$res[$fila]['cantidad_asignados'] = count($cantidad_sin_asignados);
           $filtro_solicitud['solcan_solicitud']    = $solicitud;
           $porcentaje_avances = navegacion::get_procentaje_solicitud_candidato ($filtro_solicitud);
           $res[$fila]['porcentaje_finalizado'] = $porcentaje_avances[0]['porcentaje_finalizado'];

           $fila++;
        }
        return $res;
	}

  static function get_cantidad_servicios_solicitud($solicitudes=null)    {
     $cantidad_servicios = 0;
     foreach ($solicitudes as $solicitud){
       foreach ($solicitud as $reg_solicitud => $value){
          if ($reg_solicitud == 'solcan_servicios') $servicios = $value;
       }
       $lista_servicios = explode(",", $servicios); //echo "<br>can ".count($lista_servicios);
       $cantidad_servicios = $cantidad_servicios + count($lista_servicios);
     }
     return $cantidad_servicios;
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
         if(isset($filtro['per_en_empresa'])) {
              $where[] = "per_en_empresa = ".quote("{$filtro['per_en_empresa']}");
         }
         if(isset($filtro['no_definido'])) {
              $where[] = "per_id >= 0";
         }else $where[] = "per_id > 0";
         $sql = "SELECT  per_id, per_tipo_documento, per_identificacion, per_nombres,
                         per_sexo, per_estado_civil, per_fecha_nacimiento, per_departamento,
                         per_ciudad, per_direccion, per_telefono, per_celular, per_mail,
                         per_fax, per_foto, per_estado, per_en_empresa,
                         estper_nombre, estper_archivo,
                         ciu_nombre, dep_nombre
                  FROM personas, ciudades, departamentos, estado_personas
                 WHERE per_ciudad = ciu_id
                   AND per_departamento = dep_id
                   AND per_estado = estper_id
              ORDER BY per_nombres;";
        if(count($where)>0) {
           $sql = sql_concatenar_where($sql, $where);
        } //print_r($sql);
        $res = consultar_fuente($sql);
        $fila = 0;
        foreach ($res as $personas) {
          foreach ($personas as $reg_persona => $value) {
             if ($reg_persona == 'per_id') $persona_id = $value;
          }
          $empresas_persona = combos_parametros::get_empresas_persona ($persona_id);
          $res[$fila]['empresa'] = $empresas_persona;
          $fila++;
        }
        $res = rs_ordenar_por_columna ($res, 'empresa');
        return $res;
	}

  static function get_contactos($filtro=null)    {
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
         if(isset($filtro['per_en_empresa'])) {
              $where[] = "per_en_empresa = ".quote("{$filtro['per_en_empresa']}");
         }
         if(isset($filtro['persed_cliente'])) {
              $where[] = "persed_cliente = ".quote("{$filtro['persed_cliente']}");
         }
         if(isset($filtro['persed_sede'])) {
              $where[] = "persed_sede = ".quote("{$filtro['persed_sede']}");
         }
         if(isset($filtro['no_definido'])) {
              $where[] = "per_id >= 0";
         }else $where[] = "per_id > 0";
         $sql = "SELECT  per_id, per_tipo_documento, per_identificacion, per_nombres,
                         per_sexo, per_estado_civil, per_fecha_nacimiento, per_departamento,
                         per_ciudad, per_direccion, per_telefono, per_celular, per_mail,
                         per_fax, per_foto, per_estado, per_en_empresa,
                         estper_nombre, estper_archivo,
                         ciu_nombre, dep_nombre,
                         persed_cliente, persed_sede, persed_persona,
                         cli_nombre, sed_nombre
                  FROM personas, ciudades, departamentos, estado_personas, persona_sedes,
                       clientes, sedes
                 WHERE per_ciudad = ciu_id
                   AND per_departamento = dep_id
                   AND per_estado = estper_id
                   AND persed_persona = per_id
                   AND persed_cliente = cli_id
                   AND persed_sede = sed_id
              ORDER BY per_nombres;";
        if(count($where)>0) {
           $sql = sql_concatenar_where($sql, $where);
        } //print_r($sql);
        $res = consultar_fuente($sql);
        return $res;
	}

  static function get_clientes ($filtro=null)    {
         $grupo_acceso = toba::usuario()->get_grupos_acceso();
         $grupo_actual = strtoupper($grupo_acceso[0]);
         $usuario      = toba::usuario()->get_id();
         if ($grupo_actual == 'CLIENTE') {
            $datos_cliente_logueado = combos_parametros::get_datos_cliente_logueado($usuario);
            $filtro['cli_id'] = $datos_cliente_logueado[0]['cli_id'];
         }
         $where = array();
         if(isset($filtro['cli_id'])) {
           $where[] = "cli_id = ".quote("{$filtro['cli_id']}");
         }
         if(isset($filtro['cli_nombre'])) {
               $where[] = "cli_nombre ILIKE ".quote("%{$filtro['cli_nombre']}%");
         }
         if(isset($filtro['cli_nit'])) {
              $where[] = "cli_nit = ".quote("{$filtro['cli_nit']}");
         }
         if(isset($filtro['cli_propietaria'])) {
              $where[] = "cli_propietaria = ".quote("{$filtro['cli_propietaria']}");
         }

         $sql = "SELECT cli_id, cli_nit, cli_nombre, cli_vigente,
                        (case when cli_propietaria = TRUE then 'PROPIETARIA' else 'CLIENTES' end) AS cli_propietaria
                   FROM clientes
              ORDER BY cli_propietaria DESC, cli_nombre;";
        if(count($where)>0) {
           $sql = sql_concatenar_where($sql, $where);
        } //print_r($sql);
        $res = toba::db()->consultar($sql);
        return $res;
	}

  static function get_cliente_sedes ($filtro=null)    {
         $where = array();
         if(isset($filtro['cli_id'])) {
           $where[] = "cli_id = ".quote("{$filtro['cli_id']}");
         }
         if(isset($filtro['cli_nombre'])) {
               $where[] = "cli_nombre ILIKE ".quote("%{$filtro['cli_nombre']}%");
         }
         if(isset($filtro['cli_nit'])) {
              $where[] = "cli_nit = ".quote("{$filtro['cli_nit']}");
         }
         if(isset($filtro['cli_propietaria'])) {
              $where[] = "cli_propietaria = ".quote("{$filtro['cli_propietaria']}");
         }

         $sql = "SELECT  cli_id, cli_nit, cli_nombre, cli_vigente, cli_propietaria,
                         sed_id, sed_cliente, sed_departamento, sed_ciudad, sed_nombre,
                         sed_direccion, sed_telefono, sed_observaciones,
                         sed_vigente, sed_celular, sed_fax, sed_mail,
                         sed_dia_facturacion, sed_dia_cierre, ciu_nombre, dep_nombre
                   FROM clientes, sedes, ciudades, departamentos
                  WHERE sed_cliente = cli_id
                    AND sed_ciudad = ciu_id
                    AND sed_departamento = dep_id
              ORDER BY cli_propietaria DESC, cli_nombre;";
        if(count($where)>0) {
           $sql = sql_concatenar_where($sql, $where);
        } //print_r($sql);
        $res = toba::db()->consultar($sql);
        return $res;
	}

  static function get_departamentos($filtro=null)    {
     $where = array();
     if(isset($filtro['dep_id'])) {
           $where[] = "dep_id = ".quote("{$filtro['dep_id']}");
     }
     if(isset($filtro['dep_nombre'])) {
           $where[] = "dep_nombre ILIKE ".quote("%{$filtro['dep_nombre']}%");
     }

     $sql = "SELECT dep_id, dep_nombre, dep_vigente
               FROM departamentos
              ORDER BY dep_nombre;";
     if(count($where)>0) {
        $sql = sql_concatenar_where($sql, $where);
     }
     return consultar_fuente($sql);
  }

  static function get_servicios($filtro=null)    {
     $where = array();
     if(isset($filtro['ser_id'])) {
           $where[] = "ser_id = ".quote("{$filtro['ser_id']}");
     }
     if(isset($filtro['ser_nombre'])) {
           $where[] = "ser_nombre ILIKE ".quote("%{$filtro['ser_nombre']}%");
     }
     $sql = "SELECT ser_id, ser_vigente, ser_nombre
               FROM servicios
           ORDER BY ser_nombre;";
     if(count($where)>0) {
        $sql = sql_concatenar_where($sql, $where);
     }
     $res = consultar_fuente($sql);
     $estado_ordenes = combos_total::get_estado_ordenes ();
     $fila = 0;
     foreach ($res as $servicios) {
       foreach ($servicios as $servicio => $value) {
         if ($servicio == 'ser_id') $ser_id = $value;
       }
       $filtro_tiempo['ser_id'] = $ser_id;
       foreach ($estado_ordenes as $ordenes) {
         foreach ($ordenes as $orden => $value) {
           if ($orden == 'estord_id') $estord_id = $value;
         }
         $filtro_tiempo['estord_id'] = $estord_id;
         switch ($estord_id) {
           case 1: $sin_asignar         = combos_parametros::get_tiempo_servicio_estado ($filtro_tiempo); break;
           case 2: $asignado            = combos_parametros::get_tiempo_servicio_estado ($filtro_tiempo); break;
           case 3: $revision_informe    = combos_parametros::get_tiempo_servicio_estado ($filtro_tiempo); break;
           case 4: $re_asignado         = combos_parametros::get_tiempo_servicio_estado ($filtro_tiempo); break;
           case 5: $re_revision_informe = combos_parametros::get_tiempo_servicio_estado ($filtro_tiempo); break;
           case 8: $finalizado          = combos_parametros::get_tiempo_servicio_estado ($filtro_tiempo); break;
           default: $sin_estado          = 0; break;
         }
       }
       $res[$fila]['tiempo_normal_entrega'] = $asignado + $revision_informe + $finalizado;
       $res[$fila]['tiempo_con_re_trabajo'] = $sin_asignar + $asignado + $revision_informe + $finalizado + $re_asignado + $re_revision_informe;
       $res[$fila]['tiempo_general']        = $sin_asignar + $asignado + $revision_informe + $finalizado;
       $fila++;
     }
     return $res;
  }
} //fin clase navegacion
?>
