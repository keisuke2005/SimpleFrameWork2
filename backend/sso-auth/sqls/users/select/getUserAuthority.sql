select 
    authority_code
from 
    users_by_access_token
where
    access_token = ?
;
