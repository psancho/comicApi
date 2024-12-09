select
    max(I.`n°`) `number`,
    I.`Série` series
from import I
group by I.`Série`
