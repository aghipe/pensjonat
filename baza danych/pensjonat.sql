/*
SQLyog Community v11.1 (64 bit)
MySQL - 5.6.10 : Database - pensjonat
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`pensjonat` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `pensjonat`;

/*Table structure for table `klienci` */

DROP TABLE IF EXISTS `klienci`;

CREATE TABLE `klienci` (
  `imie` varchar(50) DEFAULT NULL,
  `nazwisko` varchar(50) DEFAULT NULL,
  `nr_klienta` int(11) NOT NULL AUTO_INCREMENT,
  `adres` varchar(100) DEFAULT NULL,
  `telefon` varchar(10) DEFAULT NULL,
  `e_mail` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`nr_klienta`)
) ENGINE=InnoDB AUTO_INCREMENT=100022 DEFAULT CHARSET=utf8;

/*Data for the table `klienci` */

insert  into `klienci`(`imie`,`nazwisko`,`nr_klienta`,`adres`,`telefon`,`e_mail`) values ('Jan','Kowalski',100001,'Wiśniowa 34/15 23-456 Kraków','123456789','jkowal@email.com'),('Jacek','Kwiatkowski',100002,'Różana 33/44 67-890 Jarosław','111222333','kwiatek@email.com'),('Marek','Nowak',100003,'Długa 67 12-345 Łódź','098765432','marnow@email.com'),('Andrzej','Malinowski',100005,'Stroma 4/15 77-888 Gdańsk','543210987','malina@email.com'),('Janusz','Kozioł',100006,'Dębowa 31 76-543 Poznań','432678567','jankoz@email.com'),('Maciej','Wojciechowski',100007,'Szara 66/13 66-666 Szczecin','444555666','macwoj@email.com'),('Kamil','Głowacki',100008,'Prosta 1/12 33-444 Olsztyn','654654654','glowa@email.com'),('Łukasz','Baran',100014,'Polna 73 65-654 Lublin','738532678','lukasz01@emai.com'),('Bartosz','Kot',100019,'Szeroka 12 55-333 Poznań','632754865','kotek@email.com'),('Marek','Wysocki',100020,'Poniedziałkowy Dół 12/7 31-123 Kraków','555123456','mawys@mail.com.pl'),('Janusz','Wolski',100021,'Nowa 22 11-555 Łódź','444666999','jwolski@emai.com');

/*Table structure for table `pokoje` */

DROP TABLE IF EXISTS `pokoje`;

