-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Aug 06, 2015 at 04:45 AM
-- Server version: 5.6.14
-- PHP Version: 5.5.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `bprbkk`
--

-- --------------------------------------------------------

--
-- Table structure for table `data_nasabah`
--

CREATE TABLE IF NOT EXISTS `data_nasabah` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `noktp` int(16) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `pinjaman` varchar(50) NOT NULL,
  `penghasilan` varchar(50) NOT NULL,
  `angunan` varchar(50) NOT NULL,
  `blbi` varchar(50) NOT NULL,
  `syarat` varchar(50) NOT NULL,
  `keputusan` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `noktp` (`noktp`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `data_prediksi`
--

CREATE TABLE IF NOT EXISTS `data_prediksi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `noktp` int(16) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `pinjaman` varchar(50) NOT NULL,
  `penghasilan` varchar(50) NOT NULL,
  `angunan` varchar(50) NOT NULL,
  `blbi` varchar(50) NOT NULL,
  `syarat` varchar(50) NOT NULL,
  `keputusan_c45` varchar(50) NOT NULL,
  `id_rulec45` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `noktp` (`noktp`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE IF NOT EXISTS `karyawan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8 NOT NULL,
  `password` varchar(50) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`id`, `username`, `password`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3');

-- --------------------------------------------------------

--
-- Table structure for table `perhitungan_c45`
--

CREATE TABLE IF NOT EXISTS `perhitungan_c45` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variabel` varchar(50) CHARACTER SET utf8 NOT NULL,
  `nilai_variabel` varchar(50) CHARACTER SET utf8 NOT NULL,
  `jml_kasus_total` varchar(5) CHARACTER SET utf8 NOT NULL,
  `jml_kasus_tidak` varchar(5) CHARACTER SET utf8 NOT NULL,
  `jml_kasus_ya` varchar(5) CHARACTER SET utf8 NOT NULL,
  `entropy` varchar(50) CHARACTER SET utf8 NOT NULL,
  `inf_gain` varchar(50) CHARACTER SET utf8 NOT NULL,
  `inf_gain_temp` varchar(50) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=ucs2 COLLATE=ucs2_croatian_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pohon_keputusan`
--

CREATE TABLE IF NOT EXISTS `pohon_keputusan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variabel` varchar(50) CHARACTER SET utf8 NOT NULL,
  `nilai_variabel` varchar(50) CHARACTER SET utf8 NOT NULL,
  `id_parent` int(11) NOT NULL,
  `keputusan` varchar(50) CHARACTER SET utf8 NOT NULL,
  `jml_kasus_tidak` varchar(5) CHARACTER SET utf8 NOT NULL,
  `jml_kasus_ya` varchar(5) CHARACTER SET utf8 NOT NULL,
  `proses` varchar(10) CHARACTER SET utf8 NOT NULL,
  `kondisi_variabel` varchar(200) CHARACTER SET utf8 NOT NULL,
  `looping_kondisi` varchar(15) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `rule_c45`
--

CREATE TABLE IF NOT EXISTS `rule_c45` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_parent` int(11) NOT NULL,
  `rule` varchar(200) CHARACTER SET utf8 NOT NULL,
  `keputusan` varchar(50) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `rule_prediksi`
--

CREATE TABLE IF NOT EXISTS `rule_prediksi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_rule` int(11) NOT NULL,
  `variabel` varchar(50) NOT NULL,
  `nilai_variabel` varchar(50) NOT NULL,
  `keputusan` varchar(50) NOT NULL,
  `cocok` varchar(50) NOT NULL,
  `pohon` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `variabel`
--

CREATE TABLE IF NOT EXISTS `variabel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variabel` varchar(50) NOT NULL,
  `nilai_variabel` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `variabel_prediksi`
--

CREATE TABLE IF NOT EXISTS `variabel_prediksi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variabel` varchar(50) NOT NULL,
  `nilai_variabel` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
