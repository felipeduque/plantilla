<?php
class ci_recordatorio_pwd extends toba_ci {
	protected $usuario;
	private   $error_cambio = '<center>Se produjo un error en el proceso de cambio de contraseña. <br>Por favor, contáctese con un administrador del sistema, gracias.';

	function ini() 	{

	}

	//-----------------------------------------------------------------------------------
	//---- Configuraciones --------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function conf__pant_inicial(toba_ei_pantalla $pantalla)	{

	}

	//-----------------------------------------------------------------------------------
	//---- formulario -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function conf__formulario(toba_ei_formulario $form)	{
        $this->usuario = toba::usuario()->get_id();
        $this->nombre  = toba::usuario()->get_nombre();
        $perfil_actual = toba::usuario()->get_perfiles_funcionales();
        $this->perfil  = $perfil_actual[0];
		if (isset($this->usuario) && (!is_null($this->usuario))) {
			$form->set_datos_defecto(array('usuario' => $this->usuario, 'nombre' => $this->nombre, 'perfil' => $this->perfil));
		}
	}

	//----------------------------------------------------------------------------------------
	//-------- Envio del email de solicitud de cambio ----------------------------------------
	//----------------------------------------------------------------------------------------
	function evt__formulario__enviar($datos) {
		if (! isset($datos['usuario'])) {
			throw new toba_error_autenticacion('No se suministro un usuario válido');
		}
        try {
			$this->guarda_clave_temporal($datos['usuario'], $datos['clave']);
            toba::notificacion()->agregar('<Center>Su Nueva Clave es <br>'.$datos['clave'], 'info');
			toba::instancia()->get_db()->cerrar_transaccion();
        } catch (toba_error $e) {
            toba::instancia()->get_db()->abortar_transaccion();
            throw new toba_error($this->error_cambio);
        }
	}

    function guarda_clave_temporal($usuario, $clave)    {
        $sql = "UPDATE apex_usuario
                   SET clave   = :clave, autentificacion = 'plano'
                 WHERE usuario = :usuario;";
        $id  = toba::instancia()->get_db()->sentencia_preparar($sql);
        toba::instancia()->get_db()->sentencia_ejecutar($id, array('usuario' => $usuario, 'clave' => $clave));
    }
}
?>
