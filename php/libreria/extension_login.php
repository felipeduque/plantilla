<?php
   require_once('libreria/combos_total.php');
   class extension_login extends toba_tp_logon {
        function pre_contenido()    {
            $datos_requerimientos = combos_total::get_requerimientos_sistema ();
            $version = '1.0';
            echo "<div class='login-titulo'>".toba_recurso::imagen_proyecto("logo_login.gif",true);
            echo "<div>".$datos_requerimientos[0]['reqsis_nombre_sistema']."</div>";
            echo "<div>versión ".$datos_requerimientos[0]['reqsis_version']."</div>";
            echo "</div>";
            echo "\n<div align='center' class='cuerpo'>\n";
	    }

        function post_contenido() {
            $datos_requerimientos = combos_total::get_requerimientos_sistema ();
            echo "</div>";
                echo "<div class='login-pie'>";
                echo "<div>Desarrollado por</div>";
                echo "<div style='color:#E3E3E3;'>".$datos_requerimientos[0]['reqsis_desarrollado_por']."</div>";
                echo "<div>Celular ".$datos_requerimientos[0]['reqsis_telefonos']."</div>";
                echo "<div>".$datos_requerimientos[0]['reqsis_mails']."</div>";
                echo "<div>Todos los Derechos Reservados.</div>";
            echo "</div>";
        }
   }
?>
