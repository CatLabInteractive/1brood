-- phpMyAdmin SQL Dump
-- version 2.11.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generatie Tijd: 16 Dec 2008 om 23:39
-- Server versie: 5.0.67
-- PHP Versie: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `b6557bro_brood`
--

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `c_id` int(11) NOT NULL auto_increment,
  `s_id` int(11) NOT NULL,
  `c_name` varchar(20) character set utf8 collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`c_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `categories_prices`
--

CREATE TABLE IF NOT EXISTS `categories_prices` (
  `cp_id` int(11) NOT NULL auto_increment,
  `c_id` int(11) NOT NULL,
  `p_id` tinyint(4) default NULL,
  `c_name` varchar(20) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`cp_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `companies`
--

CREATE TABLE IF NOT EXISTS `companies` (
  `c_id` int(11) NOT NULL auto_increment,
  `c_name` varchar(100) NOT NULL,
  `c_adres` varchar(100) NOT NULL,
  `c_postcode` varchar(15) NOT NULL,
  `c_gemeente` varchar(100) NOT NULL,
  `c_hour` tinyint(4) NOT NULL default '-1',
  PRIMARY KEY  (`c_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `companies_shop`
--

CREATE TABLE IF NOT EXISTS `companies_shop` (
  `c_id` int(11) NOT NULL,
  `s_id` int(11) NOT NULL,
  PRIMARY KEY  (`c_id`,`s_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `im_users`
--

CREATE TABLE IF NOT EXISTS `im_users` (
  `im_user` varchar(50) NOT NULL,
  `im_player` int(11) NOT NULL,
  `im_key` varchar(6) NOT NULL,
  `im_activated` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`im_user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `o_id` int(11) NOT NULL auto_increment,
  `c_id` int(11) NOT NULL,
  `s_id` int(11) NOT NULL,
  `o_isDone` enum('0','1') NOT NULL,
  `o_orderDate` datetime default NULL,
  PRIMARY KEY  (`o_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `order_prods`
--

CREATE TABLE IF NOT EXISTS `order_prods` (
  `op_id` int(11) NOT NULL auto_increment,
  `o_id` int(11) NOT NULL,
  `p_id` int(11) NOT NULL,
  `p_pid` int(11) NOT NULL default '0',
  `plid` int(11) NOT NULL,
  `op_message` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL,
  `op_amount` int(11) NOT NULL,
  `op_price` decimal(11,2) NOT NULL,
  PRIMARY KEY  (`op_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `players`
--

CREATE TABLE IF NOT EXISTS `players` (
  `plid` int(11) NOT NULL auto_increment,
  `realname` varchar(150) collate utf8_unicode_ci default NULL,
  `email` text collate utf8_unicode_ci,
  `password1` varchar(32) collate utf8_unicode_ci default NULL,
  `password2` varchar(32) collate utf8_unicode_ci default NULL,
  `activated` tinyint(1) NOT NULL,
  `firstname` varchar(100) collate utf8_unicode_ci NOT NULL,
  `lastname` varchar(100) collate utf8_unicode_ci NOT NULL,
  `seckey` varchar(8) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`plid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `players_comp`
--

CREATE TABLE IF NOT EXISTS `players_comp` (
  `plid` int(11) NOT NULL auto_increment,
  `c_id` int(11) NOT NULL,
  `isApproved` tinyint(1) NOT NULL default '0',
  `compStatus` tinyint(4) NOT NULL default '-1',
  `poefboek` decimal(11,2) NOT NULL,
  PRIMARY KEY  (`plid`,`c_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `players_poefboeklog`
--

CREATE TABLE IF NOT EXISTS `players_poefboeklog` (
  `l_id` int(11) NOT NULL auto_increment,
  `plid` int(11) NOT NULL,
  `c_id` int(11) NOT NULL,
  `l_amount` decimal(11,2) NOT NULL,
  `l_newpoef` decimal(11,2) default NULL,
  `l_date` datetime NOT NULL,
  `l_action` enum('order','moderator') NOT NULL,
  `l_actor` int(11) NOT NULL,
  `l_description` varchar(250) character set utf8 collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`l_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `players_shop`
--

CREATE TABLE IF NOT EXISTS `players_shop` (
  `plid` int(11) NOT NULL,
  `s_id` int(11) NOT NULL,
  PRIMARY KEY  (`plid`,`s_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `p_id` int(11) NOT NULL auto_increment,
  `s_id` int(11) NOT NULL,
  `c_id` int(11) NOT NULL default '0',
  `p_name` varchar(100) NOT NULL,
  `p_info` varchar(250) NOT NULL,
  `p_price` varchar(55) NOT NULL,
  PRIMARY KEY  (`p_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `shops`
--

CREATE TABLE IF NOT EXISTS `shops` (
  `s_id` int(11) NOT NULL auto_increment,
  `s_name` varchar(100) character set utf8 collate utf8_unicode_ci default NULL,
  `s_email` varchar(100) character set utf8 collate utf8_unicode_ci default NULL,
  `s_adres` varchar(100) character set utf8 collate utf8_unicode_ci default NULL,
  `s_postcode` varchar(100) character set utf8 collate utf8_unicode_ci default NULL,
  `s_gemeente` varchar(100) character set utf8 collate utf8_unicode_ci default NULL,
  `s_message` text character set utf8 collate utf8_unicode_ci,
  `s_currency` varchar(3) character set utf8 collate utf8_unicode_ci NOT NULL default '€',
  PRIMARY KEY  (`s_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

