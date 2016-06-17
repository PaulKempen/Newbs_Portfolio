# Newbs-Portfolio
Portfolio Website Project Document Repository

This website was created as part of the coursework for Bachelores of Applied Science in Information System at Olympic College.

It's purpose is to host a group (class/cohort/team/etc) and allow each member to select their areas of focus and tell a little bit about themselves and showcase some selected work samples for potential employers to review.

Some features of this website include:
- Editable "defining" text from within the admin control page so no html code needs to be edited to tailor this page to your group
- Fully customizable website colors via a web interface in the admin control page which lets the admins set all the background colors and text colors to anything they desire without messing with any css.
- All data stored in MySQL database for security and ease of access.
- Designed to run on XAMPP/LAMPP (Apache webserver, MySQL database) with minimal initial configuration.

#Initial Setup

Basic technology needed for the website:<br/>
**OS:** I only have instructions for win32 but Linux/Unix shouldnt be too much different<br/>
**Web server:** Apache<br/>
**Database:** MySQL<br/>
**for email:** sendmail<br/>
(above 3 are bundled with XAMPP)

**Database setup**

- Import into mysql: newbs\_portfolio\_db.sql from the sql folder<br/>
this file contains the database structure and basic data
- if not already done so, set a password for root and any other no password users for mysql

**Required Environment variables (to be set on host machine):**

- NEWBS\_DB = newbs\_portfolio
- NEWBS\_SERVER = localhost 
- NEWBS\_USER = (create new user in MySql (phpmyadmin) and enter user name here and password below) 
- NEWBS\_PW = 

**To prevent viewing “indexed” file structure under htdocs:**<br/>
Open **http.conf** (XAMPP/apache/conf/http.conf in windows)<br/>
Under **\<Directory "[drive]:/XAMPP/htdocs"\>**<br/>
Remove **"Indexes"** from **"Options"** to prevent viewing of files in all htdocs directories

**To get (PHP) mail() working(requires a gmail account):**<br/>
For ubuntu I found this tutorial which I have not been able to verify/validate:<br/>
http://lukepeters.me/blog/getting-the-php-mail-function-to-work-on-ubuntu

under windows edit these 2 files with the following:

**XAMPP/php/php.ini:**<br/>
Under \[mail function\] (line 1130 for me)
- sendmail_path = "\"*(driveletter)*:\XAMPP\sendmail\sendmail.exe\" -t"
 
**XAMPP/sendmail/sendmail.ini:**<br/>
Make sure these are set as followed:
- smtp_server=smtp.gmail.com
- smtp_port=465
- smtp_ssl=ssl
- auth_username=(gmail email address which i would recommend be created specifically for this and not use a personal account)
- auth_password=(password to above email address)
- force_sender=(gmail email address repeated)

#Once the website is operational
initial admin acount is:
- username: siteAdmin
- password: Password1

It is strongly recommended to change this password and make your own admin accounts, at which point this one can be deleted.
