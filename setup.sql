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
  `contact_email` varchar(255) NOT NULL DEFAULT '',
  `clients_served` varchar(50) NOT NULL DEFAULT '50+',
  `digital_services_intro` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `profile` (`full_name`,`title`,`bio`,`email`,`phone`,`location`,`born`,`company`,`company_url`,`role`,`facebook_url`,`tiktok_url`,`whatsapp_url`,`linkedin_url`,`youtube_url`,`cv_file`,`avatar`,`og_image`,`contact_email`,`clients_served`,`digital_services_intro`) VALUES
('Tanka Prasad Adhikari','Founder & Chief Executive Officer','Tanka Prasad Adhikari, the Founder of Aakash Digital Pvt. Ltd., leads the digital transformation of Nepal\'s cooperative sector. With a focus on fintech solutions and automation, he empowers financial institutions to operate efficiently in the digital age.','aakashpame@gmail.com','+977 985-6026434','Pokhara, Nepal','19 March 1990 — Pokhara, Nepal','Aakash Digital Pvt. Ltd.','https://www.aakashdigital.com.np','Founder & CEO','https://www.facebook.com/akashadhikari2','https://www.tiktok.com/@tankaadhikari34','https://wa.me/9779856026434','https://www.linkedin.com/in/tanka-adhikari','https://www.youtube.com/@aakashpame','files/canada.pdf','img/avatar.jpg','','aakashpame@gmail.com','50+','Need a professional website, reliable web hosting, or custom email hosting? I provide end-to-end digital solutions tailored for businesses, cooperatives, and startups in Nepal.');

-- Education
CREATE TABLE IF NOT EXISTS `education` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `degree_code` varchar(50) NOT NULL DEFAULT '',
  `degree_name` varchar(255) NOT NULL DEFAULT '',
  `institution` varchar(255) NOT NULL DEFAULT '',
  `period` varchar(100) NOT NULL DEFAULT '',
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `education` (`degree_code`,`degree_name`,`institution`,`period`,`sort_order`) VALUES
('MBS','Master of Business Studies (MBS - Management)','Janapriya Multiple Campus, Tribhuvan University','Graduated: November 2019',1),
('BBS','Bachelor of Business Studies (BBS - Management)','Prithivi Narayan Campus, Tribhuvan University','Graduated: June 2013',2),
('PCL','Proficiency Certificate Level (PCL) in Management','Prithivi Narayan Campus, Tribhuvan University','Graduated: March 2008',3),
('SLC','School Leaving Certificate (10th Grade)','Shree Siddha Baraha Secondary School, Board of Nepal','Graduated: February 2004',4);

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

INSERT IGNORE INTO `experience` (`company`,`role`,`period`,`description`,`color`,`sort_order`) VALUES
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

INSERT IGNORE INTO `training` (`icon`,`name`,`organizer`,`year`,`certificate_file`,`certificate_url`,`sort_order`) VALUES
('laptop','Web Designing Training','Code IT — HTML, CSS, JavaScript, UI/UX','2024','','',1),
('certificate','Project Approach to Co-Operative Development','National Cooperative Union of India (NCUI) & CENTRABANK, New Delhi','2022','','',2),
('users','Social Work — Charter President','Lions Clubs International','2019','','',3),
('certificate','Manager Training','Nepal Federation of Saving & Credit Cooperative Unions Ltd. (NEFSCUN)','2018','','',4),
('certificate','National Workshop on Account Supervisory Committee of Cooperatives','Nepal Federation of Saving & Credit Cooperative Unions Ltd. (NEFSCUN)','2017','','',5),
('globe','Exposure Programme in SHG, JLG, and Farmers Club','Reserve Bank of India','2017','','',6),
('certificate','CEO Workshop','Nepal Federation of Saving & Credit Cooperative Unions Ltd. (NEFSCUN)','2016','','',7),
('chart-bar','Account, Audit, and Tax Management Training','Management Training Consultant Service Center','2012','','',8),
('university','Banking Course','KFA Training School, Kathmandu','2010','','',9),
('book-open','Diploma Course','Friends of Nepali Village, Pokhara','2009','','',10),
('laptop','Basic Computer Knowledge','2000 Computer Institution, Pokhara','2007','','',11),
('certificate','Radio Program Training','Multi Skill Development Training Institution, Pokhara','2007','','',12);

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

