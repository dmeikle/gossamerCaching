
      <?php

date_default_timezone_set('America/Vancouver');

$site_path =  realpath(dirname(__FILE__));// strip the /web from it

 define ('__SITE_PATH', $site_path);
 define ('__CACHE_DIRECTORY', __SITE_PATH . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR);

//include_once('phpunit.configuration.php');
require_once(__SITE_PATH . '/../vendor/composer/ClassLoader.php');
 $loader = new Composer\Autoload\ClassLoader();

      // register classes with namespaces
      $loader->add('Gossamer\\Caching', __SITE_PATH .'/../src');

      $loader->add('Monolog', __SITE_PATH.'/../vendor/monolog/monolog/src');

      // activate the autoloader
      $loader->register();

      // to enable searching the include path (eg. for PEAR packages)
      $loader->setUseIncludePath(true);
