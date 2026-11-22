CREATE DATABASE hotel_db;

USE hotel_db;

CREATE TABLE tbHospedes (
    codigo_hospede INT AUTO_INCREMENT,
    nome_hospede VARCHAR(50) NOT NULL,
    cpf_hospede VARCHAR(14) NOT NULL,
    telefone_hospede VARCHAR(15),
    cidade_hospede VARCHAR(30),
    CONSTRAINT pk_hospedes PRIMARY KEY(codigo_hospede)
);

INSERT INTO tbHospedes(nome_hospede, cpf_hospede, telefone_hospede, cidade_hospede)
VALUES('Ana Silva', '123.456.789-10', '41999991111', 'Curitiba');
INSERT INTO tbHospedes(nome_hospede, cpf_hospede, telefone_hospede, cidade_hospede)
VALUES('Pedro Santos', '234.567.891-20', '41988882222', 'Londrina');
INSERT INTO tbHospedes(nome_hospede, cpf_hospede, telefone_hospede, cidade_hospede)
VALUES('Maria Oliveira', '345.678.912-30', '41977773333', 'Maringá');
INSERT INTO tbHospedes(nome_hospede, cpf_hospede, telefone_hospede, cidade_hospede)
VALUES('Lucas Ferreira', '456.789.123-40', '41966664444', 'Ponta Grossa');
INSERT INTO tbHospedes(nome_hospede, cpf_hospede, telefone_hospede, cidade_hospede)
VALUES('Julia Costa', '567.891.234-50', '41955553333', 'Cascavel');


CREATE TABLE tbFuncionarios (
    codigo_funcionario INT AUTO_INCREMENT,
    nome_funcionario VARCHAR(50) NOT NULL,
    cargo_funcionario VARCHAR(30),
    salario_funcionario DECIMAL(10,2) NOT NULL,
    telefone_funcionario VARCHAR(15),
    CONSTRAINT pk_funcionarios PRIMARY KEY(codigo_funcionario)
);

INSERT INTO tbFuncionarios(nome_funcionario, cargo_funcionario, salario_funcionario, telefone_funcionario)
VALUES('João Pereira', 'Recepcionista', 2500.00, '41999990000');
INSERT INTO tbFuncionarios(nome_funcionario, cargo_funcionario, salario_funcionario, telefone_funcionario)
VALUES('Carla Souza', 'Gerente', 4500.00, '41988880000');
INSERT INTO tbFuncionarios(nome_funcionario, cargo_funcionario, salario_funcionario, telefone_funcionario)
VALUES('Lucas Mendes', 'Camareiro', 2000.00, '41977770000');
INSERT INTO tbFuncionarios(nome_funcionario, cargo_funcionario, salario_funcionario, telefone_funcionario)
VALUES('Mariana Lima', 'Recepcionista', 2800.00, '41966660000');
INSERT INTO tbFuncionarios(nome_funcionario, cargo_funcionario, salario_funcionario, telefone_funcionario)
VALUES('Rafael Alves', 'Administrador', 5000.00, '41955550000');


CREATE TABLE tbTipos_Quarto (
    codigo_tipo INT AUTO_INCREMENT,
    nome_tipo VARCHAR(20) NOT NULL,
    descricao_tipo VARCHAR(50),
    valor_diaria DECIMAL(10,2) NOT NULL,
    capacidade INT,
    CONSTRAINT pk_tipos_quarto PRIMARY KEY(codigo_tipo)
);

INSERT INTO tbTipos_Quarto(nome_tipo, descricao_tipo, valor_diaria, capacidade)
VALUES('Simples', 'Quarto com uma cama', 150.00, 1);
INSERT INTO tbTipos_Quarto(nome_tipo, descricao_tipo, valor_diaria, capacidade)
VALUES('Duplo', 'Quarto com duas camas', 250.00, 2);
INSERT INTO tbTipos_Quarto(nome_tipo, descricao_tipo, valor_diaria, capacidade)
VALUES('Luxo', 'Quarto com cama king size', 400.00, 3);
INSERT INTO tbTipos_Quarto(nome_tipo, descricao_tipo, valor_diaria, capacidade)
VALUES('Familia', 'Quarto com tres camas', 350.00, 4);
INSERT INTO tbTipos_Quarto(nome_tipo, descricao_tipo, valor_diaria, capacidade)
VALUES('Premium', 'Quarto com hidromassagem', 550.00, 2);


