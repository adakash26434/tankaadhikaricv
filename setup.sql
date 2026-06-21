-- ============================================================
-- Tanka Prasad Adhikari — Portfolio Setup SQL (Full Dynamic)
-- Run this file once in your cPanel phpMyAdmin
-- Run again to ADD INDEXES (safe to re-run — IF NOT EXISTS used)
-- ============================================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Profile
CREATE TABLE IF NOT EXISTS `profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `full_name` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `bio` text NOT NULL,
  `email` varchar(255) NOT NULL DEFAULT '',
  `phone` varchar(100) NOT NULL DEFAULT '',
  `location` varchar(255) NOT NULL DEFAULT '',
  `born` varchar(100) NOT NULL DEFAULT '',
  `company` varchar(255) NOT NULL DEFAULT '',
  `company_url` varchar(255) NOT NULL DEFAULT '',
  `role` varchar(255) NOT NULL DEFAULT '',
  `facebook_url` varchar(255) NOT NULL DEFAULT '',
  `tiktok_url` varchar(255) NOT NULL DEFAULT '',
  `whatsapp_url` varchar(255) NOT NULL DEFAULT '',
  `linkedin_url` varchar(255) NOT NULL DEFAULT '',
  `youtube_url` varchar(255) NOT NULL DEFAULT '',
  `cv_file` varchar(255) NOT NULL DEFAULT 'files/canada.pdf',
  `avatar` varchar(255) NOT NULL DEFAULT 'img/avatar.jpg',
  `og_image` varchar(255) NOT NULL DEFAULT '',
  `contact_email` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `profile` (`full_name`,`title`,`bio`,`email`,`phone`,`location`,`born`,`company`,`company_url`,`role`,`facebook_url`,`tiktok_url`,`whatsapp_url`,`linkedin_url`,`youtube_url`,`cv_file`,`avatar`) VALUES
('Tanka Prasad Adhikari','Founder & Chief Executive Officer','Tanka Prasad Adhikari, the Founder of Aakash Digital Pvt. Ltd., leads the digital transformation of Nepal\'s cooperative sector. With a focus on fintech solutions and automation, he empowers financial institutions to operate efficiently in the digital age.','aakashpame@gmail.com','+977 985-6026434','Pokhara, Nepal','19 March 1990 — Pokhara, Nepal','Aakash Digital Pvt. Ltd.','https://www.aakashdigital.com.np','Founder & CEO','https://www.facebook.com/akashadhikari2','https://www.tiktok.com/@tankaadhikari34','https://wa.me/9779856026434','https://www.linkedin.com/in/tanka-adhikari','https://www.youtube.com/@aakashpame','files/canada.pdf','img/avatar.jpg');

-- Education
CREATE TABLE IF NOT EXISTS `education` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `degree_code` varchar(50) NOT NULL DEFAULT '',
  `degree_name` varchar(255) NOT NULL DEFAULT '',
  `institution` varchar(255) NOT NULL DEFAULT '',
  `period` varchar(100) NOT NULL DEFAULT '',
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `education` (`degree_code`,`degree_name`,`institution`,`period`,`sort_order`) VALUES
('MBS','Master of Business Studies','Pokhara University — Prithvi Narayan Campus, Pokhara','2013 – 2015',1),
('BBS','Bachelor of Business Studies','Prithvi Narayan Campus, Pokhara','2007 – 2011',2),
('PCL','Proficiency Certificate Level (Commerce)','Prithvi Narayan Campus, Pokhara','2005 – 2007',3),
('SLC','School Leaving Certificate','Bhadrakali Bal Bidhyalaya, Pokhara','2005',4);

-- Work Experience
CREATE TABLE IF NOT EXISTS `experience` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `period` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `color` varchar(50) NOT NULL DEFAULT 'cyan',
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `experience` (`company`,`role`,`period`,`description`,`color`,`sort_order`) VALUES
('AAKASH DIGITAL PVT. LTD.','Founder / CEO','Jun 2017 – Present','Automation of documents related to loan investment and recovery in cooperative institutions, leading digital transformation in Nepal\'s cooperative sector through Aakash DMS.','violet',1),
('JAYA MANAKAMANA SAVING AND CREDIT CO-OPERATIVE LTD.','CEO / Manager','Jul 2016 – Aug 2023','Worked with the board of directors to establish objectives, oversee financial structure, ensure compliance, manage HR and daily operations, and strengthen community relations for sustainable cooperative growth.','cyan',2),
('BIHANI SAVING AND CREDIT CO-OPERATIVE LTD.','CEO / Manager','Jul 2013 – Jun 2016','Directed cooperative operations, maintained fiscal discipline, coordinated strategic planning, and strengthened the cooperative\'s service delivery and compliance with regulatory bodies.','cyan',3),
('ADARSHA SAMAJ NATIONAL DAILY','Marketing Officer','Jul 2011 – Jun 2013','Managed advertising coordination, collaborated with marketing agencies, oversaw budgeting and campaign execution, and built strong relations with business partners and advertisers.','cyan',4),
('TEJ INVESTMENT COMPANY','Marketing Officer','Mar 2010 – Mar 2011','Planned and executed marketing strategies, handled client communications, and managed recovery and debt tracking activities while strengthening customer trust and brand presence.','cyan',5),
('NEHA ENTERPRISES PVT. LTD.','Sales Person','May 2008 – Apr 2009','Provided customer service and product guidance, maintained stock organization, and enhanced customer satisfaction through professional sales and communication.','cyan',6);

-- Training & Certificates
CREATE TABLE IF NOT EXISTS `training` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `icon` varchar(50) NOT NULL DEFAULT 'certificate',
  `name` varchar(255) NOT NULL DEFAULT '',
  `organizer` varchar(255) NOT NULL DEFAULT '',
  `year` varchar(50) NOT NULL DEFAULT '',
  `certificate_file` varchar(255) NOT NULL DEFAULT '',
  `certificate_url` varchar(500) NOT NULL DEFAULT '',
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `training` (`icon`,`name`,`organizer`,`year`,`sort_order`) VALUES
('award','Bikalpa Business Award — 2071','Bikalpa Organization','2071 BS',1),
('certificate','Training on Cooperative Management','Ministry of Land Management, Nepal','2072 BS',2),
('laptop','Computer Operator Training','Prithivi Computer Centre','1993',3),
('book-open','Introduction to Financial Planning','Nepal College of Chartered Accountancy','2019',4),
('shield-alt','DMS Operator Training','Aakash Digital Pvt. Ltd.','2018',5),
('hand-holding-usd','Loan Recovery Training','Gandaki Cooperative Association','2017',6),
('university','Higher Education in Cooperative','Pokhara University, FOM','2016',7),
('chart-bar','Financial Statement Analysis','Nepal Commerce Campus','2015',8),
('users','HR Management Workshop','FOM, Pokhara','2014',9),
('leaf','Sustainable Business Practices','FNCCI Gandaki','2014',10),
('bullhorn','Marketing Strategy Workshop','Nepal Marketing Professionals','2013',11),
('globe','E-Governance Training','Ministry of Federal Affairs','2020',12);

-- Awards & Nominations
CREATE TABLE IF NOT EXISTS `awards` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(255) NOT NULL,
  `organization` varchar(255) NOT NULL,
  `year` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `url` varchar(255) NOT NULL DEFAULT '',
  `image1` varchar(255) NOT NULL DEFAULT '',
  `image2` varchar(255) NOT NULL DEFAULT '',
  `color` varchar(50) NOT NULL DEFAULT 'cyan',
  `icon` varchar(50) NOT NULL DEFAULT 'fa-trophy',
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `awards` (`title`,`organization`,`year`,`description`,`url`,`image1`,`image2`,`color`,`icon`,`sort_order`) VALUES
('ICT Award – Startup Category','ICT Awards Nepal','2024','Recognized for innovation and contribution to Nepal\'s technology sector through Aakash Digital Pvt. Ltd.','https://apply.ictaward.org/forms/province-startup-ict-recognition-2025/apply','img/ict.jpg','img/ict2.jpg','cyan','fa-trophy',1),
('Innovate Finance — Pride in FinTech Powerlist','Innovate Finance, UK','Powerlist 2026','Listed among global Startup leaders in FinTech by Innovate Finance, UK, for outstanding impact and innovation.','https://www.innovatefinance.com/pride-in-fintech-2025/#Standout45','img/powerlist.jpg','img/powerlist2.jpg','violet','fa-star',2);

-- Research Publications
CREATE TABLE IF NOT EXISTS `research` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(500) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `pdf_file` varchar(255) NOT NULL DEFAULT '',
  `year` varchar(50) NOT NULL DEFAULT '',
  `journal` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(500) NOT NULL DEFAULT '',
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `research` (`title`,`description`,`pdf_file`,`year`,`sort_order`) VALUES
('Digital Transformation in Nepal\'s Cooperative Sector','Research on digitizing cooperative operations for efficiency and transparency, with focus on document management systems.','files/research1.pdf','2020',1),
('FinTech Innovation and Rural Financial Inclusion','Study on how fintech solutions bridge the financial inclusion gap in rural Nepal\'s cooperative institutions.','files/research2.pdf','2021',2),
('E-Governance Framework for Cooperatives','Framework proposal for implementing e-governance in Nepal\'s cooperative federations.','files/research3.pdf','2022',3),
('Impact of Automation on Loan Recovery Rates','Quantitative analysis of automation\'s effect on loan recovery efficiency in Gandaki Province cooperatives.','files/research4.pdf','2023',4),
('Cooperative Leadership in the Digital Age','Leadership models for managing digital transformation in traditional cooperative institutions.','files/research5.pdf','2024',5);

-- News & Online Publications
CREATE TABLE IF NOT EXISTS `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `image` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(500) NOT NULL DEFAULT '',
  `source` varchar(255) NOT NULL DEFAULT '',
  `pdf_file` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `news` (`image`,`title`,`source`,`pdf_file`,`url`,`sort_order`) VALUES
