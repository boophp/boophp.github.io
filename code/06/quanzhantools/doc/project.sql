/*
qzuser Users     4
*/

CREATE TABLE ly_user_0
(
    uid int not NULL default '0' PRIMARY KEY,
    username varchar(32) NOT NULL default '',
    email varchar(64) NOT NULL default '',
    password varchar(32) NOT NULL default '',
    unique(email),
    unique(username)
)ENGINE=INNODB DEFAULT CHARSET=UTF8;



/**
0,2 => 3308
1,3 => 3301
*/


/*

qzuser Groups  
*/


CREATE TABLE ly_group(
    gid int not NULL default '0' PRIMARY KEY,
    groupname varchar(32) NOT NULL default ''
)ENGINE=INNODB DEFAULT CHARSET=UTF8;


/**
  all=>3308
*/

/*
qzphoto Albums 

*/


CREATE TABLE ly_album(
    albumid int not NULL default '0' PRIMARY KEY,
    uid int NOT NULL default '0',
    albumname varchar(32) NOT NULL default '',
    KEY(uid, albumid)
)ENGINE=INNODB DEFAULT CHARSET=UTF8;


/**
  all=>3308
*/
/*
qzphoto Photos  4
*/


CREATE TABLE ly_photo_0(
    pid int not NULL default '0' PRIMARY KEY,
    uid int NOT NULL default '0',
    albumid int NOT NULL default '0',
    title varchar(32) NOT NULL default '',
    logo varchar(64) NOT NULL default '',
    password varchar(32) NOT NULL default '',
   key(uid, albumid)
)ENGINE=INNODB DEFAULT CHARSET=UTF8;


/**
0,2 => 3308
1,3 => 3301
*/
