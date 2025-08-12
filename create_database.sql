-- Create database if not exists
CREATE DATABASE IF NOT EXISTS jenbury_financial CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE jenbury_financial;

-- Create tables
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE NOT NULL,
    email_verified BOOLEAN DEFAULT FALSE NOT NULL,
    role VARCHAR(20) DEFAULT 'user' NOT NULL,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) NULL,
    price DECIMAL(10, 2) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE NOT NULL,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    `order` INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE NOT NULL,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS contents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    module_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL,
    content TEXT NOT NULL,
    file_path VARCHAR(255) NULL,
    `order` INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE NOT NULL,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS purchases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NULL,
    module_id INT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_status VARCHAR(50) NOT NULL,
    transaction_id VARCHAR(255) NULL,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    expires DATETIME NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE SET NULL ON UPDATE CASCADE
);

-- Insert sample data
-- Admin user (password: admin123)
INSERT INTO users (email, password, first_name, last_name, is_active, email_verified, role, created, modified)
VALUES ('admin@jenburyfinancial.com', '$2y$10$JmQDvnULGNLXCGNFJdJ5eeQeGMxZ0Ux.O.nZ5ULQoH9AxRq0jQHXe', 'Admin', 'User', 1, 1, 'admin', NOW(), NOW());

-- Regular user (password: user123)
INSERT INTO users (email, password, first_name, last_name, is_active, email_verified, role, created, modified)
VALUES ('user@example.com', '$2y$10$JmQDvnULGNLXCGNFJdJ5eeQeGMxZ0Ux.O.nZ5ULQoH9AxRq0jQHXe', 'John', 'Doe', 1, 1, 'user', NOW(), NOW());

-- Courses
INSERT INTO courses (title, description, image, price, is_active, created, modified)
VALUES 
('Financial Planning Basics', 'Learn the fundamentals of financial planning and set yourself up for financial success. This comprehensive course covers budgeting, saving, debt management, and more.', 'courses/financial-planning.jpg', 199.00, 1, NOW(), NOW()),
('Investment Strategies', 'Discover different investment options and strategies for building wealth. This course covers stocks, bonds, mutual funds, ETFs, and more.', 'courses/investment-strategies.jpg', 249.00, 1, NOW(), NOW()),
('Retirement Planning', 'Prepare for a comfortable retirement with this comprehensive guide to retirement planning. Learn about retirement accounts, social security, and creating a retirement income plan.', 'courses/retirement-planning.jpg', 299.00, 1, NOW(), NOW());

-- Modules for Financial Planning Basics
INSERT INTO modules (course_id, title, description, `order`, price, is_active, created, modified)
VALUES 
(1, 'Budgeting Fundamentals', 'Learn how to create and maintain a budget that works for your lifestyle.', 1, 49.00, 1, NOW(), NOW()),
(1, 'Debt Reduction Strategies', 'Discover effective strategies to reduce and eliminate debt.', 2, 49.00, 1, NOW(), NOW()),
(1, 'Building an Emergency Fund', 'Learn why an emergency fund is important and how to build one.', 3, 49.00, 1, NOW(), NOW()),
(1, 'Setting Financial Goals', 'Discover how to set and achieve your financial goals.', 4, 49.00, 1, NOW(), NOW());

-- Modules for Investment Strategies
INSERT INTO modules (course_id, title, description, `order`, price, is_active, created, modified)
VALUES 
(2, 'Introduction to Investing', 'Learn the basics of investing and different investment options.', 1, 59.00, 1, NOW(), NOW()),
(2, 'Stock Market Basics', 'Understand how the stock market works and how to invest in stocks.', 2, 59.00, 1, NOW(), NOW()),
(2, 'Bond Investing', 'Learn about bond investing and how to incorporate bonds into your portfolio.', 3, 59.00, 1, NOW(), NOW()),
(2, 'Mutual Funds and ETFs', 'Discover the benefits of mutual funds and ETFs and how to invest in them.', 4, 59.00, 1, NOW(), NOW());

-- Modules for Retirement Planning
INSERT INTO modules (course_id, title, description, `order`, price, is_active, created, modified)
VALUES 
(3, 'Retirement Account Types', 'Learn about different retirement account types and their benefits.', 1, 69.00, 1, NOW(), NOW()),
(3, 'Social Security Benefits', 'Understand how social security works and how to maximize your benefits.', 2, 69.00, 1, NOW(), NOW()),
(3, 'Retirement Income Planning', 'Learn how to create a retirement income plan that will last.', 3, 69.00, 1, NOW(), NOW()),
(3, 'Estate Planning Basics', 'Discover the basics of estate planning and why it\'s important.', 4, 69.00, 1, NOW(), NOW());

-- Contents for Budgeting Fundamentals module
INSERT INTO contents (module_id, title, type, content, file_path, `order`, is_active, created, modified)
VALUES 
(1, 'Introduction to Budgeting', 'text', 'Budgeting is the process of creating a plan for how you will spend your money. This spending plan is called a budget. Creating this spending plan allows you to determine in advance whether you will have enough money to do the things you need to do or would like to do.', NULL, 1, 1, NOW(), NOW()),
(1, 'Types of Budgets', 'text', 'There are several types of budgets you can use, including zero-based budgeting, 50/30/20 budget, envelope system, and more. This lesson will cover each type and help you determine which is best for your situation.', NULL, 2, 1, NOW(), NOW()),
(1, 'Creating Your First Budget', 'video', 'In this video, we\'ll walk through the process of creating your first budget step by step.', 'contents/budgeting-video.mp4', 3, 1, NOW(), NOW()),
(1, 'Budgeting Worksheet', 'file', 'Download this worksheet to help you create your budget.', 'contents/budgeting-worksheet.pdf', 4, 1, NOW(), NOW());

-- Contents for Introduction to Investing module
INSERT INTO contents (module_id, title, type, content, file_path, `order`, is_active, created, modified)
VALUES 
(5, 'Why Invest?', 'text', 'Investing is one of the most effective ways to build wealth and achieve your financial goals. This lesson explains why investing is important and how it can help you build wealth over time.', NULL, 1, 1, NOW(), NOW()),
(5, 'Investment Options Overview', 'text', 'There are many different investment options available, including stocks, bonds, mutual funds, ETFs, real estate, and more. This lesson provides an overview of each option.', NULL, 2, 1, NOW(), NOW()),
(5, 'Risk and Return', 'video', 'In this video, we\'ll discuss the relationship between risk and return and how to determine your risk tolerance.', 'contents/risk-return-video.mp4', 3, 1, NOW(), NOW()),
(5, 'Creating an Investment Plan', 'file', 'Download this worksheet to help you create your investment plan.', 'contents/investment-plan-worksheet.pdf', 4, 1, NOW(), NOW());

-- Sample purchases
INSERT INTO purchases (user_id, course_id, module_id, amount, payment_status, transaction_id, created, modified, expires)
VALUES 
(2, 1, NULL, 199.00, 'completed', 'txn_123456789', NOW(), NOW(), NULL),
(2, NULL, 5, 59.00, 'completed', 'txn_987654321', NOW(), NOW(), NULL);