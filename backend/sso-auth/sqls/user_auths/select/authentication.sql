select
    *
from
    user_auths
where
    identifier = ?
and
    credential = ?
;