INSERT IGNORE INTO `awards` (`title`,`organization`,`year`,`description`,`url`,`image1`,`image2`,`color`,`icon`,`sort_order`) VALUES
('ICT Award – Startup Category','ICT Awards Nepal','2024','Recognized for innovation and contribution to Nepal\'s technology sector through Aakash Digital Pvt. Ltd.','https://apply.ictaward.org/forms/province-startup-ict-recognition-2025/apply','img/ict.jpg','img/ict2.jpg','cyan','fa-trophy',1),
('SIP 2026 Canada — Selected Researcher','Scholars in Ireland Programme (SIP), Ireland','2026','Selected as a researcher for SIP 2026 Canada. Abstract titled "Digital Transformation of Nepalese Cooperatives Through Aakash DMS" accepted for oral presentation. Supported by Global Affairs Canada & Irish Aid.','https://sipireland.ie/','img/sip2026.jpg','img/sip2026-2.jpg','cyan','fa-globe-americas',2),
('Innovate Finance — Pride in FinTech Powerlist','Innovate Finance, UK','Powerlist 2026','Listed among global Startup leaders in FinTech by Innovate Finance, UK, for outstanding impact and innovation.','https://www.innovatefinance.com/pride-in-fintech-2025/#Standout45','img/powerlist.jpg','img/powerlist2.jpg','violet','fa-star',3);

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

INSERT IGNORE INTO `research` (`title`,`description`,`pdf_file`,`year`,`journal`,`url`,`sort_order`) VALUES
('Cooperative Weak Documentation and Automation','Investigated why the loan documents of cooperatives are weak and suggested measures to improve them, including proper digitization, standardized documentation, and staff training.','files/canada2.pdf','2024','Akash DMS Research Series','',1),
('Digital Transformation of Nepalese Cooperatives Through Aakash DMS (SIP2026 Canada)','Selected for oral presentation at SIP 2026 Canada. Keywords: Digital transformation, cooperatives, workflow automation, document management, financial inclusion, Nepal.','files/SIP2026-Canada-Abstract.pdf','2026','Research Now — SIP26-CA-166','https://sipireland.ie/',2),
('Leading the Financial Digital Era in Nepal: Akash Digital\'s Contribution','This paper addresses a critical challenge in Financial Inclusion and FinTech in Emerging Economies — specifically, the systemic lack of digitization within Nepal\'s high-impact cooperative financial sector.','files/canada1.pdf','2025','Research Now','https://www.researchgate.net/publication/leading-financial-digital-era-nepal-akash-digital-contribution',3),
('Consumer Satisfaction Among Major Internet Service Providers in Pokhara','Surveyed customers in Pokhara to determine the percentage satisfied with internet services, highlighting service gaps and areas for improvement. In partial fulfillment of the requirement of the degree of Master in Business Studies (MBS).','files/research-paper-paperless-loan.pdf','2015','MBS Thesis — Pokhara University','https://www.researchgate.net/publication/consumer-satisfaction-isps-pokhara',4),
('Concerns About Paperless Loan Applications','Explored how many people are concerned about the future loan application process without physical documents and identified strategies to ease the transition to paperless workflows.','files/canada4.pdf','2025','Akash DMS Research Series','',5);

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

INSERT IGNORE INTO `news` (`image`,`title`,`source`,`pdf_file`,`url`,`sort_order`) VALUES
('files/news1.jpeg','आकाश डिजिटलमा टंक अधिकारीको प्रेरक यात्रा…','वित्तीय पोस्ट — असार १०, २०८२','','https://bittiyapost.com/news/2025/06/24/17259',1),
('files/news2.jpeg','सहकारी क्षेत्रको डिजिटल यात्रा — धिप्री डटकम','धिप्री डटकम — Online News','','https://dhipri.com/posts/1674',2),
('files/news3.jpeg','टंक अधिकारी: सहकारी डिजिटल यात्रा','धिप्री डटकम — Online News','','https://dhipri.com/posts/1269',3),
('files/news4.jpeg','सहकारी क्षेत्रमा डिजिटल परिवर्तन','सहकारी अखवार — Online News','','https://sahakariakhabar.com/news/1711866423',4),
('files/news5.jpeg','सहकारीखबर: टंक अधिकारीको योगदान','सहकारीखबर — Online News','','https://www.sahakarikhabar.com/2024/03/13/tank-adhikari/',5);

