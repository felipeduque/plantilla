<?php
class extension_encabezado extends toba_ei_formulario {
    function extender_objeto_js()    {
        $encabezado = toba::memoria()->get_dato_instancia('datos_encabezado');
        echo 'var mi_arreglo_js = ' . toba_js::arreglo($encabezado);
        echo "
            {$this->objeto_js}.ini = function (es_inicial) {
                 var encabezado = mi_arreglo_js[0];
                 if (! es_inicial && (encabezado > 0)) {
                    this.ef('serenc_vehiculo').set_solo_lectura(true);
                 }
            }

            {$this->objeto_js}.evt__fictri_registrado__procesar = function (es_inicial) {
                 var registrados  = this.ef('fictri_registrado').get_estado();
                 var paciente = this.ef('serenc_vehiculo').get_estado();
                 if (es_inicial && paciente > 0) registrados = 1;
                 if (registrados != 'nopar') {
                   {$this->objeto_js}.ocultar_mostrar_registrados (registrados);
                 }
             }


             {$this->objeto_js}.evt__fictri_aux_identificacion__procesar = function(es_inicial) {
                if (! es_inicial) {
                    var identificacion = this.ef('fictri_aux_identificacion').get_estado();
                    var datos_vehiculo = [];
                    datos_vehiculo['veh_placa'] = identificacion;
                    this.controlador.ajax('datos_vehiculo', datos_vehiculo, this, this.validar_vehiculo);
                    return false;
                }
             }

             {$this->objeto_js}.validar_vehiculo = function(datos_vehiculo) {
                var veh_id = datos_vehiculo['pac_id'];
                var registrado = 1;
                var no_registrado = 0;
                if (veh_id > 0) {
                  var confirmar = confirm('YA EXISTE UN Vehiculo CON PLACA ' + datos_vehiculo['veh_placa'] + ' CONTINUAR COMO paciente REGISTRADO ?');
                  this.ef('fictri_aux_identificacion').set_estado (null);
                  if (confirmar) {
                    this.ef('serenc_vehiculo').set_estado (registrado);
                    this.ef('serenc_vehiculo').set_estado (veh_id);
                    {$this->objeto_js}.ocultar_mostrar_registrados (registrado);
                  }
                  return false;
                }
                this.ef('serenc_vehiculo').set_estado (no_registrado);
                {$this->objeto_js}.ocultar_mostrar_registrados (no_registrado);
                return true;
              }

              {$this->objeto_js}.ocultar_mostrar_registrados = function(registrados) {
                 if (registrados == 1) {
                    this.ef('serenc_vehiculo').mostrar();
                    this.ef('fictri_aux_primer_nombre').ocultar();
                    this.ef('fictri_aux_segundo_nombre').ocultar();
                    this.ef('fictri_aux_primer_apellido').ocultar();
                    this.ef('fictri_aux_segundo_apellido').ocultar();
                    this.ef('fictri_aux_tipo_documento').ocultar();

                    this.ef('fictri_aux_identificacion').ocultar();
                    this.ef('fictri_aux_telefono').ocultar();
                    this.ef('fictri_aux_celular').ocultar();
                    this.ef('fictri_aux_direccion').ocultar();
                    this.ef('fictri_aux_nacimiento').ocultar();
                  }
                  else {
                    var no_definido = 0;
                    this.ef('serenc_vehiculo').resetear_estado();
                    this.ef('serenc_vehiculo').ocultar();
                    this.ef('fictri_aux_primer_nombre').mostrar();
                    this.ef('fictri_aux_segundo_nombre').mostrar();
                    this.ef('fictri_aux_primer_apellido').mostrar();
                    this.ef('fictri_aux_segundo_apellido').mostrar();
                    this.ef('fictri_aux_tipo_documento').mostrar();

                    this.ef('fictri_aux_identificacion').mostrar();
                    this.ef('fictri_aux_telefono').mostrar();
                    this.ef('fictri_aux_celular').mostrar();
                    this.ef('fictri_aux_direccion').mostrar();
                    this.ef('fictri_aux_nacimiento').mostrar();
                  }
              }
          ";
        }
}
?>
