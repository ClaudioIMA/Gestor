CREATE TABLE gestor_equipamento
(
    id INTEGER PRIMARY KEY NOT NULL,
    name TEXT,
    setor TEXT,
    ativo char(1)
);

CREATE TABLE gestor_produto
(
    id INTEGER PRIMARY KEY NOT NULL,
    codigo TEXT,
    descricao TEXT,
    prod_hora INT,
    ciclo FLOAT,
    ativo char(1)
);

CREATE TABLE gestor_ordem
(
    id INTEGER PRIMARY KEY NOT NULL,
    numero INT,
    produto_id INT,
    qtde INT,
    data_inicio timestamp,
    data_fim timestamp,
    status TEXT,
    ativo char(1),
    FOREIGN KEY(produto_id) REFERENCES gestor_produto(id)
);

CREATE TABLE gestor_agenda
(
    id INTEGER PRIMARY KEY NOT NULL,
    eqpto_id INT,
    ordem_id INT,
    system_user_id INT,
    data_inicio timestamp,
    data_previsao timestamp,
    FOREIGN KEY(system_user_id) REFERENCES system_user(id),
    FOREIGN KEY(ordem_id) REFERENCES gestor_ordem(id),
    FOREIGN KEY(eqpto_id) REFERENCES gestor_equipamento(id)
);


CREATE TABLE gestor_perda
(
    id INTEGER PRIMARY KEY NOT NULL,
    codigo INT,
    descricao TEXT,
    message TEXT,
    eqpto_id INT,
    ativo char(1),
    FOREIGN KEY(eqpto_id) REFERENCES gestor_equipamento(id)
);

CREATE TABLE gestor_apontamento
(
    id INTEGER PRIMARY KEY NOT NULL,
    system_user_id INT,
    ordem_id INT,
    quantidade INT,
    perda_id INT,
    qtde_perda INT,
    data timestamp,
    ativo char(1),
    FOREIGN KEY(system_user_id) REFERENCES system_user(id),
    FOREIGN KEY(ordem_id) REFERENCES gestor_ordem(id),
    FOREIGN KEY(perda_id) REFERENCES gestor_perda(id)
);

CREATE TABLE gestor_parada
(
    id INTEGER PRIMARY KEY NOT NULL,
    codigo INT,
    descricao TEXT,
    eqpto_id INT,
    ativo char(1),
    FOREIGN KEY(eqpto_id) REFERENCES gestor_equipamento(id)
);

CREATE TABLE gestor_diario
(
    id INTEGER PRIMARY KEY NOT NULL,
    parada_id INT,
    system_user_id INT,
    eqpto_id INT,
    data_inicio datetime NULL,
    data_fim datetime NULL,
    FOREIGN KEY(system_user_id) REFERENCES system_user(id),
    FOREIGN KEY(eqpto_id) REFERENCES gestor_equipamento(id),
    FOREIGN KEY(parada_id) REFERENCES gestor_parada(id)
);

CREATE TABLE gestor_produto_ordens
(
    id INTEGER PRIMARY KEY NOT NULL,
    produto_id INT,
    ordem_id INT,
    FOREIGN KEY(produto_id) REFERENCES gestor_produto(id),
    FOREIGN KEY(ordem_id) REFERENCES gestor_ordem(id)
);
