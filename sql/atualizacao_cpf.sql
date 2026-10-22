-- =====================================================================
-- MotorApp - adiciona a coluna CPF na tabela cliente
-- Rode UMA vez no phpMyAdmin (aba SQL) com o banco "motorapp" selecionado
-- SE o seu banco já existia antes (a tela "Meus dados" do app usa o CPF).
-- Não apaga nenhum dado. Se a coluna já existir, não faz nada.
-- =====================================================================
ALTER TABLE cliente ADD COLUMN IF NOT EXISTS cpf varchar(14) DEFAULT NULL AFTER telefone;
