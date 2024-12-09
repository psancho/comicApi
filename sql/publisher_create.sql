drop table if exists publisher;

create table publisher (
    id int unsigned not null auto_increment,
    `name` varchar(45) default null,
    primary key (id)
);
