-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 30, 2025 at 03:36 PM -- (Note: This timestamp might be outdated after modification)
-- Server version: 11.7.2-MariaDB
-- PHP Version: 8.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jenbury_financial_am`
--

-- --------------------------------------------------------

--
-- Table structure for table `contents`
--

CREATE TABLE `contents` (
  `id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `order` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contents`
--

INSERT INTO `contents` (`id`, `module_id`, `title`, `type`, `content`, `file_path`, `order`, `is_active`, `created`, `modified`) VALUES
-- Module 1
(1, 1, 'Lesson 1: The Psychology of Money & Mindset Shifts', 'text', 'How your beliefs about money were formed\nCommon money mindset blocks (e.g., scarcity mindset, fear of investing)\nHow to develop a wealth-building mindset\nStrategies for overcoming financial anxiety and impulsive spending', NULL, 1, 1, NOW(), NOW()),
(2, 1, 'Lesson 2: Tracking Your Money', 'text', 'Why tracking your money is the first step to financial control\nTools & apps for tracking income and expenses\nIdentifying where your money is leaking and how to fix it', NULL, 2, 1, NOW(), NOW()),
(3, 1, 'Lesson 3: Setting Financial Goals That Work', 'text', 'How to create SMART financial goals (Specific, Measurable, Achievable, Relevant, Time-bound)\nThe importance of setting short-term, medium-term, and long-term goals\nHow to stay motivated and hold yourself accountable', NULL, 3, 1, NOW(), NOW()),
-- Module 2
(4, 2, 'Lesson 1: What is the Bucket Strategy?', 'text', 'Explanation of the Bucket Strategy\nHow to divide your income into different spending and saving buckets\nThe benefits of this method over traditional budgeting', NULL, 1, 1, NOW(), NOW()),
(5, 2, 'Lesson 2: Setting Up Your Buckets', 'text', 'The key buckets you’ll need:\nLiving Expenses Bucket (Needs) – 60-70% of income\nRent/mortgage, bills, groceries, transport\nHow to optimize expenses without cutting your quality of life\nSavings & Security Bucket (Emergency Fund) – 10-20% of income\nBuilding an emergency fund (how much & where to keep it)\nAutomating savings for consistency\nInvesting & Growth Bucket – 10-20% of income\nWhy investing is key to long-term wealth\nSimple investment options for beginners\nLifestyle & Fun Bucket – 10-15% of income\nGuilt-free spending on entertainment, travel, hobbies\nWhy this bucket prevents budget burnout', NULL, 2, 1, NOW(), NOW()),
(6, 2, 'Lesson 3: Adjusting Your Buckets for Your Life', 'text', 'How to personalize the percentages based on your needs\nMaking your budget flexible so it evolves with your income\nTools & apps to automate your bucket system', NULL, 3, 1, NOW(), NOW()),
-- Module 3
(7, 3, 'Lesson 1: Understanding Debt', 'text', 'What is "good debt" vs. "bad debt"?\nThe impact of high-interest debt on your finances\nWhy avoiding minimum payments is key to becoming debt-free', NULL, 1, 1, NOW(), NOW()),
(8, 3, 'Lesson 2: The Best Debt Repayment Methods', 'text', 'Debt Snowball Method vs. Debt Avalanche Method – Which is better for you?\nStep-by-step guide to paying off debt faster\nThe psychological benefits of seeing progress in debt repayment', NULL, 2, 1, NOW(), NOW()),
(9, 3, 'Lesson 3: Avoiding Debt Traps', 'text', 'Recognizing risky financial habits\nHow to avoid payday loans and credit card debt cycles\nTips to improve your credit score', NULL, 3, 1, NOW(), NOW()),
-- Module 4
(10, 4, 'Lesson 1: The Role of an Emergency Fund', 'text', 'How much should you save? (3-6 months of expenses)\nWhere to store your emergency fund for easy access but protection', NULL, 1, 1, NOW(), NOW()),
(11, 4, 'Lesson 2: Understanding Personal Insurance', 'text', 'Life insurance, income protection, and health insurance explained\nHow to assess what type of coverage you actually need', NULL, 2, 1, NOW(), NOW()),
(12, 4, 'Lesson 3: Reducing Financial Risks', 'text', 'Wills, estate planning, and legal protection\nProtecting your income and assets', NULL, 3, 1, NOW(), NOW()),
-- Module 5
(13, 5, 'Lesson 1: Automating Your Savings', 'text', 'The power of "paying yourself first"\nBest savings accounts for high interest & low fees', NULL, 1, 1, NOW(), NOW()),
(14, 5, 'Lesson 2: Short-Term vs. Long-Term Savings', 'text', 'Emergency fund vs. investment savings\nHow to balance saving and spending', NULL, 2, 1, NOW(), NOW()),
-- Module 6
(15, 6, 'Lesson 1: Investing Basics', 'text', 'What is investing, and why is it important?\nThe difference between stocks, bonds, and index funds', NULL, 1, 1, NOW(), NOW()),
(16, 6, 'Lesson 2: How to Start Investing', 'text', 'How much money do you need?\nHow to choose a beginner-friendly investment platform', NULL, 2, 1, NOW(), NOW()),
(17, 6, 'Lesson 3: Avoiding Common Investment Mistakes', 'text', 'Market timing vs. long-term strategy\nUnderstanding risk vs. reward', NULL, 3, 1, NOW(), NOW()),
-- Module 7
(18, 7, 'Lesson 1: Understanding Superannuation', 'text', 'How does superannuation work?\nEmployer contributions & voluntary contributions', NULL, 1, 1, NOW(), NOW()),
(19, 7, 'Lesson 2: Choosing the Right Super Fund', 'text', 'Comparing fees, investment options & performance', NULL, 2, 1, NOW(), NOW()),
(20, 7, 'Lesson 3: Growing Your Super', 'text', 'How small changes now lead to a big impact later', NULL, 3, 1, NOW(), NOW()),
-- Module 8
(21, 8, 'Lesson 1: Setting Long-Term Financial Goals', 'text', 'Planning for the next 1, 5, and 10 years', NULL, 1, 1, NOW(), NOW()),
(22, 8, 'Lesson 2: Creating Your Personalized Financial Plan', 'text', 'How to combine budgeting, saving, and investing into a single system', NULL, 2, 1, NOW(), NOW()),
(23, 8, 'Lesson 3: Staying on Track', 'text', 'How to review and update your financial plan\nWhere to find ongoing support and resources', NULL, 3, 1, NOW(), NOW());

-- --------------------------------------------------------

--
-- Table structure for table `content_blocks`
--

CREATE TABLE `content_blocks` (
  `id` int(11) NOT NULL,
  `parent` varchar(128) NOT NULL,
  `slug` varchar(128) NOT NULL,
  `label` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL,
  `type` varchar(32) NOT NULL,
  `value` text DEFAULT NULL,
  `previous_value` text DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `content_blocks`
--

INSERT INTO `content_blocks` (`id`, `parent`, `slug`, `label`, `description`, `type`, `value`, `previous_value`, `modified`) VALUES
(1, 'home', 'home-title-line-1', 'Home Title Line 1', 'First line of homepage animated title.', 'text', 'Jenbury Financial', NULL, '2025-03-30 12:43:14'),
(2, 'home', 'home-title-line-2', 'Home Title Line 2', 'Second line of homepage animated title.', 'text', 'KnowledgeCenter', NULL, '2025-03-30 12:43:19'),
(3, 'home', 'home-author-credit', 'Author Credit Line', 'Credit line under the title.', 'text', 'by Andrea Jenkins', NULL, '2025-03-30 11:51:13'),
(4, 'courses', 'courses-title', 'Courses Page Title', 'H1 heading on the courses index page.', 'text', 'Financial Education Courses', NULL, '2025-03-30 11:51:13'),
(5, 'courses', 'courses-description', 'Courses Page Description', 'Short intro paragraph on the courses index.', 'text', 'Explore our comprehensive range of financial education courses designed to help you achieve your financial goals. Purchase individual modules or save with our full course bundles.', NULL, '2025-03-30 11:51:13'),
(6, 'courses', 'no-courses-message', 'No Courses Message', 'Message shown when no courses are available.', 'text', 'No courses are currently available. Please check back later.', NULL, '2025-03-30 11:51:13'),
(7, 'global', 'logo', 'Site Logo', 'Shown in navigation or header.', 'image', '/content-blocks/uploads/logo.a4a656ae02a22a3d74376cf9eb8e20d2.avif', NULL, '2025-03-30 12:21:05'),
(8, 'home', 'home-title-line-1', 'Home Title Line 1', 'First line of homepage animated title.', 'text', 'JenburyFinancial', NULL, '2025-03-30 11:51:42'),
(9, 'home', 'home-title-line-2', 'Home Title Line 2', 'Second line of homepage animated title.', 'text', 'KnowledgeCenter', NULL, '2025-03-30 11:51:42'),
(10, 'home', 'home-author-credit', 'Author Credit Line', 'Credit line under the title.', 'text', 'by Andrea Jenkins', NULL, '2025-03-30 11:51:42'),
(11, 'courses', 'courses-title', 'Courses Page Title', 'H1 heading on the courses index page.', 'text', 'Financial Education Courses', NULL, '2025-03-30 11:51:42'),
(12, 'courses', 'courses-description', 'Courses Page Description', 'Short intro paragraph on the courses index.', 'text', 'Explore our comprehensive range of financial education courses designed to help you achieve your financial goals. Purchase individual modules or save with our full course bundles.', NULL, '2025-03-30 11:51:42'),
(13, 'courses', 'no-courses-message', 'No Courses Message', 'Message shown when no courses are available.', 'text', 'No courses are currently available. Please check back later.', NULL, '2025-03-30 11:51:42'),
(14, 'global', 'logo', 'Site Logo', 'Shown in navigation or header.', 'image', NULL, NULL, '2025-03-30 11:51:42'),
(15, 'about-us', 'about-us-content', 'About Us - Main Content', 'Main block of code shown on the About Us page.', 'html', '\n                    <h2>Our Story</h2>\n                    <p>We are a small business, established in 2023 who sell candles to sick children.</p>\n                ', NULL, '2025-03-30 14:08:27'),
(16, 'global', 'logo', 'Logo', 'Shown on the home page, and also in the top left of each other page.', 'image', NULL, NULL, '2025-03-30 14:08:35'),
(17, 'home', 'website-title', 'Website Title', 'Heading shown on the main page, and also in the browser tab.', 'text', 'CakePHP Content Blocks Plugin', NULL, '2025-03-30 14:08:42'),
(18, 'site', 'site-name', '', 'Site Name (Header/Footer)', 'text', 'Jenbury Financial', NULL, '2025-03-30 15:23:58'),
(19, 'site', 'footer-tagline', '', 'Footer: Tagline', 'text', 'Empowering your financial future with expert-led online courses.', NULL, '2025-03-30 15:23:58'),
(20, 'site', 'footer-quick-links-title', '', 'Footer: Quick Links Title', 'text', 'Quick Links', NULL, '2025-03-30 15:23:58'),
(21, 'site', 'footer-legal-title', '', 'Footer: Legal Links Title', 'text', 'Legal', NULL, '2025-03-30 15:23:58'),
(22, 'site', 'footer-link-terms-text', '', 'Footer: Terms Link Text', 'text', 'Terms of Service', NULL, '2025-03-30 15:23:58'),
(23, 'site', 'footer-link-privacy-text', '', 'Footer: Privacy Link Text', 'text', 'Privacy Policy', NULL, '2025-03-30 15:23:58'),
(24, 'site', 'footer-copyright-text', '', 'Footer: Copyright Name', 'text', 'Jenbury Financial', NULL, '2025-03-30 15:23:58'),
(25, 'site', 'navbar-link-home-text', '', 'Main Nav: Home Link', 'text', 'Home', NULL, '2025-03-30 15:23:58'),
(26, 'site', 'navbar-link-courses-text', '', 'Main Nav: Courses Link', 'text', 'Courses', NULL, '2025-03-30 15:23:58'),
(27, 'site', 'navbar-link-about-text', '', 'Main Nav: About Link', 'text', 'About', NULL, '2025-03-30 15:23:58'),
(28, 'site', 'navbar-link-faq-text', '', 'Main Nav: FAQ Link', 'text', 'FAQ', NULL, '2025-03-30 15:23:58'),
(29, 'site', 'navbar-link-contact-text', '', 'Main Nav: Contact Link', 'text', 'Contact', NULL, '2025-03-30 15:23:58'),
(30, 'site', 'user-dropdown-dashboard-text', '', 'User Dropdown: Dashboard Link', 'text', 'Dashboard', NULL, '2025-03-30 15:23:58'),
(31, 'site', 'user-dropdown-my-courses-text', '', 'User Dropdown: My Courses Link', 'text', 'My Courses', NULL, '2025-03-30 15:23:58'),
(32, 'site', 'user-dropdown-my-modules-text', '', 'User Dropdown: My Modules Link', 'text', 'My Modules', NULL, '2025-03-30 15:23:58'),
(33, 'site', 'user-dropdown-purchase-history-text', '', 'User Dropdown: Purchase History Link', 'text', 'Purchase History', NULL, '2025-03-30 15:23:58'),
(34, 'site', 'user-dropdown-profile-text', '', 'User Dropdown: Profile Link', 'text', 'Profile', NULL, '2025-03-30 15:23:58'),
(35, 'site', 'user-dropdown-change-password-text', '', 'User Dropdown: Change Password Link', 'text', 'Change Password', NULL, '2025-03-30 15:23:58'),
(36, 'site', 'user-dropdown-admin-dashboard-text', '', 'User Dropdown: Admin Dashboard Link', 'text', 'Admin Dashboard', NULL, '2025-03-30 15:23:58'),
(37, 'site', 'user-dropdown-logout-text', '', 'User Dropdown: Logout Link', 'text', 'Logout', NULL, '2025-03-30 15:23:58'),
(38, 'site', 'user-nav-login-text', '', 'Logged Out Nav: Login Button', 'text', 'Login', NULL, '2025-03-30 15:23:58'),
(39, 'site', 'user-nav-register-text', '', 'Logged Out Nav: Register Button', 'text', 'Register', NULL, '2025-03-30 15:23:58'),
(40, 'site', 'home-page-title-tag', '', 'Homepage: Browser Title Tag', 'text', 'Jenbury Financial Knowledge Center', NULL, '2025-03-30 15:23:58'),
(41, 'site', 'home-meta-description', '', 'Homepage: Meta Description Tag', 'text', 'Welcome to Jenbury Financial Knowledge Center - Your path to financial excellence', NULL, '2025-03-30 15:23:58'),
(42, 'site', 'home-main-heading-1', '', 'Homepage: Main Heading (Part 1)', 'text', 'Jenbury   Financial', NULL, '2025-03-30 15:23:58'),
(43, 'site', 'home-main-heading-2', '', 'Homepage: Main Heading (Part 2)', 'text', 'Knowledge   Center', NULL, '2025-03-30 15:23:58'),
(44, 'site', 'home-subtitle', '', 'Homepage: Subtitle', 'text', 'by Andrea Jenkins', NULL, '2025-03-30 15:23:58'),
(45, 'site', 'home-cta-button-text', '', 'Homepage: Call-to-Action Button Text', 'text', '<span>Learn More</span><span class=\"arrow\">→</span>', NULL, '2025-03-30 15:23:58'),
(46, 'site', 'home-cta-button-link', '', 'Home-cta-button-link (Update Description)', 'text', 'Home-cta-button-link', NULL, '2025-03-30 15:23:58'),
(47, 'site', 'home-login-button-text', '', 'Homepage: Login Button Text', 'text', 'Log In', NULL, '2025-03-30 15:23:58'),
(48, 'site', 'home-signup-button-text', '', 'Homepage: Sign Up Button Text', 'text', 'Sign Up', NULL, '2025-03-30 15:23:58'),
(49, 'site', 'home-logout-button-text', '', 'Homepage: Logout Button Text', 'text', 'Logout', NULL, '2025-03-30 15:23:58'),
(50, 'site', 'courses-page-title-tag', '', 'Courses-page-title-tag (Update Description)', 'text', 'Courses-page-title-tag', NULL, '2025-03-30 15:23:58'),
(51, 'site', 'courses-header-title', '', 'Courses-header-title (Update Description)', 'text', 'Courses-header-title', NULL, '2025-03-30 15:23:58'),
(52, 'site', 'courses-header-description', '', 'Courses-header-description (Update Description)', 'text', 'Courses-header-description', NULL, '2025-03-30 15:23:58'),
(53, 'site', 'courses-learning-outcomes-title', '', 'Courses-learning-outcomes-title (Update Description)', 'text', 'Courses-learning-outcomes-title', NULL, '2025-03-30 15:23:58'),
(54, 'site', 'courses-bundle-card-title', '', 'Courses-bundle-card-title (Update Description)', 'text', 'Courses-bundle-card-title', NULL, '2025-03-30 15:23:58'),
(55, 'site', 'courses-bundle-savings-prefix', '', 'Courses-bundle-savings-prefix (Update Description)', 'text', 'Courses-bundle-savings-prefix', NULL, '2025-03-30 15:23:58'),
(56, 'site', 'courses-bundle-savings-suffix', '', 'Courses-bundle-savings-suffix (Update Description)', 'text', 'Courses-bundle-savings-suffix', NULL, '2025-03-30 15:23:58'),
(57, 'site', 'courses-bundle-savings-unit', '', 'Courses-bundle-savings-unit (Update Description)', 'text', 'Courses-bundle-savings-unit', NULL, '2025-03-30 15:23:58'),
(58, 'site', 'courses-bundle-individual-price-label', '', 'Courses-bundle-individual-price-label (Update Description)', 'text', 'Courses-bundle-individual-price-label', NULL, '2025-03-30 15:23:58'),
(59, 'site', 'courses-bundle-price-label', '', 'Courses-bundle-price-label (Update Description)', 'text', 'Courses-bundle-price-label', NULL, '2025-03-30 15:23:58'),
(60, 'site', 'courses-bundle-savings-amount-label', '', 'Courses-bundle-savings-amount-label (Update Description)', 'text', 'Courses-bundle-savings-amount-label', NULL, '2025-03-30 15:23:58'),
(61, 'site', 'courses-buy-bundle-button-text', '', 'Courses-buy-bundle-button-text (Update Description)', 'text', 'Courses-buy-bundle-button-text', NULL, '2025-03-30 15:23:58'),
(62, 'site', 'courses-browse-modules-link-text', '', 'Courses-browse-modules-link-text (Update Description)', 'text', 'Courses-browse-modules-link-text', NULL, '2025-03-30 15:23:58'),
(63, 'site', 'courses-available-modules-title', '', 'Courses-available-modules-title (Update Description)', 'text', 'Courses-available-modules-title', NULL, '2025-03-30 15:23:58'),
(64, 'site', 'courses-module-lesson-count-label', '', 'Courses-module-lesson-count-label (Update Description)', 'text', 'Courses-module-lesson-count-label', NULL, '2025-03-30 15:23:58'),
(65, 'site', 'courses-enroll-now-button-text', '', 'Courses-enroll-now-button-text (Update Description)', 'text', 'Courses-enroll-now-button-text', NULL, '2025-03-30 15:23:58'),
(66, 'site', 'courses-no-modules-message', '', 'Courses-no-modules-message (Update Description)', 'text', 'Courses-no-modules-message', NULL, '2025-03-30 15:23:58'),
(67, 'site', 'courses-no-courses-message', '', 'Courses-no-courses-message (Update Description)', 'text', 'Courses-no-courses-message', NULL, '2025-03-30 15:23:58'),
(68, 'site', 'dashboard-page-title-tag', '', 'Dashboard-page-title-tag (Update Description)', 'text', 'Dashboard-page-title-tag', NULL, '2025-03-30 15:23:58'),
(69, 'site', 'dashboard-main-heading', '', 'Dashboard-main-heading (Update Description)', 'text', 'Dashboard-main-heading', NULL, '2025-03-30 15:23:58'),
(70, 'site', 'dashboard-welcome-prefix', '', 'Dashboard-welcome-prefix (Update Description)', 'text', 'Dashboard-welcome-prefix', NULL, '2025-03-30 15:23:58'),
(71, 'site', 'dashboard-welcome-suffix', '', 'Dashboard-welcome-suffix (Update Description)', 'text', 'Dashboard-welcome-suffix', NULL, '2025-03-30 15:23:58'),
(72, 'site', 'dashboard-welcome-description', '', 'Dashboard-welcome-description (Update Description)', 'text', 'Dashboard-welcome-description', NULL, '2025-03-30 15:23:58'),
(73, 'site', 'dashboard-my-courses-title', '', 'Dashboard-my-courses-title (Update Description)', 'text', 'Dashboard-my-courses-title', NULL, '2025-03-30 15:23:58'),
(74, 'site', 'dashboard-progress-label', '', 'Dashboard-progress-label (Update Description)', 'text', 'Dashboard-progress-label', NULL, '2025-03-30 15:23:58'),
(75, 'site', 'dashboard-progress-unit', '', 'Dashboard-progress-unit (Update Description)', 'text', 'Dashboard-progress-unit', NULL, '2025-03-30 15:23:58'),
(76, 'site', 'dashboard-continue-learning-courses-button', '', 'Dashboard-continue-learning-courses-button (Update Description)', 'text', 'Dashboard-continue-learning-courses-button', NULL, '2025-03-30 15:23:58'),
(77, 'site', 'dashboard-view-all-courses-button', '', 'Dashboard-view-all-courses-button (Update Description)', 'text', 'Dashboard-view-all-courses-button', NULL, '2025-03-30 15:23:58'),
(78, 'site', 'dashboard-my-modules-title', '', 'Dashboard-my-modules-title (Update Description)', 'text', 'Dashboard-my-modules-title', NULL, '2025-03-30 15:23:58'),
(79, 'site', 'dashboard-module-course-label', '', 'Dashboard-module-course-label (Update Description)', 'text', 'Dashboard-module-course-label', NULL, '2025-03-30 15:23:58'),
(80, 'site', 'dashboard-continue-learning-modules-button', '', 'Dashboard-continue-learning-modules-button (Update Description)', 'text', 'Dashboard-continue-learning-modules-button', NULL, '2025-03-30 15:23:58'),
(81, 'site', 'dashboard-view-all-modules-button', '', 'Dashboard-view-all-modules-button (Update Description)', 'text', 'Dashboard-view-all-modules-button', NULL, '2025-03-30 15:23:58'),
(82, 'site', 'dashboard-empty-state-title', '', 'Dashboard-empty-state-title (Update Description)', 'text', 'Dashboard-empty-state-title', NULL, '2025-03-30 15:23:58'),
(83, 'site', 'dashboard-empty-state-description', '', 'Dashboard-empty-state-description (Update Description)', 'text', 'Dashboard-empty-state-description', NULL, '2025-03-30 15:23:58'),
(84, 'site', 'dashboard-empty-state-browse-button', '', 'Dashboard-empty-state-browse-button (Update Description)', 'text', 'Dashboard-empty-state-browse-button', NULL, '2025-03-30 15:23:58'),
(85, 'site', 'dashboard-sidebar-quick-links-title', '', 'Dashboard-sidebar-quick-links-title (Update Description)', 'text', 'Dashboard-sidebar-quick-links-title', NULL, '2025-03-30 15:23:58'),
(86, 'site', 'dashboard-sidebar-link-profile', '', 'Dashboard-sidebar-link-profile (Update Description)', 'text', 'Dashboard-sidebar-link-profile', NULL, '2025-03-30 15:23:58'),
(87, 'site', 'dashboard-sidebar-link-my-courses', '', 'Dashboard-sidebar-link-my-courses (Update Description)', 'text', 'Dashboard-sidebar-link-my-courses', NULL, '2025-03-30 15:23:58'),
(88, 'site', 'dashboard-sidebar-link-my-modules', '', 'Dashboard-sidebar-link-my-modules (Update Description)', 'text', 'Dashboard-sidebar-link-my-modules', NULL, '2025-03-30 15:23:58'),
(89, 'site', 'dashboard-sidebar-link-purchase-history', '', 'Dashboard-sidebar-link-purchase-history (Update Description)', 'text', 'Dashboard-sidebar-link-purchase-history', NULL, '2025-03-30 15:23:58'),
(90, 'site', 'dashboard-sidebar-link-browse-courses', '', 'Dashboard-sidebar-link-browse-courses (Update Description)', 'text', 'Dashboard-sidebar-link-browse-courses', NULL, '2025-03-30 15:23:58'),
(91, 'site', 'dashboard-sidebar-recent-activity-title', '', 'Dashboard-sidebar-recent-activity-title (Update Description)', 'text', 'Dashboard-sidebar-recent-activity-title', NULL, '2025-03-30 15:23:58'),
(92, 'site', 'dashboard-sidebar-recommended-courses-title', '', 'Dashboard-sidebar-recommended-courses-title (Update Description)', 'text', 'Dashboard-sidebar-recommended-courses-title', NULL, '2025-03-30 15:23:58'),
(93, 'site', 'dashboard-sidebar-view-course-button', '', 'Dashboard-sidebar-view-course-button (Update Description)', 'text', 'Dashboard-sidebar-view-course-button', NULL, '2025-03-30 15:23:58'),
(94, 'site', 'user-dropdown-manage-site-content-text', '', 'Manage Site Content (Auto-generated)', 'text', 'Manage Site Content', NULL, '2025-03-30 15:00:21'),
(95, 'site', 'user-dropdown-manage-site-content-text', '', 'Manage Site Content (Auto-generated)', 'text', 'Manage Site Content', NULL, '2025-03-30 15:00:46'),
(96, 'site', 'user-dropdown-manage-site-content-text', '', 'Manage Site Content (Auto-generated)', 'text', 'Manage Site Content', NULL, '2025-03-30 15:00:57'),
(97, 'site', 'user-dropdown-manage-site-content-text', '', 'Manage Site Content (Auto-generated)', 'text', 'Manage Site Content', NULL, '2025-03-30 15:06:58'),
(98, 'site', 'user-dropdown-manage-site-content-text', '', 'Manage Site Content (Auto-generated)', 'text', 'Manage Site Content', NULL, '2025-03-30 15:07:02'),
(99, 'site', 'user-dropdown-manage-site-content-text', '', 'Manage Site Content (Auto-generated)', 'text', 'Manage Site Content', NULL, '2025-03-30 15:07:09'),
(100, 'site', 'user-dropdown-manage-site-content-text', '', 'Manage Site Content (Auto-generated)', 'text', 'Manage Site Content', NULL, '2025-03-30 15:07:14'),
(101, 'site', 'user-dropdown-manage-site-content-text', '', 'Manage Site Content (Auto-generated)', 'text', 'Manage Site Content', NULL, '2025-03-30 15:07:28'),
(102, 'site', 'user-dropdown-manage-site-content-text', '', 'Manage Site Content (Auto-generated)', 'text', 'Manage Site Content', NULL, '2025-03-30 15:07:43'),
(103, 'site', 'user-dropdown-manage-site-content-text', '', 'Manage Site Content (Auto-generated)', 'text', 'Manage Site Content', NULL, '2025-03-30 15:10:17'),
(104, 'site', 'user-dropdown-manage-site-content-text', '', 'Manage Site Content (Auto-generated)', 'text', 'Manage Site Content', NULL, '2025-03-30 15:10:34'),
(105, 'site', 'user-dropdown-manage-site-content-text', '', 'Manage Site Content (Auto-generated)', 'text', 'Manage Site Content', NULL, '2025-03-30 15:10:38'),
(106, 'site', 'user-dropdown-manage-site-content-text', '', 'Manage Site Content (Auto-generated)', 'text', 'Manage Site Content', NULL, '2025-03-30 15:10:46'),
(107, 'site', 'user-dropdown-manage-site-content-text', '', 'Manage Site Content (Auto-generated)', 'text', 'Manage Site Content', NULL, '2025-03-30 15:15:52'),
(108, 'site', 'user-dropdown-manage-site-content-text', '', 'Manage Site Content (Auto-generated)', 'text', 'Manage Site Content', NULL, '2025-03-30 15:20:14'),
(109, 'site', 'user-dropdown-manage-site-content-text', '', 'Manage Site Content (Auto-generated)', 'text', 'Manage Site Content', NULL, '2025-03-30 15:22:04'),
(110, 'site', 'user-dropdown-manage-site-content-text', '', 'Manage Site Content (Auto-generated)', 'text', 'Manage Site Content', NULL, '2025-03-30 15:23:58'),
(111, 'site', 'user-dropdown-manage-site-content-text', '', 'Manage Site Content (Auto-generated)', 'text', 'Manage Site Content', NULL, '2025-03-30 15:24:42');

-- --------------------------------------------------------

--
-- Table structure for table `content_blocks_phinxlog`
--

CREATE TABLE `content_blocks_phinxlog` (
  `version` bigint(20) NOT NULL,
  `migration_name` varchar(100) DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `breakpoint` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `content_blocks_phinxlog`
--

INSERT INTO `content_blocks_phinxlog` (`version`, `migration_name`, `start_time`, `end_time`, `breakpoint`) VALUES
(20230402063959, 'ContentBlocksMigration', '2025-03-30 11:44:23', '2025-03-30 11:44:23', 0);

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `title`, `description`, `image`, `price`, `is_active`, `created`, `modified`) VALUES
(1, 'Finances for Individuals', 'The Essential Guide to Managing Your Finances\nA self-paced course for individuals aged 20-45 who want to take control of their money, eliminate debt, and start investing with confidence.\nLearning Outcomes\nBy the end of this course, students will:\n✅ Understand where their money is going and how to track expenses effectively\n✅ Create a realistic and sustainable budget using the Bucket Strategy\n✅ Develop strategies to pay off debt efficiently\n✅ Learn the basics of saving, risk management, and investing for long-term financial security\n✅ Build a financial plan that helps them achieve their short- and long-term goals', NULL, 199.00, 1, NOW(), NOW()),
(2, 'The Jenbury Method for up-and-coming financial planners', 'Placeholder description for the Jenbury Method course.', NULL, 299.00, 1, NOW(), NOW());

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `order` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `course_id`, `title`, `description`, `order`, `price`, `is_active`, `created`, `modified`) VALUES
(1, 1, 'Understanding Your Finances & Money Mindset', 'Goal: Help students shift their mindset, understand their financial habits, and gain clarity on where their money is going.', 1, 49.00, 1, NOW(), NOW()),
(2, 1, 'Budgeting with the Bucket Strategy', 'Goal: Teach students how to create a budget using the Bucket Strategy, a simple and practical way to allocate money.', 2, 49.00, 1, NOW(), NOW()),
(3, 1, 'Debt Management Strategies', 'Goal: Equip students with effective strategies for reducing and eliminating debt.', 3, 49.00, 1, NOW(), NOW()),
(4, 1, 'Risk Management & Financial Protection', 'Goal: Help students protect their financial future through emergency funds and insurance.', 4, 49.00, 1, NOW(), NOW()),
(5, 1, 'Saving for the Future', 'Goal: Teach students how to build strong savings habits for different financial goals.', 5, 49.00, 1, NOW(), NOW()),
(6, 1, 'Investing for Beginners', 'Goal: Introduce students to investing and help them start investing confidently.', 6, 49.00, 1, NOW(), NOW()),
(7, 1, 'Superannuation & Retirement Planning', 'Goal: Help students understand and optimize their superannuation.', 7, 49.00, 1, NOW(), NOW()),
(8, 1, 'Building a Sustainable Financial Plan', 'Goal: Guide students in creating a long-term financial success strategy.', 8, 49.00, 1, NOW(), NOW());

-- --------------------------------------------------------

--
-- Table structure for table `phinxlog`
--

CREATE TABLE `phinxlog` (
  `version` bigint(20) NOT NULL,
  `migration_name` varchar(100) DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `breakpoint` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `phinxlog`
--

INSERT INTO `phinxlog` (`version`, `migration_name`, `start_time`, `end_time`, `breakpoint`) VALUES
(20250315000001, 'Initial', '2025-03-30 12:57:53', '2025-03-30 12:57:53', 0),
(20250315085400, 'AddRoleToUsers', '2025-03-30 12:57:53', '2025-03-30 12:57:53', 0),
(20250330125717, 'RemoveContentBlocksTable', '2025-03-30 12:57:53', '2025-03-30 12:57:53', 0),
(20250330125941, 'CreateSiteContentsTable', '2025-03-30 13:00:02', '2025-03-30 13:00:02', 0);

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `module_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_status` varchar(50) NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchases`
--

