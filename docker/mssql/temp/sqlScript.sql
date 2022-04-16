USE company;

DROP TABLE IF EXISTS Noten;

DROP TABLE IF EXISTS Nutzer;

create table Nutzer (
    userId int auto_increment,
    username varchar(500),
    durchschnitt FLOAT default 0.00,
    passwort varchar(500),
    primary key(userId)
);

INSERT INTO
    Nutzer (username, passwort)
VALUES
    ("enes", "enes");

INSERT INTO
    Nutzer (username, passwort)
VALUES
    ("tolga", "tolga");


create table Noten (
    id int auto_increment,
    note decimal(2, 1),
    durchgefallen int default 0,
    credits int default 0,
    fach varchar(500),
    versuch int default 1,
    nutzer int,
    foreign key(nutzer) references Nutzer(userId),
    primary key(id)
);

/*
 --Add grade
 CREATE
 OR ALTER PROCEDURE addGrade @note DECIMAL(2, 1),
 @fach varchar(25),
 @userId int,
 @credits int AS
 INSERT INTO
 Noten (note, fach, nutzer, credits)
 VALUES
 (@note, @fach, @userId, @credits) --Get all grades
 CREATE
 OR ALTER PROCEDURE getGrades @userId int AS
 SELECT
 *
 FROM
 Noten
 WHERE
 nutzer = @userId --Add user
 CREATE
 OR ALTER PROCEDURE addUser @username varchar(25),
 @password varchar(128) AS
 INSERT INTO
 Nutzer (username, passwort)
 VALUES
 (@username, @password) --Get User
 CREATE
 OR ALTER PROCEDURE getUser @username varchar(25) AS
 INSERT INTO
 Nutzer (username)
 VALUES
 (@username) CREATE
 OR ALTER PROCEDURE getUser @username varchar(25) AS
 SELECT
 *
 FROM
 Nutzer
 WHERE
 username = @username --Delete grade
 CREATE
 OR ALTER PROCEDURE deleteGrade @id int AS
 DELETE FROM
 Noten
 WHERE
 id = @id --Update grade
 CREATE
 OR ALTER PROCEDURE updateGrade @note DECIMAL(2, 1),
 @id int AS
 UPDATE
 Noten
 SET
 note = @note
 WHERE
 id = @id --Get average grade
 CREATE
 OR ALTER PROCEDURE getAvg @userId int AS
 SELECT
 durchschnitt
 FROM
 Nutzer
 WHERE
 userId = @userId --Average grade
 CREATE
 OR ALTER PROCEDURE avgGrade @userId int AS DECLARE @avg DECIMAL(2, 1) = (
 SELECT
 SUM(credits * note) / SUM(credits) AS avg
 FROM
 Noten
 WHERE
 nutzer = @userId
 )
 UPDATE
 Nutzer
 SET
 durchschnitt = @avg
 WHERE
 userId = @userId --Trigger update grade
 CREATE
 OR ALTER TRIGGER deleteGradeTrigger ON Noten For DELETE AS BEGIN DECLARE @userId int = (
 SELECT
 nutzer
 FROM
 deleted
 ) --Calculate new average grade
 EXEC avgGrade @userId
 end --Set failure
 CREATE
 OR ALTER PROCEDURE setFail @versuche int,
 @note FLOAT,
 @id int AS IF @versuche > 2
 AND @note > 4.0
 UPDATE
 Noten
 SET
 durchgefallen = 1
 WHERE
 id = @id
 ELSE
 UPDATE
 Noten
 SET
 durchgefallen = 0
 WHERE
 id = @id --Trigger update grade
 CREATE
 OR ALTER TRIGGER updateGradeTrigger ON Noten
 after
 UPDATE
 AS BEGIN Declare @versuch int = (
 SELECT
 versuch
 FROM
 inserted
 ) DECLARE @note DECIMAL(3, 2) = (
 SELECT
 note
 FROM
 inserted
 ) DECLARE @userId int = (
 SELECT
 nutzer
 FROM
 inserted
 ) DECLARE @id int = (
 SELECT
 id
 FROM
 inserted
 ) --Check if user didnt pass exam
 EXEC setFail @versuch,
 @note,
 @id --Calculate new average grade
 EXEC avgGrade @userId
 end --Trigger insert grade
 CREATE
 OR ALTER TRIGGER didntPassTrigger ON Noten
 AFTER
 INSERT
 AS BEGIN DECLARE @user int = (
 SELECT
 nutzer
 FROM
 inserted
 ) DECLARE @fach varchar(25) = (
 SELECT
 fach
 FROM
 inserted
 ) DECLARE @note DECIMAL(3, 2) = (
 SELECT
 note
 FROM
 inserted
 ) DECLARE @id int = (
 SELECT
 id
 FROM
 inserted
 ) IF (
 SELECT
 COUNT(*) AS counter
 FROM
 Noten
 WHERE
 nutzer = @user
 AND fach = @fach
 ) >= 2 BEGIN --Delete new insert
 DELETE FROM
 Noten
 where
 id = @id DECLARE @versuch int = (
 SELECT
 versuch
 FROM
 Noten
 WHERE
 nutzer = @user
 AND fach = @fach
 ) + 1 IF @versuch <= 3 BEGIN --Update grade
 UPDATE
 Noten
 SET
 note = @note,
 versuch = versuch + 1
 WHERE
 nutzer = @user
 AND fach = @fach --Check if user didnt pass exam
 DECLARE @nId int = (
 SELECT
 id
 FROM
 Noten
 WHERE
 nutzer = @user
 AND fach = @fach
 ) EXEC setFail @versuch,
 @note,
 @nId
 END
 ELSE
 DELETE FROM
 Noten
 where
 id = @id
 END --Calculate new average grade
 EXEC avgGrade @user
 END
 */