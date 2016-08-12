<?php
/**
 * Esta clase fue y será generada automáticamente. NO EDITAR A MANO.
 * @ignore
 */
class lavautos_autoload 
{
	static function existe_clase($nombre)
	{
		return isset(self::$clases[$nombre]);
	}

	static function cargar($nombre)
	{
		if (self::existe_clase($nombre)) { 
			 require_once(dirname(__FILE__) .'/'. self::$clases[$nombre]); 
		}
	}

	static protected $clases = array(
		'lavautos_ci' => 'extension_toba/componentes/lavautos_ci.php',
		'lavautos_cn' => 'extension_toba/componentes/lavautos_cn.php',
		'lavautos_datos_relacion' => 'extension_toba/componentes/lavautos_datos_relacion.php',
		'lavautos_datos_tabla' => 'extension_toba/componentes/lavautos_datos_tabla.php',
		'lavautos_ei_arbol' => 'extension_toba/componentes/lavautos_ei_arbol.php',
		'lavautos_ei_archivos' => 'extension_toba/componentes/lavautos_ei_archivos.php',
		'lavautos_ei_calendario' => 'extension_toba/componentes/lavautos_ei_calendario.php',
		'lavautos_ei_codigo' => 'extension_toba/componentes/lavautos_ei_codigo.php',
		'lavautos_ei_cuadro' => 'extension_toba/componentes/lavautos_ei_cuadro.php',
		'lavautos_ei_esquema' => 'extension_toba/componentes/lavautos_ei_esquema.php',
		'lavautos_ei_filtro' => 'extension_toba/componentes/lavautos_ei_filtro.php',
		'lavautos_ei_firma' => 'extension_toba/componentes/lavautos_ei_firma.php',
		'lavautos_ei_formulario' => 'extension_toba/componentes/lavautos_ei_formulario.php',
		'lavautos_ei_formulario_ml' => 'extension_toba/componentes/lavautos_ei_formulario_ml.php',
		'lavautos_ei_grafico' => 'extension_toba/componentes/lavautos_ei_grafico.php',
		'lavautos_ei_mapa' => 'extension_toba/componentes/lavautos_ei_mapa.php',
		'lavautos_servicio_web' => 'extension_toba/componentes/lavautos_servicio_web.php',
		'lavautos_comando' => 'extension_toba/lavautos_comando.php',
		'lavautos_modelo' => 'extension_toba/lavautos_modelo.php',
	);
}
?>