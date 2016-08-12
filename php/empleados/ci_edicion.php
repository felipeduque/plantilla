<?php
require_once('libreria/navegacion.php');
class ci_edicion extends toba_ci {
	function get_relacion()		{
		return $this->controlador->get_relacion();
	}

    function conf()      {
        $this->s__mensaje['pantalla'] = 'INFORMACIÓN PERSONAL DEL EMPLEADO';
        $operacion = toba::memoria()->get_dato_instancia('operacion');
        if ($operacion == 'agregar')  {
          $persona = 'Nuevo';
          $encabezado =  '<TABLE CELLSPACING=0 WIDTH=100%><CAPTION><H1><font color=green>'.$this->s__mensaje['pantalla'].'</font></H1></CAPTION>'.
                            '<TR><TD WIDTH=10%><B>Persona<TD><font color=red><b>'.$persona.
                        '</B></TABLE>';
        }
        else {
            $datos_encabezado = toba::memoria()->get_dato_instancia('encabezado');
            $encabezado =  '<TABLE CELLSPACING=0 WIDTH=100%><CAPTION><H1><font color=green>'.$this->s__mensaje['pantalla'].'</font></H1></CAPTION>'.
                           '<TR><TD WIDTH=10%><B>Identificación<TD></B>'.$datos_encabezado[0]['per_identificacion'].
                           '<TR><TD WIDTH=10%><B>Persona<TD><font color=blue><b>'.$datos_encabezado[0]['per_nombres'].
                           '</TABLE>';
        }
		$this->controlador()->pantalla()->set_descripcion($encabezado,"info");
	}

    //-------------------------------------------------------------------
	//--- Pantalla 'persona'
	//-------------------------------------------------------------------
	function conf__form_persona() {
		$datos = $this->get_relacion()->tabla('personas')->get();
		return $datos;
	}

	function evt__form_persona__modificacion($registro) 	{
        $this->get_relacion()->tabla('personas')->set($registro);
	}

    function ajax__datos_persona($parametros, toba_ajax_respuesta $respuesta) {
        $datos_persona = navegacion::get_personas($parametros);
        $parametros['per_id'] = $datos_persona[0]['per_id'];
        $operacion = toba::memoria()->get_dato_instancia('operacion');
        $parametros['operacion'] = 0;
        if ($operacion == 'agregar') $parametros['operacion'] = 1;
        if (! $datos_persona) $parametros['per_id'] = 0;
           $respuesta->set($parametros);
    }
}
?>
