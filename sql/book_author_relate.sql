insert into book_author (book_id, author_id, contribution)
select
    B.id book_id,
    A.id author_id,
    'dessin' as contribution
from book B
left join series S on S.id = B.series_id
join import I on
    case when I.Titre = '-' then I.`Série` like B.title else I.Titre = B.title end
    and (B.`number` is null or I.`n°` = B.`number`)
    and (S.id is null or I.`Série` = S.title)
join author A on A.`name` like I.`Dessin 1`

union

select
    B.id book_id,
    A.id author_id,
    'texte' as contribution
from book B
left join series S on S.id = B.series_id
join import I on
    case when I.Titre = '-' then I.`Série` like B.title else I.Titre = B.title end
    and (B.`number` is null or I.`n°` = B.`number`)
    and (S.id is null or I.`Série` = S.title)
join author A on A.`name` like I.`Texte 1`

union

select
    B.id book_id,
    A.id author_id,
    'dessin' as contribution
from book B
left join series S on S.id = B.series_id
join import I on
    case when I.Titre = '-' then I.`Série` like B.title else I.Titre = B.title end
    and (B.`number` is null or I.`n°` = B.`number`)
    and (S.id is null or I.`Série` = S.title)
join author A on A.`name` like I.`Dessin 2`

union

select
    B.id book_id,
    A.id author_id,
    'texte' as contribution
from book B
left join series S on S.id = B.series_id
join import I on
    case when I.Titre = '-' then I.`Série` like B.title else I.Titre = B.title end
    and (B.`number` is null or I.`n°` = B.`number`)
    and (S.id is null or I.`Série` = S.title)
join author A on A.`name` like I.`Texte 2`
