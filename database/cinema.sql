
-- Database: cinema.sql

-- Drop tables if they already exists
DROP TABLE IF EXISTS `user`;
DROP TABLE IF EXISTS `cultural_piece_of_work`;
DROP TABLE IF EXISTS `user_attended`;
DROP TABLE IF EXISTS `cultural_event`;
DROP TABLE IF EXISTS `hall`;
DROP TABLE IF EXISTS `perfomer`;
DROP TABLE IF EXISTS `seat`;
DROP TABLE IF EXISTS `stars_in`;

-- Table structure for user
CREATE TABLE `user`(
    `username` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `surname` VARCHAR(255) NOT NULL,
    `date_of_birth` DATE NOT NULL,
    `phone_number` VARCHAR(255),
    `e_mail` VARCHAR(255),          
    `rights` ENUM('admin', 'redactor', 'cashier', 'viewer') NOT NULL,
    `password` VARCHAR(255) NOT NULL -- Should be saved by trigger because of hash guess
) ENGINE=InnoDB CHARSET=utf8;

-- Table for all the movies given user saw
CREATE TABLE `user_attended`(
    `username` VARCHAR(255) NOT NULL,
    `id_piece_of_work` VARCHAR(255) NOT NULL
) ENGINE=InnoDB CHARSET=utf8;

-- Table structure for performance
CREATE TABLE `cultural_piece_of_work`(
    `id_piece_of_work` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `genre` VARCHAR(255) NOT NULL,
    `type` VARCHAR(255) NOT NULL, -- should it be enum or just varchar?
    `picture` VARCHAR(255) NOT NULL, -- what about this one?
    `description` VARCHAR(1024) NOT NULL,
    `duration` INT NOT NULL,
    `rating` FLOAT(4, 2)
) ENGINE=InnoDB CHARSET=utf8;

-- Table structure event
CREATE TABLE `cultural_event`(
    `id_cultural_event` VARCHAR(255) NOT NULL,
    `date` DATE NOT NULL,
    `time` TIME NOT NULL,
    `price` FLOAT(4, 2) NOT NULL,
    `id_piece_of_work` VARCHAR(255) NOT NULL,
    `hall_num` VARCHAR(10) NOT NULL
) ENGINE=InnoDB CHARSET=utf8;

-- Table structure for hall
CREATE TABLE `hall`(
    `hall_num` VARCHAR(10) NOT NULL,
    `number_of_rows` INT NOT NULL,
    `number_of_columns` INT NOT NULL,
    `address` VARCHAR(255) NOT NULL 
) ENGINE=InnoDB CHARSET=utf8;

-- Table structure for performer
CREATE TABLE `performer`(
    `performer_id` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `surname` VARCHAR(255) NOT NULL
) ENGINE=InnoDB CHARSET=utf8;

-- Table structure for seat
CREATE TABLE `seat`(
    `cultural_event_id` VARCHAR(255) NOT NULL,
    -- we will see whether we need this or not
    -- `cultural_event_time` TIME NOT NULL, --why is this var name diffrent? than the one in cultural event?
    -- `cultural_event_date` DATE NOT NULL,
    `row` INT NOT NULL,
    `column` INT NOT NULL,
    `state` ENUM('taken', 'reserved','available') NOT NULL
) ENGINE=InnoDB CHARSET=utf8;

-- Table for connesting stars that star in a piece of work
CREATE TABLE `stars_in`(
    `stars_in_id` VARCHAR(255) NOT NULL,
    `performer_id` VARCHAR(255) NOT NULL,
    `id_piece_of_work` VARCHAR(255) NOT NULL
) ENGINE=InnoDB CHARSET=utf8;

-- Constraints for primary keys
ALTER TABLE `user`
ADD PRIMARY KEY(`username`);

ALTER TABLE `user_attended`
ADD PRIMARY KEY(`username`, `id_piece_of_work`);

ALTER TABLE `cultural_piece_of_work`
ADD PRIMARY KEY(`id_piece_of_work`);

ALTER TABLE `cultural_event`
ADD PRIMARY KEY(`id_cultural_event`);

ALTER TABLE `hall`
ADD PRIMARY KEY(`hall_num`);

ALTER TABLE `performer`
ADD PRIMARY KEY(`performer_id`);

ALTER TABLE `seat`
ADD PRIMARY KEY(`cultural_event_id`, `row`, `column`);

ALTER TABLE `stars_in`
ADD PRIMARY KEY(`stars_in_id`);

