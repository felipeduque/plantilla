<?php
require_once('libreria/navegacion.php');
require_once('libreria/combos_parametros.php');
class ci_prestamos extends toba_ci  {
    protected $s__filtro;

    //-------- FILTRO ----
    function evt__filtro__filtrar($datos)    {
        if (array_no_nulo($datos)) {
            $this->s__filtro = $datos;
        } else {
            toba::notificacion()->agregar('Favor Ingresar los Datos de Búsqueda', 'info');
        }
    }

    function conf__filtro($filtro)    {
		if (isset($this->s__filtro)) {
			$filtro->set_datos($this->s__filtro);
		}
	}

	function evt__filtro__cancelar() 	{
		unset($this->s__filtro);
	}

    function ini__operacion ()  {
        $datos_tipo_vehiculo = toba::memoria()->eliminar_dato_instancia('datos_tipo_vehiculo');
    }

    function conf ()  {
        $this->s__mensaje['pantalla'] = 'PRESTAMOS Y LIQUIDACIÓN DEL DÍA ';
        $subtitulo = 'SOLO SE MUESTRAN LOS SERVICIOS CON ESTADO FINALIZADOS';
        $encabezado =  '<TABLE CELLSPACING=0 WIDTH=100%><CAPTION><font color=green><H1><CENTER>'.$this->s__mensaje['pantalla'].'</H1></CAPTION>'.
                        '<TR><TD><font color=blue><CENTER><STRONG><H3>'.$subtitulo.
                       '</TABLE>';
        $this->pantalla()->set_descripcion($encabezado, "info");
	}
	//---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)  	{
        $estado_finalizado = 3;
        $this->s__filtro['no_definido']   = true;
        $this->s__filtro['serenc_estado'] = $estado_finalizado;
        //$filtro['serenc_fecha']  = date('Y-m-d');
		if (isset($this->s__filtro)) {
		  $datos = combos_parametros::get_liquidar_empleado($this->s__filtro);
          $cuadro->set_datos($datos);
        }
	}

	function evt__cuadro__seleccion($datos)	{
        $estado_finalizado = 3;
        $this->s__filtro['serenc_estado']   = $estado_finalizado;
        $this->s__filtro['serenc_empleado'] = $datos['per_id'];
        $datos_empleado = combos_parametros::get_liquidar_empleado($datos);
        toba::memoria()->set_dato_instancia('datos_prestamos', $datos_empleado);
		$this->dep('datos')->cargar($datos);
	}

    function evt__cancelar()  	{
		$this->resetear();
	}

	function resetear()  	{
        toba::memoria()->eliminar_dato_instancia('datos_prestamos');
		$this->dep('datos')->resetear();
	}

    function evt__procesar()	{
		$this->dep('datos')->sincronizar();
        toba::notificacion()->agregar('<h2><center>OK<br>Operación Exitosa', 'info');
		$this->resetear();
	}

     function conf__form_prestamos()  {
        $datos_prestamos = toba::memoria()->get_dato_instancia('datos_prestamos');
        if (! $datos_prestamos)  $this->dep('form_prestamos')->set_ocultar_agregar();
        $datos = $this->dep('datos')->tabla('empleado_prestamos')->get_filas();
        return  $datos;
    }

    function evt__form_prestamos__modificacion($datos)  {
          $this->dep('datos')->tabla('empleado_prestamos')->procesar_filas($datos);
    }

    function evt__cuadro__paz_salvo($datos)	{
        $estado_finalizado = 3;

        $filtro_encabezado['serenc_estado']   = $estado_finalizado;
        $filtro_encabezado['serenc_empleado'] = $datos['per_id'];

        $datos_liquidacion = combos_parametros::get_liquidar_empleado($filtro_encabezado);

        $this->set_estado_servicios ($filtro_encabezado);
        $filtro_prestamo['emppre_empleado'] = $datos['per_id'];
        $this->set_eliminar_prestamos ($filtro_prestamo);

        $mensaje = '<center><h1>PAZ Y SALVO<br>'.$datos_liquidacion[0]['per_nombres'].'<br>PAGADO '.'<FONT COLOR=blue>'.number_format($datos_liquidacion[0]['saldo'],0);
        toba::notificacion()->agregar($mensaje, 'info');
		$this->resetear();
	}

    static function set_estado_servicios ($filtro=null)    {
        $where = array();
        if(isset($filtro['serenc_empleado'])) {
           $where[] = "serenc_empleado = ".quote("{$filtro['serenc_empleado']}");
        }
        if(isset($filtro['serenc_estado'])) {
           $where[] = "serenc_estado = ".quote("{$filtro['serenc_estado']}");
        }
        $estado_liquidado = 5;
        $sql = " UPDATE servicio_encabezado SET serenc_estado = {$estado_liquidado};";
        if(count($where)>0) {
           $sql = sql_concatenar_where($sql, $where);
        } //print_r($sql);
        $res = consultar_fuente($sql);
        return $res;
    }

    static function set_eliminar_prestamos ($filtro=null)    {
        $where = array();
        if(isset($filtro['emppre_empleado'])) {
           $where[] = "emppre_empleado = ".quote("{$filtro['emppre_empleado']}");
        }
        $sql = " DELETE FROM empleado_prestamos;";
        if(count($where)>0) {
           $sql = sql_concatenar_where($sql, $where);
        } //print_r($sql);
        $res = consultar_fuente($sql);
        return $res;
    }
}
?>