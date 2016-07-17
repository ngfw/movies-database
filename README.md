Movie Database 2
================

2x Fast, Optimized and Beautiful
--------------------------------

[Get Started](#installation)

* * * * *

### Requirements and Features

* * * * *

#### Requirements {#requirements_and_features_requirements}

-   Apache or Nginx Webserver
-   PHP 5.3 +
-   PHP PDO
-   PHP CURL
-   PHP MBSTRING
-   PHP Zlib
-   Youtube API key
-   TMDB API Key
-   Facebook APP ID and Secret

#### Features {#requirements_and_features_features}

-   Bootstrap 3
-   Mobile first, Responsive Design
-   TMDB API Integration
-   Youtube API Integration
-   Facebook API Integration
-   Administration panel
-   Cache system
-   Translation System (100% web manageable)
-   Advertisement management 
-   Coded 100% object-oriented
-   100% automatic movie poster 
-   Easy Installation
-   User profile pages
-   User movie collections

 

### Installation

* * * * *

Upload all the files from **Webfiles **directory to your server,\
 after uploading files is done,
visit **http://example.com/install.php **to proceed with installation.

 

**Step 1:**

Installer will first check if your server meets the requirement.

If you see Errors in red color, please correct them before moving to
next step, otherwise if all check values are green,\
 then you can proceed to next step.

**![](assets/images/image_2.png)**

 

**Step 2:**

 

Assuming you already have database setup, enter your Database
credentials

 

**Host**: Database IP address, if you do not know what is this, just
enter: locahost

**Database Name**: Enter database Name

**Username**: Enter database username

**Password:** Enter database password

 

 

**Step 3:**

 

if you get two success messages saying Database configuration file is
created and Database queries are executed then you can continue to Step
4.


 

If you get Notice saying, Database configuration file could not be
created, you will need to create it manually.

FTP to your server and navigate to Application/Config/ directory.

create new file with filename 'database.php' and paste configuration
provided in grey box on installation page.

 

**Step 4:**

 

All settings values are required, Once you fill out all fileds you can
continue to Step 5, use links provided next to field to obrain all
information needed.


 

**Step 5:**

Congratulations, Application is set up. Please remove 'install.php' form
your server.\
 
### Administration

* * * * *

**Important!**Everytime you make changes from administration panel, you
will have to clear the cache, including making changes to Translation,
Settings or Pages.\
 \
 **Cache Clear** button is located on Administration **Homepage.**

\
 **Home**

- Shows Users activity and website basic statistics.

**Pages**

- Static pages management tool, four pages are provided with basic
content, you may need to update content to match your website.

**Ads**

- You can replace the values of already build ads to display your ads or
you can create new ad.\
 Please note that you will have to modify .phtml files to activate your
new ad(s).\
 for example if you create new ad with size "668x60" you will have to
use this code in .phtml file:

\

``` {.prettyprint .lang-php .linenums}
<?php

   $localAd = array();

   foreach ($this->ads as $ad):

        if ($ad['adSize'] == "668x60"):

            $localAd[] = $ad['adValue'];

        endif;

    endforeach;

    if (!empty($localAd)):

        shuffle($localAd);

        echo $localAd[0];

    endif;

?>
```

**Users**

- Shows users authenticated via Facbook

**Administrators**

- You can add multiple administrators, there must be at least one
administrator

**Translation**

- You can add new languages and translate right from the web page.\
 if you need to delete language, please use delete button next to
language, DO NOT try to delete translation phrases one by one as it will
delete for every language.

**Settings**

- Settings should be setup during installation, this page will allow you
to add/modify/delete settings.

 

### Compression

* * * * *

If for some reason you need to disable output compression open up
index.php and on line 12, change:

``` {.prettyprint .lang-php .linenums}
define('COMPRESS_OUTPUT', true);
```

with

``` {.prettyprint .lang-php .linenums}
define('COMPRESS_OUTPUT', false);
```

If application is running in **DEVELOPMENT\_ENVIRONMENT **compression
will be automatically disabled.

 

### Configuration

* * * * *

Only configuration that is out of the admin panel is Caching and Default
Language setting, to edit these configurations open up
Application/Config/application.ini file, on line 5, you can Enable or
Disable Caching system by changing

``` {.prettyprint .lang-plain .linenums}
EnableCaching = true
```

to:

``` {.prettyprint .lang-plain .linenums}
EnableCaching = false
```

Cache time is defined in seconds on line 8, default 86400 (1 day)

``` {.prettyprint .lang-plain .linenums}
CacheTime = 86400;
```

if you would like to change default language, you can do so on line 10:

``` {.prettyprint .lang-plain .linenums}
DefaultLanguage = "en"
```

 
