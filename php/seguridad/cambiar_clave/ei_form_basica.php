<?php
class ei_form_basica extends toba_ei_formulario  {
	function extender_objeto_js()  {
		echo "
		//---- Validacion de EFs -----------------------------------
		{$this->objeto_js}.evt__clave__validar = function() {
			if (this.ef('clave').get_estado().indexOf(' ') != -1) {
               this.ef('clave').set_error('La Clave No puede contener espacios.');
               return false;
            }
            return true;
		}
		";
	}
}
?>