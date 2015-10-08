CREATE TABLE usuario (
	id int(11) NOT NULL AUTO_INCREMENT,
	nome varchar(100) NOT NULL,
	id_facebook varchar(64) NOT NULL,
	tipo varchar(9) NOT NULL CHECK (tipo='aluno' OR tipo='professor'),
	email varchar(100) NOT NULL,
	PRIMARY KEY (id)
);

ALTER TABLE `usuario` ADD `id_universidade` INT(11) NULL DEFAULT NULL AFTER `email`;
ALTER TABLE usuario ADD FOREIGN KEY (id_universidade) REFERENCES universidade(id)

CREATE TABLE usuario_unesp (
	id int(11) NOT NULL AUTO_INCREMENT,
	nome varchar(100) NOT NULL,
	tipo varchar(9) NOT NULL CHECK (tipo='aluno' OR tipo='professor'),
	usuario varchar(50) NOT NULL UNIQUE,
	senha varchar(50) NOT NULL,
	id_universidade int(11),
	PRIMARY KEY (id),
	FOREIGN KEY (id_universidade) REFERENCES universidade(id)
);

CREATE TABLE universidade (
	id int(11) NOT NULL AUTO_INCREMENT,
	nome varchar(100) NOT NULL,
	PRIMARY KEY (id)
);

CREATE TABLE turma (
	id int(11) NOT NULL AUTO_INCREMENT,
	nome varchar(100) NOT NULL,
	data_criacao timestamp DEFAULT NOW(),
	id_professor int(11) NOT NULL,
	id_universidade int(11) NOT NULL,
	PRIMARY KEY (id),
	FOREIGN KEY (id_professor) REFERENCES usuario(id),
	FOREIGN KEY (id_universidade) REFERENCES universidade(id)
);

CREATE TABLE usuario_turma (
	id_usuario int(11) NOT NULL,
	id_turma int(11) NOT NULL,
	FOREIGN KEY (id_usuario) REFERENCES usuario(id),
	FOREIGN KEY (id_turma) REFERENCES turma(id),
	PRIMARY KEY (id_usuario, id_turma)
);

CREATE TABLE atividade (
	id int(11) NOT NULL AUTO_INCREMENT,
	id_turma int(11) NOT NULL,
	titulo varchar(100) NOT NULL,
	data_criacao timestamp NOT NULL,
	data_entrega timestamp,
	liberado boolean NOT NULL DEFAULT false,
	PRIMARY KEY (id),
	FOREIGN KEY (id_turma) REFERENCES turma(id)
);

CREATE TABLE resolucao (
	id_atividade int(11) NOT NULL,
	id_usuario int(11) NOT NULL,
	data_entrega timestamp DEFAULT NOW(),
	concluido boolean NOT NULL DEFAULT false,
	FOREIGN KEY (id_usuario) REFERENCES usuario(id),
	FOREIGN KEY (id_atividade) REFERENCES atividade(id),
	PRIMARY KEY (id_usuario, id_atividade)
);

ALTER TABLE resolucao ADD PRIMARY KEY (id_usuario, id_atividade);