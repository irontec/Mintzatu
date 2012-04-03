create table hdKarma_config(
id_config mediumint unsigned auto_increment,
iden varchar(20) not null unique,
value varchar(255),
primary key(id_config)
) ENGINE=INNODB default charset=UTF8;
insert into hdKarma_config(iden,value) values('path','/hdKarma/');

create table hdKarma_dirs(
id_dir mediumint unsigned auto_increment,
id_parent mediumint unsigned not null default 0, 
nombre varchar(255),
deleteable enum('0','1') not null default '0',
primary key(id_dir)
) ENGINE=INNODB default charset=UTF8; 


create table hdKarma_files(
id_file mediumint unsigned auto_increment,
nombre varchar(255),
url varchar(255),
crc32 varchar(10),
primary key(id_file)
) ENGINE=INNODB default charset=UTF8;
