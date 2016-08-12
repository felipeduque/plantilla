<?php
  class extension_encabezado extends toba_ei_formulario   {
      function extender_objeto_js()    {
        echo  "
              {$this->objeto_js}.ini = function () {
                this.ef('aux_placa').input().onkeyup = function() {
					var ef = {$this->objeto_js}.ef('aux_placa');
					ef.set_estado(ef.get_estado().toUpperCase());
				}

                this.ef('aux_nombre').input().onkeyup = function() {
					var ef = {$this->objeto_js}.ef('aux_nombre');
					ef.set_estado(ef.get_estado().toUpperCase());
				}
			  }

              {$this->objeto_js}.evt__serenc_estado__procesar = function (es_inicial) {
                 var anulado = 4;
                 var estado  = this.ef('serenc_estado').get_estado();
                 //if (estado == anulado) this.ef('serenc_observacion').mostrar();
                 //else   this.ef('serenc_observacion').ocultar();
              }

              {$this->objeto_js}.evt__aux_vehiculo_registrado__procesar = function (es_inicial) {
                 var registrados  = this.ef('aux_vehiculo_registrado').get_estado();
                 var vehiculo = this.ef('serenc_vehiculo').get_estado();
                 if (es_inicial && vehiculo > 0) registrados = 1;
                 if (registrados == 0 || registrados != 'nopar') {
                  {$this->objeto_js}.ocultar_mostrar_registrados (registrados);
                 }
              }

              {$this->objeto_js}.evt__serenc_vehiculo__procesar = function(es_inicial) {
                //if (! es_inicial) {
                    var vehiculo_id = this.ef('serenc_vehiculo').get_estado();
                    var datos_vehiculo = [];
                    datos_vehiculo['veh_id'] = vehiculo_id;
                    this.controlador.ajax('datos_vehiculo', datos_vehiculo, this, this.asignar_datos_vehiculo);
                    return false;
                //}
              }

              {$this->objeto_js}.asignar_datos_vehiculo = function(datos_vehiculo) {
                var vehiculo_id   = datos_vehiculo['veh_id'];
                var tipo_vehiculo = datos_vehiculo['veh_tipo_vehiculo'];
                if (tipo_vehiculo) {
                  this.ef('serenc_tipo_vehiculo').set_solo_lectura (false);
                  this.ef('serenc_tipo_vehiculo').set_estado (tipo_vehiculo);
                  //this.ef('serenc_tipo_vehiculo').set_solo_lectura (true);
                  var vacio = '';
                  if (datos_vehiculo['veh_propietario_identificacion']) this.ef('aux_identificacion').set_estado (datos_vehiculo['veh_propietario_identificacion']);
                  else this.ef('aux_identificacion').set_estado (vacio);
                  if (datos_vehiculo['veh_propietario_nombres']) this.ef('aux_nombre').set_estado (datos_vehiculo['veh_propietario_nombres']);
                  else this.ef('aux_nombre').set_estado (vacio);
                  if (datos_vehiculo['veh_direccion']) this.ef('aux_direccion').set_estado (datos_vehiculo['veh_direccion']);
                  else this.ef('aux_direccion').set_estado (vacio);
                  if (datos_vehiculo['veh_fijo']) this.ef('aux_fijo').set_estado (datos_vehiculo['veh_fijo']);
                  else this.ef('aux_fijo').set_estado (vacio);
                  if (datos_vehiculo['veh_movil']) this.ef('aux_movil').set_estado (datos_vehiculo['veh_movil']);
                  else this.ef('aux_movil').set_estado (vacio);
                  if (datos_vehiculo['veh_mail']) this.ef('aux_mail').set_estado (datos_vehiculo['veh_mail']);
                  else this.ef('aux_mail').set_estado (vacio);
                }
                return true;
              }

              {$this->objeto_js}.evt__aux_placa__procesar = function(es_inicial) {
                if (! es_inicial) {
                    var placa = this.ef('aux_placa').get_estado();
                    var datos_vehiculo = [];
                    datos_vehiculo['veh_placa'] = placa;
                    this.controlador.ajax('datos_vehiculo', datos_vehiculo, this, this.validar_vehiculo);
                    return false;
                }
              }

             {$this->objeto_js}.validar_vehiculo = function(datos_vehiculo) {
                var vehiculo_id   = datos_vehiculo['veh_id'];
                var tipo_vehiculo = datos_vehiculo['veh_tipo_vehiculo'];
                var registrado = 1;
                var no_registrado = 0;
                if (vehiculo_id > 0) {
                  var confirmar = confirm('YA EXISTE UN vehiculo CON PLACA ' + datos_vehiculo['veh_placa'] + ' CONTINUAR COMO vehiculo REGISTRADO ?');
                  this.ef('aux_placa').set_estado (null);
                  if (confirmar) {
                    this.ef('aux_vehiculo_registrado').set_estado (registrado);
                    this.ef('serenc_tipo_vehiculo').set_estado (tipo_vehiculo);
                    this.ef('serenc_vehiculo').set_estado (vehiculo_id);
                    this.ef('aux_placa').ocultar ();
                    {$this->objeto_js}.ocultar_mostrar_registrados (registrado);
                    {$this->objeto_js}.asignar_datos_vehiculo (datos_vehiculo);
                  }
                  return false;
                }
                this.ef('aux_vehiculo_registrado').set_estado (no_registrado);
                {$this->objeto_js}.ocultar_mostrar_registrados (no_registrado);
                return true;
              }

              {$this->objeto_js}.ocultar_mostrar_registrados = function(registrados) {
                  if (registrados == 1) {
                     this.ef('serenc_vehiculo').mostrar();
                     this.ef('aux_placa').ocultar();
                  }
                  else {
                     var no_definido = 0;
                     this.ef('serenc_vehiculo').resetear_estado();
                     this.ef('serenc_vehiculo').ocultar();
                     this.ef('aux_placa').mostrar();
                     var vacio = '';
                     this.ef('aux_identificacion').set_estado (vacio);
                     this.ef('aux_nombre').set_estado (vacio);
                     this.ef('aux_direccion').set_estado (vacio);
                     this.ef('aux_fijo').set_estado (vacio);
                     this.ef('aux_movil').set_estado (vacio);
                     this.ef('aux_mail').set_estado (vacio);
                  }
              }
		";
	  }
  }
?>
