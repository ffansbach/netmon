-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 09, 2013 at 04:44 PM
-- Server version: 5.5.33-MariaDB-1~wheezy-log
-- PHP Version: 5.4.20-1~dotdeb.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `freifunksql5`
--
--CREATE DATABASE IF NOT EXISTS `freifunksql5` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
--USE `freifunksql5`;

-- --------------------------------------------------------

--
-- Table structure for table `chipsets`
--

CREATE TABLE IF NOT EXISTS `chipsets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `name` varchar(100) NOT NULL,
  `hardware_name` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `hardware_name` (`hardware_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `value` text NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `crawl_batman_advanced_interfaces`
--

CREATE TABLE IF NOT EXISTS `crawl_batman_advanced_interfaces` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `router_id` int(11) NOT NULL,
  `crawl_cycle_id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `status` varchar(20) NOT NULL,
  `crawl_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `router_id` (`router_id`),
  KEY `crawl_cycle_id` (`crawl_cycle_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `crawl_batman_advanced_originators`
--

CREATE TABLE IF NOT EXISTS `crawl_batman_advanced_originators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `router_id` int(11) NOT NULL,
  `crawl_cycle_id` int(11) NOT NULL,
  `originator` varchar(17) NOT NULL,
  `link_quality` int(11) NOT NULL,
  `nexthop` varchar(17) NOT NULL,
  `outgoing_interface` varchar(20) NOT NULL,
  `last_seen` varchar(20) NOT NULL,
  `crawl_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `router_id` (`router_id`),
  KEY `crawl_cycle_id` (`crawl_cycle_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `crawl_cycle`
--

CREATE TABLE IF NOT EXISTS `crawl_cycle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `crawl_date` datetime NOT NULL,
  `crawl_date_end` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `crawl_interfaces`
--

CREATE TABLE IF NOT EXISTS `crawl_interfaces` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `router_id` int(11) NOT NULL,
  `crawl_cycle_id` int(11) NOT NULL,
  `interface_id` int(11) NOT NULL,
  `crawl_date` datetime NOT NULL,
  `name` varchar(20) NOT NULL,
  `mac_addr` varchar(17) NOT NULL,
  `traffic_rx` bigint(30) NOT NULL,
  `traffic_rx_avg` bigint(30) NOT NULL,
  `traffic_tx` bigint(30) NOT NULL,
  `traffic_tx_avg` bigint(30) NOT NULL,
  `wlan_mode` varchar(20) NOT NULL,
  `wlan_frequency` varchar(10) NOT NULL,
  `wlan_essid` varchar(50) NOT NULL,
  `wlan_bssid` varchar(17) NOT NULL,
  `wlan_tx_power` varchar(10) NOT NULL,
  `mtu` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `router_id` (`router_id`),
  KEY `crawl_cycle_id` (`crawl_cycle_id`),
  KEY `interface_id` (`interface_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `crawl_routers`
--

CREATE TABLE IF NOT EXISTS `crawl_routers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `router_id` int(11) NOT NULL,
  `crawl_cycle_id` int(11) NOT NULL,
  `crawl_date` datetime NOT NULL,
  `status` varchar(20) NOT NULL,
  `hostname` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `location` varchar(250) NOT NULL,
  `latitude` varchar(15) NOT NULL,
  `longitude` varchar(15) NOT NULL,
  `luciname` varchar(40) NOT NULL,
  `luciversion` varchar(40) NOT NULL,
  `distname` varchar(40) NOT NULL,
  `distversion` varchar(40) NOT NULL,
  `chipset` varchar(20) NOT NULL,
  `cpu` varchar(100) NOT NULL,
  `memory_total` int(11) NOT NULL,
  `memory_caching` int(11) NOT NULL,
  `memory_buffering` int(11) NOT NULL,
  `memory_free` int(11) NOT NULL,
  `loadavg` varchar(8) NOT NULL,
  `processes` varchar(8) NOT NULL,
  `uptime` varchar(15) NOT NULL,
  `idletime` varchar(15) NOT NULL,
  `local_time` varchar(60) NOT NULL,
  `community_essid` varchar(100) NOT NULL,
  `community_nickname` varchar(100) NOT NULL,
  `community_email` varchar(100) NOT NULL,
  `community_prefix` varchar(15) NOT NULL,
  `batman_advanced_version` varchar(20) NOT NULL,
  `fastd_version` varchar(20) NOT NULL,
  `kernel_version` varchar(30) NOT NULL,
  `configurator_version` int(11) NOT NULL,
  `nodewatcher_version` int(11) NOT NULL,
  `firmware_version` varchar(250) NOT NULL,
  `firmware_revision` varchar(250) NOT NULL,
  `openwrt_core_revision` int(11) NOT NULL,
  `openwrt_feeds_packages_revision` int(11) NOT NULL,
  `client_count` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `router_id` (`router_id`),
  KEY `crawl_cycle_id` (`crawl_cycle_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `crawl_services`
--

CREATE TABLE IF NOT EXISTS `crawl_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NOT NULL,
  `crawl_cycle_id` int(11) NOT NULL,
  `crawl_date` datetime NOT NULL,
  `status` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  KEY `crawl_cycle_id` (`crawl_cycle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dns_ressource_records`
--

CREATE TABLE IF NOT EXISTS `dns_ressource_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dns_zone_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `host` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `pri` int(11) NOT NULL,
  `destination_id` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `dns_zone_id` (`dns_zone_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dns_zones`
--

CREATE TABLE IF NOT EXISTS `dns_zones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `pri_dns` varchar(255) NOT NULL,
  `sec_dns` varchar(255) NOT NULL,
  `serial` int(11) NOT NULL,
  `refresh` int(11) NOT NULL,
  `retry` int(11) NOT NULL,
  `expire` int(11) NOT NULL,
  `ttl` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `crawl_cycle_id` int(11) NOT NULL,
  `object` varchar(20) NOT NULL,
  `object_id` int(11) NOT NULL,
  `action` varchar(200) NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `crawl_cycle_id` (`crawl_cycle_id`),
  KEY `object` (`object`),
  KEY `object_id` (`object_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `event_notifications`
--

CREATE TABLE IF NOT EXISTS `event_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `action` varchar(200) NOT NULL,
  `object` varchar(30) NOT NULL,
  `notify` tinyint(1) NOT NULL,
  `notified` tinyint(1) NOT NULL,
  `notification_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `action` (`action`),
  KEY `object` (`object`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `interfaces`
--

CREATE TABLE IF NOT EXISTS `interfaces` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `router_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `name` varchar(100) NOT NULL,
  `mac_addr` varchar(150) NOT NULL,
  `protected` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `router_id` (`router_id`),
  KEY `router_id_2` (`router_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ips`
--

CREATE TABLE IF NOT EXISTS `ips` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `interface_id` int(11) NOT NULL,
  `network_id` int(11) NOT NULL,
  `router_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `ip` varchar(100) NOT NULL,
  `netmask` int(3) NOT NULL,
  `ipv` int(11) NOT NULL,
  `protected` tinyint(1) NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`),
  KEY `router_id` (`router_id`),
  KEY `ipv` (`ipv`),
  KEY `project_id` (`project_id`),
  KEY `interface_id` (`interface_id`),
  KEY `network_id` (`network_id`),
  KEY `router_id_2` (`router_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `networks`
--

CREATE TABLE IF NOT EXISTS `networks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `netmask` int(11) NOT NULL,
  `ipv` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `is_wlan` tinyint(4) NOT NULL,
  `wlan_essid` varchar(200) NOT NULL,
  `wlan_bssid` varchar(100) NOT NULL,
  `wlan_channel` int(11) NOT NULL,
  `is_vpn` tinyint(4) NOT NULL,
  `vpn_server` varchar(100) NOT NULL,
  `vpn_server_port` int(11) NOT NULL,
  `vpn_server_device` varchar(20) NOT NULL,
  `vpn_server_proto` varchar(20) NOT NULL,
  `vpn_server_ca_crt` text NOT NULL,
  `vpn_server_ca_key` text NOT NULL,
  `vpn_server_pass` varchar(200) NOT NULL,
  `vpn_client_config` text NOT NULL,
  `vpn_client_config_needs_script` tinyint(4) NOT NULL,
  `vpn_client_config_script` text NOT NULL,
  `is_ccd_ftp_sync` tinyint(4) NOT NULL,
  `ccd_ftp_folder` varchar(250) NOT NULL,
  `ccd_ftp_username` varchar(100) NOT NULL,
  `ccd_ftp_password` varchar(200) NOT NULL,
  `is_olsr` tinyint(4) NOT NULL,
  `is_batman_adv` tinyint(4) NOT NULL,
  `is_ipv4` tinyint(1) NOT NULL,
  `ipv4_host` varchar(15) NOT NULL,
  `ipv4_netmask` varchar(10) NOT NULL,
  `ipv4_dhcp_kind` varchar(30) NOT NULL,
  `is_ipv6` tinyint(1) NOT NULL,
  `is_geo_specific` tinyint(4) NOT NULL,
  `geo_polygons` text NOT NULL,
  `dns_server` varchar(200) NOT NULL,
  `website` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `is_ipv4` (`is_ipv4`),
  KEY `is_batman_adv` (`is_batman_adv`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `routers`
--

CREATE TABLE IF NOT EXISTS `routers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `crawl_method` varchar(30) NOT NULL,
  `hostname` varchar(40) NOT NULL,
  `allow_router_auto_assign` tinyint(1) NOT NULL,
  `router_auto_assign_login_string` varchar(250) NOT NULL,
  `router_auto_assign_hash` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `location` varchar(250) NOT NULL,
  `latitude` varchar(15) NOT NULL,
  `longitude` varchar(15) NOT NULL,
  `chipset_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `chipset_id` (`chipset_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `routers_not_assigned`
--

CREATE TABLE IF NOT EXISTS `routers_not_assigned` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `hostname` varchar(50) NOT NULL,
  `router_auto_assign_login_string` varchar(50) NOT NULL,
  `interface` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `router_adds`
--

CREATE TABLE IF NOT EXISTS `router_adds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `router_id` int(11) NOT NULL,
  `adds_allowed` tinyint(1) NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `router_id` (`router_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE IF NOT EXISTS `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `port` int(11) NOT NULL,
  `visible` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `service_dns_ressource_records`
--

CREATE TABLE IF NOT EXISTS `service_dns_ressource_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NOT NULL,
  `dns_ressource_record_id` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  KEY `dns_ressource_record_id` (`dns_ressource_record_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `service_ips`
--

CREATE TABLE IF NOT EXISTS `service_ips` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NOT NULL,
  `ip_id` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  KEY `ip_id` (`ip_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(32) NOT NULL,
  `nickname` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `openid` varchar(100) NOT NULL,
  `api_key` varchar(32) NOT NULL,
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
  `allow_node_delegation` tinyint(1) NOT NULL,
  `notification_method` varchar(20) NOT NULL,
  `permission` varchar(20) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `update_date` datetime NOT NULL,
  `activated` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_remember_mes`
--

CREATE TABLE IF NOT EXISTS `user_remember_mes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `session_id` varchar(100) NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `variable_splash_clients`
--

CREATE TABLE IF NOT EXISTS `variable_splash_clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `router_id` int(11) NOT NULL,
  `mac_addr` varchar(150) NOT NULL,
  `ip` varchar(100) NOT NULL,
  `ipv` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `router_id` (`router_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `crawl_batman_advanced_interfaces`
--
ALTER TABLE `crawl_batman_advanced_interfaces`
  ADD CONSTRAINT `crawl_batman_advanced_interfaces_ibfk_1` FOREIGN KEY (`router_id`) REFERENCES `routers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `crawl_batman_advanced_interfaces_ibfk_2` FOREIGN KEY (`crawl_cycle_id`) REFERENCES `crawl_cycle` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `crawl_batman_advanced_originators`
--
ALTER TABLE `crawl_batman_advanced_originators`
  ADD CONSTRAINT `crawl_batman_advanced_originators_ibfk_1` FOREIGN KEY (`crawl_cycle_id`) REFERENCES `crawl_cycle` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `crawl_batman_advanced_originators_ibfk_2` FOREIGN KEY (`router_id`) REFERENCES `routers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `crawl_interfaces`
--
ALTER TABLE `crawl_interfaces`
  ADD CONSTRAINT `crawl_interfaces_ibfk_1` FOREIGN KEY (`crawl_cycle_id`) REFERENCES `crawl_cycle` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `crawl_interfaces_ibfk_2` FOREIGN KEY (`interface_id`) REFERENCES `interfaces` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `crawl_interfaces_ibfk_3` FOREIGN KEY (`router_id`) REFERENCES `routers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `crawl_routers`
--
ALTER TABLE `crawl_routers`
  ADD CONSTRAINT `crawl_routers_ibfk_1` FOREIGN KEY (`router_id`) REFERENCES `routers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `crawl_routers_ibfk_2` FOREIGN KEY (`crawl_cycle_id`) REFERENCES `crawl_cycle` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `crawl_services`
--
ALTER TABLE `crawl_services`
  ADD CONSTRAINT `crawl_services_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `crawl_services_ibfk_2` FOREIGN KEY (`crawl_cycle_id`) REFERENCES `crawl_cycle` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dns_ressource_records`
--
ALTER TABLE `dns_ressource_records`
  ADD CONSTRAINT `dns_ressource_records_ibfk_1` FOREIGN KEY (`dns_zone_id`) REFERENCES `dns_zones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dns_ressource_records_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dns_zones`
--
ALTER TABLE `dns_zones`
  ADD CONSTRAINT `dns_zones_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `interfaces`
--
ALTER TABLE `interfaces`
  ADD CONSTRAINT `interfaces_ibfk_1` FOREIGN KEY (`router_id`) REFERENCES `routers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ips`
--
ALTER TABLE `ips`
  ADD CONSTRAINT `ips_ibfk_1` FOREIGN KEY (`network_id`) REFERENCES `networks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ips_ibfk_2` FOREIGN KEY (`interface_id`) REFERENCES `interfaces` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `networks`
--
ALTER TABLE `networks`
  ADD CONSTRAINT `networks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `routers`
--
ALTER TABLE `routers`
  ADD CONSTRAINT `routers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_remember_mes`
--
ALTER TABLE `user_remember_mes`
  ADD CONSTRAINT `user_remember_mes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
