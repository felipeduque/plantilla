<?php
     //-- Se deja en sesion cual es la instancia que se necesita editar
     toba::memoria()->set_dato_instancia('instancia', toba::instancia()->get_id());
     //El parametro proyecto_hint marca cual es el proyecto por defecto a utilizar
     toba::memoria()->set_dato_instancia('proyecto_hint', toba::proyecto()->get_id());

     //-- Se pide la url de la operacion prinicipal de toba_usuarios
     $url = toba::vinculador()->get_url('toba_usuarios', '1000228', array(), array('celda_memoria' => 'usuarios'));

     if (isset($url) || $url != '') {
        echo toba_js::abrir();
        echo "
          var opciones = {'width': 1000, 'scrollbars' : 1, 'height': 600, 'resizable': 1};
          abrir_popup('usuarios', '$url', opciones);";
        echo toba_js::cerrar();
     } else {
          throw new toba_error('Para administrar usuarios es necesario tener cargado el proyecto toba_usuarios en la instancia');
}

?>