('img/news-screenshot.jpg','ICT Award Winner 2024 — Aakash Digital','Gorkhapatra National Daily','files/news1.pdf','',1),
('img/news1-screenshot.jpg','Tanka Adhikari Named in Innovate Finance Powerlist 2026','Mero Online News','files/news2.pdf','',2),
('img/news2-screenshot.jpg','Digital Cooperative Management: Aakash DMS Revolution','Online Khabar','files/news3.pdf','',3),
('img/news3-screenshot.jpg','Pokhara Entrepreneur Leads Digital Transformation','Nagarik Daily','files/news4.pdf','',4),
('img/news4-screenshot.jpg','FinTech Innovation Award for Aakash Digital','Karobar Daily','files/news5.pdf','',5),
('img/news5-screenshot.jpg','Cooperative Sector Digitization — Success Story','Hamro Patro News','files/news6.pdf','',6);

-- Skills
CREATE TABLE IF NOT EXISTS `skills` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `category` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `level` int(11) NOT NULL DEFAULT 80,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `skills` (`category`,`name`,`level`,`sort_order`) VALUES
('Professional','Leadership & Strategy',95,1),
('Professional','Cooperative Management',92,2),
('Professional','FinTech Consulting',90,3),
('Professional','Digital Transformation',88,4),
('Code','Web Development',75,5),
('Code','PHP / MySQL',70,6),
('Code','JavaScript',65,7),
('Software','Microsoft Office',90,8),
('Software','Aakash DMS',95,9),
('Software','Database Management',80,10),
('Language','Nepali',99,11),
('Language','English',75,12),
('Language','Hindi',80,13);

