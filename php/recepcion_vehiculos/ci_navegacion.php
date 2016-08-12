<?php
require_once('libreria/navegacion.php');
require_once('libreria/combos_parametros.php');
//----------------------------------------------------------------
class ci_navegacion extends toba_ci {
	protected $s__filtro;

	function get_relacion() 	{
		return $this->dependencia('datos');
	}

	function get_editor() 	{
		return $this->dependencia("editor");
	}

	function conf__edicion()	{
        $encabezado = toba::memoria()->get_dato_instancia('encabezado');
        $this->pantalla()->set_descripcion($encabezado,"info");
		if (! $this->get_relacion()->esta_cargada()) {
			$this->pantalla()->eliminar_evento('eliminar');
            //$this->pantalla()->eliminar_evento('imprimir_factura_venta');
		}
	}

	function evt__agregar() 	{
        toba::memoria()->set_dato_instancia('operacion', 'agregar');
        toba::memoria()->eliminar_dato_instancia('datos_encabezado');
		$this->set_pantalla('edicion');
	}

	function evt__eliminar() 	{
		$this->get_relacion()->eliminar();
		$this->set_pantalla('seleccion');
	}

	function evt__cancelar() 	{
		$this->get_editor()->disparar_limpieza_memoria();
		$this->get_relacion()->resetear();
		$this->set_pantalla('seleccion');
	}

	function evt__procesar() 	{
        $operacion = toba::memoria()->get_dato_instancia('operacion');
        $encabezado = $this->get_relacion()->tabla('servicio_encabezado')->get();
        $items_servicios  = $this->get_relacion()->tabla('servicio_detalles')->get_filas();
        if (($operacion != 'agregar') && (! $encabezado || ! $items_servicios)) {
          toba::notificacion()->agregar('<center>Datos del Servicio Incompleto<br>Revisar y Completar', 'error');
          return false;
        }
		$this->get_relacion()->sincronizar();
        //recuperar con claves
        $encabezado = $this->get_relacion()->tabla('servicio_encabezado')->get();
        $servicios  = $this->get_relacion()->tabla('servicio_detalles')->get_filas();
        $clave['serenc_id']            = $encabezado['serenc_id'];
        $clave['serenc_tipo_vehiculo'] = $encabezado['serenc_tipo_vehiculo'];
        $clave['serenc_vehiculo']      = $encabezado['serenc_vehiculo'];
        $mensaje = '<center>OK OPERACIÓN EXITOSA - EN SERVICIO ['.$encabezado['serenc_id'].']';

        $mensaje_agregar = null;
        if ($operacion == 'agregar') {
             $datos_servicio = navegacion::get_servicio_encabezado($clave);
             if (! $encabezado['encser_vehiculo']) {
               $filtro_vehiculo['veh_placa'] = $encabezado['aux_placa'];
               $existe_vehiculo = navegacion::get_vehiculos($filtro_vehiculo);
               if (! $existe_vehiculo[0]['veh_placa']) {
                  $nuevo_vehiculo = $this->set_crear_nuevo_vehiculo ($encabezado);
                  $clave['serenc_vehiculo'] = $nuevo_vehiculo['serenc_vehiculo'];
               }
             }
             toba::memoria()->set_dato_instancia('clave', $clave);
             $encabezado = $datos_servicio[0];
        }else $this->set_actualizar_vehiculo ($encabezado);
        $estado_ocupado = 2;
        $filtro_turno['tur_empleado'] = $encabezado['serenc_empleado'];
        $filtro_turno['estado_turno'] = $estado_ocupado;
        combos_parametros::set_estado_turno ($filtro_turno);
        $mensaje = '<center><h1>OK Operación exitosa <br> EN Servicio ['.$encabezado['serenc_id'].']';
        toba::notificacion()->agregar($mensaje, 'info');

        $this->get_relacion()->resetear();
        $this->set_pantalla('seleccion');
        //$this->evt__cuadro_encabezado__seleccion($clave);
	}

	//-------------------------------------------------------------------
	//-- DEPENDENCIAS
	//-------------------------------------------------------------------
	//-------- FILTRO ----
	function evt__filtro_encabezado__filtrar($datos)	{
        if (array_no_nulo($datos)) {
			$this->s__filtro = $datos;
		} else {
			toba::notificacion()->agregar('Favor Ingresar los Datos de Búsqueda', 'info');
		}
	}

