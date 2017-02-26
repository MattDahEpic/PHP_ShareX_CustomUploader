# PHP ShareX Custom Uploader
A custom uploader for all types of files uploaded by ShareX. Tested with Apache 2.4.18 and PHP 7

## Installation
1. Download this repository and change the details in `uploader_settings.inc.php`
2. Upload `index.php`, `install.php`, `RENAME.htaccess`, and `uploader_settings.inc.php` to your server. Rename `RENAME.htaccess` to `.htaccess` once it's uploaded.
3. Browse to `install.php` and let it do it's work. Resolve any problems if they come up.
4. Delete `install.php`. It's not needed after this.
5. Import the ShareX Custom Uploaders (`uploader_*.sxcu`) and change the details as necessary.
6. Give it a test, it should work!
