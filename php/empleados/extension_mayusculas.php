<?php
class extension_mayusculas extends toba_ei_formulario {
	function extender_objeto_js()	{
		echo "
            {$this->objeto_js}.evt__per_identificacion__procesar = function(es_inicial) {
                if (! es_inicial) {
                    var identificacion = this.ef('per_identificacion').get_estado();
                    var datos_persona = [];
                    datos_persona['per_identificacion'] = identificacion;
                    this.controlador.ajax('datos_persona', datos_persona, this, this.validar_persona);
                    return false;
                }
             }

             {$this->objeto_js}.validar_persona = function(datos_persona) {
                var per_id = datos_persona['per_id'];
                var operacion = datos_persona['operacion'];
                if (per_id > 0 && operacion == 1) {
                  alert('YA EXISTE UNA PERSONA CON IDENTIFICACION ' + datos_persona['per_identificacion'] + ' CAMBIAR O REGRESAR PARA BUSCARLA');
                  for (id_ef in this.efs()) { this.ef(id_ef).set_solo_lectura (true); }
                  this.ef('per_identificacion').set_solo_lectura (false);
                  this.ef('per_identificacion').seleccionar ();
                  return false;
                }
                for (id_ef in this.efs()) { this.ef(id_ef).set_solo_lectura (false); }
                return true;
            }

			{$this->objeto_js}.ini = function () {
				this.ef('per_nombres').input().keydown = function() {
					var ef = {$this->objeto_js}.ef('per_nombres');
					ef.set_estado(ef.get_estado().toUpperCase());
				}
			}
		  ";
		}
}
?>
