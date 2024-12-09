insert into author (`name`)
select distinct *
from (
    select `Dessin 1` as author from `import` where `Dessin 1` is not null
    union
    select `Dessin 2` as author from `import` where `Dessin 2` is not null
    union
    select `Texte 1` as author from `import` where `Texte 1` is not null
    union
    select `Texte 2` as author from `import` where `Texte 2` is not null
) A
order by author;
