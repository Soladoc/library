select pg_terminate_backend(pid)
    from pg_stat_activity
    where pid <> pg_backend_pid();

drop schema if exists library cascade;
create schema library;
