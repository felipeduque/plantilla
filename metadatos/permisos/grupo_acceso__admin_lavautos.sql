
------------------------------------------------------------
-- apex_usuario_grupo_acc
------------------------------------------------------------
INSERT INTO apex_usuario_grupo_acc (proyecto, usuario_grupo_acc, nombre, nivel_acceso, descripcion, vencimiento, dias, hora_entrada, hora_salida, listar, permite_edicion) VALUES (
	'lavautos', --proyecto
	'admin_lavautos', --usuario_grupo_acc
	'Administrador Lavautos', --nombre
	NULL, --nivel_acceso
	NULL, --descripcion
	NULL, --vencimiento
	NULL, --dias
	NULL, --hora_entrada
	NULL, --hora_salida
	NULL, --listar
	'0'  --permite_edicion
);

------------------------------------------------------------
-- apex_usuario_grupo_acc_item
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'lavautos', --proyecto
	'admin_lavautos', --usuario_grupo_acc
	NULL, --item_id
	'1'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'lavautos', --proyecto
	'admin_lavautos', --usuario_grupo_acc
	NULL, --item_id
	'2'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'lavautos', --proyecto
	'admin_lavautos', --usuario_grupo_acc
	NULL, --item_id
	'6575'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'lavautos', --proyecto
	'admin_lavautos', --usuario_grupo_acc
	NULL, --item_id
	'6576'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'lavautos', --proyecto
	'admin_lavautos', --usuario_grupo_acc
	NULL, --item_id
	'6577'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'lavautos', --proyecto
	'admin_lavautos', --usuario_grupo_acc
	NULL, --item_id
	'6587'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'lavautos', --proyecto
	'admin_lavautos', --usuario_grupo_acc
	NULL, --item_id
	'6607'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'lavautos', --proyecto
	'admin_lavautos', --usuario_grupo_acc
	NULL, --item_id
	'6608'  --item
);
--- FIN Grupo de desarrollo 0
