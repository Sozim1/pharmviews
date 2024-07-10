Aqui voce pode adaptar para o seu tipo de framework que ele ira funcionar.
     Caso de duvida em apliocações como cakePHP, codeigniter, fique a vontade para entrar em contato


CREATE TABLE IF NOT EXISTS tipo_acao
(
    codigo_acao INT AUTO_INCREMENT PRIMARY KEY,
    nome_acao VARCHAR(100) NOT NULL
    );

CREATE TABLE IF NOT EXISTS acao
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_acao INT,
    investimento DOUBLE,
    data_prevista DATE,
    data_cadastro DATE,
    FOREIGN KEY (codigo_acao) REFERENCES tipo_acao (codigo_acao)
    );

INSERT INTO tipo_acao (nome_acao) VALUES
                                      ('Palestra'),
                                      ('Evento'),
                                      ('Apoio Gráfico');