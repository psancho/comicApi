insert into series (title)
select distinct concat(upper(left(`Série`, 1)), lower(substring(`Série`, 2))) from import
where `Série` not in ('zz derniers', 'AUTEURS', 'DIVERS', 'BRETECHER', 'ENKI BILAL', 'LOUP', 'MANARA', 'MORDILLO', 'REISER', 'SERRE', 'WOLINSKI');
