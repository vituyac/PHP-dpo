CREATE TABLE IF NOT EXISTS users_comments (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    email VARCHAR(100) NOT NULL,
    comment TEXT,
    created_at TIMESTAMP DEFAULT NOW()
);