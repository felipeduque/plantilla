<?php
require_once('libreria/navegacion.php');
class ci_servicio_encabezado extends toba_ci  {
	protected $s__datos_filtro;

	//---- Filtro -----------------------------------------------------------------------

	function conf__filtro(toba_ei_formulario $filtro)	{
        $this->s__mensaje['pantalla'] = 'INFORMACIÓN DEL SERVICIO';
        $subtitulo = 'BUSCAR POR LOS CAMPOS INDICADOS - POR DEFECTO SE MUESTRAN LOS DEL DIA ACTUAL';
        $encabezado =  '<TABLE CELLSPACING=0 WIDTH=100%><CAPTION><H1><font color=green><CENTER>'.$this->s__mensaje['pantalla'].'</H1></CAPTION>'.
                        '<TR><TD><font color=green><CENTER><STRONG>'.$subtitulo.
                       '</TABLE>';
        $this->pantalla()->set_descripcion($encabezado, "info");
		if (isset($this->s__datos_filtro)) {
			$filtro->set_datos($this->s__datos_filtro);
		}
	}

	function evt__filtro__filtrar($datos)	{
		$this->s__datos_filtro = $datos;
	}

	function evt__filtro__cancelar()	{
		unset($this->s__datos_filtro);
	}

	//---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)	{
        $this->dep('cuadro')->desactivar_modo_clave_segura();
		if (isset($this->s__datos_filtro)) {
            $datos_encabezado = navegacion::get_servicio_encabezado($this->s__datos_filtro);
			$cuadro->set_datos($datos_encabezado);
		} else {
            $this->s__datos_filtro['serenc_fecha'] = date('Y-m-d');
			$datos_encabezado = navegacion::get_servicio_encabezado($this->s__datos_filtro);
			$cuadro->set_datos($datos_encabezado);
		}
	}

	function evt__cuadro__seleccion($datos)  	{
        $datos_encabezado = navegacion::get_servicio_encabezado($datos);
        toba::memoria()->set_dato_instancia('clave', $datos);
        toba::memoria()->set_dato_instancia('datos_encabezado', $datos_encabezado[0]);
		$this->dep('datos')->cargar($datos);
	}

	//---- Formulario -------------------------------------------------------------------

	function conf__formulario(toba_ei_formulario $form)  	{
		if ($this->dep('datos')->esta_cargada()) {
            $datos = $this->dep('datos')->tabla('servicio_encabezado')->get();
            $usuario = toba::usuario()->get_id();
            if ($datos['serenc_usuario'] != $usuario) {
               $form->set_solo_lectura();
               $form->eliminar_evento('modificacion');
               $form->eliminar_evento('baja');
               $this->s__mensaje['pantalla'] = 'INFORMACIÓN TRIAGE - SOLO DE CONSULTA';
               $encabezado =  '<TABLE CELLSPACING=0 WIDTH=100%><CAPTION><H1><font color=green><CENTER>'.$this->s__mensaje['pantalla'].'</H1></CAPTION>'.
                               '</TABLE>';
               $this->pantalla()->set_descripcion($encabezado, "info");
            }
			$form->set_datos($datos);
		}
	}

	function evt__formulario__alta($datos)	{
        $datos['serenc_usuario'] = toba::usuario()->get_id();
        $datos['serenc_fecha_registro'] = date('Y-m-d h:i');
        $datos = $this->get_completar_registro ($datos);
		$this->dep('datos')->tabla('servicio_encabezado')->set($datos);
		$this->dep('datos')->sincronizar();
        $datos_vehiculo = $this->dep('datos')->tabla('servicio_encabezado')->get();
        if (! $datos_vehiculo['serenc_registrado']) {
             $filtro_paciente['pac_identificacion'] = $datos_vehiculo['serenc_aux_identificacion'];
             $existe_paciente = navegacion::get_pacientes($filtro_paciente);
             if (! $existe_paciente[0]['pac_identificacion']) {
                $nuevo_paciente = $this->set_crear_nuevo_vehiculo ($datos_vehiculo);
             }
        }
        $this->s__datos_filtro['serenc_id'] = $datos_vehiculo['serenc_id'];
        $mensaje = '<center><H1>TRIAGE Nro. '.$datos_vehiculo['serenc_id'].'<BR>CORRECTAMENTE ALMACENADO';
        toba::notificacion()->agregar($mensaje, 'info');
		$this->resetear();
	}

    function set_crear_nuevo_vehiculo ($datos_vehiculo)     {
       //creamos el cliente nuevo
       $no_definido = 0;

       if($datos_vehiculo['serenc_id']) {
          $where[] = "serenc_id = ".quote("{$datos_vehiculo['serenc_id']}");
       }

       if ($datos_vehiculo['serenc_paciente'] == $no_definido) {
          if (! $datos_vehiculo['serenc_aux_identificacion']) $datos_vehiculo['serenc_aux_identificacion'] = '00000';
          if (! $datos_vehiculo['serenc_paciente']) $datos_vehiculo['serenc_paciente'] = $no_definido;

          $nombre_completo = $datos_vehiculo['serenc_aux_primer_apellido'].' '.$datos_vehiculo['serenc_aux_segundo_apellido'].' '.$datos_vehiculo['serenc_aux_primer_nombre'].' '.$datos_vehiculo['serenc_aux_segundo_nombre'];
          $nombre_completo = quote($nombre_completo);
          $datos_vehiculo = quote($datos_vehiculo);
          $sql = " INSERT INTO pacientes(
                                pac_tipo_documento, pac_identificacion, pac_fecha_nacimiento,
                                pac_primer_nombre, pac_segundo_nombre,
                                pac_primer_apellido, pac_segundo_apellido,
                                pac_nombre_completo,
                                pac_telefono, pac_celular,pac_direccion)
                   VALUES ({$datos_vehiculo['serenc_aux_tipo_documento']}, {$datos_vehiculo['serenc_aux_identificacion']}, {$datos_vehiculo['serenc_aux_nacimiento']},
                           {$datos_vehiculo['serenc_aux_primer_nombre']}, {$datos_vehiculo['serenc_aux_segundo_nombre']},
                           {$datos_vehiculo['serenc_aux_primer_apellido']}, {$datos_vehiculo['serenc_aux_segundo_apellido']},
                           {$nombre_completo},
                           {$datos_vehiculo['serenc_aux_telefono']}, {$datos_vehiculo['serenc_aux_celular']}, {$datos_vehiculo['serenc_aux_direccion']});";
          consultar_fuente($sql);
          $paciente = recuperar_secuencia('pacientes_pac_id_seq');
          toba::notificacion()->agregar('<center>Se crea el PACIENTE con los datos básicos <br> ACTUALIZAR en la Interfaz de Pacientes', 'info');

         //pasamos a
         $registrado = 1;
         $sql = " UPDATE  fichas_triage
                     SET  serenc_paciente             = {$paciente},
                          serenc_registrado           = {$registrado},
                          serenc_aux_primer_nombre    = null,
                          serenc_aux_segundo_nombre   = null,
                          serenc_aux_primer_apellido  = null,
                          serenc_aux_segundo_apellido = null,
                          serenc_aux_tipo_documento   = 0,
                          serenc_aux_identificacion   = null,
                          serenc_aux_telefono         = null,
                          serenc_aux_celular          = null,
                          serenc_aux_direccion        = null,
                          serenc_aux_nacimiento       = null;";
         if(count($where)>0) {
           $sql = sql_concatenar_where($sql, $where);
         } //print_r($sql);
         consultar_fuente($sql);
         $clave['serenc_id'] = $paciente;
         return $clave;
       }
    }

	function evt__formulario__modificacion($datos)	{
        $datos['serenc_usuario'] = toba::usuario()->get_id();
        $datos['serenc_fecha_registro'] = date('Y-m-d h:i');
        $datos = $this->get_completar_registro ($datos);
		$this->dep('datos')->tabla('servicio_encabezado')->set($datos);
		$this->dep('datos')->sincronizar();
        $datos_vehiculo = $this->dep('datos')->tabla('servicio_encabezado')->get();
        $this->s__datos_filtro['serenc_id'] = $datos_vehiculo['serenc_id'];
        $mensaje = '<center><H1>SERVICIO Nro. '.$datos_vehiculo['serenc_id'].'<BR>CORRECTAMENTE ACTUALIZADO';
        toba::notificacion()->agregar($mensaje, 'info');
		$this->resetear();
	}

    function get_completar_registro ($registro) 	{
        $no_registrado = 0;   $registrado = 1;
        if ($registro['aux_identificacion']){
          $filtro_cliente['pac_identificacion'] = $registro['serenc_aux_identificacion'];
          $datos_paciente = navegacion::get_pacientes ($filtro_cliente);
          if ($datos_cliente[0]['pac_identificacion']) {
            $registro['serenc_paciente'] = $datos_cliente[0]['pac_id'];
            $registro['serenc_registrado'] = $registrado;
          }
          elseif ($registro['serenc_aux_identificacion']) {
            $registro['serenc_paciente'] = 0;
          }
        }
        return $registro;
	}

	function evt__formulario__baja()  	{
		$this->dep('datos')->eliminar_todo();
		$this->resetear();
	}

	function evt__formulario__cancelar()	{
		$this->resetear();
	}

	function resetear()  	{
		$this->dep('datos')->resetear();
	}

    function conf__form_servicios()  {
        $datos_tipo_vehiculo = toba::memoria()->get_dato_instancia('servicio_encabezado');
        if (! $datos_tipo_vehiculo)  $this->dep('form_servicios')->set_ocultar_agregar();
        $datos = $this->dep('datos')->tabla('servicio_detalles')->get_filas();
        //$datos = rs_ordenar_por_columna ($datos, 'tvs_servicio');
        return  $datos;
    }

    function evt__form_servicios__modificacion($datos)  {
          $this->dep('datos')->tabla('servicio_detalles')->procesar_filas($datos);
    }

    function ajax__datos_pacinete($parametros, toba_ajax_respuesta $respuesta) {
        $datos_cliente = navegacion::get_pacientes($parametros);
        $parametros['veh_id'] = $datos_cliente[0]['veh_id'];
        if (! $datos_cliente) $parametros['veh_id'] = 0;
           $respuesta->set($parametros);
    }

}

?>