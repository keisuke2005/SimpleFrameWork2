select 
    * 
from 
    refresh_tokens
where 
    refresh_token = ?
and
    extract(epoch from  current_timestamp - create_date) < expires
;