CREATE DATABASE bdmat CHARACTER SET utf8 COLLATE utf8_general_ci;
use bdmat;

create table domaine(
	idDom integer(5) NOT NULL AUTO_INCREMENT,
	nomDom varchar (50),
	PRIMARY KEY (idDom)
)ENGINE=InnoDB;

create table designation(
	idDes integer(5) NOT NULL AUTO_INCREMENT,
	nomDes varchar (50) NOT NULL,
	idDom_domaine integer(5) NOT NULL,
	PRIMARY KEY (idDes),
	CONSTRAINT FK_designation_idDom_domaine FOREIGN KEY (idDom_domaine) REFERENCES domaine(idDom)
)ENGINE=InnoDB;

CREATE TABLE `labo` (
	`idLabo` int(2) NOT NULL AUTO_INCREMENT,
	`nomLabo` varchar(20) NOT NULL,
	PRIMARY KEY (`idLabo`)
) ENGINE=InnoDB;

create table etat(
	idEtat integer(3) NOT NULL AUTO_INCREMENT,
	nomEtat varchar (50) NOT NULL,
	PRIMARY KEY (idEtat)
)ENGINE=InnoDB;

create table statut(
	idStatut integer(3) NOT NULL AUTO_INCREMENT,
	nomStatut varchar (50) NOT NULL,
	PRIMARY KEY (idStatut)
)ENGINE=InnoDB;

create table localisation(
	idLocal integer(3) NOT NULL AUTO_INCREMENT,
	nomLocal varchar (50) NOT NULL,
	idLabo_labo int(2) NULL,
	PRIMARY KEY (idLocal),
	CONSTRAINT FK_localisation_idLabo_labo FOREIGN KEY (idLabo_labo) REFERENCES labo(idLabo)
)ENGINE=InnoDB;

create table typeCapteur(
	idTypeC integer(3) NOT NULL AUTO_INCREMENT,
	nomTypeC varchar (50) NOT NULL,
	PRIMARY KEY (idTypeC)
)ENGINE=InnoDB;

CREATE TABLE instrument(
	numInstru varchar(20) NOT NULL,
	ancienNum varchar(20),
	trescalId integer(10),
	idDes_designation integer(5),
	marque varchar(50),
	modele varchar(30),
	numSerie varchar(30),
	affectF varchar(10),
	date_derniereInt date ,
	date_futureInt date,
	periodicite varchar(25),
	idEtat_etat integer(3),
	idLocal_localisation integer(3),
	idStatut_statut integer(3),
	commentaire TEXT,
	PRIMARY KEY (numInstru),
	CONSTRAINT FK_instrument_idDes_designation FOREIGN KEY (idDes_designation) REFERENCES designation(idDes),
	CONSTRAINT FK_instrument_idEtat_etat FOREIGN KEY (idEtat_etat) REFERENCES etat(idEtat),
	CONSTRAINT FK_instrument_idLocal_localisation FOREIGN KEY (idLocal_localisation) REFERENCES localisation(idLocal),
	CONSTRAINT FK_instrument_idStatut_statut FOREIGN KEY (idStatut_statut) REFERENCES statut(idStatut)
)ENGINE=InnoDB;
ALTER TABLE `instrument` ADD INDEX ( `trescalId` );
ALTER TABLE `instrument` ADD INDEX ( `numSerie` );

create table equipement_emc(
	idEquip integer(5) NOT NULL AUTO_INCREMENT,
	nomEquip varchar (50) NOT NULL,
	PRIMARY KEY (idEquip)
)ENGINE=InnoDB;

create table designation_emc(
	idDes integer(5) NOT NULL AUTO_INCREMENT,
	fonction varchar (50) NOT NULL,
	idEquip_equipement_emc integer(5) NOT NULL,
	PRIMARY KEY (idDes),
	CONSTRAINT FK_designation_emc_idEquip_equipement_emc FOREIGN KEY (idEquip_equipement_emc) REFERENCES equipement_emc(idEquip)
)ENGINE=InnoDB;

