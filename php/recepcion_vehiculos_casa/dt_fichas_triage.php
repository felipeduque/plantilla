<?php
class dt_fichas_triage extends toba_datos_tabla
{
	function get_listado($filtro=array())
	{
		$where = array();
		if (isset($filtro['fictri_id'])) {
			$where[] = "fictri_id = ".quote($filtro['fictri_id']);
		}
		if (isset($filtro['fictri_fecha'])) {
			$where[] = "fictri_fecha = ".quote($filtro['fictri_fecha']);
		}
		if (isset($filtro['fictri_paciente'])) {
			$where[] = "fictri_paciente = ".quote($filtro['fictri_paciente']);
		}
		if (isset($filtro['fictri_aux_identificacion'])) {
			$where[] = "fictri_aux_identificacion ILIKE ".quote("%{$filtro['fictri_aux_identificacion']}%");
		}
		$sql = "SELECT
			t_ft.fictri_id,
			t_ft.fictri_fecha,
			t_h.hor_nombre as fictri_hora_nombre,
			t_m.min_nombre as fictri_minuto_nombre,
			t_r.reg_nombre as fictri_registrado_nombre,
			t_p.pac_nombre_completo as fictri_paciente_nombre,
			t_ft.fictri_aux_primer_nombre,
			t_ft.fictri_aux_segundo_nombre,
			t_ft.fictri_aux_primer_apellido,
			t_ft.fictri_aux_segundo_apellido,
			t_td.tpd_nombre as fictri_aux_tipo_documento_nombre,
			t_ft.fictri_aux_identificacion,
			t_ft.fictri_aux_telefono,
			t_ft.fictri_aux_celular,
			t_ft.fictri_aux_direccion,
			t_ft.fictri_aux_nacimiento,
			t_e.eap_nit as fictri_eapbs_nombre,
			t_ft.fictri_motivo_consulta,
			t_ft.fictri_antecedentes,
			t_ft.fictri_escala_glasgow,
            t_ft.fictri_peso,
			t_ft.fictri_nivel_consciencia,
			t_ft.fictri_frecuencia_cardiaca,
			t_ft.fictri_frecuencia_fetal,
			t_ft.fictri_frecuencia_respira,
			t_ft.fictri_tas,
			t_ft.fictri_tad,
			t_ft.fictri_tam,
			t_ft.fictri_temperatura,
			t_ft.fictri_oximetria,
			t_ft.fictri_glucometria,
			t_tp.tippul_nombre as fictri_tipo_pulso_nombre,
			t_tt.tiptri_nombre as fictri_tipo_triage_nombre,
			t_ft.fictri_idx,
			t_tc.tipcon_nombre as fictri_tipo_conducta_nombre,
			t_te.tipesp_nombre as fictri_tipo_espera_nombre,
			t_ft.fictri_observacion,
			t_ft.fictri_fecha_registro,
			t_ft.fictri_usuario,
            n_c.nivcon_nombre
		FROM
			fichas_triage as t_ft	LEFT OUTER JOIN horas as t_h ON (t_ft.fictri_hora = t_h.hor_id)
			LEFT OUTER JOIN minutos as t_m ON (t_ft.fictri_minuto = t_m.min_id)
			LEFT OUTER JOIN registrados as t_r ON (t_ft.fictri_registrado = t_r.reg_id)
			LEFT OUTER JOIN pacientes as t_p ON (t_ft.fictri_paciente = t_p.pac_id)
			LEFT OUTER JOIN niveles_conciencia as n_c ON (t_ft.fictri_nivel_consciencia = n_c.nivcon_id)
			LEFT OUTER JOIN eapbs as t_e ON (t_ft.fictri_eapbs = t_e.eap_id)
			LEFT OUTER JOIN tipo_pulsos as t_tp ON (t_ft.fictri_tipo_pulso = t_tp.tippul_id)
			LEFT OUTER JOIN tipo_triages as t_tt ON (t_ft.fictri_tipo_triage = t_tt.tiptri_id)
			LEFT OUTER JOIN tipo_conductas as t_tc ON (t_ft.fictri_tipo_conducta = t_tc.tipcon_id)
			LEFT OUTER JOIN tipo_esperas as t_te ON (t_ft.fictri_tipo_espera = t_te.tipesp_id),
			tipo_documentos as t_td
		WHERE
				t_ft.fictri_aux_tipo_documento = t_td.tpd_id
		ORDER BY fictri_aux_primer_nombre";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
		return toba::db('triage_present')->consultar($sql);
	}

}

?>