-- Projects
CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `url` varchar(255) NOT NULL DEFAULT '',
  `image1` varchar(255) NOT NULL DEFAULT '',
  `image2` varchar(255) NOT NULL DEFAULT '',
  `tags` varchar(500) NOT NULL DEFAULT '',
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `projects` (`title`,`subtitle`,`description`,`url`,`image1`,`image2`,`tags`,`sort_order`) VALUES
('Aakash DMS System','Digital Cooperative Management System','Nepal\'s premier fintech solution for cooperatives — complete automation of loan documents, member management, reporting dashboards, and document digitization. Used by cooperatives across Gandaki Province.','https://www.aakashdigital.com.np','img/aakashdms1.jpg','img/aakashdms2.jpg','Fintech,Document Automation,Loan Management,Cooperative',1),
('Aakash Digital — Corporate Website','Company Website & Product Showcase','Official company website for Aakash Digital Pvt. Ltd. showcasing the DMS software, services, client cooperatives, and digital transformation mission in Nepal\'s financial sector.','https://www.aakashdigital.com.np','img/akashdigital-thumb.jpg','','Corporate Website,Web Design,Nepali',2),
('Jaya Sahakari Website','Cooperative Website Development','Developed a responsive bilingual (Nepali/English) website for Jaya Manakamana Saving & Credit Cooperative with online presence, member information, and interest rate display.','https://jayasahakari.com.np','img/jayasahakari-thumb.jpg','','Web Development,Bilingual,Responsive,Cooperative',3),
('Bishal Saving Co-op Website','Cooperative Website Design','Designed and developed a professional website for Bishal Saving and Credit Cooperative Ltd. enabling digital presence and member communication.','','img/bishalsaving-thumb.jpg','','Web Design,Cooperative,Responsive',4),
('Lekhanath Saving Co-op Website','Cooperative Website Design','Built a bilingual cooperative website for Lekhanath Saving and Credit Cooperative with information portal, contact forms, and financial information display.','','img/lekhanathsaving-thumb.jpg','','Web Development,Bilingual,Cooperative',5);

