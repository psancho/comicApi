drop table if exists book;

create table book (
    id int unsigned not null auto_increment,
    series_id int unsigned null default null,
    `number` int unsigned null default null,
    title varchar(128) not null,
    publisher_id int unsigned not null,
    flags tinyint unsigned not null default 0
        comment '1: owned, 2: author_series, 4: biopic',
    primary key (id),
    constraint `fk_book_series_id` foreign key (`series_id`) references `series` (`id`) on delete cascade on update restrict,
    constraint `fk_book_publisher_id` foreign key (`publisher_id`) references `publisher` (`id`) on delete cascade on update restrict
);
