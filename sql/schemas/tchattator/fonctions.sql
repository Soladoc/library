set schema 'tchattator';

set
    plpgsql.extra_errors to 'all';

create function send_msg (p_id_compte_sender int, p_id_compte_recipient int, p_content varchar) returns int as $$
select
    case
        when (
            -- check is not blocked globally
            select
                full_block_expires_at < localtimestamp
            from
                pact._membre
            where
                id = p_id_compte_sender
        )
        or (
            -- or by recipient
            select
                expires_at < localtimestamp
            from
                _single_block
            where
                id_membre = p_id_compte_sender
                and id_professionnel = p_id_compte_recipient
        ) then 0 -- errstatus_error
        else (
            insert into
                _msg (id_compte_sender, id_compte_recipient, content)
            values
                -- condisder 0 as the admin user id.
                (nullif(p_id_compte_sender, 0), p_id_compte_recipient, p_content)
            returning
                msg_id
        )
    end;
$$ language sql strict stable;