CREATE TABLE `pokoje` (
  `nr_pokoju` varchar(3) NOT NULL,
  `pojemnosc` int(11) DEFAULT NULL,
  PRIMARY KEY (`nr_pokoju`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `pokoje` */

insert  into `pokoje`(`nr_pokoju`,`pojemnosc`) values ('1',1),('101',1),('102',2),('103',3),('104',4),('2',2),('3',3),('4',4);

/*Table structure for table `wynajecia` */

DROP TABLE IF EXISTS `wynajecia`;

CREATE TABLE `wynajecia` (
  `nr_rezerwacji` int(6) NOT NULL AUTO_INCREMENT,
  `nr_klienta` int(11) DEFAULT NULL,
  `imie_klienta` varchar(50) DEFAULT NULL,
  `nazwisko_klienta` varchar(50) DEFAULT NULL,
  `nr_pokoju` varchar(3) DEFAULT NULL,
  `pojemnosc_pokoju` int(11) DEFAULT NULL,
  `data_przyjazdu` date DEFAULT NULL,
  `data_wyjazdu` date DEFAULT NULL,
  `stan` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`nr_rezerwacji`),
  KEY `nr klienta` (`nr_klienta`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8;

/*Data for the table `wynajecia` */

insert  into `wynajecia`(`nr_rezerwacji`,`nr_klienta`,`imie_klienta`,`nazwisko_klienta`,`nr_pokoju`,`pojemnosc_pokoju`,`data_przyjazdu`,`data_wyjazdu`,`stan`) values (1,123456,'Jan','Kowalski','1',1,'2013-06-15','2013-06-22','rezerwacja'),(2,467976,'Marcin','Nowak',NULL,1,'2013-07-23','2013-07-27','rezerwacja'),(27,100005,'Andrzej','Malinowski',NULL,1,'2013-08-11','2013-08-15','rezerwacja'),(28,100005,'Andrzej','Malinowski',NULL,1,'2013-08-11','2013-08-15','rezerwacja'),(29,100005,'Andrzej','Malinowski',NULL,1,'2013-08-11','2013-08-15','rezerwacja'),(30,100005,'Andrzej','Malinowski',NULL,1,'2013-08-11','2013-08-15','rezerwacja'),(31,100005,'Andrzej','Malinowski',NULL,2,'2013-08-11','2013-08-15','rezerwacja'),(32,100005,'Andrzej','Malinowski',NULL,2,'2013-08-11','2013-08-15','rezerwacja'),(33,100005,'Andrzej','Malinowski',NULL,3,'2013-08-11','2013-08-15','rezerwacja'),(34,100005,'Andrzej','Malinowski',NULL,3,'2013-08-11','2013-08-15','rezerwacja'),(35,100005,'Andrzej','Malinowski',NULL,4,'2013-08-11','2013-08-15','rezerwacja'),(36,100006,'Janusz','Kozioł',NULL,1,'2013-08-01','2013-08-09','rezerwacja'),(37,100006,'Janusz','Kozioł',NULL,1,'2013-08-01','2013-08-09','rezerwacja'),(38,100006,'Janusz','Kozioł',NULL,2,'2013-08-01','2013-08-09','rezerwacja'),(39,100006,'Janusz','Kozioł',NULL,3,'2013-08-01','2013-08-09','rezerwacja'),(40,100007,'Maciej','Wojciechowski',NULL,1,'2013-07-11','2013-07-16','rezerwacja'),(41,100007,'Maciej','Wojciechowski',NULL,2,'2013-07-11','2013-07-16','rezerwacja'),(42,100007,'Maciej','Wojciechowski',NULL,2,'2013-07-11','2013-07-16','rezerwacja'),(43,100007,'Maciej','Wojciechowski',NULL,4,'2013-07-11','2013-07-16','rezerwacja'),(44,100008,'Kamil','Głowacki',NULL,1,'2013-09-14','2013-09-20','rezerwacja'),(45,100008,'Kamil','Głowacki',NULL,2,'2013-09-14','2013-09-20','rezerwacja'),(46,100008,'Kamil','Głowacki',NULL,2,'2013-09-14','2013-09-20','rezerwacja'),(47,100008,'Kamil','Głowacki',NULL,4,'2013-09-14','2013-09-20','rezerwacja'),(51,100019,'Bartosz','Kot',NULL,1,'2013-07-01','2013-07-03','rezerwacja'),(52,100019,'Bartosz','Kot',NULL,1,'2013-07-01','2013-07-03','rezerwacja'),(53,100019,'Bartosz','Kot',NULL,2,'2013-07-01','2013-07-03','rezerwacja'),(54,100019,'Bartosz','Kot',NULL,2,'2013-07-01','2013-07-03','rezerwacja'),(55,100019,'Bartosz','Kot',NULL,1,'2013-09-01','2013-09-03','rezerwacja'),(56,100019,'Bartosz','Kot',NULL,1,'2013-09-01','2013-09-03','rezerwacja'),(57,100019,'Bartosz','Kot',NULL,2,'2013-09-01','2013-09-03','rezerwacja'),(58,100019,'Bartosz','Kot',NULL,2,'2013-09-01','2013-09-03','rezerwacja'),(59,100020,'Marek','Wysocki',NULL,1,'2013-05-05','2013-05-08','rezerwacja'),(60,100021,'Janusz','Wolski',NULL,1,'2013-10-12','2013-10-17','rezerwacja'),(61,100021,'Janusz','Wolski',NULL,2,'2013-10-12','2013-10-17','rezerwacja'),(62,100021,'Janusz','Wolski',NULL,4,'2013-10-12','2013-10-17','rezerwacja'),(63,100021,'Janusz','Wolski',NULL,4,'2013-10-12','2013-10-17','rezerwacja');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
