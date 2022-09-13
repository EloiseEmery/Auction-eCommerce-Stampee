/* TABLES */
CREATE TABLE UtilisateurRole (
    role_id smallint(6) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    role varchar(40) NOT NULL
);

CREATE TABLE TimbreCondition (
    condition_id smallint(6) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    condition_timbre varchar(40) NOT NULL
);

CREATE TABLE TimbrePaysOrigine (
    pays_id smallint(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    pays_timbre varchar(255) NOT NULL
);

CREATE TABLE TimbreType (
    type_id smallint(6) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    type_timbre varchar(40) NOT NULL
);

CREATE TABLE Utilisateur (
	utilisateur_id smallint(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	utilisateur_nom varchar(50) NOT NULL,
	utilisateur_prenom varchar(50) NOT NULL,
	utilisateur_courriel varchar(255) NOT NULL UNIQUE,
	utilisateur_mdp varchar(255) NOT NULL,
	utilisateur_profil smallint(6) unsigned NOT NULL,
	FOREIGN KEY(utilisateur_profil) REFERENCES UtilisateurRole(role_id),
	PRIMARY KEY (utilisateur_id)
);

CREATE TABLE Timbre (
	timbre_id smallint(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
	timbre_nom varchar(255) NOT NULL,
	timbre_description text,
	timbre_format varchar(100) NOT NULL,
	timbre_annee_emission varchar(4),
	timbre_couleur varchar(100) NOT NULL,
	timbre_tirage varchar(255) NOT NULL,
	timbre_certifie tinyint(1) UNSIGNED NOT NULL,
	timbre_type smallint(6) UNSIGNED NOT NULL,
	timbre_condition smallint(6) UNSIGNED NOT NULL,
	timbre_pays smallint(11) UNSIGNED NOT NULL,
	timbre_utilisateur smallint(11) UNSIGNED NOT NULL,
	FOREIGN KEY(timbre_type) REFERENCES TimbreType(type_id),
    FOREIGN KEY(timbre_condition) REFERENCES TimbreCondition(condition_id),
	FOREIGN KEY(timbre_pays) REFERENCES TimbrePaysOrigine(pays_id),
	FOREIGN KEY(timbre_utilisateur) REFERENCES Utilisateur(utilisateur_id)
);

CREATE TABLE Images (
	image_id smallint(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
	image_url text,
	image_titre varchar(255),
	image_principale tinyint(1) UNSIGNED,
	timbre_id smallint(11) UNSIGNED NOT NULL,
	FOREIGN KEY(timbre_id) REFERENCES Timbre(timbre_id)
);

CREATE TABLE Enchere (
	enchere_id smallint(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
	enchere_date_debut date,
	enchere_date_fin date,
	enchere_prix_plancher DOUBLE,
	enchere_archive tinyint(1) UNSIGNED,
	timbre_id smallint(11) UNSIGNED NOT NULL,
	FOREIGN KEY(timbre_id) REFERENCES Timbre(timbre_id)
);

CREATE TABLE Commentaire (
	commentaire_id smallint(6) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
	commentaire text,
	commentaire_date_publication date,
	enchere_id smallint(11) UNSIGNED NOT NULL,
	utilisateur_id smallint(11) UNSIGNED NOT NULL,
	FOREIGN KEY(enchere_id) REFERENCES Enchere(enchere_id),
	FOREIGN KEY(utilisateur_id) REFERENCES Utilisateur(utilisateur_id)
);

CREATE TABLE Offre (
    offre_mise DOUBLE,
	offre_date_mise datetime,
    enchere_id smallint(11) UNSIGNED NOT NULL, 
    utilisateur_id smallint(11) UNSIGNED NOT NULL,
    FOREIGN KEY(enchere_id) REFERENCES Enchere(enchere_id),
    FOREIGN KEY(utilisateur_id) REFERENCES Utilisateur(utilisateur_id),
    PRIMARY KEY(enchere_id, utilisateur_id, offre_date_mise)
);

CREATE TABLE Favoris (
    enchere_id smallint(11) UNSIGNED NOT NULL, 
    utilisateur_id smallint(11) UNSIGNED NOT NULL,
    FOREIGN KEY(enchere_id) REFERENCES Enchere(enchere_id),
    FOREIGN KEY(utilisateur_id) REFERENCES Utilisateur(utilisateur_id),
    PRIMARY KEY(enchere_id, utilisateur_id)
);


/* INSERTIONS */
INSERT INTO UtilisateurRole (role) VALUES ("utilisateur"), ("administrateur");

INSERT INTO Utilisateur VALUES
(1, "Emery", "Eloise", "eloise.emery@hotmail.com", SHA2("abc123", 512), 2),
(2, "Dembele", "Mamadou", "mamadou.dembele@hotmail.com", SHA2("abc123", 512), 1),
(3, "Chevalier",  "Guillaume", "guillaume.chevalier@hotmail.com", SHA2("abc123", 512), 1);

INSERT INTO TimbreCondition (condition_timbre) VALUES
("Parfaite"),
("Excellente"),
("Bonne"),
("Moyenne"),
("Endommagé");

INSERT INTO TimbreType (type_timbre) VALUES
("Général"),
("Courrier aérien"),
("Livret"),
("Port dû"),
("Carte postale"),
("Semi postal"),
("Entier postal");

INSERT INTO TimbrePaysOrigine (pays_timbre) VALUES
('Afghanistan')
,('Aland Islands')
,('Albania')
,('Algeria')
,('American Samoa')
,('Andorra')
,('Angola')
,('Anguilla')
,('Antarctica')
,('Argentina')
,('Armenia')
,('Aruba')
,('Australia')
,('Austria')
,('Azerbaijan')
,('Bahamas')
,('Bahrain')
,('Bangladesh')
,('Barbados')
,('Belarus')
,('Belgium')
,('Belize')
,('Benin')
,('Bermuda')
,('Bhutan')
,('Bolivia')
,('Bonaire')
,('Bosnia')
,('Botswana')
,('Bouvet Island')
,('Brazil')
,('Brunei')
,('Bulgaria')
,('Burkina Faso')
,('Burundi')
,('Cambodia')
,('Cameroon')
,('Canada')
,('Cape Verde')
,('Cayman Islands')
,('Chad')
,('Chile')
,('China')
,('Christmas Island')
,('Colombia')
,('Comoros')
,('Congo')
,('Cook Islands')
,('Costa Rica')
,('Ivory Coast')
,('Croatia')
,('Cuba')
,('Curacao')
,('Cyprus')
,('Czech Republic')
,('Denmark')
,('Djibouti')
,('Dominica')
,('Dominican Republic')
,('Ecuador')
,('Egypt')
,('El Salvador')
,('Equatorial Guinea')
,('Eritrea')
,('Estonia')
,('Ethiopia')
,('Faroe Islands')
,('Fiji')
,('Finland')
,('France')
,('French Guiana')
,('French Polynesia')
,('Gabon')
,('Gambia')
,('Georgia')
,('Germany')
,('Ghana')
,('Gibraltar')
,('Greece')
,('Greenland')
,('Grenada')
,('Guadaloupe')
,('Guam')
,('Guatemala')
,('Guernsey')
,('Guinea')
,('Guinea-Bissau')
,('Guyana')
,('Haiti')
,('Honduras')
,('Hong Kong')
,('Hungary')
,('Iceland')
,('India')
,('Indonesia')
,('Iran')
,('Iraq')
,('Ireland')
,('Isle of Man')
,('Israel')
,('Italy')
,('Jamaica')
,('Japan')
,('Jersey')
,('Jordan')
,('Kazakhstan')
,('Kenya')
,('Kiribati')
,('Kosovo')
,('Kuwait')
,('Kyrgyzstan')
,('Laos')
,('Latvia')
,('Lebanon')
,('Lesotho')
,('Liberia')
,('Libya')
,('Liechtenstein')
,('Lithuania')
,('Luxembourg')
,('Macao')
,('Macedonia')
,('Madagascar')
,('Malawi')
,('Malaysia')
,('Maldives')
,('Mali')
,('Malta')
,('Marshall Islands')
,('Martinique')
,('Mauritania')
,('Mauritius')
,('Mayotte')
,('Mexico')
,('Micronesia')
,('Moldava')
,('Monaco')
,('Mongolia')
,('Montenegro')
,('Montserrat')
,('Morocco')
,('Mozambique')
,('Myanmar (ma)')
,('Namibia')
,('Nauru')
,('Nepal')
,('Netherlands')
,('New Caledonia')
,('New Zealand')
,('Nicaragua')
,('Niger')
,('Nigeria')
,('Niue')
,('Norfolk Island')
,('North Korea')
,('Norway')
,('Oman')
,('Pakistan')
,('Palau')
,('Palestine')
,('Panama')
,('Papua New Guinea')
,('Paraguay')
,('Peru')
,('Phillipines')
,('Pitcairn')
,('Poland')
,('Portugal')
,('Puerto Rico')
,('Qatar')
,('Reunion')
,('Romania')
,('Russia')
,('Rwanda')
,('Saint Barthelemy')
,('Saint Helena')
,('Saint Lucia')
,('Saint Martin')
,('Samoa')
,('San Marino')
,('Saudi Arabia')
,('Senegal')
,('Serbia')
,('Seychelles')
,('Sierra Leone')
,('Singapore')
,('Sint Maarten')
,('Slovakia')
,('Slovenia')
,('Solomon Islands')
,('Somalia')
,('South Africa')
,('South Korea')
,('South Sudan')
,('Spain')
,('Sri Lanka')
,('Sudan')
,('Suriname')
,('Swaziland')
,('Sweden')
,('Switzerland')
,('Syria')
,('Taiwan')
,('Tajikistan')
,('Tanzania')
,('Thailand')
,('Togo')
,('Tokelau')
,('Tonga')
,('Tunisia')
,('Turkey')
,('Turkmenistan')
,('Tuvalu')
,('Uganda')
,('Ukraine')
,('United Kingdom')
,('United States')
,('Uruguay')
,('Uzbekistan')
,('Vanuatu')
,('Vatican City')
,('Venezuela')
,('Vietnam')
,('Western Sahara')
,('Yemen')
,('Zambia')
,('Zimbabwe');

INSERT INTO Timbre (
			timbre_id,
			timbre_nom,     	   
			timbre_description,  
			timbre_format, 	 	 
			timbre_annee_emission,  
			timbre_couleur,		   
			timbre_tirage,		   
			timbre_certifie, 	   
			timbre_type, 		  
			timbre_condition,	   
			timbre_pays, 		  
			timbre_utilisateur) VALUES 
			(1, 'CYPRUS 95 LH', 'description de mon premier timbre.', 'bloc 3 timbres, 3x6 pc', 1996, 'rouge et bleu', 10, 1, 3, 1, 217, 1),
			(2, 'AFFORDABLE GENUINE SCOTT USED SET', 'description de mon deuxième timbre.', 'bloc 6 timbres, 4x8 pc', 2001, 'vert, rouge, orange, bleu et brun', 50, 1, 3, 3, 13, 2),
			(3, 'US California Scott', 'description de mon troisième timbre.', '1 timbre, 1x1 pc', 2022, 'beige et vert', 45, 0, 6, 3, 217, 2),
			(4, 'Used 50¢ XF Well Centered GEM With PFC Graded', 'description de mon quatrime timbre.', 'bloc 1 timbre, 4x2 pc', 1990, 'bleu et gris', 5, 1, 4, 4, 21, 1),
			(5, 'USA 1857 Scott #36 Used. Deep color', 'description de mon cinquième timbre.', 'bloc 1 timbre, 3x1.5 pc', 1856, 'vert', 1, 1, 7, 1, 217, 1);

INSERT INTO Enchere (
			enchere_date_debut, 
			enchere_date_fin, 
			enchere_prix_plancher, 
			enchere_archive,
			timbre_id) VALUES
			(STR_TO_DATE("2022-05-14", "%Y-%m-%d"), STR_TO_DATE("2022-05-30", "%Y-%m-%d"), 70.00, 0, 1),
			(STR_TO_DATE("2022-05-14", "%Y-%m-%d"), STR_TO_DATE("2022-05-17", "%Y-%m-%d"), 100.00, 1, 1),
			(STR_TO_DATE("2022-05-08", "%Y-%m-%d"), STR_TO_DATE("2022-05-10", "%Y-%m-%d"), 20.00, 1, 2),
			(STR_TO_DATE("2022-05-02", "%Y-%m-%d"), STR_TO_DATE("2022-05-27", "%Y-%m-%d"), 50.00, 0, 2),
			(STR_TO_DATE("2022-05-01", "%Y-%m-%d"), STR_TO_DATE("2022-05-29", "%Y-%m-%d"), 75.00, 0, 4),
			(STR_TO_DATE("2022-05-16", "%Y-%m-%d"), STR_TO_DATE("2022-05-28", "%Y-%m-%d"), 5.00, 0, 3),
			(STR_TO_DATE("2022-05-25", "%Y-%m-%d"), STR_TO_DATE("2022-05-30", "%Y-%m-%d"), 10.00, 0, 5);

INSERT INTO Images (`image_url`, `image_titre`, `image_principale`, `timbre_id`) VALUES
('default-timbre-image-principale.jpg', 'image principale par défaut', 1, 1),
('default-timbre-image-principale.jpg', 'image principale par défaut', 1, 2),
('default-timbre-image-principale.jpg', 'image principale par défaut', 1, 3),
('default-timbre-image-principale.jpg', 'image principale par défaut', 1 , 4),
('default-timbre-image-principale.jpg', 'image principale par défaut', 1 , 5);