select 
    * 
from 
    authorization_codes 
where 
    extract(epoch from  current_timestamp - create_date) < expires 
and 
    authorization_code = ?;