CREATE TABLE tbReservas (
    codigo_reserva INT AUTO_INCREMENT,
    hospede_codigo INT NOT NULL,
    funcionario_codigo INT NOT NULL,
    data_entrada DATE NOT NULL,
    data_saida DATE NOT NULL,
    valor_total DECIMAL(10,2),
    CONSTRAINT pk_reservas PRIMARY KEY(codigo_reserva),
    CONSTRAINT fk_reservas_hospedes FOREIGN KEY(hospede_codigo) REFERENCES tbHospedes(codigo_hospede) ON DELETE cascade,
    CONSTRAINT fk_reservas_funcionarios FOREIGN KEY(funcionario_codigo) REFERENCES tbFuncionarios(codigo_funcionario) ON DELETE RESTRICT
);

INSERT INTO tbReservas(hospede_codigo, funcionario_codigo, data_entrada, data_saida, valor_total)
VALUES(1, 1, '2026-11-05', '2026-11-08', 450.00);
INSERT INTO tbReservas(hospede_codigo, funcionario_codigo, data_entrada, data_saida, valor_total)
VALUES(2, 2, '2026-11-10', '2026-11-12', 500.00);
INSERT INTO tbReservas(hospede_codigo, funcionario_codigo, data_entrada, data_saida, valor_total)
VALUES(3, 1, '2026-11-15', '2026-11-18', 1200.00);
INSERT INTO tbReservas(hospede_codigo, funcionario_codigo, data_entrada, data_saida, valor_total)
VALUES(4, 3, '2026-11-20', '2026-11-22', 700.00);
INSERT INTO tbReservas(hospede_codigo, funcionario_codigo, data_entrada, data_saida, valor_total)
VALUES(5, 4, '2026-11-25', '2026-11-28', 1650.00);


CREATE TABLE tbQuartos (
    codigo_quarto INT AUTO_INCREMENT,
    numero_quarto INT NOT NULL,
    andar_quarto INT,
    situacao_quarto VARCHAR(20),
    tipo_quarto_codigo INT NOT NULL,
    CONSTRAINT pk_quartos PRIMARY KEY(codigo_quarto),
    CONSTRAINT fk_quartos_tipos FOREIGN KEY(tipo_quarto_codigo) REFERENCES tbTipos_Quarto(codigo_tipo) ON DELETE RESTRICT
);

INSERT INTO tbQuartos(numero_quarto, andar_quarto, situacao_quarto, tipo_quarto_codigo)
VALUES(101, 1, 'Disponivel', 1);
INSERT INTO tbQuartos(numero_quarto, andar_quarto, situacao_quarto, tipo_quarto_codigo)
VALUES(202, 2, 'Ocupado', 2);
INSERT INTO tbQuartos(numero_quarto, andar_quarto, situacao_quarto, tipo_quarto_codigo)
VALUES(303, 3, 'Disponivel', 3);
INSERT INTO tbQuartos(numero_quarto, andar_quarto, situacao_quarto, tipo_quarto_codigo)
VALUES(404, 4, 'Ocupado', 4);
INSERT INTO tbQuartos(numero_quarto, andar_quarto, situacao_quarto, tipo_quarto_codigo)
VALUES(505, 5, 'Disponivel', 5);


CREATE TABLE tbReservas_Quartos (
    codigo_reservas_quartos INT AUTO_INCREMENT,
    reserva_codigo INT NOT NULL,
    quarto_codigo INT NOT NULL,
    CONSTRAINT pk_reservas_quartos PRIMARY KEY(codigo_reservas_quartos),
    CONSTRAINT fk_reservas_quartos_reservas FOREIGN KEY(reserva_codigo) REFERENCES tbReservas(codigo_reserva) ON DELETE RESTRICT,
    CONSTRAINT fk_reservas_quartos_quartos FOREIGN KEY(quarto_codigo) REFERENCES tbQuartos(codigo_quarto) ON DELETE RESTRICT
);

INSERT INTO tbReservas_Quartos(reserva_codigo, quarto_codigo)
VALUES(1, 1);
INSERT INTO tbReservas_Quartos(reserva_codigo, quarto_codigo)
VALUES(2, 2);
INSERT INTO tbReservas_Quartos(reserva_codigo, quarto_codigo)
VALUES(3, 3);
INSERT INTO tbReservas_Quartos(reserva_codigo, quarto_codigo)
VALUES(4, 4);
INSERT INTO tbReservas_Quartos(reserva_codigo, quarto_codigo)
VALUES(5, 5); 