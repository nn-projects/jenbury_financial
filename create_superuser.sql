CREATE USER 'superuser'@'localhost' IDENTIFIED BY 'yourpassword';
GRANT ALL PRIVILEGES ON *.* TO 'superuser'@'localhost';
FLUSH PRIVILEGES;