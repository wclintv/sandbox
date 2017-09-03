//////////////Acquire Domain and Web Hosting////////////////

1)  Purchase a domain on GoDaddy.
2)  Purchase linux webhosting on GoDaddy.
3)  Setup webhosting with the domain you purchased.
4) setup cpanel with a username and password...this occurs during the setup wizard for webhosting.


///////////////////PUTTY SETUP///////////////////////////
1) Download and instal Putty SSH if you don't already have it:  http://www.putty.org/
2) CPanel- enable SSH
5) Putty- enter the IP Address of the new domain and open a connection
6) use the cpanel username and password to login.

////////////////INSTALL COMPOSER/////////////////////
follow instrustion on composer.com for a linux local installation.
https://getcomposer.org/download/



//////////////////INSTALLING LARAVEL//////////////////////
1)  Putty Terminal should default to the root directory...the same directory that public_html is in.  We want to install laravel to this directory.  The installation should be protected in this directory.
2)	In Putty, use composer to install laravel (DON'T use laravel/installer)...following install documentation at laravel.com.

	composer create-project --prefer-dist laravel/laravel snapdsk

////////////////LARAVEL SETUP ON GODADDY SHARED HOSTING///////////////////
Since we cant change the web root on a godaddy shared hosting site, we need to do some redirects
to make laravel work properly.  follow this tutorial from:

	https://medium.com/laravel-news/the-simple-guide-to-deploy-laravel-5-application-on-shared-hosting-1a8d0aee923e#.zgza14z6h

1) Using cpanel FileManager or Filezilla, Copy all files from /snapdsk/public to /public_html.

*NOTE: Remember to copy the /snapdsk/public/.htaccess to the /public_html also. Don’t forget this...the file may be hidden if you are using cpanel Filemanager.

2) Open /public_html/index.php with a text editor.

*NOTE: Don’t modify /snapdsk/public/index.php, only modify /public_html/index.php, remember this!!!

3)  Find the following line

		require __DIR__.’/../bootstrap/autoload.php’;
		$app = require_once __DIR__.’/../bootstrap/app.php’;

	And update them to the correct paths as following

		require __DIR__.’/../snapdsk/bootstrap/autoload.php’;
		$app = require_once __DIR__.’/../snapdsk/bootstrap/app.php’;

4)  Almost done, it is time to set permissions for the project/storage directory, it should be writable.

		chmod -R o+w snapdsk/storage

5)  config application variables in the snapdsk/.env

///////////////////LARAVEL SETTING///////////////////////////
1) snapdsk uses laravel database queue driver...we need to setup the tables in the database.
		php artisan queue:table
		php artisan migrate


/////////////////SETUP FTP ACCOUNTS/////////////
you can use the root cpanel login username and password, or you must setup custom ftp accounts in cpanel.




///////////////Checking PHP configuration/////////////////
snapdsk depends on the following global php libraries:
1) PEAR - typically installed by default.
2) OAuth - typically installed via PEAR, but cPanel may allow you to setup OAuth.  
	Go to the 'Select PHP version' in cPanel...make sure the OAuth is checked.
3) PDF
4) node.js	->  follow instruction here to install node on godaddy shared hosting:
		https://ferugi.com/blog/nodejs-on-godaddy-shared-cpanel/
5) vue.cli (installed via 'npm' commandline)  ->  npm install -g vue-cli
6) webpack (installed via 'vue init' commandline)	->	vue init webpack-simple snapdsk-vue
		follow tutorial by 'devlop' on youtube 'Vue 2.0 and Laravel 5.3'


-- create a phpinfo() page and navigate to it in a web browser. This will display all the global modules installed in PHP.
-- php can be updated via SSH Putty...or in cPanel. 
-- For cPanel, 
		Go to the 'Software' heading on the home page.  
		Click on 'Select PHP version'. 
		Make sure OAuth & PDF are checked.  
		Save your Changes.
		Rerun phpinfo() to confirmt he modules were installed.










////////////////INTUIT WEBHOOKS SETUP/////////////////
https://developer.intuit.com/docs/0100_quickbooks_online/0300_references/0000_programming_guide/0020_webhooks


