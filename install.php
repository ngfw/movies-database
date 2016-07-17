<?php
error_reporting(-1);
// Same as error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
/*
 * Set Development environment to false if you want to disable PHP error and warnings
 */
define('DEVELOPMENT_ENVIRONMENT', true);

/*
 * Path for directory where Application and Library is Located
 */
define('ROOT', realpath(dirname(__FILE__)));

/*
 * Directory where index.php is located
 */
define('PUBLIC_PATH', realpath(dirname(__FILE__)));
/*
 * Alias for DIRECOTRY_SEPERATOR
 */
define('DS', DIRECTORY_SEPARATOR);

/*
 * Name for Application Directory
 */
define('APPDIR', 'Application');
define('VENDOR', realpath(dirname(__FILE__)."/vendor/"));
require VENDOR . DIRECTORY_SEPARATOR . 'autoload.php';
?>
<!DOCTYPE html>
<html lang="en">
  	<head>
	    <meta charset="utf-8">
	    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	    <meta name="description" content="Movie Database 2 Installer">
	    <meta name="author" content="Nick Gejadze">
	    <link rel="shortcut icon" href="favicon.ico">
	    <title>Installer</title>
	    <link href="Assets/css/bootstrap.min.css" rel="stylesheet">
	    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	    <script type="text/javascript" src="Assets/js/bootstrap.min.js"></script>
	    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.1.15/jquery.form-validator.min.js"></script>
		
	    <style>
	    xmp{
	    	background-color: #ccc;
	    	padding: 15px;
	    	border: 1px solid #999;
	    }
	    </style>
	</head>
  	<body>
    <div id="wrap">
		<div class="container">
	        <div class="page-header">
	          	<h1>Movie Database 2 Installer</h1>
	        </div>
	        	<?php
	        	if(!isset($_GET['s'])):
	        		// avoid PHP Notice..
	        		$_GET['s'] = null;
	        	endif;
	        	switch($_GET['s']){
	        		default:
	        			$permitNextStep = true;
	        			?>
	        			<div class="progress">
							<div class="progress-bar" role="progressbar" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100" style="width: 1%;">
								<span class="sr-only">0% Complete</span>
							</div>
						</div>
	        			<p class="lead"> System Check</p>
	        			<hr />

	        			<div class="col-md-12 clearfix">
	        				<p class="pull-left text-left">Checking PHP Version:</p>
	        				<p class="pull-right text-right">
	        					<?php
	        						if (version_compare(phpversion(), '5.3.10', '<')) {
									    echo "<span class='text-danger'>".phpversion()."; Please update to new version <i class='glyphicon glyphicon-exclamation-sign'></i></span>";
									    $permitNextStep = false;
									}else{
										echo "<span class='text-success'>".phpversion()." <i class='glyphicon glyphicon-check'></i></span>";
									}
	        					?>
	        				</p>
	        			</div>

	        			<div class="col-md-12 clearfix">
	        				<p class="pull-left text-left">Checking CACHE Directory:</p>
	        				<p class="pull-right text-right">
	        					<?php
	        						if (is_writable(dirname(__FILE__)."/TMP/CACHE/testfile.txt")) {
									    echo "<span class='text-danger'>".dirname(__FILE__)."/TMP/CACHE/ is not writable <i class='glyphicon glyphicon-exclamation-sign'></i></span>";
									    $permitNextStep = false;
									}else{
										echo "<span class='text-success'>Cache dir is writable <i class='glyphicon glyphicon-check'></i></span>";
									}
	        					?>
	        				</p>
	        			</div>
	        			
	        			<div class="col-md-12 clearfix">
	        				<p class="pull-left text-left">Checking PDO Driver:</p>
	        				<p class="pull-right text-right">
	        					<?php
	        						if (!class_exists('PDO')) {
									    echo "<span class='text-danger'>Please install PDO Driver <i class='glyphicon glyphicon-exclamation-sign'></i></span>";
									    $permitNextStep = false;
									}else{
										echo "<span class='text-success'>PDO is available <i class='glyphicon glyphicon-check'></i></span>";
									}
	        					?>
	        				</p>
	        			</div>

	        			<div class="col-md-12 clearfix">
	        				<p class="pull-left text-left">Checking CURL:</p>
	        				<p class="pull-right text-right">
	        					<?php
	        						if (!function_exists('curl_version')) {
									    echo "<span class='text-danger'>Please install CURL extension <i class='glyphicon glyphicon-exclamation-sign'></i></span>";
									    $permitNextStep = false;
									}else{
										echo "<span class='text-success'>CURL is available <i class='glyphicon glyphicon-check'></i></span>";
									}
	        					?>
	        				</p>
	        			</div>

	        			<div class="col-md-12 clearfix">
	        				<p class="pull-left text-left">Checking Zlib:</p>
	        				<p class="pull-right text-right">
	        					<?php
	        						if (!extension_loaded( 'zlib' )) {
									    echo "<span class='text-danger'>Zlib not available, Please disable compression in index.php <i class='glyphicon glyphicon-exclamation-sign'></i></span>";
									    $permitNextStep = true;
									}else{
										echo "<span class='text-success'>Zlib is available <i class='glyphicon glyphicon-check'></i></span>";
									}
	        					?>
	        				</p>
	        			</div>
	        			

	        			<hr />
	        			<?php
	        			if($permitNextStep):
	        				echo "<a href='?s=step2' class='btn btn-primary'>Step 2 <i class='glyphicon glyphicon-circle-arrow-right'></i></a>";
	        			else:
	        				echo '<div class="alert alert-danger">Please fix failed tests to continue</div>';
						endif;
	        		break;
	        		case "step2":
	        			?>
	        			<div class="progress">
							<div class="progress-bar" role="progressbar" aria-valuenow="24" aria-valuemin="0" aria-valuemax="100" style="width: 25%;">
								<span class="sr-only">25% Complete</span>
							</div>
						</div>
	        			<p class="lead"> Database Credentials</p>
	        			<form class="form-horizontal" role="form" action="?s=step3" method="post">
						  <div class="form-group">
						    <label for="host" class="col-lg-2 control-label">Host:</label>
						    <div class="col-lg-10">
						      <input type="text" class="form-control" id="host" name="host" placeholder="localhost" data-validation="required">
						      <p class="help-block">Usually "localhost", enter IP/Hostname if mysql is on different server</p>
						    </div>
						  </div>
						  <div class="form-group">
						    <label for="dbname" class="col-lg-2 control-label">Database Name:</label>
						    <div class="col-lg-10">
						      <input type="text" class="form-control" id="dbname" name="dbname" placeholder="ex. MovieDatabase" data-validation="required">
						      <p class="help-block">Name of you database</p>
						    </div>
						  </div>
						  <div class="form-group">
						    <label for="dbusername" class="col-lg-2 control-label">Database Username:</label>
						    <div class="col-lg-10">
						      <input type="text" class="form-control" id="dbusername" name="dbusername" placeholder="Username" data-validation="required">
						    </div>
						  </div>
						  <div class="form-group">
						    <label for="dbpassword" class="col-lg-2 control-label">Database Password:</label>
						    <div class="col-lg-10">
						      <input type="text" class="form-control" id="dbpassword" name="dbpassword" placeholder="Password">
						    </div>
						  </div>

						  <hr />
						  <div class="form-group">
						    <div class="col-lg-offset-2 col-lg-10">
						      <button type="submit" class="btn btn-primary">Step 3 <i class='glyphicon glyphicon-circle-arrow-right'></i></button>
						    </div>
						  </div>
						</form>
						<script> $.validate(); </script>
	        			<?php
	        		break;
	        		case "step3":
	        			?>
	        			<div class="progress">
							<div class="progress-bar" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 50%;">
								<span class="sr-only">50% Complete</span>
							</div>
						</div>
	        			<?php
	        			$options['dbtype'] = "mysql";
	        			$options['host'] = $_POST['host'];
	        			$options['dbname'] = $_POST['dbname'];
	        			$options['username'] = $_POST['dbusername'];
	        			$options['password'] = $_POST['dbpassword'];
	        			$db = new ngfw\Database($options);
	        			if($db){
	        				//write db config to file
	        				$configContent = "";
							$configContent .= "<?php \n";
							$configContent .= "return array( \n";
							$configContent .= "\t'dbtype' => 'mysql', \n";
							$configContent .= "\t'port' => 3306, \n";
							$configContent .= "\t'host' => '".$_POST['host']."', \n";
							$configContent .= "\t'dbname' => '".$_POST['dbname']."', \n";
							$configContent .= "\t'username' => '".$_POST['dbusername']."', \n";
							$configContent .= "\t'password' => '".$_POST['dbpassword']."', \n";
							$configContent .= "\t'charset' => 'utf8' \n";
							$configContent .= "); \n";
							$configFile = APPDIR . DS . "Config" . DS . "database.php";
							$write = @file_put_contents($configFile, $configContent);
							if($write === false){
								echo "<p class='lead'><i class='glyphicon glyphicon-exclamation-sign'></i> Could not write configuration file<br />
									Please create 'Application/Config/database.php' file and add the following configuration manually</p>";
								echo "<br /><xmp>".$configContent."</xmp>";
								echo "Please Create database configuration file before you continue.";
							}else{
								echo '<div class="alert alert-success">
								        Database configuration file created successfully.
								      </div>';
							}
							$query="
CREATE TABLE `Admin` (
  `AdminID` int(15) NOT NULL AUTO_INCREMENT,
  `Username` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `Password` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Email` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`AdminID`),
  UNIQUE KEY `UsernameX` (`Username`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

BEGIN;
INSERT INTO `Admin` VALUES ('1', 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin@x.com');
COMMIT;

CREATE TABLE `Ads` (
  `adID` int(255) NOT NULL AUTO_INCREMENT,
  `adSize` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `adValue` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`adID`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;


BEGIN;
INSERT INTO `Ads` VALUES ('1', '336x280', '<img src=\"http://placehold.it/336x280\">'), ('2', '728x90', '<img src=\"http://placehold.it/728x90\">'), ('3', '250x250', '<img src=\"http://placehold.it/250x250\">');
COMMIT;

CREATE TABLE `Favorite` (
  `FavoriteID` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `MovieID` int(255) NOT NULL,
  `UserID` int(255) NOT NULL,
  `Added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`FavoriteID`),
  UNIQUE KEY `Movie_User` (`MovieID`,`UserID`),
  KEY `MovieX` (`MovieID`),
  KEY `UserX` (`UserID`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `MovieViews` (
  `MovieViewID` int(255) NOT NULL AUTO_INCREMENT,
  `MovieID` int(255) NOT NULL,
  `Views` int(255) NOT NULL,
  PRIMARY KEY (`MovieViewID`),
  UNIQUE KEY `PageIDX` (`MovieID`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `Page` (
  `PageID` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `Title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Content` text COLLATE utf8_unicode_ci NOT NULL,
  `Created` datetime NOT NULL,
  `Active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`PageID`),
  UNIQUE KEY `slug` (`Slug`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

BEGIN;
INSERT INTO `Page` VALUES ('1', 'About us', 'about-us', '<p><MY_WEBSITE_ADDRESS> is a user-friendly movie database, used worldwide. As an online movie database, it provides viewers with information on past and current movies, including the movie credits relating to both cast and production crew. <MY_WEBSITE_ADDRESS> was launched in 2013 and holds more than 140 000 database entries to date. A well-received feature of the website is the ability to provide users high quality posters, screenshots and view movie trailers on the movie entry by simply linking to an offsite YouTube video.</p>\r\n<br />\r\n<p>Placed prominently on the website\'s homepage, current movies are open at the box office along with the movies to be released in the coming weeks. Clicking on an entry allows the user to view the movie’s posters, screenshots, trailer or teaser clip, and in most cases, everything listed.<br />\r\nIn addition to providing information on past and current movies, the website also hosts up to date information on actors and crew members with bios written by users.</p>', '2013-10-24 09:08:54', '1'), ('2', 'Privacy Policy', 'privacy-policy', '<div style=\"font-family:arial\"><strong>What information do we collect?</strong> <br /><br />We collect information from you when you Log in with Facebook.  <br /><br />When ordering or registering on our site, as appropriate, you may be asked to enter your: name or e-mail address. You may, however, visit our site anonymously.<br /><br />Google, as a third party vendor, uses cookies to serve ads on your site.\r\nGoogle\'s use of the DART cookie enables it to serve ads to your users based on their visit to your sites and other sites on the Internet.\r\nUsers may opt out of the use of the DART cookie by visiting the Google ad and content network privacy policy..<br /><br /><strong>What do we use your information for?</strong> <br /><br />Any of the information we collect from you may be used in one of the following ways: <br /><br />; To personalize your experience<br />(your information helps us to better respond to your individual needs)<br /><br /><strong>How do we protect your information?</strong> <br /><br />We implement a variety of security measures to maintain the safety of your personal information when you enter, submit, or access your personal information. <br /><br /><strong>Do we use cookies?</strong> <br /><br />Yes (Cookies are small files that a site or its service provider transfers to your computers hard drive through your Web browser (if you allow) that enables the sites or service providers systems to recognize your browser and capture and remember certain information.<br /><br /><strong>Do we disclose any information to outside parties?</strong> <br /><br />We do not sell, trade, or otherwise transfer to outside parties your personally identifiable information. This does not include trusted third parties who assist us in operating our website, conducting our business, or servicing you, so long as those parties agree to keep this information confidential. We may also release your information when we believe release is appropriate to comply with the law, enforce our site policies, or protect ours or others rights, property, or safety. However, non-personally identifiable visitor information may be provided to other parties for marketing, advertising, or other uses.<br /><br /><strong>Third party links</strong> <br /><br /> Occasionally, at our discretion, we may include or offer third party products or services on our website. These third party sites have separate and independent privacy policies. We therefore have no responsibility or liability for the content and activities of these linked sites. Nonetheless, we seek to protect the integrity of our site and welcome any feedback about these sites.<br /><br /><strong>Childrens Online Privacy Protection Act Compliance</strong> <br /><br />We are in compliance with the requirements of COPPA (Childrens Online Privacy Protection Act), we do not collect any information from anyone under 13 years of age. Our website, products and services are all directed to people who are at least 13 years old or older.<br /><br /><strong>Online Privacy Policy Only</strong> <br /><br />This online privacy policy applies only to information collected through our website and not to information collected offline.<br /><br /><strong>Terms and Conditions</strong> <br /><br />Please also visit our Terms and Conditions section establishing the use, disclaimers, and limitations of liability governing the use of our website at <a href=\"<MY_WEBSITE_ADDRESS>en/page/terms-and-conditions\"><MY_WEBSITE_ADDRESS>en/page/terms-and-conditions</a><br /><br /><strong>Your Consent</strong> <br /><br />By using our site, you consent to our web site privacy policy.<br /><br /><strong>Changes to our Privacy Policy</strong> <br /><br />If we decide to change our privacy policy, we will update the Privacy Policy modification date below. <br /><br />This policy was last modified on 22 October 2013<br /><br /><strong>Contacting Us</strong> <br /><br />If there are any questions regarding this privacy policy you may contact us using the information below. <br /><br /><MY_WEBSITE_ADDRESS><br /><br /><span></span></div>', '2013-10-24 08:09:40', '1'), ('3', 'Terms and Conditions', 'terms-and-conditions', '<h1>Terms and Conditions (\"Agreement\")</h1>\r\n<p>This Agreement was last modified on October 22, 2013.</p>\r\n\r\n<p>Please read these Terms and Conditions (\"Agreement\", \"Terms and Conditions\") carefully before using <MY_WEBSITE_ADDRESS> (\"the Site\") operated by <MY_WEBSITE_ADDRESS> (\"us\", \"we\", or \"our\"). This Agreement sets forth the legally binding terms and conditions for your use of the Site at <MY_WEBSITE_ADDRESS>.</p>\r\n<p>By accessing or using the Site in any manner, including, but not limited to, visiting or browsing the Site or contributing content or other materials to the Site, you agree to be bound by these Terms and Conditions. Capitalized terms are defined in this Agreement.</p>\r\n\r\n<p><strong>Termination</strong><br />We may terminate your access to the Site, without cause or notice, which may result in the forfeiture and destruction of all information associated with you. All provisions of this Agreement that by their nature should survive termination shall survive termination, including, without limitation, ownership provisions, warranty disclaimers, indemnity, and limitations of liability.</p>\r\n\r\n<p><strong>Links To Other Sites</strong><br />Our Site may contain links to third-party sites that are not owned or controlled by <MY_WEBSITE_ADDRESS>.</p>\r\n<p><MY_WEBSITE_ADDRESS> has no control over, and assumes no responsibility for, the content, privacy policies, or practices of any third party sites or services. We strongly advise you to read the terms and conditions and privacy policy of any third-party site that you visit.</p>\r\n\r\n<p><strong>Governing Law</strong><br />This Agreement (and any further rules, polices, or guidelines incorporated by reference) shall be governed and construed in accordance with the laws of United States, without giving effect to any principles of conflicts of law.</p>\r\n\r\n<p><strong>Changes To This Agreement</strong><br />We reserve the right, at our sole discretion, to modify or replace these Terms and Conditions by posting the updated terms on the Site. Your continued use of the Site after any such changes constitutes your acceptance of the new Terms and Conditions.</p>\r\n<p>Please review this Agreement periodically for changes. If you do not agree to any of this Agreement or any changes to this Agreement, do not use, access or continue to access the Site or discontinue any use of the Site immediately.</p>\r\n\r\n<p><strong>Contact Us</strong><br />If you have any questions about this Agreement, please contact us.</p>', '2013-10-26 23:34:17', '1'), ('4', 'Contact us', 'contact-us', '<iframe width=\"100%\" height=\"250\" frameborder=\"0\" scrolling=\"no\" marginheight=\"0\" marginwidth=\"0\" src=\"http://maps.google.com/maps?f=q&source=s_q&hl=en&geocode=&q=central%2Bpartk%2Bny&ie=UTF8&z=12&t=m&iwloc=near&output=embed\"></iframe>\r\n\r\n<div class=\"row\">\r\n	<div class=\"col-md-2\">\r\n		<address>\r\n		  <strong>Company, Inc.</strong><br>\r\n		  123 Address ave, Suite 321<br>\r\n		  Manhattan, NY 123456<br>\r\n		  <abbr title=\"Phone\">P:</abbr> (123) 456-7890\r\n		</address>\r\n	</div>\r\n	<div class=\"col-md-6\">\r\n		<div class=\"fb-follow\" data-href=\"http://www.facebook.com/zuck\" data-width=\"The pixel width of the plugin\" data-height=\"The pixel height of the plugin\" data-colorscheme=\"light\" data-layout=\"standard\" data-show-faces=\"true\"></div>\r\n	</div>\r\n	<div class=\"col-md-4\">\r\n		<a href=\"https://twitter.com/twitter\" class=\"twitter-follow-button\" data-show-count=\"false\" data-size=\"large\" data-dnt=\"true\">Follow @twitter</a>\r\n		<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+\'://platform.twitter.com/widgets.js\';fjs.parentNode.insertBefore(js,fjs);}}(document, \'script\', \'twitter-wjs\');</script>\r\n	</div>\r\n</div>', '2013-10-24 08:28:03', '1');
COMMIT;

CREATE TABLE `Settings` (
  `SettingID` int(255) NOT NULL AUTO_INCREMENT,
  `SettingName` varchar(255) NOT NULL,
  `SettingValue` varchar(255) NOT NULL,
  PRIMARY KEY (`SettingID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

CREATE TABLE `Translation` (
  `TranslationID` int(255) NOT NULL AUTO_INCREMENT,
  `Source` varchar(255) NOT NULL,
  `Translation` varchar(255) NOT NULL,
  `Language` varchar(10) NOT NULL,
  PRIMARY KEY (`TranslationID`),
  KEY `Language` (`Language`)
) ENGINE=MyISAM AUTO_INCREMENT=313 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

BEGIN;
INSERT INTO `Translation` VALUES ('1', 'sitename', 'Movies Database 2', 'en'), ('2', 'defaultMetaDescription', 'Movies Database, Information about movies', 'en'), ('3', 'defaultMetaKeywords', 'Movies,Database,Boxoffice,Search,Coming out', 'en'), ('4', 'genres', 'Genres', 'en'), ('5', 'nowPlaying', 'Now Playing', 'en'), ('6', 'upcomingMovies', 'Upcoming Movies', 'en'), ('7', 'genrePageTitle', '%s - Page %s', 'en'), ('8', 'genrePageMetaDescription', 'Movies for genre %s Page %s', 'en'), ('9', 'genrePageHeader', 'Movies in \'%s\' genre', 'en'), ('10', 'trailer', 'Trailer', 'en'), ('11', 'posters', 'Posters', 'en'), ('12', 'images', 'Images', 'en'), ('13', 'similarMovies', 'Similar Movies', 'en'), ('14', 'directors', 'Directors:', 'en'), ('15', 'director', 'Director:', 'en'), ('16', 'writers', 'Writers:', 'en'), ('17', 'writer', 'Writer:', 'en'), ('18', 'cast', 'Cast', 'en'), ('19', 'crew', 'Crew', 'en'), ('20', 'download', 'Download', 'en'), ('22', 'previous', 'Previous', 'en'), ('23', 'next', 'Next', 'en'), ('24', 'popularMovies', 'Popular movies', 'en'), ('25', 'popularMoviesDescription', 'Popular movies', 'en'), ('26', 'popularMoviesKeywords', 'Popular Movies, Most Viewed Movies, Top Rated Movies', 'en'), ('27', 'popularMoviesHeader', 'Popular movies', 'en'), ('28', 'popularPeople', 'Popular People', 'en'), ('29', 'popularPeopleDescription', 'Popular people', 'en'), ('30', 'popularPeopleKeywords', 'Popular people, Most Viewed People', 'en'), ('31', 'popularPeopleHeader', 'Popular people', 'en'), ('32', 'emptyQuery', '<strong>Sorry!</strong> search query was empty', 'en'), ('33', 'noResults', '<strong>Sorry!</strong> no results found. ', 'en'), ('34', 'searchResultFor', 'Search result for', 'en'), ('35', 'alsoKnownAs', 'Also Known as', 'en'), ('36', 'birthday', 'Birthday', 'en'), ('37', 'placeOfBirth', 'Place of birth', 'en'), ('38', 'biography', 'Biography', 'en'), ('39', 'credits', 'Credits', 'en'), ('40', 'minute', 'минут', 'en'), ('41', 'status', 'Status', 'en'), ('42', 'budget', 'Budget', 'en'), ('43', 'revenue', 'Revenue', 'en'), ('44', 'runtime', 'Run time', 'en'), ('45', 'production_companies', 'Production Companies', 'en'), ('46', 'production_company', 'Production Company', 'en'), ('47', 'production_countries', 'Production Countries', 'en'), ('48', 'production_country', 'Production Country', 'en'), ('49', 'login_with_facebook', 'Login with Facebook', 'en'), ('50', 'please_sign_in', 'Please sign in', 'en'), ('51', 'username', 'Username', 'en'), ('52', 'password', 'Password', 'en'), ('53', 'username_or_password_is_incorrect', 'Username or Password is incorrect', 'en'), ('54', 'you_have_successfully_logged_out', 'You have successfully logged out', 'en'), ('55', 'sign_in', 'Sign in', 'en'), ('56', 'delete?', 'Delete?', 'en'), ('57', 'are_you_sure_you_want_to_delete?', 'Are you sure you want to delete?', 'en'), ('58', 'delete', 'Delete', 'en'), ('59', 'cancel', 'Cancel', 'en'), ('60', 'home', 'Home', 'en'), ('61', 'ads', 'Ads', 'en'), ('62', 'users', 'Users', 'en'), ('63', 'administrators', 'Administrators', 'en'), ('64', 'translation', 'Translation', 'en'), ('65', 'settings', 'Settings', 'en'), ('66', 'log_out', 'Log out', 'en'), ('67', 'category', 'Category', 'en'), ('68', 'website', 'Website', 'en'), ('69', 'categories', 'Categories', 'en'), ('70', 'release_date', 'Release date', 'en'), ('71', 'edit', 'Edit', 'en'), ('72', 'add', 'Add', 'en'), ('73', 'edit_translation', 'Edit \'%s\' Translation', 'en'), ('74', 'add_translation', 'Add Translation', 'en'), ('75', 'add_new_translation', 'Add New Translation', 'en'), ('76', 'add_administrator', 'Add Admin', 'en'), ('77', 'email', 'Email', 'en'), ('78', 'action', 'Action', 'en'), ('79', 'edit_the_administrator', 'Edit the administrator', 'en'), ('80', 'source', 'Source', 'en'), ('81', 'language', 'Language', 'en'), ('82', 'update', 'Update', 'en'), ('83', 'loading', 'Loading...', 'en'), ('84', 'translated_out_of', 'Translated \'%s\' out of \'%s\'', 'en'), ('85', 'edit_the_setting', 'Edit the Setting', 'en'), ('86', 'setting_name', 'Setting Name', 'en'), ('87', 'setting_value', 'Setting Value', 'en'), ('88', 'add_setting', 'Add Setting', 'en'), ('89', 'sort_by_id', 'Sort by ID', 'en'), ('90', 'sort_by_facebook_username', 'Sort By Username', 'en'), ('91', 'user_id', 'User ID', 'en'), ('92', 'facebook_id', 'Facebook ID', 'en'), ('93', 'name', 'Name', 'en'), ('94', 'first_name', 'Firstname', 'en'), ('95', 'last_name', 'Lastname', 'en'), ('96', 'facebook_link', 'Facebook URL', 'en'), ('97', 'facebook_username', 'Facebook Username', 'en'), ('98', 'gender', 'Gender', 'en'), ('99', 'timezone', 'Timezone', 'en'), ('100', 'first', 'First', 'en'), ('101', 'previous', 'Previous', 'en'), ('102', 'next', 'Next', 'en'), ('103', 'last', 'Last', 'en'), ('104', 'sort_by_name', 'Sort By Name', 'en'), ('105', 'edit_the_ads', 'Edit the Ads', 'en'), ('106', 'ad_size', 'AD Size', 'en'), ('107', 'ad_value', 'AD Value', 'en'), ('108', 'add_ad', 'Add AD', 'en'), ('109', 'ad_id', 'AD ID', 'en'), ('110', 'add_new_language', 'Add New Language', 'en'), ('111', 'enter_language_code', 'Enter Language \'%s\' Code', 'en'), ('112', 'as', 'as', 'en'), ('113', 'log_out', 'Log out', 'en'), ('114', 'no', 'No', 'en'), ('115', 'yes', 'Yes', 'en'), ('116', 'page_content', 'Page Content', 'en'), ('117', 'active', 'Active', 'en'), ('118', 'page_slug', 'Page Slug', 'en'), ('119', 'page_title', 'Page Title', 'en'), ('120', 'add_page', 'Add Page', 'en'), ('121', 'pages', 'Pages', 'en'), ('122', 'no_one_will_see_your_collections', 'No one will see your collections', 'en'), ('123', 'private_profile', 'Private Profile', 'en'), ('124', 'clear_cache', 'Clear Cache', 'en'), ('125', 'stats', 'Stats', 'en'), ('126', 'please_enable_facebook', 'Please enable Facebook from settings to view user activity', 'en'), ('127', 'added_to_watched', ' %s added %s to Watched collection', 'en'), ('128', 'added_to_wanttowatch', ' %s added %s to Want to watch collection', 'en'), ('129', 'ago', 'ago', 'en'), ('130', 'added_to_favorites', ' %s added %s to Favorites', 'en'), ('131', 'website_stats', 'Website stats', 'en'), ('132', 'social_stats', 'Social Stats', 'en'), ('133', 'total_watched', 'Watched', 'en'), ('134', 'total_want_to_watch', 'Wanted to watch', 'en'), ('135', 'total_favorites', 'Favorites', 'en'), ('136', 'movies_viewed_on_website', 'Total Movies Viewed', 'en'), ('137', 'load_more', 'Load More...', 'en'), ('138', 'profile_page_is_private', 'This profile page is private.', 'en'), ('139', 'watched', 'Watched', 'en'), ('140', 'want_to_watch', 'Want to Watch', 'en'), ('141', 'favorites', 'Favorites', 'en'), ('142', 'favorite', 'Favorite', 'en'), ('143', 'profile', 'Profile', 'en'), ('144', 'search', 'Search', 'en'), ('145', 'year', 'year', 'en'), ('146', 'month', 'month', 'en'), ('147', 'week', 'week', 'en'), ('148', 'day', 'day', 'en'), ('149', 'hour', 'hour', 'en'), ('150', 'minute', 'minute', 'en'), ('151', 'second', 'second', 'en'), ('152', 'about-us', 'About us', 'en'), ('153', 'privacy-policy', 'Privacy Policy', 'en'), ('154', 'terms-and-conditions', 'Terms and Conditions', 'en'), ('155', 'contact-us', 'Contact us', 'en'), ('156', 'sitename', 'База фильмов 2', 'ru'), ('157', 'defaultMetaDescription', 'База фильмов, Информация о фильмах', 'ru'), ('158', 'defaultMetaKeywords', 'Фильмы, базы данных, Boxoffice, поиск', 'ru'), ('159', 'genres', 'Жанры', 'ru'), ('160', 'nowPlaying', 'Сейчас идет в кинотеатрах', 'ru'), ('161', 'upcomingMovies', 'Предстоящие фильмы', 'ru'), ('162', 'genrePageTitle', '%s - страница %s', 'ru'), ('163', 'genrePageMetaDescription', 'Фильмы по жанром %s страница %s', 'ru'), ('164', 'genrePageHeader', 'Фильмы в жанре \'%s\' ', 'ru'), ('165', 'trailer', 'трейлер', 'ru'), ('166', 'posters', 'плакаты', 'ru'), ('167', 'images', 'изображения', 'ru'), ('168', 'similarMovies', 'похожие фильмы', 'ru'), ('169', 'directors', 'Директори:', 'ru'), ('170', 'director', 'Директор:', 'ru'), ('171', 'writers', 'Писатели:', 'ru'), ('172', 'writer', 'Писатель:', 'ru'), ('173', 'cast', 'в ролях', 'ru'), ('174', 'crew', 'экипаж', 'ru'), ('175', 'download', 'скачать', 'ru'), ('177', 'previous', 'предыдущий', 'ru'), ('178', 'next', 'следующий', 'ru'), ('179', 'popularMovies', 'Популярные фильме', 'ru'), ('180', 'popularMoviesDescription', 'Популярные фильме', 'ru'), ('181', 'popularMoviesKeywords', 'Популярные фильмы, Самые популярные фильмы, Лучшие фильмы', 'ru'), ('182', 'popularMoviesHeader', 'Популярные фильме', 'ru'), ('183', 'popularPeople', 'Популярные люди', 'ru'), ('184', 'popularPeopleDescription', 'Популярные люди', 'ru'), ('185', 'popularPeopleKeywords', 'Популярные люди, Самые популярные люди', 'ru'), ('186', 'popularPeopleHeader', 'Популярные люди', 'ru'), ('187', 'emptyQuery', '<strong>Извините!</strong> Поисковый запрос был пуст', 'ru'), ('188', 'noResults', '<strong>Извините!</strong> результатов не найдено.', 'ru'), ('189', 'searchResultFor', 'Результат поиска по запросу', 'ru'), ('190', 'alsoKnownAs', 'Также известный, как', 'ru'), ('191', 'birthday', 'День рождения', 'ru'), ('192', 'placeOfBirth', 'Место рождения', 'ru'), ('193', 'biography', 'Биография', 'ru'), ('194', 'credits', 'Кредиты', 'ru'), ('195', 'minute', 'мин', 'ru'), ('196', 'status', 'Статус', 'ru'), ('197', 'budget', 'Бюджет', 'ru'), ('198', 'revenue', 'Доходы', 'ru'), ('199', 'runtime', 'Время', 'ru'), ('200', 'production_companies', 'Производство компании', 'ru'), ('201', 'production_company', 'Производственная компания', 'ru'), ('202', 'production_countries', 'Страны производства', 'ru'), ('203', 'production_country', 'Страна производства', 'ru'), ('204', 'login_with_facebook', 'Войти с facebook', 'ru'), ('205', 'please_sign_in', 'войдите в систему', 'ru'), ('206', 'username', 'имя пользователя', 'ru'), ('207', 'password', 'пароль', 'ru'), ('208', 'username_or_password_is_incorrect', 'неверный имя пользователя или пароль', 'ru'), ('209', 'you_have_successfully_logged_out', 'Вы успешно вышли из системы', 'ru'), ('210', 'sign_in', 'вход', 'ru'), ('211', 'delete?', 'удалить?', 'ru'), ('212', 'are_you_sure_you_want_to_delete?', 'Вы уверены, что хотите удалить?', 'ru'), ('213', 'delete', 'удалить', 'ru'), ('214', 'cancel', 'отменить', 'ru'), ('215', 'home', 'дома', 'ru'), ('216', 'ads', 'реклама', 'ru'), ('217', 'users', 'пользователей', 'ru'), ('218', 'administrators', 'администраторы', 'ru'), ('219', 'translation', 'перевод', 'ru'), ('220', 'settings', 'настройки', 'ru'), ('221', 'log_out', 'выйти', 'ru'), ('222', 'category', 'категория', 'ru'), ('223', 'website', 'сайт', 'ru'), ('224', 'categories', 'категории', 'ru'), ('225', 'release_date', 'дата выхода', 'ru'), ('226', 'edit', 'редактировать', 'ru'), ('227', 'add', 'добавить', 'ru'), ('228', 'edit_translation', 'редактирование перевода', 'ru'), ('229', 'add_translation', 'добавить перевод', 'ru'), ('230', 'add_new_translation', 'добавить новый перевод', 'ru'), ('231', 'add_administrator', 'добавить администратор', 'ru'), ('232', 'email', 'электронная почта', 'ru'), ('233', 'action', 'действие', 'ru'), ('234', 'edit_the_administrator', 'редактировать администратор', 'ru'), ('235', 'source', 'источник', 'ru'), ('236', 'language', 'язык', 'ru'), ('237', 'update', 'обновлять', 'ru'), ('238', 'loading', 'загрузка', 'ru'), ('239', 'translated_out_of', 'переведены из', 'ru'), ('240', 'edit_the_setting', 'изменения настроек', 'ru'), ('241', 'setting_name', 'имя', 'ru'), ('242', 'setting_value', 'значение', 'ru'), ('243', 'add_setting', 'добавить параметр', 'ru'), ('244', 'sort_by_id', 'сортировать по ID', 'ru'), ('245', 'sort_by_facebook_username', 'сортировать по имени пользователя', 'ru'), ('246', 'user_id', 'ID пользователя', 'ru'), ('247', 'facebook_id', 'Facebook ID', 'ru'), ('248', 'name', 'название', 'ru'), ('249', 'first_name', 'имя', 'ru'), ('250', 'last_name', 'фамилия', 'ru'), ('251', 'facebook_link', 'ссылка', 'ru'), ('252', 'facebook_username', 'имя пользователя', 'ru'), ('253', 'gender', 'пол', 'ru'), ('254', 'timezone', 'часовой пояс', 'ru'), ('255', 'first', 'первый', 'ru'), ('256', 'last', 'последний', 'ru'), ('257', 'sort_by_name', 'сортировать по названию', 'ru'), ('258', 'edit_the_ads', 'редактировать объявление', 'ru'), ('259', 'ad_size', 'размер рекламы', 'ru'), ('260', 'ad_value', 'значение рекламы', 'ru'), ('261', 'add_ad', 'добавить рекламный', 'ru'), ('262', 'ad_id', 'ID рекламы', 'ru'), ('263', 'add_new_language', 'добавить новый язык', 'ru'), ('264', 'enter_language_code', 'введите код языка', 'ru'), ('265', 'as', 'как', 'ru'), ('266', 'no', 'не', 'ru'), ('267', 'yes', 'да', 'ru'), ('268', 'page_content', 'содержимое страницы', 'ru'), ('269', 'active', 'активный', 'ru'), ('270', 'page_slug', 'пуля страници', 'ru'), ('271', 'page_title', 'название страницы', 'ru'), ('272', 'add_page', 'добавить страницу', 'ru'), ('273', 'pages', 'страницы', 'ru'), ('274', 'no_one_will_see_your_collections', 'никто не увидит ваши коллекции', 'ru'), ('275', 'private_profile', 'личный профиль', 'ru'), ('276', 'clear_cache', 'Очистить кэш', 'ru'), ('277', 'stats', 'статистика', 'ru'), ('278', 'please_enable_facebook', 'Пожалуйста, включите facebook', 'ru'), ('279', 'added_to_watched', '%s добавил %s, в коллекцию смотрел', 'ru'), ('280', 'added_to_wanttowatch', '%s добавил %s, в коллекцию Хочется смотреть', 'ru'), ('281', 'ago', 'назад', 'ru'), ('282', 'added_to_favorites', ' %s добавил %s, в коллекцию Избранное', 'ru'), ('283', 'website_stats', 'статистика сайта', 'ru'), ('284', 'social_stats', 'социальная статистика', 'ru'), ('285', 'total_watched', 'Всего смотрели', 'ru'), ('286', 'total_want_to_watch', 'Всего Хочется смотреть', 'ru'), ('287', 'total_favorites', 'Всего Избранное', 'ru'), ('288', 'movies_viewed_on_website', 'просмотрели фильмы на веб-сайте', 'ru'), ('289', 'load_more', 'загрузить еще', 'ru'), ('290', 'profile_page_is_private', 'страница профиля личное', 'ru'), ('291', 'watched', 'смотрел', 'ru'), ('292', 'want_to_watch', 'хочу посмотреть', 'ru'), ('293', 'favorites', 'избранные', 'ru'), ('294', 'favorite', 'избранное', 'ru'), ('295', 'profile', 'профиль', 'ru'), ('296', 'search', 'поиск', 'ru'), ('297', 'year', 'год', 'ru'), ('298', 'month', 'месяцев', 'ru'), ('299', 'week', 'недель', 'ru'), ('300', 'day', 'дней', 'ru'), ('301', 'hour', 'часов', 'ru'), ('302', 'second', 'секунд', 'ru'), ('303', 'about-us', 'о нас', 'ru'), ('304', 'privacy-policy', 'Конфиденциальность', 'ru'), ('305', 'terms-and-conditions', 'Условия и правила', 'ru'), ('306', 'contact-us', 'Контакт', 'ru'), ('307', 'surprisePageTitle', 'Surprise me!', 'en'), ('308', 'surprisePageTitle', 'удивить меня', 'ru'), ('309', 'surprisePageDescription', 'Surprise me, shows movie suggestions', 'en'), ('310', 'surprisePageDescription', 'удивить меня', 'ru'), ('311', 'surprisePageKeywords', 'surprise, suggestion, movies', 'en'), ('312', 'surprisePageKeywords', 'удивить меня', 'ru');
COMMIT;

CREATE TABLE `Users` (
  `UserID` int(255) NOT NULL AUTO_INCREMENT,
  `FacebookID` bigint(255) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `FirstName` varchar(255) NOT NULL,
  `LastName` varchar(255) NOT NULL,
  `FacebookLink` varchar(255) NOT NULL,
  `FacebookUsername` varchar(255) NOT NULL,
  `Gender` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Timezone` varchar(11) NOT NULL,
  `Private` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`UserID`),
  KEY `FacebookIDX` (`FacebookID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

CREATE TABLE `WantToWatch` (
  `WantToWatchID` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `MovieID` int(255) NOT NULL,
  `UserID` int(255) NOT NULL,
  `Added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`WantToWatchID`),
  UNIQUE KEY `Movie_User` (`MovieID`,`UserID`) USING BTREE,
  KEY `MovieX` (`MovieID`),
  KEY `UserX` (`UserID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `Watched` (
  `WatchedID` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `MovieID` int(255) NOT NULL,
  `UserID` int(255) NOT NULL,
  `Added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`WatchedID`),
  UNIQUE KEY `Movie_User` (`MovieID`,`UserID`),
  KEY `MovieX` (`MovieID`) USING BTREE,
  KEY `UserX` (`UserID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
							$db->query($query);
							
							echo '<br /><div class="alert alert-success">
								   Database queries executed.
							   </div><br /> ';
							echo "<hr /><a href='?s=step4' class='btn btn-primary'>Step 4 <i class='glyphicon glyphicon-circle-arrow-right'></i></a>";
	        			}
	        		break;
	        		case "step4":
	        			?>
	        			<div class="progress">
							<div class="progress-bar" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%;">
								<span class="sr-only">75% Complete</span>
							</div>
						</div>
	        			
	        			<p class="lead"> Application Settings</p>
	        			<form class="form-horizontal" role="form" action="?s=step5" method="post">
						  <div class="form-group">
						    <label for="Youtube_API_Key" class="col-lg-2 control-label">Youtube API Key:</label>
						    <div class="col-lg-10">
						      <input type="text" class="form-control" id="Youtube_API_Key" name="Youtube_API_Key" placeholder="Enter API key here" data-validation="required">
						      <p class="help-block">You can register youtube API key at <em><i class="glyphicon glyphicon-share"></i> <a href="https://developers.google.com/youtube/registering_an_application" target="_blank">https://developers.google.com/youtube/registering_an_application</a></em></p>
						    </div>
						  </div>

						  <div class="form-group">
						    <label for="Youtube_API_Key" class="col-lg-2 control-label">TMDB API Key:</label>
						    <div class="col-lg-10">
						      <input type="text" class="form-control" id="TMPDB_Api_Key" name="TMPDB_Api_Key" placeholder="Enter API key here" data-validation="required">
						      <p class="help-block">You can register TMDB API key at <em><i class="glyphicon glyphicon-share"></i> <a href="https://www.themoviedb.org/documentation/api" target="_blank">https://www.themoviedb.org/documentation/api</a></em></p>
						    </div>
						  </div>

						  <div class="form-group">
						    <label for="fb:app_id" class="col-lg-2 control-label">Facebook Application ID (App ID/API Key):</label>
						    <div class="col-lg-10">
						      <input type="text" class="form-control" id="fb:app_id" name="fb:app_id" placeholder="1234.." data-validation="required">
						      <p class="help-block">You can create and obtain application ID and Secret at <em><i class="glyphicon glyphicon-share"></i> <a href="https://developers.facebook.com/apps" target="_blank">https://developers.facebook.com/apps</a></em></p>
						    </div>
						  </div>

						  <div class="form-group">
						    <label for="fb:app_id" class="col-lg-2 control-label">Facebook Application Secret (App Secret):</label>
						    <div class="col-lg-10">
						      <input type="text" class="form-control" id="fb:secret" name="fb:secret" placeholder="1234.." data-validation="required">
						      <p class="help-block">You can create and obtain application ID and Secret at <em><i class="glyphicon glyphicon-share"></i> <a href="https://developers.facebook.com/apps" target="_blank">https://developers.facebook.com/apps</a></em></p>
						    </div>
						  </div>

						  <div class="form-group">
						    <label for="fb:admins" class="col-lg-2 control-label">Facebook Admin(s) ID:</label>
						    <div class="col-lg-10">
						      <input type="text" class="form-control" id="fb:admins" name="fb:admins" placeholder="1234.." data-validation="required">
						      <p class="help-block">You can find your ID at <em><i class="glyphicon glyphicon-share"></i> <a href="https://developers.facebook.com/tools/explorer/" target="_blank">https://developers.facebook.com/tools/explorer/</a></em></p>
						    </div>
						  </div>

						  <div class="form-group">
						    <label for="Allow_Facebook_Authentication" class="col-lg-2 control-label">Allow Facebook Authentication?</label>
						    <div class="col-lg-10">
						      <input type="text" class="form-control" id="Allow_Facebook_Authentication" name="Allow_Facebook_Authentication" value="true" placeholder="true or false" data-validation="required">
						      <p class="help-block">Value must be boolean, true or false, in lowercase</p>
						    </div>
						  </div>
						  <input type="hidden" id="Number_Of_Profile_Movies" name="Number_Of_Profile_Movies" value="10">
						  <hr />
						  <div class="form-group">
						    <div class="col-lg-offset-2 col-lg-10">
						      <button type="submit" class="btn btn-primary">Step 5 <i class='glyphicon glyphicon-circle-arrow-right'></i></button>
						    </div>
						  </div>
						 </form>
						 <script> $.validate(); </script>
						<?php
	        		break;
	        		case "step5":
	        			
	        			// This will also check if config and DB work..
	        			$dbconfig = include(ROOT . DS . APPDIR . DS . 'Config' . DS . 'database.php');
        				$db = new ngfw\Database($dbconfig);
	        			$query = new ngfw\Query();
	        			foreach($_POST as $key => $post):
	        				$check = $db->fetchRow("select * from Settings where SettingName = '".$key."'");
	        				if(!$check){
		        				$data = array();
		        				$data['SettingName'] = $key;
		        				$data['SettingValue'] = $post;
		        				$query->insert("Settings", $data);
			        			$result = $db->query($query->getQuery());
			        		}
	        			endforeach;
	        			
	        			?>
	        			<div class="progress">
							<div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
								<span class="sr-only">100% Complete</span>
							</div>
						</div>
						<div class="text-center">
							<h2 class="text-success"> <i class="glyphicon glyphicon-ok"></i> Congratulations!</h2>
							<p class="lead">Applicaiton is installed successfully</p>
							<p><br /><strong> Please delete install.php</strong></p>
							<br />
							<p><?php 
							$pageURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
							if ($_SERVER["SERVER_PORT"] != "80"){
    							$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    						}else{
    							$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
							}
							$pageURL = str_replace("install.php?s=step5", "", $pageURL);
							echo "You can sign-in to administrator panel at <a href='".$pageURL."admin.php'>".$pageURL."admin.php</a>";
							?>
							<div class="panel panel-default" style="width:200px; margin:0 auto;">
							  <div class="panel-heading">Credentials</div>
							  <div class="panel-body">
							    <strong>Username:</strong> admin <br/>
							    <strong>Password:</strong> admin <br />
							  </div>
							</div>
						</p>
						</div>
						<?php
	        		break;
	        	}//switch
	        	?>
	        	
	      </div>
    </div>
	</body>
</html>
