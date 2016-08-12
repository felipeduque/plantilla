<?php
require_once('libreria/navegacion.php');
require_once('libreria/combos_parametros.php');
//----------------------------------------------------------------
class ci_navegacion extends toba_ci {
	protected $s__filtro;
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
		$this->s__mensaje['pantalla'] = 'INFORMACIÓN DE SERVICIOS ENTRE FECHAS ';
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

	function conf__cuadro_encabezado($cuadro) 	{
        $this->dep('cuadro_encabezado')->desactivar_modo_clave_segura();
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

}
?>
