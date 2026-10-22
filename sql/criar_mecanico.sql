-- =====================================================================
-- MotorApp - criar o login do DONO DA OFICINA (mecânico)
-- Rode no phpMyAdmin (aba SQL) com o banco "motorapp" selecionado.
-- NÃO apaga nem altera nenhuma tabela: só insere um usuário.
--
-- Login criado:
--   e-mail: dono@oficina.com
--   senha : oficina123
-- (a senha está criptografada com password_hash, funciona no app e no site)
-- =====================================================================

INSERT INTO cliente (nome, email, telefone, senha, tipo)
VALUES ('Dono da Oficina', 'dono@oficina.com', '11999990000',
        '$2y$10$f9KpPZwMLtKGRbKx1KGLQOtrhanpN4BYZ6noWtQjnYm9sXPdoFzX.', 'mecanico');

-- ---------------------------------------------------------------------
-- OPCIONAL: transformar uma conta que já existe em mecânico
-- (troque o e-mail e tire os dois traços do começo da linha)
-- ---------------------------------------------------------------------
-- UPDATE cliente SET tipo = 'mecanico' WHERE email = 'email@exemplo.com';
