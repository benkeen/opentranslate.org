-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 02, 2013 at 08:08 PM
-- Server version: 5.5.9
-- PHP Version: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `opentranslate`
--

-- --------------------------------------------------------

--
-- Table structure for table `tr_accounts`
--

CREATE TABLE `tr_accounts` (
  `account_id` mediumint(8) unsigned NOT NULL,
  `account_type` enum('admin','project_manager','translator') CHARACTER SET latin1 NOT NULL DEFAULT 'translator',
  `status` enum('active','disabled','pending','blacklisted','deleted') CHARACTER SET latin1 NOT NULL DEFAULT 'active',
  `date_created` datetime NOT NULL,
  `created_by_account_id` mediumint(11) unsigned NOT NULL,
  `last_modified` datetime NOT NULL,
  `last_logged_in` datetime DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(30) NOT NULL,
  `ui_language_id` smallint(5) unsigned NOT NULL DEFAULT '25',
  `ui_num_data_per_page` smallint(5) unsigned DEFAULT '10',
  `receive_email_notifications` enum('yes','no') NOT NULL,
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tr_accounts`
--

INSERT INTO `tr_accounts` VALUES(1, 'admin', 'active', '0000-00-00 00:00:00', 0, '2011-08-10 14:49:58', '2013-02-02 19:31:55', 'Ben', 'Keen', 'ben.keen@gmail.com', 'j4opf0fpp9', 25, 10, 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `tr_account_settings`
--

CREATE TABLE `tr_account_settings` (
  `account_id` int(10) unsigned NOT NULL,
  `setting_name` varchar(50) CHARACTER SET latin1 NOT NULL,
  `setting_value` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`account_id`,`setting_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tr_account_settings`
--


-- --------------------------------------------------------

--
-- Table structure for table `tr_data`
--

CREATE TABLE `tr_data` (
  `data_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `data_category_order` smallint(6) NOT NULL DEFAULT '1',
  `version_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `data_label` varchar(255) NOT NULL,
  `data` text NOT NULL,
  `data_size` int(11) DEFAULT NULL,
  `comments_for_translators` mediumtext,
  `creation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_modified` datetime NOT NULL,
  `created_by_account_id` mediumint(11) unsigned NOT NULL,
  `use_html_editor` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`data_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tr_data`
--


-- --------------------------------------------------------

--
-- Table structure for table `tr_data_questions`
--

CREATE TABLE `tr_data_questions` (
  `question_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `creation_date` datetime NOT NULL,
  `response_to_question_id` mediumint(9) DEFAULT NULL,
  `status` enum('read','unread') NOT NULL DEFAULT 'unread',
  `project_id` mediumint(8) unsigned NOT NULL,
  `data_id` mediumint(8) unsigned NOT NULL,
  `language_id` mediumint(8) unsigned DEFAULT NULL,
  `account_id` varchar(10) NOT NULL,
  `subject` varchar(250) DEFAULT NULL,
  `message` mediumtext CHARACTER SET latin1,
  PRIMARY KEY (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tr_data_questions`
--


-- --------------------------------------------------------

--
-- Table structure for table `tr_data_translations`
--

CREATE TABLE `tr_data_translations` (
  `translation_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `data_id` int(10) unsigned NOT NULL DEFAULT '0',
  `translator_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `last_reviewer_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `language_id` int(10) unsigned NOT NULL DEFAULT '0',
  `translation` text NOT NULL,
  `percent_translated` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `translation_last_change_date` datetime NOT NULL,
  `translation_status` enum('in_review','completed') NOT NULL DEFAULT 'in_review',
  `review_count` smallint(2) unsigned NOT NULL DEFAULT '0',
  `approval_override_account_id` mediumint(9) DEFAULT NULL,
  `approval_override_date` datetime DEFAULT NULL,
  PRIMARY KEY (`translation_id`),
  KEY `data_id` (`data_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tr_data_translations`
--


-- --------------------------------------------------------

--
-- Table structure for table `tr_data_translation_history`
--

CREATE TABLE `tr_data_translation_history` (
  `translation_history_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `translation_id` int(10) unsigned NOT NULL DEFAULT '0',
  `account_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `translation` text NOT NULL,
  `reason_for_change` enum('new','invalid','edit') DEFAULT 'new',
  `change_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`translation_history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tr_data_translation_history`
--


-- --------------------------------------------------------

--
-- Table structure for table `tr_data_translation_reviews`
--

CREATE TABLE `tr_data_translation_reviews` (
  `review_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `translation_id` int(10) unsigned NOT NULL,
  `translator_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `review` enum('excellent','good','fair','poor','invalid') CHARACTER SET latin1 NOT NULL DEFAULT 'excellent',
  `date_reviewed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`review_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tr_data_translation_reviews`
--


-- --------------------------------------------------------

--
-- Table structure for table `tr_data_version_changes`
--

CREATE TABLE `tr_data_version_changes` (
  `version_id` mediumint(8) unsigned NOT NULL,
  `data_id` mediumint(8) unsigned NOT NULL,
  `new_data_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`version_id`,`data_id`,`new_data_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tr_data_version_changes`
--


-- --------------------------------------------------------

--
-- Table structure for table `tr_data_version_deletions`
--

CREATE TABLE `tr_data_version_deletions` (
  `data_id` mediumint(8) unsigned NOT NULL,
  `version_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`data_id`,`version_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tr_data_version_deletions`
--


-- --------------------------------------------------------

--
-- Table structure for table `tr_languages`
--

CREATE TABLE `tr_languages` (
  `language_id` smallint(8) unsigned NOT NULL AUTO_INCREMENT,
  `iso_639_code` char(2) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `google_translate_code` varchar(10) DEFAULT NULL,
  `language_name` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `rtl_language` enum('yes','no') NOT NULL DEFAULT 'no',
  `ui_version_available` enum('yes','no') CHARACTER SET latin1 NOT NULL DEFAULT 'no',
  PRIMARY KEY (`language_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=140 ;

--
-- Dumping data for table `tr_languages`
--

INSERT INTO `tr_languages` VALUES(1, 'AA', NULL, 'Afar', 'no', 'no');
INSERT INTO `tr_languages` VALUES(2, 'AB', NULL, 'Abkhazian', 'no', 'no');
INSERT INTO `tr_languages` VALUES(3, 'AF', 'af', 'Afrikaans', 'no', 'no');
INSERT INTO `tr_languages` VALUES(4, 'AM', '', 'Amharic', 'no', 'no');
INSERT INTO `tr_languages` VALUES(5, 'AR', 'ar', 'Arabic', 'no', 'no');
INSERT INTO `tr_languages` VALUES(6, 'AS', NULL, 'Assamese', 'no', 'no');
INSERT INTO `tr_languages` VALUES(7, 'AY', NULL, 'Aymara', 'no', 'no');
INSERT INTO `tr_languages` VALUES(8, 'AZ', '', 'Azerbaijani', 'no', 'no');
INSERT INTO `tr_languages` VALUES(9, 'BA', NULL, 'Bashkir', 'no', 'no');
INSERT INTO `tr_languages` VALUES(10, 'BE', 'be', 'Byelorussian', 'no', 'no');
INSERT INTO `tr_languages` VALUES(11, 'BG', 'bg', 'Bulgarian', 'no', 'no');
INSERT INTO `tr_languages` VALUES(12, 'BH', '', 'Bihari', 'no', 'no');
INSERT INTO `tr_languages` VALUES(13, 'BI', NULL, 'Bislama', 'no', 'no');
INSERT INTO `tr_languages` VALUES(14, 'BN', '', 'Bengali', 'no', 'no');
INSERT INTO `tr_languages` VALUES(15, 'BO', '', 'Tibetan', 'no', 'no');
INSERT INTO `tr_languages` VALUES(16, 'BR', NULL, 'Breton', 'no', 'no');
INSERT INTO `tr_languages` VALUES(17, 'CA', 'ca', 'Catalan', 'no', 'no');
INSERT INTO `tr_languages` VALUES(18, 'CO', NULL, 'Corsican', 'no', 'no');
INSERT INTO `tr_languages` VALUES(19, 'CS', 'cs', 'Czech', 'no', 'no');
INSERT INTO `tr_languages` VALUES(20, 'CY', 'cy', 'Welsh', 'no', 'no');
INSERT INTO `tr_languages` VALUES(21, 'DA', 'da', 'Danish', 'no', 'no');
INSERT INTO `tr_languages` VALUES(22, 'DE', 'de', 'German', 'no', 'no');
INSERT INTO `tr_languages` VALUES(23, 'DZ', NULL, 'Bhutani', 'no', 'no');
INSERT INTO `tr_languages` VALUES(24, 'EL', 'el', 'Greek', 'no', 'no');
INSERT INTO `tr_languages` VALUES(25, 'EN', 'en', 'English', 'no', 'yes');
INSERT INTO `tr_languages` VALUES(26, 'EO', '', 'Esperanto', 'no', 'no');
INSERT INTO `tr_languages` VALUES(27, 'ES', 'es', 'Spanish', 'no', 'no');
INSERT INTO `tr_languages` VALUES(28, 'ET', 'et', 'Estonian', 'no', 'no');
INSERT INTO `tr_languages` VALUES(29, 'EU', '', 'Basque', 'no', 'no');
INSERT INTO `tr_languages` VALUES(30, 'FA', 'fa', 'Persian', 'no', 'no');
INSERT INTO `tr_languages` VALUES(31, 'FI', 'fi', 'Finnish', 'no', 'no');
INSERT INTO `tr_languages` VALUES(32, 'FJ', NULL, 'Fiji', 'no', 'no');
INSERT INTO `tr_languages` VALUES(33, 'FO', NULL, 'Faeroese', 'no', 'no');
INSERT INTO `tr_languages` VALUES(34, 'FR', 'fr', 'French', 'no', 'no');
INSERT INTO `tr_languages` VALUES(35, 'FY', NULL, 'Frisian', 'no', 'no');
INSERT INTO `tr_languages` VALUES(36, 'GA', 'ga', 'Irish', 'no', 'no');
INSERT INTO `tr_languages` VALUES(37, 'GD', NULL, 'Gaelic', 'no', 'no');
INSERT INTO `tr_languages` VALUES(38, 'GL', 'gl', 'Galician', 'no', 'no');
INSERT INTO `tr_languages` VALUES(39, 'GN', '', 'Guarani', 'no', 'no');
INSERT INTO `tr_languages` VALUES(40, 'GU', NULL, 'Gujarati', 'no', 'no');
INSERT INTO `tr_languages` VALUES(41, 'HA', NULL, 'Hausa', 'no', 'no');
INSERT INTO `tr_languages` VALUES(42, 'HI', 'hi', 'Hindi', 'no', 'no');
INSERT INTO `tr_languages` VALUES(43, 'HR', 'hr', 'Croatian', 'no', 'no');
INSERT INTO `tr_languages` VALUES(44, 'HU', 'hu', 'Hungarian', 'no', 'no');
INSERT INTO `tr_languages` VALUES(45, 'HY', '', 'Armenian', 'no', 'no');
INSERT INTO `tr_languages` VALUES(46, 'IA', NULL, 'Interlingua', 'no', 'no');
INSERT INTO `tr_languages` VALUES(47, 'IE', NULL, 'Interlingue', 'no', 'no');
INSERT INTO `tr_languages` VALUES(48, 'IK', NULL, 'Inupiak', 'no', 'no');
INSERT INTO `tr_languages` VALUES(49, 'IN', 'id', 'Indonesian', 'no', 'no');
INSERT INTO `tr_languages` VALUES(50, 'IS', 'is', 'Icelandic', 'no', 'no');
INSERT INTO `tr_languages` VALUES(51, 'IT', 'it', 'Italian', 'no', 'no');
INSERT INTO `tr_languages` VALUES(52, 'IW', 'iw', 'Hebrew', 'no', 'no');
INSERT INTO `tr_languages` VALUES(53, 'JA', 'ja', 'Japanese', 'no', 'no');
INSERT INTO `tr_languages` VALUES(54, 'JI', 'yi', 'Yiddish', 'no', 'no');
INSERT INTO `tr_languages` VALUES(55, 'JW', NULL, 'Javanese', 'no', 'no');
INSERT INTO `tr_languages` VALUES(56, 'KA', '', 'Georgian', 'no', 'no');
INSERT INTO `tr_languages` VALUES(57, 'KK', '', 'Kazakh', 'no', 'no');
INSERT INTO `tr_languages` VALUES(58, 'KL', NULL, 'Greenlandic', 'no', 'no');
INSERT INTO `tr_languages` VALUES(59, 'KM', NULL, 'Cambodian', 'no', 'no');
INSERT INTO `tr_languages` VALUES(60, 'KN', '', 'Kannada', 'no', 'no');
INSERT INTO `tr_languages` VALUES(61, 'KO', 'ko', 'Korean', 'no', 'no');
INSERT INTO `tr_languages` VALUES(62, 'KS', NULL, 'Kashmiri', 'no', 'no');
INSERT INTO `tr_languages` VALUES(63, 'KU', '', 'Kurdish', 'no', 'no');
INSERT INTO `tr_languages` VALUES(64, 'KY', NULL, 'Kirghiz', 'no', 'no');
INSERT INTO `tr_languages` VALUES(65, 'LA', NULL, 'Latin', 'no', 'no');
INSERT INTO `tr_languages` VALUES(66, 'LN', NULL, 'Lingala', 'no', 'no');
INSERT INTO `tr_languages` VALUES(67, 'LO', '', 'Laothian', 'no', 'no');
INSERT INTO `tr_languages` VALUES(68, 'LT', 'lt', 'Lithuanian', 'no', 'no');
INSERT INTO `tr_languages` VALUES(69, 'LV', 'lv', 'Latvian', 'no', 'no');
INSERT INTO `tr_languages` VALUES(70, 'MG', NULL, 'Malagasy', 'no', 'no');
INSERT INTO `tr_languages` VALUES(71, 'MI', NULL, 'Maori', 'no', 'no');
INSERT INTO `tr_languages` VALUES(72, 'MK', 'mk', 'Macedonian', 'no', 'no');
INSERT INTO `tr_languages` VALUES(73, 'ML', '', 'Malayalam', 'no', 'no');
INSERT INTO `tr_languages` VALUES(74, 'MN', '', 'Mongolian', 'no', 'no');
INSERT INTO `tr_languages` VALUES(75, 'MO', NULL, 'Moldavian', 'no', 'no');
INSERT INTO `tr_languages` VALUES(76, 'MR', '', 'Marathi', 'no', 'no');
INSERT INTO `tr_languages` VALUES(77, 'MS', 'ms', 'Malay', 'no', 'no');
INSERT INTO `tr_languages` VALUES(78, 'MT', 'mt', 'Maltese', 'no', 'no');
INSERT INTO `tr_languages` VALUES(79, 'MY', '', 'Burmese', 'no', 'no');
INSERT INTO `tr_languages` VALUES(80, 'NA', NULL, 'Nauru', 'no', 'no');
INSERT INTO `tr_languages` VALUES(81, 'NE', '', 'Nepali', 'no', 'no');
INSERT INTO `tr_languages` VALUES(82, 'NL', 'nl', 'Dutch', 'no', 'no');
INSERT INTO `tr_languages` VALUES(83, 'NO', 'no', 'Norwegian', 'no', 'no');
INSERT INTO `tr_languages` VALUES(84, 'OC', NULL, 'Occitan', 'no', 'no');
INSERT INTO `tr_languages` VALUES(85, 'OM', NULL, 'Oromo', 'no', 'no');
INSERT INTO `tr_languages` VALUES(86, 'OR', '', 'Oriya', 'no', 'no');
INSERT INTO `tr_languages` VALUES(87, 'PA', '', 'Punjabi', 'no', 'no');
INSERT INTO `tr_languages` VALUES(88, 'PL', 'pl', 'Polish', 'no', 'no');
INSERT INTO `tr_languages` VALUES(89, 'PS', '', 'Pashto', 'no', 'no');
INSERT INTO `tr_languages` VALUES(90, 'PT', 'pt', 'Portuguese', 'no', 'no');
INSERT INTO `tr_languages` VALUES(91, 'QU', NULL, 'Quechua', 'no', 'no');
INSERT INTO `tr_languages` VALUES(92, 'RM', NULL, 'Rhaeto-Romance', 'no', 'no');
INSERT INTO `tr_languages` VALUES(93, 'RN', NULL, 'Kirundi', 'no', 'no');
INSERT INTO `tr_languages` VALUES(94, 'RO', 'ro', 'Romanian', 'no', 'no');
INSERT INTO `tr_languages` VALUES(95, 'RU', 'ru', 'Russian', 'no', 'no');
INSERT INTO `tr_languages` VALUES(96, 'RW', NULL, 'Kinyarwanda', 'no', 'no');
INSERT INTO `tr_languages` VALUES(97, 'SA', '', 'Sanskrit', 'no', 'no');
INSERT INTO `tr_languages` VALUES(98, 'SD', '', 'Sindhi', 'no', 'no');
INSERT INTO `tr_languages` VALUES(99, 'SG', NULL, 'Sangro', 'no', 'no');
INSERT INTO `tr_languages` VALUES(100, 'SH', NULL, 'Serbo-Croatian', 'no', 'no');
INSERT INTO `tr_languages` VALUES(101, 'SI', '', 'Singhalese', 'no', 'no');
INSERT INTO `tr_languages` VALUES(102, 'SK', 'sk', 'Slovak', 'no', 'no');
INSERT INTO `tr_languages` VALUES(103, 'SL', 'sl', 'Slovenian', 'no', 'no');
INSERT INTO `tr_languages` VALUES(104, 'SM', NULL, 'Samoan', 'no', 'no');
INSERT INTO `tr_languages` VALUES(105, 'SN', NULL, 'Shona', 'no', 'no');
INSERT INTO `tr_languages` VALUES(106, 'SO', NULL, 'Somali', 'no', 'no');
INSERT INTO `tr_languages` VALUES(107, 'SQ', 'sq', 'Albanian', 'no', 'no');
INSERT INTO `tr_languages` VALUES(108, 'SR', 'sr', 'Serbian', 'no', 'no');
INSERT INTO `tr_languages` VALUES(109, 'SS', NULL, 'Siswati', 'no', 'no');
INSERT INTO `tr_languages` VALUES(110, 'ST', NULL, 'Sesotho', 'no', 'no');
INSERT INTO `tr_languages` VALUES(111, 'SU', NULL, 'Sudanese', 'no', 'no');
INSERT INTO `tr_languages` VALUES(112, 'SV', 'sv', 'Swedish', 'no', 'no');
INSERT INTO `tr_languages` VALUES(113, 'SW', '', 'Swahili', 'no', 'no');
INSERT INTO `tr_languages` VALUES(114, 'TA', '', 'Tamil', 'no', 'no');
INSERT INTO `tr_languages` VALUES(115, 'TE', NULL, 'Tegulu', 'no', 'no');
INSERT INTO `tr_languages` VALUES(116, 'TG', '', 'Tajik', 'no', 'no');
INSERT INTO `tr_languages` VALUES(117, 'TH', 'th', 'Thai', 'no', 'no');
INSERT INTO `tr_languages` VALUES(118, 'TI', NULL, 'Tigrinya', 'no', 'no');
INSERT INTO `tr_languages` VALUES(119, 'TK', NULL, 'Turkmen', 'no', 'no');
INSERT INTO `tr_languages` VALUES(120, 'TL', 'tl', 'Filipino', 'no', 'no');
INSERT INTO `tr_languages` VALUES(121, 'TN', NULL, 'Setswana', 'no', 'no');
INSERT INTO `tr_languages` VALUES(122, 'TO', NULL, 'Tonga', 'no', 'no');
INSERT INTO `tr_languages` VALUES(123, 'TR', 'tr', 'Turkish', 'no', 'no');
INSERT INTO `tr_languages` VALUES(124, 'TS', NULL, 'Tsonga', 'no', 'no');
INSERT INTO `tr_languages` VALUES(125, 'TT', NULL, 'Tatar', 'no', 'no');
INSERT INTO `tr_languages` VALUES(126, 'TW', NULL, 'Twi', 'no', 'no');
INSERT INTO `tr_languages` VALUES(127, 'UK', 'uk', 'Ukrainian', 'no', 'no');
INSERT INTO `tr_languages` VALUES(128, 'UR', '', 'Urdu', 'no', 'no');
INSERT INTO `tr_languages` VALUES(129, 'UZ', '', 'Uzbek', 'no', 'no');
INSERT INTO `tr_languages` VALUES(130, 'VI', 'vi', 'Vietnamese', 'no', 'no');
INSERT INTO `tr_languages` VALUES(131, 'VO', NULL, 'Volapuk', 'no', 'no');
INSERT INTO `tr_languages` VALUES(132, 'WO', NULL, 'Wolof', 'no', 'no');
INSERT INTO `tr_languages` VALUES(133, 'XH', NULL, 'Xhosa', 'no', 'no');
INSERT INTO `tr_languages` VALUES(134, 'YO', NULL, 'Yoruba', 'no', 'no');
INSERT INTO `tr_languages` VALUES(135, 'ZH', 'zh-CN', 'Simplified Chinese', 'no', 'no');
INSERT INTO `tr_languages` VALUES(136, 'ZU', NULL, 'Zulu', 'no', 'no');
INSERT INTO `tr_languages` VALUES(137, 'ZH', 'zh-TW', 'Traditional Chinese', 'no', 'no');
INSERT INTO `tr_languages` VALUES(138, 'PT', NULL, 'Portuguese (Brazilian)', 'no', 'no');
INSERT INTO `tr_languages` VALUES(139, 'PT', NULL, 'Portuguese (European)', 'no', 'no');

-- --------------------------------------------------------

--
-- Table structure for table `tr_logs_export`
--

CREATE TABLE `tr_logs_export` (
  `project_id` int(11) NOT NULL,
  `version_id` int(11) NOT NULL,
  `language_id` mediumint(9) NOT NULL,
  `export_date` datetime NOT NULL,
  `translations_last_modified_date` datetime NOT NULL,
  PRIMARY KEY (`project_id`,`version_id`,`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tr_logs_export`
--


-- --------------------------------------------------------

--
-- Table structure for table `tr_projects`
--

CREATE TABLE `tr_projects` (
  `project_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `status` enum('new','offline','online','archived') CHARACTER SET latin1 NOT NULL DEFAULT 'new',
  `name` varchar(255) NOT NULL,
  `description` mediumtext,
  `translator_notes` mediumtext,
  `creation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `origin_language_id` smallint(9) NOT NULL DEFAULT '0',
  `project_visibility` enum('public','private') CHARACTER SET latin1 NOT NULL DEFAULT 'public',
  `trust_threshold` tinyint(4) NOT NULL DEFAULT '3',
  `translator_blacklist_threshold` tinyint(4) NOT NULL DEFAULT '3',
  `anonymous_translators` enum('yes','no') NOT NULL DEFAULT 'yes',
  `enable_ftp` enum('yes','no') NOT NULL DEFAULT 'no',
  `ftp_settings_confirmed` enum('yes','no') NOT NULL DEFAULT 'no',
  `ftp_hostname` varchar(250) DEFAULT NULL,
  `ftp_site_folder` varchar(250) DEFAULT NULL,
  `ftp_username` varchar(100) DEFAULT NULL,
  `ftp_password` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tr_projects`
--


-- --------------------------------------------------------

--
-- Table structure for table `tr_project_banned_translators`
--

CREATE TABLE `tr_project_banned_translators` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `version_id` int(11) unsigned DEFAULT NULL,
  `language_id` int(11) unsigned DEFAULT NULL,
  `translator_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tr_project_banned_translators`
--


-- --------------------------------------------------------

--
-- Table structure for table `tr_project_categories`
--

CREATE TABLE `tr_project_categories` (
  `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL DEFAULT '0',
  `category_name` varchar(255) NOT NULL,
  `category_order` smallint(5) unsigned NOT NULL DEFAULT '1',
  `parent_category_id` mediumint(9) NOT NULL DEFAULT '0',
  `export_only` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tr_project_categories`
--


-- --------------------------------------------------------

--
-- Table structure for table `tr_project_languages`
--

CREATE TABLE `tr_project_languages` (
  `project_id` mediumint(8) unsigned NOT NULL,
  `language_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`project_id`,`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tr_project_languages`
--


-- --------------------------------------------------------

--
-- Table structure for table `tr_project_managers`
--

CREATE TABLE `tr_project_managers` (
  `project_manager_id` mediumint(8) unsigned NOT NULL,
  `can_create_projects` enum('yes','no') CHARACTER SET latin1 NOT NULL DEFAULT 'no',
  `can_create_project_manager_accounts` enum('yes','no') CHARACTER SET latin1 NOT NULL DEFAULT 'yes',
  `can_create_translator_accounts` enum('yes','no') CHARACTER SET latin1 NOT NULL DEFAULT 'yes',
  `can_export_data` enum('yes','no') CHARACTER SET latin1 NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`project_manager_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tr_project_managers`
--


-- --------------------------------------------------------

--
-- Table structure for table `tr_project_manager_projects`
--

CREATE TABLE `tr_project_manager_projects` (
  `account_id` int(10) unsigned NOT NULL,
  `project_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`account_id`,`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tr_project_manager_projects`
--


-- --------------------------------------------------------

--
-- Table structure for table `tr_project_news`
--

CREATE TABLE `tr_project_news` (
  `news_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `project_id` mediumint(9) NOT NULL,
  `status` enum('online','draft','delete') NOT NULL DEFAULT 'online',
  `creation_date` datetime NOT NULL,
  `created_by` mediumint(9) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` mediumtext NOT NULL,
  PRIMARY KEY (`news_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tr_project_news`
--


-- --------------------------------------------------------

--
-- Table structure for table `tr_project_news_translators`
--

CREATE TABLE `tr_project_news_translators` (
  `news_id` mediumint(8) unsigned NOT NULL,
  `translator_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`news_id`,`translator_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tr_project_news_translators`
--


-- --------------------------------------------------------

--
-- Table structure for table `tr_project_questions`
--

CREATE TABLE `tr_project_questions` (
  `question_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` mediumint(8) unsigned NOT NULL,
  `account_id` mediumint(8) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `response_to_question_id` int(11) DEFAULT NULL,
  `status` enum('read','unread') NOT NULL DEFAULT 'unread',
  `thread_status` enum('new','in_progress','resolved','defer') DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` mediumtext,
  PRIMARY KEY (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tr_project_questions`
--


-- --------------------------------------------------------

--
-- Table structure for table `tr_project_translators`
--

CREATE TABLE `tr_project_translators` (
  `project_id` int(11) unsigned NOT NULL,
  `language_id` int(11) unsigned NOT NULL,
  `translator_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`project_id`,`language_id`,`translator_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tr_project_translators`
--


-- --------------------------------------------------------

--
-- Table structure for table `tr_project_versions`
--

CREATE TABLE `tr_project_versions` (
  `version_id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `is_base_version` enum('yes','no') DEFAULT 'yes',
  `parent_version_id` mediumint(8) unsigned DEFAULT NULL,
  `project_id` int(8) unsigned NOT NULL DEFAULT '0',
  `version_label` varchar(255) NOT NULL DEFAULT '1',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `synopsis` mediumtext,
  `may_translate` enum('yes','no') CHARACTER SET latin1 NOT NULL DEFAULT 'no',
  `is_visible` enum('yes','no') CHARACTER SET latin1 NOT NULL DEFAULT 'no',
  `export_types` set('PHP','XML','text','CSV','Excel') CHARACTER SET latin1 NOT NULL DEFAULT 'CSV,Excel',
  `show_labels_on_translator_pages` enum('yes','no') NOT NULL DEFAULT 'no',
  `export_folder` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`version_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tr_project_versions`
--


-- --------------------------------------------------------

--
-- Table structure for table `tr_project_version_export_settings`
--

CREATE TABLE `tr_project_version_export_settings` (
  `version_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `php_comments_header` mediumtext,
  `php_translation_var_name` varchar(250) NOT NULL DEFAULT 'LANG',
  `php_data_to_export` mediumtext,
  PRIMARY KEY (`version_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tr_project_version_export_settings`
--


-- --------------------------------------------------------

--
-- Table structure for table `tr_project_version_language_stats`
--

CREATE TABLE `tr_project_version_language_stats` (
  `version_id` int(11) unsigned NOT NULL,
  `language_id` int(11) unsigned NOT NULL,
  `percent_translated` smallint(5) unsigned NOT NULL DEFAULT '0',
  `percent_reliability` smallint(5) unsigned NOT NULL DEFAULT '0',
  `php_filename` varchar(250) DEFAULT NULL,
  `php_export_status` enum('Complete','Incomplete') NOT NULL DEFAULT 'Incomplete',
  `translation_last_change_date` datetime NOT NULL,
  PRIMARY KEY (`version_id`,`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tr_project_version_language_stats`
--


-- --------------------------------------------------------

--
-- Table structure for table `tr_session_locked_data`
--

CREATE TABLE `tr_session_locked_data` (
  `lock_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) unsigned NOT NULL,
  `data_id` int(10) unsigned NOT NULL,
  `lock_start` varchar(12) NOT NULL,
  `lock_end` varchar(12) NOT NULL,
  PRIMARY KEY (`lock_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tr_session_locked_data`
--


-- --------------------------------------------------------

--
-- Table structure for table `tr_session_locked_data_language`
--

CREATE TABLE `tr_session_locked_data_language` (
  `account_id` int(10) unsigned NOT NULL,
  `data_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `lock_start` varchar(12) NOT NULL,
  `lock_end` varchar(12) NOT NULL,
  PRIMARY KEY (`account_id`,`data_id`,`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tr_session_locked_data_language`
--


-- --------------------------------------------------------

--
-- Table structure for table `tr_session_locked_translations`
--

CREATE TABLE `tr_session_locked_translations` (
  `lock_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) unsigned NOT NULL,
  `data_translation_id` int(10) unsigned NOT NULL,
  `lock_start` varchar(12) NOT NULL,
  `lock_end` varchar(12) NOT NULL,
  PRIMARY KEY (`lock_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tr_session_locked_translations`
--


-- --------------------------------------------------------

--
-- Table structure for table `tr_translators`
--

CREATE TABLE `tr_translators` (
  `translator_id` mediumint(8) unsigned NOT NULL,
  `translation_disclaimer` enum('only_for_original_project','use_anywhere') NOT NULL DEFAULT 'only_for_original_project',
  `blacklist_count` smallint(2) NOT NULL DEFAULT '0',
  `blacklister_admin_id` mediumint(8) unsigned DEFAULT NULL,
  `blacklist_reason` varchar(250) DEFAULT NULL,
  `total_translations` mediumint(9) NOT NULL DEFAULT '0',
  `total_reviews` mediumint(9) unsigned NOT NULL DEFAULT '0',
  `total_review_points` mediumint(9) unsigned NOT NULL DEFAULT '0',
  `total_percent_reliable` smallint(6) unsigned NOT NULL DEFAULT '0',
  `total_translation_points` mediumint(9) unsigned NOT NULL DEFAULT '0',
  `default_bulk_translate_view` enum('detailed','short') NOT NULL DEFAULT 'short',
  PRIMARY KEY (`translator_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tr_translators`
--


-- --------------------------------------------------------

--
-- Table structure for table `tr_translator_languages`
--

CREATE TABLE `tr_translator_languages` (
  `translator_id` mediumint(8) unsigned NOT NULL,
  `language_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`translator_id`,`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tr_translator_languages`
--


-- --------------------------------------------------------

--
-- Table structure for table `tr_translator_points`
--

CREATE TABLE `tr_translator_points` (
  `translator_id` mediumint(8) unsigned NOT NULL,
  `origin_language_id` mediumint(8) unsigned NOT NULL,
  `target_language_id` mediumint(8) unsigned NOT NULL,
  `percent_reliability` tinyint(3) unsigned NOT NULL,
  `num_peer_reviews` mediumint(9) unsigned NOT NULL DEFAULT '0',
  `num_translations` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `num_reviews` mediumint(9) unsigned NOT NULL DEFAULT '0',
  `review_points` mediumint(9) unsigned NOT NULL DEFAULT '0',
  `translation_points` mediumint(9) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`translator_id`,`origin_language_id`,`target_language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tr_translator_points`
--


-- --------------------------------------------------------

--
-- Table structure for table `tr_translator_project_settings`
--

CREATE TABLE `tr_translator_project_settings` (
  `translator_id` mediumint(8) unsigned NOT NULL,
  `project_id` mediumint(8) unsigned NOT NULL,
  `may_credit_translator` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`translator_id`,`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tr_translator_project_settings`
--

