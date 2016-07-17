Installation
-------------------------

With Composer (Recommended)

	Composer create-project -sdev --repository-url="http://ng.framework.im/" ngfw/skeleton-app /path/to/project/

<dl>
  <dt>assuming you have installed Composer globally</dt>
</dl>


Alternately, clone the repository and manually invoke composer using the
shipped composer.phar:

	cd my/project/dir
	git clone git://github.com/ngfw/skeleton-app.git .
	php composer.phar self-update
	php composer.phar installed
