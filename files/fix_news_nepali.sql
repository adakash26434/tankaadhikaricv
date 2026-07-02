-- Fix news table Nepali text (run in phpMyAdmin)
UPDATE `news` SET 
title = 'आकाश डिजिटलमा टंक अधिकारीको प्रेरक यात्रा…',
source = 'वित्तीय पोस्ट — असार १०, २०८२'
WHERE id = 1;

UPDATE `news` SET 
title = 'सहकारी क्षेत्रको डिजिटल यात्रा — धिप्री डटकम',
source = 'धिप्री डटकम — Online News'
WHERE id = 2;

UPDATE `news` SET 
title = 'टंक अधिकारी: सहकारी डिजिटल यात्रा',
source = 'धिप्री डटकम — Online News'
WHERE id = 3;

UPDATE `news` SET 
title = 'सहकारी क्षेत्रमा डिजिटल परिवर्तन',
source = 'सहकारी अखवार — Online News'
WHERE id = 4;

UPDATE `news` SET 
title = 'सहकारीखबर: टंक अधिकारीको योगदान',
source = 'सहकारीखबर — Online News'
WHERE id = 5;
