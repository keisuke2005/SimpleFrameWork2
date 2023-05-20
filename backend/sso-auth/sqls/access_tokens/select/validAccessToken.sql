select 
    * 
from 
    access_tokens
where 
    access_token = ?
and
    extract(epoch from  current_timestamp - create_date) < expires
;