
-- Database: cinema.sql

-- Drop tables if they already exists
DROP TABLE IF EXISTS `user_attended`;
DROP TABLE IF EXISTS `user`;
DROP TABLE IF EXISTS `stars_in`;
DROP TABLE IF EXISTS `seat`;
DROP TABLE IF EXISTS `performer`;
DROP TABLE IF EXISTS `cultural_event`;
DROP TABLE IF EXISTS `hall`;
DROP TABLE IF EXISTS `cultural_piece_of_work`;





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
    `id_piece_of_work` int(8) NOT NULL
) ENGINE=InnoDB CHARSET=utf8;

-- Table structure for performance
CREATE TABLE `cultural_piece_of_work`(
    `id_piece_of_work` int(8) PRIMARY KEY NOT NULL AUTO_INCREMENT,
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
    `id_cultural_event` int(8) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `date` DATE NOT NULL,
    `time` TIME NOT NULL,
    `price` FLOAT(4, 2) NOT NULL,
    `id_piece_of_work` int(8) NOT NULL,
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
    `performer_id` int(8) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `surname` VARCHAR(255) NOT NULL
) ENGINE=InnoDB CHARSET=utf8;

-- Table structure for seat
CREATE TABLE `seat`(
    `cultural_event_id` int(8) NOT NULL,
    -- we will see whether we need this or not
    -- `cultural_event_time` TIME NOT NULL, --why is this var name diffrent? than the one in cultural event?
    -- `cultural_event_date` DATE NOT NULL,
    `row` INT NOT NULL,
    `column` INT NOT NULL,
    `state` ENUM('taken', 'reserved','available') NOT NULL
) ENGINE=InnoDB CHARSET=utf8;

-- Table for connesting stars that star in a piece of work
CREATE TABLE `stars_in`(
    `stars_in_id` int(8) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `performer_id` int(8) NOT NULL,
    `id_piece_of_work` int(8) NOT NULL
) ENGINE=InnoDB CHARSET=utf8;

-- Constraints for primary keys
ALTER TABLE `user`
ADD PRIMARY KEY(`username`);

ALTER TABLE `user_attended`
ADD PRIMARY KEY(`username`, `id_piece_of_work`);

ALTER TABLE `hall`
ADD PRIMARY KEY(`hall_num`);

-- ALTER TABLE `performer`
-- ADD PRIMARY KEY(`performer_id`);

ALTER TABLE `seat`
ADD PRIMARY KEY(`cultural_event_id`, `row`, `column`);


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
    ('admin','Jožko', 'Mrkvicka', '1989-01-11','0903456789','admin@admin.com','admin', '$2y$10$4YH5Pyou8dsyCIHT0zbcEuReuZ86WAPSTUS0OvJB1Mc0.zNXA481q'), -- HESLO: admin
    ('red','Robert', 'Mrkvicka', '1979-05-10','0903456987','red@gmail.com','redactor', '$2y$10$rdwJ8T1z7.GB2p3AOFlc4eR1qAHqTkeY4fwB6Z2xT/s262lguovL6'), -- HESLO: ahoj
    ('dolar','Ján', 'Mečiar', '1999-03-27','0903456789','admin@admin.com','cashier', '$2y$10$8Z/97BmOjIIel9yTqUW/kO/MeHxeKtU8gxdQEuiQqRfuejCadYG2K'),   -- HESLO: fiko
    ('Maniac42','Adela', 'Ostrolúcka', '1889-08-03','0903456789','admin@admin.com','viewer', '$2y$10$MRn3.qFHltGEasjknt0XeOYTGlniT4OVVqWdHr5T5tgi4REe.WVmy'), -- HESLO: ahoj
    ('user','Jozko', 'Mrkvicka', '1989-11-01','0903456789','admin@admin.com','viewer', '$2y$10$hpN46XsemmBP35MoapSMZu4rC38Q8WUqgfytiWnZcsLi1AuqNGKHy'); -- HESL: ahoj

