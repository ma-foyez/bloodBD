-- Fix users with NULL names by setting name = username
UPDATE users 
SET name = username 
WHERE name IS NULL OR name = '';

-- Display updated users
SELECT id, name, email, username FROM users;
