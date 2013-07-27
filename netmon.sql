-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 27, 2013 at 03:34 AM
-- Server version: 5.5.32-MariaDB-1~wheezy-log
-- PHP Version: 5.4.17-1~dotdeb.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `freifunksql5`
--
CREATE DATABASE IF NOT EXISTS `freifunksql5` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `freifunksql5`;

-- --------------------------------------------------------

--
-- Table structure for table `chipsets`
--

CREATE TABLE IF NOT EXISTS `chipsets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `name` varchar(100) NOT NULL,
  `hardware_name` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `hardware_name` (`hardware_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

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
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

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
  KEY `crawl_batman_advanced_interfaces_id` (`id`),
  KEY `router_id` (`router_id`),
  KEY `crawl_cycle_id` (`crawl_cycle_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `crawl_batman_advanced_originators`
--

CREATE TABLE IF NOT EXISTS `crawl_batman_advanced_originators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `router_id` int(11) NOT NULL,
  `crawl_cycle_id` int(11) NOT NULL,
  `originator` varchar(100) NOT NULL,
  `link_quality` int(11) NOT NULL,
  `nexthop` varchar(100) NOT NULL,
  `outgoing_interface` varchar(20) NOT NULL,
  `last_seen` varchar(20) NOT NULL,
  `crawl_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `router_id` (`router_id`),
  KEY `crawl_cycle_id` (`crawl_cycle_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `crawl_cycle`
--

CREATE TABLE IF NOT EXISTS `crawl_cycle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `crawl_date` datetime NOT NULL,
  `crawl_date_end` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

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
  `name` varchar(100) NOT NULL,
  `mac_addr` varchar(150) NOT NULL,
  `ipv4_addr` varchar(40) NOT NULL,
  `ipv6_addr` varchar(200) NOT NULL,
  `ipv6_link_local_addr` varchar(200) NOT NULL,
  `traffic_rx` bigint(30) NOT NULL,
  `traffic_rx_avg` bigint(30) NOT NULL,
  `traffic_tx` bigint(30) NOT NULL,
  `traffic_tx_avg` bigint(30) NOT NULL,
  `wlan_mode` varchar(20) NOT NULL,
  `wlan_frequency` varchar(10) NOT NULL,
  `wlan_essid` varchar(50) NOT NULL,
  `wlan_bssid` varchar(40) NOT NULL,
  `wlan_tx_power` varchar(10) NOT NULL,
  `mtu` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `crawl_interfaces_id` (`id`),
  KEY `router_id` (`router_id`),
  KEY `crawl_cycle_id` (`crawl_cycle_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `crawl_ips`
--

CREATE TABLE IF NOT EXISTS `crawl_ips` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_id` int(11) NOT NULL,
  `crawl_cycle_id` int(11) NOT NULL,
  `crawl_date` datetime NOT NULL,
  `ping_avg` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ip_id` (`ip_id`),
  KEY `crawl_cycle_id` (`crawl_cycle_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `crawl_olsr`
--

CREATE TABLE IF NOT EXISTS `crawl_olsr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `router_id` int(11) NOT NULL,
  `crawl_cycle_id` int(11) NOT NULL,
  `olsrd_hna` text NOT NULL,
  `olsrd_neighbors` text NOT NULL,
  `olsrd_links` text NOT NULL,
  `olsrd_mid` text NOT NULL,
  `olsrd_routes` text NOT NULL,
  `olsrd_topology` text NOT NULL,
  `crawl_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `ping` varchar(20) NOT NULL,
  `hostname` varchar(40) NOT NULL,
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
  `memory_total` varchar(15) NOT NULL,
  `memory_caching` varchar(15) NOT NULL,
  `memory_buffering` varchar(30) NOT NULL,
  `memory_free` varchar(15) NOT NULL,
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
  KEY `id` (`id`),
  KEY `crawl_routers_id` (`id`),
  KEY `router_id` (`router_id`),
  KEY `crawl_cycle_id` (`crawl_cycle_id`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

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
  `crawled_ipv4_addr` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  KEY `crawl_cycle_id` (`crawl_cycle_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dns_hosts`
--

CREATE TABLE IF NOT EXISTS `dns_hosts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `host` varchar(250) NOT NULL,
  `ipv4_id` int(11) NOT NULL,
  `ipv6_id` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `host` (`host`)
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
  KEY `crawl_cycle_id` (`crawl_cycle_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

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
  PRIMARY KEY (`id`)
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
  KEY `router_id` (`router_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

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
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ip_ranges`
--

CREATE TABLE IF NOT EXISTS `ip_ranges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `interface_id` int(11) NOT NULL,
  `router_id` int(11) NOT NULL,
  `ip_start` varchar(100) NOT NULL,
  `ip_end` varchar(100) NOT NULL,
  `create_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `olsr_crawl_data`
--

CREATE TABLE IF NOT EXISTS `olsr_crawl_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `crawl_id` int(11) NOT NULL,
  `olsrd_hna` text NOT NULL,
  `olsrd_neighbors` text NOT NULL,
  `olsrd_links` text NOT NULL,
  `olsrd_mid` text NOT NULL,
  `olsrd_routes` text NOT NULL,
  `olsrd_topology` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

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
  `chipset_id` int(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `router_adds`
--

CREATE TABLE IF NOT EXISTS `router_adds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `router_id` int(11) NOT NULL,
  `adds_allowed` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE IF NOT EXISTS `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `router_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `port` int(11) NOT NULL,
  `url_prefix` varchar(20) NOT NULL,
  `visible` int(11) NOT NULL,
  `notify` tinyint(1) NOT NULL,
  `notification_wait` int(11) NOT NULL,
  `notified` tinyint(1) NOT NULL,
  `last_notification` datetime NOT NULL,
  `use_netmons_url` tinyint(1) NOT NULL,
  `url` varchar(250) NOT NULL,
  `create_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `service_ips`
--

CREATE TABLE IF NOT EXISTS `service_ips` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NOT NULL,
  `ip_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `subnets`
--

CREATE TABLE IF NOT EXISTS `subnets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subnet_type` varchar(10) NOT NULL,
  `host` varchar(15) NOT NULL,
  `netmask` int(11) NOT NULL,
  `real_host` varchar(15) NOT NULL,
  `real_netmask` int(11) NOT NULL,
  `dhcp_kind` varchar(7) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `essid` varchar(100) NOT NULL,
  `bssid` varchar(20) NOT NULL,
  `channel` int(11) NOT NULL,
  `website` varchar(200) NOT NULL,
  `polygons` text NOT NULL,
  `dns_server` varchar(200) NOT NULL,
  `vpn_server` varchar(100) DEFAULT NULL,
  `vpn_server_port` int(11) NOT NULL,
  `vpn_server_device` varchar(10) NOT NULL,
  `vpn_server_proto` varchar(10) NOT NULL,
  `vpn_server_ca_crt` text NOT NULL,
  `vpn_server_ca_key` text NOT NULL,
  `vpn_server_pass` varchar(255) NOT NULL,
  `ftp_sync` tinyint(4) NOT NULL,
  `ftp_ccd_folder` varchar(200) NOT NULL,
  `ftp_ccd_username` varchar(20) NOT NULL,
  `ftp_ccd_password` varchar(30) NOT NULL,
  `create_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tlds`
--

CREATE TABLE IF NOT EXISTS `tlds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `tld` varchar(20) NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

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
  `activated` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

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
  PRIMARY KEY (`id`)
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
