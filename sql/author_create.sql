drop table if exists author;

create table author (
    id int unsigned not null auto_increment,
    `name` varchar(45) default null,
    primary key (id)
);
