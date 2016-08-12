<?php
  class extension_ml extends toba_ei_formulario_ml {
    function extender_objeto_js()      {
          echo  "
              {$this->objeto_js}.evt__emppre_valor_prestamo__procesar = function(es_inicial, fila) {
                  if (! es_inicial) {$this->objeto_js}.refrescar_saldo(es_inicial, fila);
              }

              {$this->objeto_js}.evt__emppre_valor_pago__procesar = function(es_inicial, fila) {
                  if (! es_inicial) {$this->objeto_js}.refrescar_saldo(es_inicial, fila);
              }

              {$this->objeto_js}.refrescar_saldo = function(es_inicial, fila) {
                  var prestamo = this.ef('emppre_valor_prestamo').ir_a_fila(fila).valor();
                  if (typeof(prestamo)!= 'number') prestamo = 0;
                  var pago     = this.ef('emppre_valor_pago').ir_a_fila(fila).valor();
                  if (typeof(pago)!= 'number') pago = 0;
                  var saldo = parseFloat(prestamo - pago);
                  this.ef('saldo').ir_a_fila(fila).cambiar_valor(saldo);
              }
           ";
      }
  }
?>
