<?php
  class extension_detalle extends toba_ei_formulario_ml   {
      function extender_objeto_js()    {
        echo  "
              {$this->objeto_js}.iniciar_fila = function  (fila, agregar_tabindex, es_inicial) {
                 this.ef('serdet_tipo_vehiculo').ir_a_fila(fila).set_solo_lectura(true);
                 if (! es_inicial){
                   var tipo_vehiculo = this.controlador.dep('form_encabezado').ef('aux_tipo_vehiculo').get_estado();
                   this.ef('serdet_tipo_vehiculo').ir_a_fila(fila).cambiar_valor(tipo_vehiculo);
                   this.ef('serdet_tipo_vehiculo').ir_a_fila(fila).set_solo_lectura(true);
                   //this.controlador.dep('form_encabezado').ef('serenc_tipo_vehiculo').set_solo_lectura(true);
                 }
              }
        ";
    }
  }
?>