<?php
  class extension_encabezado extends toba_ei_formulario   {
      function extender_objeto_js()    {
        $servicio_encabezado = toba::memoria()->get_dato_instancia('datos_encabezado');
        $datos_encabezado[0] = $servicio_encabezado[0]['serenc_vehiculo'];
        echo 'var mi_arreglo_js = ' . toba_js::arreglo($datos_encabezado);
        echo  "
              {$this->objeto_js}.ini = function (es_inicial) {
                var vehiculo_id = mi_arreglo_js[0];
                this.ef('aux_placa').input().onkeyup = function() {
					var ef = {$this->objeto_js}.ef('aux_placa');
					ef.set_estado(ef.get_estado().toUpperCase());
				}

                this.ef('aux_nombre').input().onkeyup = function() {
					var ef = {$this->objeto_js}.ef('aux_nombre');
					ef.set_estado(ef.get_estado().toUpperCase());
				}

                if (! es_inicial && (vehiculo_id > 0)) {
                    var datos_vehiculo = [];
                    datos_vehiculo['veh_id'] = vehiculo_id;
                    this.controlador.ajax('datos_vehiculo', datos_vehiculo, this, this.cargar_datos_vehiculo);
                    return false;
                }
			  }

              {$this->objeto_js}.evt__aux_placa__procesar = function(es_inicial) {
                if (! es_inicial) {
                    var placa = this.ef('aux_placa').get_estado();
                    var placa_final = placa.replace(/\s/g, '');   //quitamos espacios
                    this.ef('aux_placa').set_estado(placa_final);
                    placa = this.ef('aux_placa').get_estado();
                    var datos_vehiculo = [];
                    datos_vehiculo['veh_placa'] = placa;
                    this.controlador.ajax('datos_vehiculo', datos_vehiculo, this, this.cargar_datos_vehiculo);
                    return false;
                }
              }

              {$this->objeto_js}.cargar_datos_vehiculo = function(datos_vehiculo) {
                var vehiculo_id   = datos_vehiculo['veh_id'];
                var tipo_vehiculo = datos_vehiculo['veh_tipo_vehiculo'];
                if (vehiculo_id) {
                  if (datos_vehiculo['veh_placa'])         this.ef('aux_placa').set_estado (datos_vehiculo['veh_placa']);
                  if (datos_vehiculo['veh_tipo_vehiculo']) this.ef('aux_tipo_vehiculo').set_estado (tipo_vehiculo);
                  if (datos_vehiculo['veh_propietario_identificacion']) this.ef('aux_identificacion').set_estado (datos_vehiculo['veh_propietario_identificacion']);
                  if (datos_vehiculo['veh_propietario_nombres']) this.ef('aux_nombre').set_estado (datos_vehiculo['veh_propietario_nombres']);
                  if (datos_vehiculo['veh_direccion']) this.ef('aux_direccion').set_estado (datos_vehiculo['veh_direccion']);
                  if (datos_vehiculo['veh_fijo']) this.ef('aux_fijo').set_estado (datos_vehiculo['veh_fijo']);
                  if (datos_vehiculo['veh_movil']) this.ef('aux_movil').set_estado (datos_vehiculo['veh_movil']);
                  if (datos_vehiculo['veh_mail']) this.ef('aux_mail').set_estado (datos_vehiculo['veh_mail']);
                  //this.ef('aux_placa').set_solo_lectura(true);
                  //this.ef('aux_tipo_vehiculo').set_solo_lectura(true);
                }else{
                  var vacio = '';
                  this.ef('aux_identificacion').set_estado (vacio);
                  this.ef('aux_nombre').set_estado (vacio);
                  this.ef('aux_direccion').set_estado (vacio);
                  this.ef('aux_fijo').set_estado (vacio);
                  this.ef('aux_movil').set_estado (vacio);
                  this.ef('aux_mail').set_estado (vacio);
                }
                return true;
              }
		";
	  }
  }
?>
