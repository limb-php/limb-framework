CREATE TABLE `meta_data` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `url` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL,
  `title` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL,
  `description` text character set utf8 collate utf8_unicode_ci NOT NULL,
  `keywords` text character set utf8 collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `url` (`url`(20))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

