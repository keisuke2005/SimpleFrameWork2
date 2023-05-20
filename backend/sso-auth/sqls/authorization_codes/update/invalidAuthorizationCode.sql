update 
    authorization_codes
set
    delete_date = current_timestamp
where
    id = ?
and 
    delete_date is null
;