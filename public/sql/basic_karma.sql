--
-- Table structure for table `karma_usuarios`
--

DROP TABLE IF EXISTS `karma_usuarios`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `karma_usuarios` (
  `id_usuario` mediumint(8) unsigned NOT NULL auto_increment,
  `login` varchar(255) NOT NULL,
  `pass` varchar(40) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `email` varchar(100) default NULL,
  `apellidos` varchar(150) default NULL,
  `fecha_nacimiento` datetime NOT NULL default '0000-00-00 00:00:00',
  `fecha_insert` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id_usuario`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `karma_roles`
--

DROP TABLE IF EXISTS `karma_roles`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `karma_roles` (
  `id_rol` mediumint(8) unsigned NOT NULL auto_increment,
  `rol` varchar(40) NOT NULL,
  `descripcion` varchar(255) default NULL,
  `borrado` enum('0','1') NOT NULL default '0',
  `fecha_insert` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id_rol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `karma_rel_roles_menus`
--

DROP TABLE IF EXISTS `karma_rel_roles_menus`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `karma_rel_roles_menus` (
  `id_rel` mediumint(8) unsigned NOT NULL auto_increment,
  `id_rol` mediumint(8) unsigned NOT NULL,
  `key_menu` varchar(50) NOT NULL,
  `fecha_insert` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id_rel`),
  KEY `id_rol` (`id_rol`),
  CONSTRAINT `karma_rel_roles_menus_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `karma_roles` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `karma_rel_usuarios_roles`
--

DROP TABLE IF EXISTS `karma_rel_usuarios_roles`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `karma_rel_usuarios_roles` (
  `id_rel` mediumint(8) unsigned NOT NULL auto_increment,
  `id_usuario` mediumint(8) unsigned NOT NULL,
  `id_rol` mediumint(8) unsigned NOT NULL,
  `fecha_insert` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id_rel`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_rol` (`id_rol`),
  CONSTRAINT `karma_rel_usuarios_roles_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `karma_usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `karma_rel_usuarios_roles_ibfk_2` FOREIGN KEY (`id_rol`) REFERENCES `karma_roles` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
