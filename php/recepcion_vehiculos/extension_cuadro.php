<?php
class extension_cuadro extends toba_ei_cuadro {
    function conf_evt__finalizar($evento, $fila) 	{
        $finalizado = 3; $anulado = 4;
        $estado_servicio = $this->datos[$fila]['serenc_estado'];
        if (($estado_servicio == $finalizado) || ($estado_servicio == $anulado)) {
          $evento->desactivar();
        }
        else $evento->activar();
	}

    function conf_evt__anular($evento, $fila) 	{
        $finalizado = 3; $anulado = 4;
        $estado_servicio = $this->datos[$fila]['serenc_estado'];
        if (($estado_servicio == $finalizado) || ($estado_servicio == $anulado)) {
          $evento->desactivar();
        }
        else $evento->activar();
	}
}
?>
