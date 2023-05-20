insert into 
authorization_codes(
    user_auth_id,
    authorization_code,
    expires
) 
values(
    ?,
    ?,
    ?
);