-- (No data inserted as per plan)

-- --------------------------------------------------------

--
-- Table structure for table `site_contents`
--

CREATE TABLE `site_contents` (
  `id` int(11) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `type` enum('text','html','image') NOT NULL DEFAULT 'text',
  `label` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `value` longtext DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `role` varchar(20) NOT NULL DEFAULT 'user',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `first_name`, `last_name`, `is_active`, `email_verified`, `role`, `created`, `modified`) VALUES
(1, 'admin@jenburyfinancial.com', '$2y$12$v2emtT2gw9bitWBAtp6eX.sf28fxbovOj67Sd0nT7.zVAS7gFTsiK', 'Admin', 'User', 1, 1, 'admin', '2025-03-15 20:18:04', '2025-03-30 11:51:42'),
(2, 'user@example.com', '$2y$10$JmQDvnULGNLXCGNFJdJ5eeQeGMxZ0Ux.O.nZ5ULQoH9AxRq0jQHXe', 'John', 'Doe', 1, 1, 'user', '2025-03-15 20:18:04', '2025-03-15 20:18:04'),
(3, 'admin@example.com', 'admin', 'Admin', 'User', 1, 1, 'admin', '2025-03-15 21:25:04', '2025-03-15 21:25:04'),
(4, 'user@test.com', '$2y$12$oksTeaJ9vF8ijNolQ3BnyuauV3ouXsS30vsPMYQ2d7Qts8Wt7a4ki', 'user', 'test', 1, 0, 'user', '2025-03-15 10:33:24', '2025-03-15 10:33:24'),
(5, 'testuser@test.com', '$2y$12$yAW3Ovm0VGqYK057.RbrYuS9Zwc4F719lOcSngLYUEKIqWMIxIT6y', 'Test', 'User', 1, 0, 'admin', '2025-03-19 22:29:54', '2025-03-19 22:29:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contents`
--
ALTER TABLE `contents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `module_id` (`module_id`);

--
-- Indexes for table `content_blocks`
--
ALTER TABLE `content_blocks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `content_blocks_phinxlog`
--
ALTER TABLE `content_blocks_phinxlog`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `phinxlog`
--
ALTER TABLE `phinxlog`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `module_id` (`module_id`);

--
-- Indexes for table `site_contents`
--
ALTER TABLE `site_contents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contents`
--
ALTER TABLE `contents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `content_blocks`
--
ALTER TABLE `content_blocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `site_contents`
--
ALTER TABLE `site_contents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `contents`
--
ALTER TABLE `contents`
  ADD CONSTRAINT `contents_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `modules`
--
ALTER TABLE `modules`
  ADD CONSTRAINT `modules_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `purchases_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `purchases_ibfk_3` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
