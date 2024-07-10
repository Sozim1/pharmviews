CREATE DATABASE pharmaviews;

USE pharmaviews;

CREATE TABLE acoes (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       acao VARCHAR(255) NOT NULL,
                       data DATE NOT NULL,
                       investimento DECIMAL(10, 2) NOT NULL
);