	function conf__filtro_encabezado() 	{
		$this->s__mensaje['pantalla'] = 'SERVICIOS SOLAMENTE DURANTE EL DÍA '.date('Y-m-d');
        $subtitulo = 'BUSCAR POR LOS CAMPOS INDICADOS';
        $encabezado =  '<TABLE CELLSPACING=0 WIDTH=100%><CAPTION><H1><font color=green><CENTER>'.$this->s__mensaje['pantalla'].'</H1></CAPTION>'.
                        '<TR><TD><font color=green><CENTER><STRONG>'.$subtitulo.
                       '</TABLE>';
        $this->pantalla()->set_descripcion($encabezado, "info");
		if(isset($this->s__filtro)){
			return $this->s__filtro;
		}
	}

	function evt__filtro_encabezado__cancelar(){
		unset($this->s__filtro);
	}

	//-------- CUADRO ----
    function evt__cuadro_encabezado__finalizar($datos)	{
        $estado_finalizado = 3;
		$this->get_relacion()->cargar($datos);
        $encabezado = $this->get_relacion()->tabla('servicio_encabezado')->get();
        $datos['serenc_estado'] = $estado_finalizado;
        $this->dep('datos')->tabla('servicio_encabezado')->set($datos);
		$this->get_relacion()->sincronizar();
        $estado_libre = 1;
        $filtro_turno['tur_empleado'] = $encabezado['serenc_empleado'];
        $filtro_turno['estado_turno'] = $estado_libre;
        combos_parametros::set_estado_turno ($filtro_turno);

        $datos_encabezado = navegacion::get_servicio_encabezado($datos);
        $mensaje = '<center><h1>FINALIZADO SERVICIO ['.$encabezado['serenc_id'].'] <BR>PLACA ['.$datos_encabezado[0]['veh_placa'].']';
        $mensaje .= '<BR><FONT COLOR=blue>'.number_format($datos_encabezado[0]['total_servicios'],0);
        toba::notificacion()->agregar($mensaje, 'info');
		$this->get_relacion()->resetear();
	}

    function evt__cuadro_encabezado__anular($datos)	{
        $estado_finalizado = 4;
		$this->get_relacion()->cargar($datos);
        $encabezado = $this->get_relacion()->tabla('servicio_encabezado')->get();
        $datos['serenc_estado'] = $estado_finalizado;
        $this->dep('datos')->tabla('servicio_encabezado')->set($datos);
		$this->get_relacion()->sincronizar();
        $estado_libre = 1;
        $filtro_turno['tur_empleado'] = $encabezado['serenc_empleado'];
        $filtro_turno['estado_turno'] = $estado_libre;
        combos_parametros::set_estado_turno ($filtro_turno);
        $mensaje = '<center><h1>OK SERVICIO ['.$encabezado['serenc_id'].'] ANULADO';
        toba::notificacion()->agregar($mensaje, 'info');
		$this->get_relacion()->resetear();
	}

	function conf__cuadro_encabezado($cuadro) 	{
        $this->dep('cuadro_encabezado')->desactivar_modo_clave_segura();
        $this->s__filtro['serenc_fecha_actual'] = date('Y-m-d');
		if(isset($this->s__filtro)) {
			$datos = navegacion::get_servicio_encabezado($this->s__filtro);
		}
        if(! $this->s__filtro['datos_propietario']) $this->set_eliminar_columnas ();
        if ($datos) $cuadro->set_datos($datos);
	}

    function set_eliminar_columnas ()	{
        $columnas[] = 'veh_propietario_nombres';
        $columnas[] = 'veh_propietario_identificacion';
        $columnas[] = 'veh_direccion';
        $columnas[] = 'veh_fijo';
        $columnas[] = 'veh_movil';
        $columnas[] = 'veh_mail';
        $this->dep('cuadro_encabezado')->eliminar_columnas($columnas);
    }

