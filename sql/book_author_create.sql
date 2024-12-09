drop table if exists book_author;

create table book_author (
    book_id int unsigned not null,
    author_id int unsigned not null,
    contribution varchar(64) null default null,
    primary key (book_id, author_id),
    constraint `fk_book_author_book_id` foreign key (`book_id`) references `book` (`id`) on delete no action on update restrict,
    constraint `fk_book_author_author_id` foreign key (`author_id`) references `author` (`id`) on delete no action on update restrict
);