-- Constraints for foreign keys
ALTER TABLE `seat`
ADD CONSTRAINT FK_event_seat
FOREIGN KEY(`cultural_event_id`) REFERENCES `cultural_event`(`id_cultural_event`)
ON DELETE CASCADE;

ALTER TABLE `cultural_event`
ADD CONSTRAINT FK_event_hall
FOREIGN KEY(`hall_num`) REFERENCES `hall`(`hall_num`)
ON DELETE CASCADE;

ALTER TABLE `cultural_event`
ADD CONSTRAINT FK_event_piece_of_work
FOREIGN KEY(`id_piece_of_work`) REFERENCES `cultural_piece_of_work`(`id_piece_of_work`)
ON DELETE CASCADE;

ALTER TABLE `stars_in`
ADD CONSTRAINT FK_stars_in_performer
FOREIGN KEY(`performer_id`) REFERENCES `performer`(`performer_id`)
ON DELETE CASCADE;

ALTER TABLE `stars_in`
ADD CONSTRAINT FK_stars_in_work
FOREIGN KEY(`id_piece_of_work`) REFERENCES `cultural_piece_of_work`(`id_piece_of_work`)
ON DELETE CASCADE;

ALTER TABLE `user_attended`
ADD CONSTRAINT FK_user_user
FOREIGN KEY(`username`) REFERENCES `user`(`username`)
ON DELETE CASCADE;

ALTER TABLE `user_attended`
ADD CONSTRAINT FK_work_work
FOREIGN KEY(`id_piece_of_work`) REFERENCES `cultural_piece_of_work`(`id_piece_of_work`)
ON DELETE CASCADE;

-- INSERT TO USER
INSERT INTO `user` 
VALUES 
    ('admin','Jožko', 'Mrkvicka', '1989-01-11','0903456789','admin@admin.com','admin', 'admin'),
    ('red','Robert', 'Mrkvicka', '1979-05-10','0903456987','red@gmail.com','redactor', 'ahoj'),
    ('dolar','Ján', 'Mečiar', '1999-03-27','0903456789','admin@admin.com','cashier', 'fiko'),
    ('Maniac42','Adela', 'Ostrolúcka', '1889-08-03','0903456789','admin@admin.com','viewer', 'ahoj'),
    ('user','Jozko', 'Mrkvicka', '1989-11-01','0903456789','admin@admin.com','viewer', 'ahoj');

-- INSERT TO cultural_piece_of_work
INSERT INTO `cultural_piece_of_work`
VALUES 
    ('01234567899874561230', 'SomFilm','komédia','film','https://images-na.ssl-images-amazon.com/images/I/51Gh9OaWVcL.jpg','som popis','120','42'),
    ('01234567899874561242', 'SomInýFilm','komédia','film','https://images-na.ssl-images-amazon.com/images/I/51Gh9OaWVcL.jpg','som dlhý popis som dlhý popis som dlhý popis som dlhý popis som dlhý popis som dlhý popis som dlhý popis som dlhý popis som dlhý popis som dlhý popis som dlhý popis som dlhý popis som dlhý popis som dlhý popis ','120','42'),
    ('01234567899874561SAA', 'SomAkčňak','akčný','film','https://images-na.ssl-images-amazon.com/images/I/51Gh9OaWVcL.jpg','som popis','120','42'),
    ('01234567899874561KMP', 'SomDivadlo','dráma','divadlo','https://images-na.ssl-images-amazon.com/images/I/51Gh9OaWVcL.jpg','som popis','120','42');

-- INSERT TO hall 
INSERT INTO `hall`
VALUES
    ('A','10','10','P.Sherman 42, Malaby bay, Sydney'),
    ('B','10','9','P.Sherman 43, Malaby bay, Sydney');

-- INSERT TO cultural_event
INSERT INTO `cultural_event`
VALUES 
    ('PRODUCTA', '2019-11-01','10:00:00','4.99', '01234567899874561230', 'A'),
    ('PRODUCTB', '2019-11-01','13:00:00','4.99', '01234567899874561230', 'B'),
    ('PRODUCTC', '2019-09-01','10:00:00','4.99', '01234567899874561SAA', 'B');

-- INSERT TO performer
INSERT INTO `performer`
VALUES
    ('P1','John','Smith'),
    ('P2','Stan','Lee');

-- INSERT TO seat
INSERT INTO `seat`
VALUES
    ('PRODUCTA', '1','1','available'),
    ('PRODUCTA', '1','2','available');

-- relationship tables
-- INSERT TO user_attended
INSERT INTO `user_attended` 
VALUES
    ('dolar', '01234567899874561SAA');