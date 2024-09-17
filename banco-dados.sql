create database crudLoginPHP;
use crudLoginPHP;

create table usuario (

	usuario_id int primary key auto_increment,
    nome varchar (255) not null,
    telefone varchar(11) not null unique,
    email varchar(255) not null unique,
    cpf varchar(11) not null unique,
    senha varchar(255) not null
);
