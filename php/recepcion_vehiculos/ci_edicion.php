<?php
require_once('libreria/navegacion.php');
class ci_edicion extends toba_ci {
    protected $s__mensaje;
    protected $s__referencia;

    function get_relacion()	 	{
		return $this->controlador->get_relacion();
	}

    function conf () 	{
        $mensaje = null;  $ayuda = null; $color = '#E6E6E6';
        switch ($this->get_id_pantalla()) {
              case 'servicio_encabezado':  $this->s__mensaje['pantalla'] = 'SERVICIOS X TIPO DE VEHÍCULOS'; break;
        }
        $operacion = toba::memoria()->get_dato_instancia('operacion');
        if ($operacion == 'agregar') {
           $factura = 'Nuevo';
           $encabezado =  '<TABLE CELLSPACING=0 WIDTH=100%><CAPTION><H1><font color=green>'.$this->s__mensaje['pantalla'].'</font></H1></CAPTION>'.
                             '<TR><TD WIDTH=15%><font color=red><B>Nro. Servicio</font><TD></B>'.$factura.
                          '</TABLE>';
           //$this->pantalla()->tab('servicio_detalles')->ocultar();
        }
        else {
          $clave = toba::memoria()->get_dato_instancia('clave');
          $datos = toba::memoria()->get_dato_instancia('datos_encabezado');
          $encabezado  =  '<TABLE CELLSPACING=0 WIDTH=100%><CAPTION><H1><font color=green>'.$this->s__mensaje['pantalla'].'</font></H1></CAPTION>'.
                            '<TR><TD WIDTH=20%><font color=red><B>Nro. SERVICIO</font><TD WIDTH=15%></B>'.$datos[0]['serenc_id'].
                                '<TD WIDTH=15%><B>PLACA<TD WIDTH=15%>'.$datos[0]['veh_placa']."</B>".
                                '<TD WIDTH=15%><B>TOTAL SERVICIOS<TD WIDTH=15% align=right>'.number_format($datos[0]['total_servicios'],0)."</B>".
                            '<TR><TD WIDTH=20%><B>FECHA SERVICIO<TD WIDTH=15%></B>'.$datos[0]['serenc_fecha'].
                                '<TD WIDTH=15%>'.'<B>CLIENTE<TD WIDTH=15%>'.$datos[0]['veh_propietario_nombres']."</B>".
                                '<TD WIDTH=15%><B>TOTAL COMPENSACION<TD WIDTH=15% align=right>'.number_format($datos[0]['total_compensacion'],0)."</B>".
                            '<TR><TD WIDTH=20%><B>ESTADO ACTUAL<TD WIDTH=15%></B>'.$datos[0]['estser_nombre'].
                                '<TD WIDTH=15%><B>TELEFONOS<TD WIDTH=15%>'.$datos[0]['veh_fijo']." - ".$datos[0]['veh_movil']."</B>".
                                '<TD WIDTH=15%><B>TOTAL SUMINISTROS<TD WIDTH=15% align=right>'.number_format($datos[0]['total_suministros'],0)."</B>".
                          '</TABLE>';        }
        $this->controlador()->pantalla()->set_descripcion($encabezado, "info");
	}

	//-------------------------------------------------------------------
	//--- Pantalla 'compras_encabezado'
	//-------------------------------------------------------------------
	function conf__form_encabezado() 	{
        $abierto = 1;
        $datos = $this->get_relacion()->tabla('servicio_encabezado')->get();
        if ($datos['serenc_estado'] && $datos['serenc_estado'] != $abierto) {
          //$this->dep('form_encabezado')->set_solo_lectura(null, true);
          //$this->dep('form_detalles')->set_solo_lectura(null, true);
          $this->dep('form_detalles')->desactivar_agregado_filas();
          $this->controlador()->pantalla()->eliminar_evento('eliminar');
          $this->controlador()->pantalla()->eliminar_evento('procesar');
          $this->dep('form_encabezado')->agregar_notificacion('<font color=red><b>Servicio Finalizado - Solo de Consulta', 'warning');
        }
        return $datos;
	}

	function evt__form_encabezado__modificacion($registro) 	{
        $no_registrado = 0; $registrado = 1;
        $registro['serenc_usuario'] = toba::usuario()->get_id();
        if ($registro['aux_placa'] && $registro['aux_tipo_vehiculo']) {
          $registro['aux_placa'] = str_replace(' ', '', $registro['aux_placa']);
          $filtro_vehiculo['veh_placa']         = $registro['aux_placa'];
          $filtro_vehiculo['veh_tipo_vehiculo'] = $registro['aux_tipo_vehiculo'];
          $datos_vehiculo = navegacion::get_vehiculos ($filtro_vehiculo);
          if ($datos_vehiculo[0]['veh_id']) {
            $registro['serenc_vehiculo']      = $datos_vehiculo[0]['veh_id'];
            $registro['serenc_tipo_vehiculo'] = $datos_vehiculo[0]['veh_tipo_vehiculo'];
          }
          elseif ($registro['aux_placa']) {
            $registro['serenc_vehiculo'] = 0; //$registro['serenc_tipo_vehiculo'] = 0;
            $registro['serenc_tipo_vehiculo'] = $registro['aux_tipo_vehiculo'];
          }
        }
        $abierta = 1;
        $operacion = toba::memoria()->get_dato_instancia('operacion');
        if ($operacion == 'agregar') $registro['serenc_estado'] = $abierta;
		$this->get_relacion()->tabla('servicio_encabezado')->set($registro);
	}

	//-------------------------------------------------------------------
	//--- Pantalla 'detalle de la cotizacion'
	//-------------------------------------------------------------------
	function conf__form_detalles()	{
         $datos = $this->get_relacion()->tabla('servicio_detalles')->get_filas();  //print_r($datos);
         //if ($datos) $this->dep('form_encabezado')->ef('serenc_tipo_vehiculo')->set_solo_lectura(true);
         return $datos;
	}

	function evt__form_detalles__modificacion($datos)	{
	    $this->get_relacion()->tabla('servicio_detalles')->procesar_filas($datos);
	}

    function ajax__datos_vehiculo($parametros, toba_ajax_respuesta $respuesta) {
        $datos_vehiculo = navegacion::get_vehiculos($parametros);
        $parametros = $datos_vehiculo[0];
        //$parametros['veh_tipo_vehiculo'] = $datos_vehiculo[0]['veh_tipo_vehiculo'];
        if (! $datos_vehiculo) $parametros['veh_id'] = 0;
           $respuesta->set($parametros);
    }
}
?>
