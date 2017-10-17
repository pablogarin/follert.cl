CREATE TABLE rol(
    id integer primary key autoincrement not null,
    nombre text
);
CREATE TABLE usuario(
    id integer primary key autoincrement not null,
    username text not null,
    password text not null,
    rol integer references rol(id),
    fechacreacion datetime default CURRENT_TIMESTAMP,
    activo integer default 1
);
CREATE TABLE configs(
    id integer primary key autoincrement not null,
    nombre text unique,
    valor text
);
CREATE TABLE texto(
    id integer primary key autoincrement not null,
    titulo varchar(255) not null,
    cuerpo text not null,
    llave varchar(120),
    activo integer default 1
);
CREATE TABLE banner(
    id integer primary key autoincrement not null,
    foto text,
    nombre text,
    activo integer default 1
);
CREATE TABLE frase(
    id integer primary key autoincrement not null,
    frase text,
    activo integer default 1
);
CREATE TABLE producto(
    id integer primary key autoincrement not null,
    nombre text unique,
    foto text,
    descripcion text,
    fecha datetime default CURRENT_TIMESTAMP,
    ficha_pdf text,
    activo integer default 1
);
CREATE TABLE servicio(
    id integer primary key autoincrement not null,
    nombre text unique,
    foto text,
    descripcion text,
    fecha datetime default CURRENT_TIMESTAMP,
    activo integer default 1
);