-- Portfolio Websites (About section gallery)
CREATE TABLE IF NOT EXISTS `portfolio_sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `image` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `subtitle` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `portfolio_sites` (`image`,`title`,`subtitle`,`url`,`sort_order`) VALUES
('img/jayasahakari-thumb.jpg','Jaya Sahakari','Cooperative Website','https://jayasahakari.com.np',1),
('img/bishalsaving-thumb.jpg','Bishal Saving Co-op','Website Design','',2),
('img/lekhanathsaving-thumb.jpg','Lekhanath Saving Co-op','Website Design','',3),
('img/akashdigital-thumb.jpg','Aakash Digital','Corporate Website','https://www.aakashdigital.com.np',4),
('img/website-screenshot.jpg','Cooperative Portal','Web Development','',5),
('img/project-screenshot.jpg','DMS Dashboard','Software Project','',6);

-- Services (About section cards)
CREATE TABLE IF NOT EXISTS `services_about` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `icon` varchar(50) NOT NULL DEFAULT 'globe',
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(500) NOT NULL DEFAULT '',
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `services_about` (`icon`,`name`,`description`,`sort_order`) VALUES
('globe','Domain Management','Secure domain registration & DNS management',1),
('envelope','Official Email','Professional business email for Tanka Prasad Adhikari',2),
('credit-card','Digital Payments','Integration for modern financial transactions',3),
('code','Web Development','Advanced UI/UX focused development',4),
('database','DMS Software','Cooperative document management systems',5),
('chart-line','FinTech Consulting','Digital transformation advisory',6),
('robot','Automation','Loan & document workflow automation',7),
('chalkboard-teacher','Training','Cooperative sector capacity building',8);

-- Interests
CREATE TABLE IF NOT EXISTS `interests` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `icon` varchar(50) NOT NULL DEFAULT 'heart',
  `name` varchar(255) NOT NULL DEFAULT '',
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `interests` (`icon`,`name`,`sort_order`) VALUES
('laptop-code','Digital Innovation',1),
('chart-line','FinTech',2),
('users','Cooperative Dev',3),
('globe','E-Governance',4),
('book','Reading',5),
('microphone','Speaking',6),
('plane','Travel',7),
('mountain','Hiking',8),
('music','Music',9),
('camera','Photography',10);

-- Contact Messages
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(500) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_read` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Performance Indexes ─────────────────────────────────────────────────
-- NOTE: Requires MySQL 8.0.29+. If host uses MySQL 5.7, run these manually
-- via phpMyAdmin → SQL tab:
--
-- CREATE INDEX idx_messages_is_read ON messages(is_read);
-- CREATE INDEX idx_messages_created ON messages(created_at);
-- CREATE INDEX idx_experience_sort ON experience(sort_order);
-- ... etc.
--
-- Or upgrade MySQL. Indexes significantly speed up queries on large tables.

DELIMITER $$
DROP PROCEDURE IF EXISTS add_idx$$
CREATE PROCEDURE add_idx(IN tbl VARCHAR(128), IN idx VARCHAR(128), IN cols VARCHAR(255))
BEGIN
  DECLARE exists_cnt INT DEFAULT 0;
  SELECT COUNT(*) INTO exists_cnt FROM information_schema.statistics
    WHERE table_schema = DATABASE() AND table_name = tbl AND index_name = idx;
  IF exists_cnt = 0 THEN
    SET @sql = CONCAT('ALTER TABLE `', tbl, '` ADD INDEX `', idx, '` (', cols, ')');
    PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
  END IF;
END$$
DELIMITER ;

CALL add_idx('messages', 'idx_messages_is_read', '`is_read`');
CALL add_idx('messages', 'idx_messages_created', '`created_at`');
CALL add_idx('experience', 'idx_experience_sort', '`sort_order`');
CALL add_idx('education', 'idx_education_sort', '`sort_order`');
CALL add_idx('skills', 'idx_skills_sort', '`sort_order`');
CALL add_idx('skills', 'idx_skills_category', '`category`');
CALL add_idx('projects', 'idx_projects_sort', '`sort_order`');
CALL add_idx('awards', 'idx_awards_sort', '`sort_order`');
CALL add_idx('news', 'idx_news_sort', '`sort_order`');
CALL add_idx('training', 'idx_training_sort', '`sort_order`');
CALL add_idx('research', 'idx_research_sort', '`sort_order`');
CALL add_idx('interests', 'idx_interests_sort', '`sort_order`');
CALL add_idx('portfolio_sites', 'idx_portfolio_sort', '`sort_order`');
CALL add_idx('services_about', 'idx_services_sort', '`sort_order`');

DROP PROCEDURE IF EXISTS add_idx$$
