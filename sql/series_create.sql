drop table if exists series;

create table series (
    id int unsigned not null auto_increment,
    title varchar(45) default null,
    primary key (id)
);