	function evt__cuadro_encabezado__seleccion($id)	{
        toba::memoria()->set_dato_instancia('clave', $id);
        $datos = navegacion::get_servicio_encabezado($id);
        toba::memoria()->set_dato_instancia('datos_encabezado', $datos);
        toba::memoria()->set_dato_instancia('operacion', 'seleccion');
		$this->get_relacion()->cargar($id);
		$this->set_pantalla('edicion');
	}

    function set_crear_nuevo_vehiculo ($servicio_encabezado)     {
       //creamos el cliente nuevo
       $no_definido = 0;   $tipo_vehiculo = 1;
       if($servicio_encabezado['serenc_vehiculo'] == $no_definido) {
          if (! $servicio_encabezado['aux_placa']) $servicio_encabezado['aux_placa'] = 'NO Definido';
          $veh_tipo_vehiculo       = quote($servicio_encabezado['aux_tipo_vehiculo']);
          $veh_placa               = quote($servicio_encabezado['aux_placa']);
          $veh_propietario         = quote($servicio_encabezado['aux_nombre']);
          $veh_identificacion      = quote($servicio_encabezado['aux_identificacion']);
          $veh_direccion           = quote($servicio_encabezado['aux_direccion']);
          $veh_fijo                = quote($servicio_encabezado['aux_fijo']);
          $veh_movil               = quote($servicio_encabezado['aux_movil']);
          $veh_mail                = quote($servicio_encabezado['aux_mail']);


          $sql = " INSERT INTO vehiculos(
                               veh_tipo_vehiculo, veh_placa, veh_propietario_nombres,
                               veh_propietario_identificacion, veh_direccion, veh_fijo, veh_movil, veh_mail)
                   VALUES ({$veh_tipo_vehiculo}, {$veh_placa}, {$veh_propietario},
                           {$veh_identificacion}, {$veh_direccion}, {$veh_fijo}, {$veh_movil}, {$veh_mail});";
          consultar_fuente($sql);
          $vehiculo = recuperar_secuencia('vehiculos_veh_id_seq');
          toba::notificacion()->agregar('<center>Se crea el Vehículo con los datos básicos <br> ACTUALIZAR en la Interfaz de Vehiculos', 'info');
       }
       if($servicio_encabezado['serenc_id']) {
          $where[] = "serenc_id = ".quote("{$servicio_encabezado['serenc_id']}");
       }
       //pasamos a servicio encabezado
       $registrado = 1;
       $sql = " UPDATE servicio_encabezado
                   SET serenc_vehiculo   = {$vehiculo};";
       if(count($where)>0) {
         $sql = sql_concatenar_where($sql, $where);
       } //print_r($sql);
       consultar_fuente($sql);
       $clave['serenc_vehiculo'] = $vehiculo;
       return $clave;
      }

      function set_actualizar_vehiculo ($servicio_encabezado)     {
          $veh_tipo_vehiculo       = quote($servicio_encabezado['aux_tipo_vehiculo']);
          $veh_placa               = quote($servicio_encabezado['aux_placa']);
          $veh_propietario         = quote($servicio_encabezado['aux_nombre']);
          $veh_identificacion      = quote($servicio_encabezado['aux_identificacion']);
          $veh_direccion           = quote($servicio_encabezado['aux_direccion']);
          $veh_fijo                = quote($servicio_encabezado['aux_fijo']);
          $veh_movil               = quote($servicio_encabezado['aux_movil']);
          $veh_mail                = quote($servicio_encabezado['aux_mail']);

          $where = array();
          $where[] = "veh_id = ".quote("{$servicio_encabezado['serenc_vehiculo']}");
          $where[] = "veh_tipo_vehiculo = ".quote("{$servicio_encabezado['serenc_tipo_vehiculo']}");
          $sql = " UPDATE vehiculos
                               SET veh_propietario_nombres = {$veh_propietario},
                               veh_propietario_identificacion = {$veh_identificacion},
                               veh_direccion = {$veh_direccion},
                               veh_fijo      = {$veh_fijo},
                               veh_movil     = {$veh_movil},
                               veh_mail      = {$veh_mail};";
          if(count($where)>0) {
             $sql = sql_concatenar_where($sql, $where);
          } //print_r($sql);
          consultar_fuente($sql);
      }
}
?>
