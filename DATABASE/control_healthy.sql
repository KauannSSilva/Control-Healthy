-- Versão para PostgreSQL do banco de dados control_healthy

-- Tabela de Usuários
CREATE TABLE usuarios (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo VARCHAR(10) CHECK (tipo IN ('paciente', 'medico', 'admin')) NOT NULL,
    data_cadastro TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Informações Adicionais do Paciente
CREATE TABLE informacoes_paciente (
    id SERIAL PRIMARY KEY,
    usuario_id INT NOT NULL,
    endereco VARCHAR(255),
    telefone VARCHAR(20),
    CONSTRAINT fk_usuario
        FOREIGN KEY(usuario_id) 
        REFERENCES usuarios(id)
        ON DELETE CASCADE
);

-- Tabela de Registros de Pressão
CREATE TABLE registros_pressao (
    id SERIAL PRIMARY KEY,
    paciente_id INT NOT NULL,
    data_hora TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    sistolica INT NOT NULL,
    diastolica INT NOT NULL,
    CONSTRAINT fk_paciente_pressao
        FOREIGN KEY(paciente_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE
);

-- Tabela de Associação Médico-Paciente
CREATE TABLE medico_paciente (
    medico_id INT NOT NULL,
    paciente_id INT NOT NULL,
    PRIMARY KEY (medico_id, paciente_id),
    CONSTRAINT fk_medico
        FOREIGN KEY(medico_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_paciente_associado
        FOREIGN KEY(paciente_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE
);