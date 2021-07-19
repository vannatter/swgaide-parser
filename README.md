# swgaide-parser

`CREATE TABLE resources (
        id int(11) unsigned NOT NULL AUTO_INCREMENT,
        name varchar(255) DEFAULT NULL,
        type_code varchar(200) DEFAULT NULL,
        type_name varchar(255) DEFAULT NULL,
        cr int(11) DEFAULT NULL,
        dr int(11) DEFAULT NULL,
        hr int(11) DEFAULT NULL,
        ma int(11) DEFAULT NULL,
        oq int(11) DEFAULT NULL,
        sr int(11) DEFAULT NULL,
        ut int(11) DEFAULT NULL,
        fl int(11) DEFAULT NULL,
        pe int(11) DEFAULT NULL,
        timestamp varchar(255) DEFAULT NULL,
        status int(11) DEFAULT 1,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
`
