insert into book (series_id, `number`, title, publisher_id, flags)
select
	S.id series_id,
    case
        when I.`n°` = 0 then null else I.`n°`
    end `number`,
    case
        when I.Titre = '-' then concat(upper(left(`Série`, 1)), lower(substring(`Série`, 2))) else I.Titre
    end title,
    P.id publisher_id,
    I.`J'ai` -- owned
    + case -- author_series
        when I.`Série` in ('BRETECHER', 'ENKI BILAL', 'LOUP', 'MANARA', 'MORDILLO', 'REISER', 'SERRE', 'WOLINSKI') then 2 else 0
    end
    + case -- biopic
        when I.`Série`= 'AUTEURS' then 4 else 0
    end flags

from `import` I
left join series S on S.title like I.`Série`
left join publisher P on P.`name` like I.`Editeur`

where I.`Série` != 'zz derniers'

order by S.title, I.`n°`;
