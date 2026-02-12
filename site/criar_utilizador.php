-- Criar primeiro utilizador admin
-- Password: admin123 (deve ser alterada após primeiro login)

INSERT INTO utilizadores (nome, email, password, tipo) 
VALUES ('Administrador', 'admin@escola.pt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Este utilizador tem:
-- Email: admin@escola.pt
-- Password: admin123