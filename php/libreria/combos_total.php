<?php
class combos_total {
  static function get_fecha_actual()	{
    return date ('d/m/Y');
  }

  static function get_hora_actual()	{
    return date ('H:i');
  }
  static function get_fecha_hora_actual()	{
    return date ('Y-m-d H:i');
  }

  static function get_requerimientos_sistema()	{
     $sql = "SELECT *
               FROM requerimientos_sistema;";
     return consultar_fuente($sql);
  }

  static function get_estado_servicios()	{
     $sql = "SELECT *
               FROM estado_servicios;";
     return consultar_fuente($sql);
  }

  static function get_perfiles()	{
     $sql = "SELECT *
               FROM perfiles;";
     return consultar_fuente($sql);
  }

  static function get_tipo_vehiculos()	{
     $sql = "SELECT *
               FROM tipo_vehiculos;";
     return consultar_fuente($sql);
  }

  static function get_servicios()	{
     $sql = "SELECT ser_id,
                    (case when ser_descripcion is not null then ('[ ' || ser_nombre || '] ' || ser_descripcion) else ser_nombre end) As ser_nombre
               FROM servicios;";
     return consultar_fuente($sql);
  }

} //fin clase combos_total
?>
