drop table if exists `import`;

create table `import` (
    `Série` varchar(64) null default null,
    `n°` int null default null,
    `Titre` varchar(128) null default null,
    `Editeur` varchar(64) null default null,
    `J'ai` tinyint null default null,
    `Dessin 1` varchar(64) null default null,
    `Texte 1` varchar(64) null default null,
    `Dessin 2` varchar(64) null default null,
    `Texte 2` varchar(64) null default null
);
