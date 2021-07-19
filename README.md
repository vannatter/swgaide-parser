# swgaide-parser

`CREATE TABLE resources (
        id int(11) unsigned NOT NULL AUTO_INCREMENT,
        name varchar(255) DEFAULT NULL,
        type_code varchar(200) DEFAULT NULL,
        type_name varchar(255) DEFAULT NULL,
        cr int(11) DEFAULT 0,
        dr int(11) DEFAULT 0,
        hr int(11) DEFAULT 0,
        ma int(11) DEFAULT 0,
        oq int(11) DEFAULT 0,
        sr int(11) DEFAULT 0,
        ut int(11) DEFAULT 0,
        fl int(11) DEFAULT 0,
        pe int(11) DEFAULT 0,
        timestamp varchar(255) DEFAULT NULL,
        status int(11) DEFAULT 1,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
`