CREATE TABLE instrument_emc(
	idInstruEmc integer(8) NOT NULL AUTO_INCREMENT,
	caracteristique varchar(100),
	numInstru_instrument varchar(20) NOT NULL,
	idDes_designation_emc integer(5),
	PRIMARY KEY (idInstruEmc),
	CONSTRAINT FK_instrument_emc_idDes_designation_emc FOREIGN KEY (idDes_designation_emc) REFERENCES designation_emc(idDes),
	CONSTRAINT FK_instrument_emc_numInstru_instrument FOREIGN KEY (numInstru_instrument) REFERENCES instrument(numInstru) ON DELETE CASCADE
)ENGINE=InnoDB;

create table unite(
	idUnite integer(3) NOT NULL AUTO_INCREMENT,
	nomUnite varchar (15) NOT NULL,
	PRIMARY KEY (idLocal),
)ENGINE=InnoDB;

CREATE TABLE instrument_vib_capteur(
	idInstruCapt integer(8) NOT NULL AUTO_INCREMENT,
	axeX varchar(20) NOT NULL,
	sensiX varchar(10) NOT NULL,
	axeY varchar(20),
	sensiY varchar(10),
	axeZ varchar(20),
	sensiZ varchar(10),
	axeZs varchar(20),
	sensiZs varchar(10),
	idUnite_unite integer(3) NULL,
	numInstru_instrument varchar(20) NOT NULL,
	idTypeC_typeCapteur integer(3) NOT NULL,
	PRIMARY KEY (idInstruCapt),
	CONSTRAINT FK_instrument_vib_capteur_numInstru_instrument FOREIGN KEY (numInstru_instrument) REFERENCES instrument(numInstru) ON DELETE CASCADE,
	CONSTRAINT FK_instrument_vib_capteur_idTypeC_typeCapteur FOREIGN KEY (idTypeC_typeCapteur) REFERENCES typeCapteur(idTypeC),
	CONSTRAINT FK_instrument_vib_capteur_idUnite_unite FOREIGN KEY (idUnite_unite) REFERENCES unite(idUnite)
)ENGINE=InnoDB;

CREATE TABLE histo_vib_capteur(
	idHistoCapt integer(8) NOT NULL AUTO_INCREMENT,
	axeX varchar(20) NOT NULL,
	sensiX varchar(10) NOT NULL,
	axeY varchar(20),
	sensiY varchar(10),
	axeZ varchar(20),
	sensiZ varchar(10),
	axeZs varchar(20),
	sensiZs varchar(10),
	date_histo date,
	idInstruCapt_instrument_vib_capteur integer(8) NOT NULL,
	PRIMARY KEY (idHistoCapt),
	CONSTRAINT FK_histo_vib_capteur_numInstru_instrument FOREIGN KEY (idInstruCapt_instrument_vib_capteur) REFERENCES instrument_vib_capteur(idInstruCapt) ON DELETE CASCADE
)ENGINE=InnoDB;
ALTER TABLE `histo_vib_capteur` ADD INDEX ( `date_histo` );

