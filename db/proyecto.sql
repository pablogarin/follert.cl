CREATE TABLE proyecto(
    id integer primary key autoincrement not null,
    titulo varchar(255) not null,
    fecha datetime default CURRENT_TIMESTAMP,
    foto varchar(128),
    texto text,
    activo integer default 1,
    orden float
);
CREATE TABLE galeria(
    id integer primary key autoincrement not null,
    foto varchar(128) not null,
    proyecto integer references proyecto(id),
    activo integer default 1,
    orden float
);
