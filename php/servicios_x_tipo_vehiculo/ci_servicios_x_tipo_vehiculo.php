<?php
require_once('libreria/navegacion.php');
require_once('libreria/combos_total.php');
class ci_servicios_x_tipo_vehiculo extends toba_ci  {

    function ini__operacion ()  {
        $datos_tipo_vehiculo = toba::memoria()->eliminar_dato_instancia('datos_tipo_vehiculo');
    }

    function conf ()  {
        $datos_tipo_vehiculo = toba::memoria()->get_dato_instancia('datos_tipo_vehiculo');
        $this->s__mensaje['pantalla'] = 'SERVICIOS X TIPO DE VEHÍCULO';
        if ($datos_tipo_vehiculo) $this->s__mensaje['pantalla'] = 'SERVICIOS X TIPO DE VEHÍCULO - '.strtoupper ($datos_tipo_vehiculo[0]['tipveh_nombre']);
        $subtitulo = 'SI VA A ASIGNAR UN NUEVO SERVICIO, VERIFICAR QUE NO EXISTA';
        $encabezado =  '<TABLE CELLSPACING=0 WIDTH=100%><CAPTION><font color=green><H1><CENTER>'.$this->s__mensaje['pantalla'].'</H1></CAPTION>'.
                        '<TR><TD><font color=blue><CENTER><STRONG><H3>'.$subtitulo.
                       '</TABLE>';
        $this->pantalla()->set_descripcion($encabezado, "info");
	}
	//---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)  	{
       // toba::memoria()->eliminar_dato_instancia('datos_tipo_vehiculo');
		$datos = combos_total::get_tipo_vehiculos();
        $cuadro->set_datos($datos);
	}

	function evt__cuadro__seleccion($datos)	{
        $datos_tipo_vehiculos = navegacion::get_tipo_vehiculos($datos);
        toba::memoria()->set_dato_instancia('datos_tipo_vehiculo', $datos_tipo_vehiculos);
		$this->dep('datos')->cargar($datos);
	}

    function evt__cancelar()  	{
		$this->resetear();
	}

	function resetear()  	{
        toba::memoria()->eliminar_dato_instancia('datos_tipo_vehiculo');
		$this->dep('datos')->resetear();
	}

    function evt__procesar()	{
		$this->dep('datos')->sincronizar();
        toba::notificacion()->agregar('<h2><center>OK<br>Operación Exitosa', 'info');
		$this->resetear();
	}

     function conf__form_servicios()  {
        $datos_tipo_vehiculo = toba::memoria()->get_dato_instancia('datos_tipo_vehiculo');
        if (! $datos_tipo_vehiculo)  $this->dep('form_servicios')->set_ocultar_agregar();
        $datos = $this->dep('datos')->tabla('tipo_vehiculo_servicios')->get_filas();
        $datos = rs_ordenar_por_columna ($datos, 'tvs_servicio');
        return  $datos;
    }

    function evt__form_servicios__modificacion($datos)  {
          $this->dep('datos')->tabla('tipo_vehiculo_servicios')->procesar_filas($datos);
    }

}
?>