CREATE TABLE instrument_vib(
	idInstruVib integer(8) NOT NULL AUTO_INCREMENT,
	numInstru_instrument varchar(20) NOT NULL,
	PRIMARY KEY (idInstruVib),
	CONSTRAINT FK_instrument_vib_numInstru_instrument FOREIGN KEY (numInstru_instrument) REFERENCES instrument(numInstru) ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE TABLE instrument_vth(
	idInstruVth integer(8) NOT NULL AUTO_INCREMENT,
	numInstru_instrument varchar(20) NOT NULL,
	PRIMARY KEY (idInstruVth),
	CONSTRAINT FK_instrument_vth_numInstru_instrument FOREIGN KEY (numInstru_instrument) REFERENCES instrument(numInstru) ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE TABLE `categUser` (
	`idCateg` int(3) NOT NULL AUTO_INCREMENT,
	`nomCateg` varchar(50) DEFAULT NULL,
	PRIMARY KEY (`idCateg`)
) ENGINE=InnoDB;

CREATE TABLE `utilisateur` (
	`idUser` int(5) NOT NULL AUTO_INCREMENT,
	`logUser` varchar(25) DEFAULT NULL,
	`pwdUser` varchar(50) DEFAULT NULL,
	nomEmp varchar(30),
	prenomEmp varchar(30),
	`idCateg_categUser` int(3) NOT NULL,
	idLabo_labo int(2) NOT NULL,
	PRIMARY KEY (`idUser`),
	CONSTRAINT `FK_UTILISATEUR_idCateg_categUser` FOREIGN KEY (`idCateg_categUser`) REFERENCES `categUser` (`idCateg`) ON DELETE CASCADE,
	CONSTRAINT FK_utilisateur_idLabo_labo FOREIGN KEY (idLabo_labo) REFERENCES labo(idLabo) ON DELETE CASCADE
) ENGINE=InnoDB;


CREATE TABLE `test` (
	`idTest` int(3) NOT NULL AUTO_INCREMENT,
	`nomTest` varchar(30) NOT NULL,
	idLabo_labo int(2) NOT NULL,
	PRIMARY KEY (`idTest`),
	CONSTRAINT FK_test_idLabo_labo FOREIGN KEY (idLabo_labo) REFERENCES labo(idLabo)
) ENGINE=InnoDB;
ALTER TABLE `test` ADD INDEX ( `nomTest` );


CREATE TABLE `PV` (
	idPv int(9) NOT NULL AUTO_INCREMENT,
	datePv date,
	titrePv TEXT,
	idLabo_labo int(2) NOT NULL,
	PRIMARY KEY (idPv),
	CONSTRAINT FK_PV_idLabo_labo FOREIGN KEY (idLabo_labo) REFERENCES labo(idLabo)
) ENGINE=InnoDB;
ALTER TABLE `PV` ADD INDEX ( `datePv` );

CREATE TABLE `PVTest` (
	idPv_PV int(9) NOT NULL,
	numInstru_instrument varchar(20) NOT NULL,
	idTest_test int(3) NOT NULL,
	PRIMARY KEY (idPv_PV,numInstru_instrument,idTest_test),
	CONSTRAINT FK_test_idPv_PV FOREIGN KEY (idPv_PV) REFERENCES PV(idPv) ON DELETE CASCADE,
	CONSTRAINT FK_test_numInstru_instrument FOREIGN KEY (numInstru_instrument) REFERENCES instrument(numInstru) ON DELETE CASCADE,
	CONSTRAINT FK_test_idTest_test FOREIGN KEY (idTest_test) REFERENCES test(idTest)
) ENGINE=InnoDB;

CREATE TABLE `pret` (
	idPret int(9) NOT NULL AUTO_INCREMENT,
	nomPret varchar(100),
	nomCorresp varchar(50),
	idLabo_labo int(2) NOT NULL,
	idLocal_localisation integer(3),
	PRIMARY KEY (idPret),
	CONSTRAINT FK_pret_idLabo_labo FOREIGN KEY (idLabo_labo) REFERENCES labo(idLabo),
	CONSTRAINT FK_pret_idLocal_localisation FOREIGN KEY (idLocal_localisation) REFERENCES localisation(idLocal)
) ENGINE=InnoDB;

CREATE TABLE `concernePret` (
	idPret_pret int(9) NOT NULL,
	numInstru_instrument varchar(20) NOT NULL,
	datePret date NOT NULL,
	dateRetour date,
	PRIMARY KEY (idPret_pret,numInstru_instrument),
	CONSTRAINT FK_concernePret_idPret_pret FOREIGN KEY (idPret_pret) REFERENCES pret(idPret) ON DELETE CASCADE,
	CONSTRAINT FK_concernePret_numInstru_instrument FOREIGN KEY (numInstru_instrument) REFERENCES instrument(numInstru) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `histo_pret` (
	idPret int(9) NOT NULL,
	nomPret varchar(100),
	nomCorresp varchar(50),
	idLabo_labo int(2) NOT NULL,
	idLocal_localisation integer(3),
	PRIMARY KEY (idPret),
	CONSTRAINT FK_histo_pret_idLabo_labo FOREIGN KEY (idLabo_labo) REFERENCES labo(idLabo),
	CONSTRAINT FK_histo_pret_idLocal_localisation FOREIGN KEY (idLocal_localisation) REFERENCES localisation(idLocal)
) ENGINE=InnoDB;

CREATE TABLE `histo_concernePret` (
	idPret_histo_pret int(9) NOT NULL,
	numInstru_instrument varchar(20) NOT NULL,
	datePret date NOT NULL,
	dateRetour date,
	PRIMARY KEY (idPret_histo_pret,numInstru_instrument,datePret),
	CONSTRAINT FK_histo_concernePret_idPret_histo_pret FOREIGN KEY (idPret_histo_pret) REFERENCES histo_pret(idPret) ON DELETE CASCADE,
	CONSTRAINT FK_histo_concernePret_numInstru_instrument FOREIGN KEY (numInstru_instrument) REFERENCES instrument(numInstru) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE ajoutInstru (
	numInstru_instrument varchar(20) NOT NULL,
	idLabo_labo int(2) NOT NULL,
	PRIMARY KEY (numInstru_instrument),
	CONSTRAINT FK_ajoutInstru_idLabo_labo FOREIGN KEY (idLabo_labo) REFERENCES labo(idLabo),
	CONSTRAINT FK_ajoutInstru_numInstru_instrument FOREIGN KEY (numInstru_instrument) REFERENCES instrument(numInstru) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE projetBidon(
	idProjet int(9) NOT NULL AUTO_INCREMENT,
	idLocal_localisation integer(3),
	PRIMARY KEY (idProjet),
	CONSTRAINT FK_projetBidon_idLocal_localisation FOREIGN KEY (idLocal_localisation) REFERENCES localisation(idLocal)
) ENGINE=InnoDB;

CREATE TABLE concerneBidon(
	idProjet_projetBidon int(9) NOT NULL,
	idInstruCapt_instrument_vib_capteur integer(8) NOT NULL,
	ordre int(5) NOT NULL,
	pied int(3),
	PRIMARY KEY (idProjet_projetBidon,idInstruCapt_instrument_vib_capteur),
	CONSTRAINT FK_concerneBidon_idProjet_projetBidon FOREIGN KEY (idProjet_projetBidon) REFERENCES projetBidon(idProjet) ON DELETE CASCADE,
	CONSTRAINT FK_concerneBidon_idInstruCapt FOREIGN KEY (idInstruCapt_instrument_vib_capteur) REFERENCES instrument_vib_capteur(idInstruCapt) ON DELETE CASCADE
) ENGINE=InnoDB;
ALTER TABLE `concernebidon` ADD INDEX( `ordre`);

insert into categUser values(1,"Administrateur");
insert into categUser values(2,"Utilisateur EMC");
insert into categUser values(3,"Utilisateur VIB");
insert into categUser values(4,"Utilisateur VTH");
insert into utilisateur values(NULL,"adminmee","_41-9azq9cf95dacd226dcf43da376cdb6cbba7035218921","Administrateur",NULL,1);

insert into labo values (1,"EMC");
insert into labo values (2,"VIB");
insert into labo values (3,"VTH");

insert into statut values (1,"Disponible");
insert into statut values (2,"En calibration");
insert into statut values (3,"Prêté");
insert into statut values (4,"Non disponible");

insert into test values (NULL,"CS",1);
insert into test values (NULL,"RS",1);
insert into test values (NULL,"RE",1);
insert into test values (NULL,"CE",1);
insert into test values (NULL,"ESD",1);
insert into test values (NULL,"RF input",1);
insert into test values (NULL,"RF output",1);
insert into test values (NULL,"EUT power supply",1);
insert into test values (NULL,"Bonding/Grounding",1);

insert into localisation values (1,"Stock",NULL);
insert into localisation values (2,"Calibration",NULL);

SET GLOBAL event_scheduler = 1 ;

CREATE EVENT ev_suppVieuxPv
    ON SCHEDULE  EVERY 1 WEEK
    DO CALL suppVieuxPv();

DELIMITER // -- On change le délimiteur
CREATE PROCEDURE suppVieuxPv ()
BEGIN
	delete from pv where period_diff(date_format(curdate(), '%Y%m'), date_format(datepv, '%Y%m')) >2;
END
//
