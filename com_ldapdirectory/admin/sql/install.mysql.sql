--
-- Table structure for table `jos_ldapd_mapping`
--

CREATE TABLE IF NOT EXISTS `#__ldapd_mapping` (
  `mid` bigint(20) NOT NULL auto_increment,
  `name` varchar(25) NOT NULL COMMENT 'Field Name',
  `displayname` varchar(25) NOT NULL COMMENT 'Display Name',
  `usereditable` tinyint(1) NOT NULL COMMENT 'Is the field user editable',
  `fromldap` tinyint(1) NOT NULL COMMENT 'LDAP Mapped',
  `ldapfield` varchar(25) NOT NULL COMMENT 'Field in LDAP to map',
  `ldapwins` tinyint(1) NOT NULL COMMENT 'LDAP overwrites local database',
  `checked_out` tinyint(1) NOT NULL,
  `checked_out_time` date NOT NULL,
  PRIMARY KEY  (`mid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `jos_ldapd_mapping`
--

INSERT INTO `#__ldapd_mapping` (`mid`, `name`, `displayname`, `usereditable`, `fromldap`, `ldapfield`, `ldapwins`, `checked_out`, `checked_out_time`) VALUES
(1, 'groupId', 'Users Group', 0, 0, '', 0, 0, '0000-00-00'),
(2, 'picture', 'Users Picture', 1, 0, '', 0, 0, '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `jos_ldapd_userdata`
--

CREATE TABLE IF NOT EXISTS `#__ldapd_userdata` (
  `id` bigint(20) NOT NULL auto_increment COMMENT 'key',
  `uid` bigint(20) NOT NULL COMMENT 'mapped from #__users',
  `mid` bigint(20) NOT NULL COMMENT 'mapped from #__ldapd_mapping',
  `data` longtext NOT NULL COMMENT 'Data',
  `blobdata` longblob,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

