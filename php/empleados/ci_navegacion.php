<?php
require_once('libreria/navegacion.php');
//----------------------------------------------------------------
class ci_navegacion extends toba_ci  {
	protected $s__filtro;

	function get_relacion() 	{
		return $this->dependencia('datos');
	}

	function get_editor()   	{
		return $this->dependencia('editor');
	}

	function conf__edicion() 	{
		if (! $this->get_relacion()->esta_cargada()) {
			$this->pantalla()->eliminar_evento('eliminar');
		}
		$hay_cambios = $this->get_relacion()->hay_cambios();
		toba::menu()->set_modo_confirmacion('Esta a punto de abandonar la edición de la persona sin grabar, ¿Desea continuar?', $hay_cambios);
	}

	function evt__agregar() 	{
        toba::memoria()->set_dato_instancia('operacion', 'agregar');
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
        $this->get_relacion()->sincronizar();
        $datos_persona  = $this->get_relacion()->tabla('personas')->get();
        $usuario_id     = $datos_persona['per_identificacion'];
        if ($datos_persona['per_perfil'] != 'lavador') {
          $existe_usuario = toba::usuario()->autenticar($usuario_id, $usuario_id);
          if (! $existe_usuario) $this->set_usuario_web ($datos_persona);
          $this->set_perfiles_web ($datos_persona);
        }
		toba::notificacion()->agregar('<h2><center>OK<br>Operación Exitosa','info');
        $this->get_relacion()->resetear();
        $clave['per_id'] = $datos_persona['per_id'];
        $this->evt__cuadro_personas__seleccion($clave);
	}

    function set_usuario_web ($datos_persona=null) 	{
      $proyecto = toba::proyecto()->get_id();
      toba::instancia()->agregar_usuario($datos_persona['per_identificacion'], $datos_persona['per_nombres'], $datos_persona['per_identificacion']);
      $mensaje = '<center>OK<br>El Usuario y Clave de Acceso es el Número de Identificación '.$datos_persona['per_identificacion'];
      $mensaje .= '<br>Cambiar la Clave en la Opción de Seguridad - Cambio de Clave';
      toba::notificacion()->agregar($mensaje,'info');
	}

    function set_perfiles_web ($datos_persona=null) 	{
      $proyecto = toba::proyecto()->get_id();
      $perfil_actual = toba::instancia()->get_perfiles_funcionales($datos_persona['per_identificacion'], $proyecto);
      if (strtoupper($perfil_actual[0]) != strtoupper($datos_persona['per_perfil'])) {
         $usuario  = $datos_persona['per_identificacion'];
         $nuevo_perfil = $datos_persona['per_perfil'];
         $accesos  = array ($proyecto => array($nuevo_perfil));
         $id_instancia = toba::instancia()->get_id();
         $modelo = toba_modelo_catalogo::instanciacion()->get_instancia($id_instancia);
	     $modelo->cambiar_acceso_usuario($usuario, $accesos);
         $mensaje .= '<center><br>Perfil asignado '.$nuevo_perfil;
         toba::notificacion()->agregar($mensaje,'info');
      }
	}

	//-------------------------------------------------------------------
	//-- DEPENDENCIAS
	//-------------------------------------------------------------------
	//-------- FILTRO ----
	function evt__filtro_personas__filtrar($datos)	{
		if (array_no_nulo($datos)) {
			$this->s__filtro = $datos;
		} else {
			toba::notificacion()->agregar('Favor Ingresar los Datos de Búsqueda', 'info');
		}
	}

	function conf__filtro_personas($filtro)	{
		$this->s__mensaje['pantalla'] = 'INFORMACIÓN EMPLEADOS';
        $subtitulo = 'BUSCAR POR LOS CAMPOS INDICADOS - ANTES DE INGRESAR UN NUEVO EMPLEADO';
        $encabezado =  '<TABLE CELLSPACING=0 WIDTH=100%><CAPTION><H1><font color=green><CENTER>'.$this->s__mensaje['pantalla'].'</H1></CAPTION>'.
                        '<TR><TD><font color=green><CENTER><STRONG>'.$subtitulo.
                       '</TABLE>';
        $this->pantalla()->set_descripcion($encabezado, "info");
		if (isset($this->s__filtro)) {
			$filtro->set_datos($this->s__filtro);
		}
	}

	function evt__filtro_personas__cancelar() 	{
		unset($this->s__filtro);
	}

	//-------- CUADRO ----
	function conf__cuadro_personas($cuadro) 	{
        $this->s__filtro['no_definido'] = FALSE;
		if (isset($this->s__filtro)) {
			$datos = navegacion::get_personas($this->s__filtro);
			$cuadro->set_datos($datos);
		}
		else{
		    $datos = navegacion::get_personas();
			$cuadro->set_datos($datos);
		}
	}

	function evt__cuadro_personas__seleccion($id) 	{
        toba::memoria()->set_dato_instancia('clave', $id);
        $datos_persona = navegacion::get_personas($id);
        toba::memoria()->set_dato_instancia('operacion', 'editar');
        toba::memoria()->set_dato_instancia('encabezado', $datos_persona);
		$this->get_relacion()->cargar($id);
        $this->set_pantalla('edicion');
	}
}
?>
