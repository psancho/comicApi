select
    S.id S_id,
    S.title,
    -- I.`Série`,
    -- I.`n°`,
    case
        when I.`n°` = 0 then null else I.`n°`
    end `n°`,
    I.`Titre`,
    -- P.id P_id,
    P.`name` P_name,
    -- I.`Editeur`,
    -- D1.id D1_id,
    D1.`name` D1_name,
    -- I.`Dessin 1`,
    -- T1.id T1_id,
    T1.`name` T1_name,
    -- I.`Texte 1`,
    -- D2.id D2_id,
    D2.`name` D2_name,
    -- I.`Dessin 2`,
    -- T2.id T2_id,
    T2.`name` T2_name,
    -- I.`Texte 2`,
    I.`J'ai` -- owned
    + case -- author_series
        when I.`Série` in ('BRETECHER', 'ENKI BILAL', 'LOUP', 'MANARA', 'MORDILLO', 'REISER', 'SERRE', 'WOLINSKI') then 2 else 0
    end
    + case -- biopic
        when I.`Série`= 'AUTEURS' then 4 else 0
    end flags,

	null `_`
from `import` I
left join series S on S.title like I.`Série`
left join publisher P on P.`name` like I.`Editeur`
left join author D1 on D1.`name` like I.`Dessin 1`
left join author D2 on D2.`name` like I.`Dessin 2`
left join author T1 on T1.`name` like I.`Texte 1`
left join author T2 on T2.`name` like I.`Texte 2`

left join `import` Z on
    Z.`Série` = 'zz derniers'
    and Z.titre like concat(I.`Série`, '%')
    and Z.`n°` = I.`n°`

where I.`Série` != 'zz derniers'

order by
    case
        when S.title is null then D1.`name` else S.title
    end,
    I.`n°`