-- Skills
CREATE TABLE IF NOT EXISTS `skills` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `category` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `level` int(11) NOT NULL DEFAULT 80,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `skills` (`category`,`name`,`level`,`sort_order`) VALUES
('Professional','Leadership & Strategy',95,1),
('Professional','Cooperative Management',92,2),
('Professional','FinTech Consulting',90,3),
('Professional','Digital Transformation',88,4),
('Professional','Communication',70,5),
('Professional','Confidence',85,6),
('Code','HTML5 / CSS3',90,7),
('Code','PHP / MySQL',70,8),
('Code','JavaScript',65,9),
('Code','Web Development',75,10),
('Software','Aakash DMS',95,11),
('Software','Microsoft Office',90,12),
('Software','Adobe Photoshop',85,13),
('Software','Adobe Illustrator',90,14),
('Software','GitHub / Version Control',90,15),
('Software','cPanel Hosting',85,16),
('Software','Database Management',80,17),
('Language','Nepali',99,18),
('Language','English',75,19),
('Language','Hindi',80,20);

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

INSERT IGNORE INTO `projects` (`title`,`subtitle`,`description`,`url`,`image1`,`image2`,`tags`,`sort_order`) VALUES
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
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `rating` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `portfolio_sites` (`image`,`title`,`subtitle`,`url`,`sort_order`) VALUES
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
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_pricing` TINYINT(1) NOT NULL DEFAULT 0,
  `price` varchar(100) NOT NULL DEFAULT '',
  `price_unit` varchar(100) NOT NULL DEFAULT '',
  `features` text,
  `accent_color` varchar(20) NOT NULL DEFAULT 'cyan',
  `cta_text` varchar(255) NOT NULL DEFAULT '',
  `cta_link` varchar(255) NOT NULL DEFAULT '#contact'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `services_about` (`icon`,`name`,`description`,`sort_order`,`is_pricing`,`price`,`price_unit`,`features`,`accent_color`,`cta_text`,`cta_link`) VALUES
('globe','Domain Management','Secure domain registration & DNS management',1,0,'','','','cyan','',''),
('envelope','Official Email','Professional business email for Tanka Prasad Adhikari',2,0,'','','','cyan','',''),
('credit-card','Digital Payments','Integration for modern financial transactions',3,0,'','','','cyan','',''),
('code','Web Development','Advanced UI/UX focused development',4,0,'','','','cyan','',''),
('database','DMS Software','Cooperative document management systems',5,0,'','','','cyan','',''),
('chart-line','FinTech Consulting','Digital transformation advisory',6,0,'','','','cyan','',''),
('robot','Automation','Loan & document workflow automation',7,0,'','','','cyan','',''),

-- Digital Services (Pricing Cards) —
('globe','Website Development','From NPR 70,000+ - One-time',1,1,'70,000+','NPR','Custom design included\nResponsive & mobile-friendly design\nSEO optimized structure\nContact forms & analytics\nFree 30-day support','cyan','Request a Quote →','#contact'),
('newspaper','News Portal','NPR 100,000 - One-time',2,1,'100,000','NPR','Next Gen News Portal\nBreaking news & categories\nUser authentication\nSocial sharing\nSEO optimized\nAdmin dashboard','violet','Get News Portal →','#contact'),
('hotel','Hotel Website','NPR 100,000 - One-time',3,1,'100,000','NPR','Complete Hotel Website\nRoom booking system\nGallery & amenities\nOnline payment ready\nMulti-language support','yellow','Get Hotel Website →','#contact'),
('calculator','Account Software','NPR 65,000 - One-time',4,1,'65,000','NPR','Complete Accounting Solution\nInvoice & expense tracking\nFinancial reports\nMulti-user access\nData backup & security','green','Get Software →','#contact'),
('cloud','Web Hosting','From NPR 20,000 /year',5,1,'20,000','NPR /year','Nepal-based servers\n99.9% uptime guarantee\nFree SSL certificate\nDaily backups\ncPanel / managed options','orange','Get Hosting →','#contact'),
('envelope','Professional Email Hosting','From NPR 25,000 /year',6,1,'25,000','NPR /year','Your domain @you.com\nyourname@yourdomain.com\n5 GB storage per mailbox\nWebmail + IMAP/SMTP\nSpam & virus protection','pink','Setup Email →','#contact'),
('shield-alt','Cyber Security Training','NPR 30,000 - Per person',7,1,'30,000','NPR','Group discounts available\nNetwork & endpoint security\nPhishing awareness\nData privacy & compliance\nIncident response basics','red','Enroll Now →','#contact');

-- Interests
CREATE TABLE IF NOT EXISTS `interests` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `icon` varchar(50) NOT NULL DEFAULT 'heart',
  `name` varchar(255) NOT NULL DEFAULT '',
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `interests` (`icon`,`name`,`sort_order`) VALUES
('search','Research & Exploring New Topics',1),
('bicycle','Cycling',2),
('plane','Traveling',3),
('music','Working with Music',4),
('laptop-code','Digital Innovation',5),
('chart-line','FinTech',6),
('users','Cooperative Development',7),
('globe','E-Governance',8),
('book','Reading',9),
('microphone','Speaking',10),
('mountain','Hiking',11),
('camera','Photography',12);

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

DROP PROCEDURE IF EXISTS add_idx;


-- ============================================================
-- UPGRADE SCRIPT: Add new columns to existing databases
-- Safe to run - won't error if columns already exist
-- ============================================================

DROP PROCEDURE IF EXISTS safe_add_column;

-- Procedure to add column only if it doesn't exist
DELIMITER $$
CREATE PROCEDURE safe_add_column(IN tbl VARCHAR(128), IN col VARCHAR(128), IN col_def VARCHAR(255))
BEGIN
  IF NOT EXISTS (
    SELECT * FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = tbl AND COLUMN_NAME = col
  ) THEN
    SET @sql = CONCAT('ALTER TABLE `', tbl, '` ADD COLUMN `', col, '` ', col_def);
    PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
  END IF;
END$$
DELIMITER ;

-- services_about: add pricing card columns
CALL safe_add_column('services_about', 'is_pricing', 'TINYINT(1) NOT NULL DEFAULT 0 AFTER `sort_order`');
CALL safe_add_column('services_about', 'price', "VARCHAR(100) NOT NULL DEFAULT '' AFTER `is_pricing`");
CALL safe_add_column('services_about', 'price_unit', "VARCHAR(100) NOT NULL DEFAULT '' AFTER `price`");
CALL safe_add_column('services_about', 'features', 'TEXT AFTER `price_unit`');
CALL safe_add_column('services_about', 'accent_color', "VARCHAR(20) NOT NULL DEFAULT 'cyan' AFTER `features`");
CALL safe_add_column('services_about', 'cta_text', "VARCHAR(255) NOT NULL DEFAULT '' AFTER `accent_color`");
CALL safe_add_column('services_about', 'cta_link', "VARCHAR(255) NOT NULL DEFAULT '#contact' AFTER `cta_text`");

-- portfolio_sites: add rating column
CALL safe_add_column('portfolio_sites', 'rating', 'TINYINT(1) NOT NULL DEFAULT 0 AFTER `sort_order`');

-- profile: add clients_served column
CALL safe_add_column('profile', 'clients_served', "VARCHAR(50) NOT NULL DEFAULT '50+' AFTER `contact_email`");

-- profile: add digital_services_intro column
CALL safe_add_column('profile', 'digital_services_intro', 'TEXT NOT NULL AFTER `clients_served`');

-- Clean up
DROP PROCEDURE IF EXISTS safe_add_column;
