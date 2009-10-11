-- phpMyAdmin SQL Dump
-- version 3.2.2deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 11, 2009 at 07:43 PM
-- Server version: 5.0.51
-- PHP Version: 5.2.11-1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `freifunksql5`
--

-- --------------------------------------------------------

--
-- Table structure for table `crawl_data`
--

CREATE TABLE IF NOT EXISTS `crawl_data` (
  `id` int(11) NOT NULL auto_increment,
  `service_id` int(11) NOT NULL,
  `crawl_time` datetime NOT NULL,
  `ping` varchar(10) NOT NULL,
  `status` varchar(20) NOT NULL,
  `nickname` varchar(30) NOT NULL,
  `hostname` varchar(30) NOT NULL,
  `email` varchar(50) default NULL,
  `location` varchar(255) NOT NULL,
  `prefix` varchar(7) default NULL,
  `ssid` varchar(255) default NULL,
  `longitude` varchar(50) default NULL,
  `latitude` varchar(50) default NULL,
  `luciname` varchar(40) default NULL,
  `luciversion` varchar(30) default NULL,
  `distname` varchar(40) default NULL,
  `distversion` varchar(30) default NULL,
  `chipset` varchar(30) NOT NULL,
  `cpu` varchar(15) default NULL,
  `network` text NOT NULL,
  `wireless_interfaces` text NOT NULL,
  `uptime` varchar(20) NOT NULL,
  `idletime` varchar(20) NOT NULL,
  `memory_total` varchar(20) NOT NULL,
  `memory_caching` varchar(20) NOT NULL,
  `memory_buffering` varchar(20) NOT NULL,
  `memory_free` varchar(20) NOT NULL,
  `loadavg` varchar(20) NOT NULL,
  `processes` varchar(20) NOT NULL,
  `olsrd_hna` text NOT NULL,
  `olsrd_neighbors` text NOT NULL,
  `olsrd_links` text NOT NULL,
  `olsrd_mid` text NOT NULL,
  `olsrd_routes` text NOT NULL,
  `olsrd_topology` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE IF NOT EXISTS `history` (
  `id` int(11) NOT NULL auto_increment,
  `object` varchar(20) NOT NULL,
  `object_id` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ips`
--

CREATE TABLE IF NOT EXISTS `ips` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `subnet_id` int(3) NOT NULL,
  `ip_ip` int(3) NOT NULL,
  `zone_start` int(11) NOT NULL,
  `zone_end` int(11) NOT NULL,
  `radius` int(11) NOT NULL,
  `vpn_client_cert` text NOT NULL,
  `vpn_client_key` text NOT NULL,
  `create_date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE IF NOT EXISTS `services` (
  `id` int(11) NOT NULL auto_increment,
  `ip_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `typ` varchar(20) NOT NULL,
  `crawler` varchar(20) NOT NULL,
  `radius` int(11) NOT NULL,
  `visible` int(11) NOT NULL,
  `notify` tinyint(1) NOT NULL,
  `notification_wait` int(11) NOT NULL,
  `notified` tinyint(1) NOT NULL,
  `last_notification` datetime NOT NULL,
  `create_date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `subnets`
--

CREATE TABLE IF NOT EXISTS `subnets` (
  `id` int(11) NOT NULL auto_increment,
  `subnet_ip` int(11) NOT NULL,
  `allows_dhcp` tinyint(1) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) default NULL,
  `description` text,
  `longitude` varchar(50) default NULL,
  `latitude` varchar(50) default NULL,
  `radius` int(11) default NULL,
  `vpn_server` varchar(100) default NULL,
  `vpn_server_port` int(11) NOT NULL,
  `vpn_server_device` varchar(10) NOT NULL,
  `vpn_server_proto` varchar(10) NOT NULL,
  `vpn_server_ca` text NOT NULL,
  `vpn_server_cert` text NOT NULL,
  `vpn_server_key` text NOT NULL,
  `vpn_server_pass` varchar(255) NOT NULL,
  `create_date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL auto_increment,
  `session_id` varchar(32) NOT NULL,
  `nickname` varchar(30) NOT NULL,
  `password` varchar(32) NOT NULL,
  `vorname` varchar(50) NOT NULL,
  `nachname` varchar(50) NOT NULL,
  `strasse` varchar(50) NOT NULL,
  `plz` varchar(20) NOT NULL,
  `ort` varchar(50) NOT NULL,
  `telefon` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `jabber` varchar(50) NOT NULL,
  `icq` varchar(20) NOT NULL,
  `website` varchar(255) NOT NULL,
  `about` text NOT NULL,
  `notification_method` varchar(20) NOT NULL,
  `permission` varchar(20) default NULL,
  `create_date` datetime default NULL,
  `activated` varchar(32) default NULL,
  `last_login` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
