-- Integral Safety CMS Database Schema
-- Import this file via phpMyAdmin in cPanel

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Users table (admin accounts)
-- --------------------------------------------------------
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','editor') DEFAULT 'editor',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Pages table (static page content)
-- --------------------------------------------------------
CREATE TABLE `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(100) NOT NULL,
  `title` varchar(200) NOT NULL,
  `meta_description` text,
  `meta_keywords` varchar(255),
  `content` longtext,
  `hero_title` varchar(200),
  `hero_subtitle` text,
  `hero_image` varchar(255),
  `is_active` tinyint(1) DEFAULT 1,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Services table
-- --------------------------------------------------------
CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(100) NOT NULL,
  `title` varchar(200) NOT NULL,
  `short_description` text,
  `content` longtext,
  `icon` varchar(50) DEFAULT 'clipboard',
  `image` varchar(255),
  `meta_description` text,
  `show_on_homepage` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Training courses table
-- --------------------------------------------------------
CREATE TABLE `training` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(100) NOT NULL,
  `title` varchar(200) NOT NULL,
  `short_description` text,
  `content` longtext,
  `duration` varchar(50),
  `certification` varchar(100),
  `delivery_method` varchar(100),
  `image` varchar(255),
  `meta_description` text,
  `show_on_homepage` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Testimonials table
-- --------------------------------------------------------
CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_name` varchar(100) NOT NULL,
  `company` varchar(100),
  `content` text NOT NULL,
  `rating` tinyint(1) DEFAULT 5,
  `image` varchar(255),
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Gallery/Media table
-- --------------------------------------------------------
CREATE TABLE `gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `alt_text` varchar(255),
  `caption` text,
  `file_size` int(11),
  `mime_type` varchar(50),
  `uploaded_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Site settings table
-- --------------------------------------------------------
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text,
  `setting_type` enum('text','textarea','image','boolean') DEFAULT 'text',
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Page sections table (for visual page builder)
-- --------------------------------------------------------
CREATE TABLE `page_sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_type` enum('page','service','training') NOT NULL DEFAULT 'page',
  `page_id` int(11) NOT NULL,
  `section_type` varchar(50) NOT NULL,
  `section_data` longtext,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_page_type_id` (`page_type`, `page_id`),
  KEY `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Contact submissions table
-- --------------------------------------------------------
CREATE TABLE `contact_submissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20),
  `company` varchar(100),
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `submitted_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Insert default admin user (password: admin123 - CHANGE THIS!)
-- --------------------------------------------------------
INSERT INTO `users` (`username`, `email`, `password`, `role`) VALUES
('admin', 'info@integralsafetyltd.co.uk', '$2y$10$8tGlCzyfMqLq0.qJHBU.guYXVrKpCNQkLGVWEuFgGz.5GGvPqRZPm', 'admin');

-- --------------------------------------------------------
-- Insert default settings
-- --------------------------------------------------------
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`) VALUES
('site_name', 'Integral Safety Ltd', 'text'),
('site_tagline', 'Health & Safety Consultants', 'text'),
('contact_email', 'info@integralsafetyltd.co.uk', 'text'),
('contact_phone', '01530 382 150', 'text'),
('address_line1', 'Coalville, Leicestershire', 'text'),
('address_line2', 'Melton Mowbray, Leicestershire', 'text'),
('facebook_url', '', 'text'),
('linkedin_url', '', 'text'),
('twitter_url', '', 'text');

-- --------------------------------------------------------
-- Insert default pages
-- --------------------------------------------------------
INSERT INTO `pages` (`slug`, `title`, `meta_description`, `hero_title`, `hero_subtitle`, `content`) VALUES
('home', 'Home', 'Leicestershire health and safety consultants with 20+ years experience. Fire risk assessments, IOSH training, H&S consultancy.', 'Leicestershire''s Trusted Health & Safety Experts', 'From fire risk assessments to IOSH training, we help Midlands businesses create safer workplaces.', ''),
('about', 'About Us', 'Learn about Integral Safety Ltd - experienced health and safety consultants serving Leicestershire and the Midlands.', 'About Integral Safety', 'Over 20 years of experience protecting your people, property, and peace of mind.', ''),
('contact', 'Contact Us', 'Get in touch with Integral Safety Ltd for a free quote on health and safety services.', 'Get In Touch', 'Ready to improve your workplace safety? Contact us for a free, no-obligation quote.', '');

COMMIT;
