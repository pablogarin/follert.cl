CREATE TABLE cliente(
    id integer primary key autoincrement not null,
    nombre varchar(255) not null,
    logo varchar(128),
    activo integer default(1),
    orden float default 0.0
);
