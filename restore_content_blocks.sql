-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 19, 2025 at 03:29 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jenbury_finance`
--

-- --------------------------------------------------------

--
-- Table structure for table `content_blocks`
--

DROP TABLE IF EXISTS `content_blocks`;
CREATE TABLE `content_blocks` (
  `id` int(11) NOT NULL,
  `parent` varchar(128) NOT NULL,
  `slug` varchar(128) NOT NULL,
  `label` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `type` varchar(32) NOT NULL,
  `value` text DEFAULT NULL,
  `previous_value` text DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `content_blocks`
--

INSERT INTO `content_blocks` (`id`, `parent`, `slug`, `label`, `description`, `type`, `value`, `previous_value`, `modified`) VALUES
(1, 'Footer', 'footer-copyright-prefix', 'Footer: Copyright Prefix', 'Text before the copyright year in the footer.', 'text', '© Copyright 2015-', NULL, '2025-04-04 04:05:00'),
(2, 'Footer', 'footer-copyright-suffix', 'Footer: Copyright Suffix', 'Text after the copyright year in the footer.', 'text', ' Jenbury Financial ABN ', NULL, '2025-03-31 03:20:18'),
(3, 'Footer', 'footer-abn', 'Footer: ABN Number', 'The ABN number displayed in the footer.', 'text', '15 089 512 587', NULL, '2025-03-31 03:20:18'),
(4, 'Footer', 'footer-corp-rep-prefix', 'Footer: Corp Rep Prefix', 'Text before the Corporate Representative Number in the footer.', 'text', ' is a Corporate Authorised Representative (No. ', NULL, '2025-03-31 03:20:18'),
(5, 'Footer', 'footer-corp-rep-no', 'Footer: Corporate Representative Number', 'The Corporate Representative Number displayed in the footer.', 'text', '1285213', NULL, '2025-03-31 03:20:18'),
(6, 'Footer', 'footer-corp-rep-suffix', 'Footer: Corp Rep Suffix', 'Text after the Corporate Representative Number in the footer.', 'text', ') of Alliance Wealth Pty Ltd AFSL ', NULL, '2025-03-31 03:20:18'),
(7, 'Footer', 'footer-afsl', 'Footer: AFSL Number', 'The AFSL Number displayed in the footer.', 'text', '449221', NULL, '2025-03-31 03:20:18'),
(8, 'Footer', 'footer-afsl-suffix', 'Footer: AFSL Suffix', 'Text after the AFSL Number in the footer.', 'text', ' ABN 93 161 647 007', NULL, '2025-03-31 03:20:18'),
(9, 'Footer', 'footer-phone-prefix', 'Footer: Phone Prefix', 'Text before the phone number in the footer.', 'text', 'Phone: ', NULL, '2025-03-31 03:20:18'),
(10, 'Footer', 'footer-phone-number', 'Footer: Phone Number', 'The phone number displayed in the footer.', 'text', '(03) 9762 0640', NULL, '2025-03-31 03:20:18'),
(11, 'Footer', 'footer-email-prefix', 'Footer: Email Prefix', 'Text before the email address in the footer.', 'text', 'Email: ', NULL, '2025-03-31 03:20:18'),
(12, 'Footer', 'footer-email-address', 'Footer: Email Address', 'The email address displayed in the footer.', 'text', 'admin@jenbury.com.au', NULL, '2025-03-31 03:20:18'),
(13, 'Footer', 'footer-link-fsg-url', 'Footer Link: Financial Services Guide', 'URL for the Financial Services Guide link in the footer.', 'text', 'https://www.centrepointalliance.com.au/wp-content/uploads/2024/04/FSG_Brochure_AW-Apr-2024.pdf', NULL, '2025-03-31 03:20:18'),
(14, 'Footer', 'footer-link-disclaimer-url', 'Footer Link: Disclaimer', 'URL for the Disclaimer link in the footer.', 'text', 'https://www.centrepointalliance.com.au/terms/', NULL, '2025-03-31 03:20:18'),
(15, 'Footer', 'footer-link-privacy-url', 'Footer Link: Privacy Policy', 'URL for the Privacy Policy link in the footer.', 'text', 'https://www.centrepointalliance.com.au/privacy/', NULL, '2025-03-31 03:20:18'),
(16, 'Footer', 'footer-link-about-url', 'Footer Link: About', 'URL for the About link in the footer.', 'text', 'https://www.jenbury.com.au/about', NULL, '2025-03-31 03:20:18'),
(17, 'Footer', 'footer-link-faq-url', 'Footer Link: FAQ', 'URL for the FAQ link in the footer.', 'text', 'https://www.jenbury.com.au/faqs', NULL, '2025-03-31 03:20:18'),
(18, 'Footer', 'footer-link-contact-url', 'Footer Link: Contact', 'URL for the Contact link in the footer.', 'text', 'https://www.jenbury.com.au/contact', NULL, '2025-03-31 03:20:18'),
(19, 'home', 'home-page-title-tag', 'Homepage: Browser Title Tag', 'Title shown in the browser tab for the homepage.', 'text', 'Jenbury Financial Knowledge Center', NULL, '2025-03-31 03:38:26'),
(20, 'home', 'home-meta-description', 'Homepage: Meta Description Tag', 'Meta description for the homepage.', 'text', 'Welcome to Jenbury Financial Knowledge Center - Your path to financial excellence', NULL, '2025-03-31 03:38:26'),
(21, 'home', 'home-main-heading-1', 'Homepage: Main Heading (Part 1)', 'First part of the main heading on the homepage.', 'text', 'Jenbury Financial', NULL, '2025-03-31 03:38:26'),
(22, 'home', 'home-main-heading-2', 'Homepage: Main Heading (Part 2)', 'Second part of the main heading on the homepage.', 'text', 'Knowledge Center', NULL, '2025-03-31 03:38:26'),
(23, 'home', 'home-subtitle', 'Homepage: Subtitle', 'Subtitle shown below the main heading on the homepage.', 'text', 'by Andrea Jenkins', NULL, '2025-03-31 03:38:26'),
(24, 'home', 'home-cta-button-text', 'Homepage: Call-to-Action Button Text', 'Text for the call-to-action button on the homepage.', 'text', '<span>Learn More</span><span class=\"arrow\">→</span>', NULL, '2025-03-31 03:38:26'),
(25, 'home', 'home-login-button-text', 'Homepage: Login Button Text', 'Text for the login button in the header.', 'text', 'Log In', NULL, '2025-03-31 03:38:26'),
(26, 'home', 'home-signup-button-text', 'Homepage: Sign Up Button Text', 'Text for the signup button in the header.', 'text', 'Sign Up', NULL, '2025-03-31 03:38:26'),
(27, 'home', 'home-logout-button-text', 'Homepage: Logout Button Text', 'Text for the logout button in the header.', 'text', 'Logout', NULL, '2025-03-31 03:38:26'),
(28, 'Navigation', 'navbar-link-courses-text', 'Main Nav: Courses Link', 'Text for the \"Courses\" link in the navigation menu.', 'text', 'Courses', NULL, '2025-04-04 04:07:01'),
(29, 'Navigation', 'user-dropdown-admin-dashboard-text', 'User Dropdown: Admin Dashboard Link', 'Text for the \"Admin Dashboard\" link in the user dropdown menu.', 'text', 'Admin Dashboard', NULL, '2025-03-31 03:38:26'),
(30, 'Navigation', 'user-dropdown-dashboard-text', 'User Dropdown: Dashboard Link', 'Text for the \"Dashboard\" link in the user dropdown menu.', 'text', 'Dashboard', NULL, '2025-03-31 03:38:26'),
(31, 'Navigation', 'user-dropdown-manage-site-content-text', 'User Dropdown: Manage Site Content Link', 'Text for the \"Manage Site Content\" link in the user dropdown menu.', 'text', 'Manage Site Content', NULL, '2025-03-31 03:38:26'),
(32, 'Navigation', 'user-dropdown-logout-text', 'User Dropdown: Logout Link', 'Text for the \"Logout\" link in the user dropdown menu.', 'text', 'Logout', NULL, '2025-03-31 03:38:26'),
(33, 'Navigation', 'user-nav-login-text', 'Logged Out Nav: Login Button', 'Text for the \"Login\" button in the navigation menu.', 'text', 'Login', NULL, '2025-03-31 03:38:26'),
(34, 'Navigation', 'user-nav-register-text', 'Logged Out Nav: Register Button', 'Text for the \"Register\" button in the navigation menu.', 'text', 'Register', NULL, '2025-03-31 03:38:26'),
(35, 'site', 'site-name', 'Site Name', 'The name of the site, used in the title and logo alt text.', 'text', 'Jenbury Financial', NULL, '2025-03-31 03:38:26'),
(36, 'Navigation', 'navbar-link-forums-text', 'Main Nav: Forums Link', 'Text for the \"Forums\" link in the navigation menu.', 'text', 'Forums', NULL, '2025-05-12 06:14:34');

--
-- Indexes for table `content_blocks`
--
ALTER TABLE `content_blocks`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `content_blocks`
--
ALTER TABLE `content_blocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;