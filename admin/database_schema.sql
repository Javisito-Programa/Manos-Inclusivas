CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS noticias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    contenido TEXT NOT NULL,
    imagen_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS donativos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_donativo VARCHAR(50) NOT NULL, -- tarjeta, transferencia, cheque, efectivo
    monto DECIMAL(10,2) NOT NULL,
    num_autorizacion VARCHAR(100),
    nombre_donante VARCHAR(255),
    email_donante VARCHAR(255),
    rfc VARCHAR(13),
    requiere_factura BOOLEAN DEFAULT FALSE,
    fecha_donativo DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Usuario por defecto: admin / admin123 (Se cambiará en producción)
-- password_hash for 'admin123' using BCRYPT
INSERT INTO usuarios (username, password_hash) VALUES ('admin', '$2y$10$Y3Hl9LzT0a.wT8O/1Zz.8e1rJ8G6.1b.CjJj4K/q.o.qW0r.2X1aO') ON DUPLICATE KEY UPDATE id=id;