-- INSERT TO cultural_piece_of_work
INSERT INTO `cultural_piece_of_work` (`name`, `genre`, `type`, `picture`, `description`, `duration`, `rating`)
VALUES 
    ('Deadpool','komédia','film','https://images-na.ssl-images-amazon.com/images/I/51Gh9OaWVcL.jpg','Než se stal Deadpoolem, byl Wadem Wilsonem (Ryan Reynolds), bývalým členem speciálních jednotek. Když mu lékaři diagnostikovali pokročilé stadium rakoviny, podrobil se experimentální léčbě v rámci programu Weapon X, známého a populárního především mezi x-menovskými mutanty. Díky tomu se Wade zbavil rakoviny, a nejenže zůstal naživu, ale také jako bonus získal schopnost rychlého samouzdravování. Bohužel však – například oproti Wolverinovi, který po podobné léčbě zůstal celkem sexy – z tohoto pokusu vyšel trvale znetvořený. ','108','81'),
    ('Harry Potter a Tajomná komnata','dobrodružný','film','https://image.tmdb.org/t/p//w780//ygsu82q2YSrIdePnM2GLGjsFFjr.jpg','Harry Potter se po prázdninách vrací do Bradavic a nastupuje do druhého ročníku. A to i přes varování domácího skřítka Dobbyho, podle kterého mu v čarodějné škole hrozí smrt. Harry nedbá nářků skřítka působícího víc škody než užitku, ale potom se skutečně začnou dít podivné věci, na stěnách se objevují neznámé nápisy a několik studentů je přepadeno tajemným přízrakem. Co s tím má společného Tajemná komnata? ','161','77'),
    ('Rýchlo a zbesilo','akčný','film','https://i.pinimg.com/564x/9f/8d/5a/9f8d5a00f1f8885dd6d4b8282e9273d5.jpg','Brianovi se konečně podaří stát se členem party vedené Dominicem, který je doslova blázen do nelegálních závodů, pořádaných většinou v noci na periférii Los Angeles. Je to obrovské divadlo pro obecenstvo zběsilých jízd, ale především posedlost pro piloty speciálně upravených superauťáků. S ohlušujícím řevem motorů, řítící se městem několikanásobkem povolené rychlosti, pění všem adrenalin v krvi. Ve hře jsou peníze, prestiž, obdiv dívek.','106','72'),
    ('Star Wars: The Force Awakens','dráma','film','https://m.media-amazon.com/images/M/MV5BOTAzODEzNDAzMl5BMl5BanBnXkFtZTgwMDU1MTgzNzE@._V1_.jpg','Luke Skywalker zmizel a galaxie zažívá temné časy. Postupně ji totiž začíná ovládat zločinecký První řád, který vzniknul na troskách Impéria. Proti Prvnímu řádu se vytvořil Odpor, do jehož čela se postavila princezna Leia. Oběma stranám je jasné, že pokud se jim podaří najít Luka Skywalkera dříve než jejich nepřátelům, vítězství bude jejich. Na planetě Jakku se právě vylodili vojáci Prvního řádu, takzvaní stormtroopeři, kteří zde hledají mapu cesty, jež má být klíčem k nalezení Luka.','136','76');

-- INSERT TO hall 
INSERT INTO `hall`
VALUES
    ('A','10','10','P.Sherman 42, Malaby bay, Sydney'),
    ('B','10','9','P.Sherman 43, Malaby bay, Sydney');

-- INSERT TO cultural_event
INSERT INTO `cultural_event` (`date`, `time`, `price`, `id_piece_of_work`, `hall_num`)
VALUES 
    ('2019-11-01','10:00:00','4.99', '1', 'A'),
    ('2019-11-01','13:00:00','4.99', '2', 'B'),
    ('2019-09-01','10:00:00','4.99', '2', 'B');

-- INSERT TO performer
INSERT INTO `performer` (`name`, `surname`)
VALUES
    ('John','Smith'),
    ('Stan','Lee'),
    ('Daniel', 'Radcliffe'),
    ('Ryan', 'Reynolds');

-- INSERT TO seat
INSERT INTO `seat`
VALUES
    ('1', '1','1','available'),
    ('1', '1','2','available');

-- relationship tables
-- INSERT TO user_attended
INSERT INTO `user_attended` 
VALUES
    ('dolar', '1'),
    ('Maniac42', '2');


-- kto kde hrá
-- INSERT TO stars_in
INSERT INTO `stars_in` (`performer_id`, `id_piece_of_work`)
VALUES
    ((SELECT performer_id FROM performer WHERE name = 'Ryan' AND surname = 'Reynolds'), '1'),     -- Ryan Reynolds - DeadPool
    ((SELECT performer_id FROM performer WHERE name = 'Daniel' AND surname = 'Radcliffe'), '2');

