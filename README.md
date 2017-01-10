Description
Helper class to detect site root path, base directory and do some convertions. Useful for such cases when the site code is run from subfolders of the document root.

Important!
The class must be initialized from php script located in the site root. Use your index.php or any kind of bootstrap script doing initialization. The class uses getcwd() to detect the site root directory.


Installation.
1. Either clone via git or use composer. For composer add this to your site's composer.json:
--
"require":
  "efoft/active-records" : "dev-master"
"repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/efoft/path-locator"
        }
    ]
--
2. Without composer don't forget to require_once the file '/vendor/efoft/path-locator/src/PathLocator.php'


Usage.

For example your web-server has document root under /var/www/html and your site resides under /test subfolder of the document root. And your going to open the URL
http://example.com/test/index.php 

// call the class from e.g. index.php
$loc = new \PathLocator();

// Get full path of the site root
echo $loc->getRootPath(). "<br>";
--> /var/www/html/test/

// Get the relative path of the subfolder. Might be used both for path and url manipulations.
echo $loc->getBaseDir(). "<br>";
--> /test/

// Get one of the connection parameters (see Connection Parameters section below)
echo $loc->getConnParam('fullurl'). "<br>";
--> http://example.com/test

// Convert arbitary file or folder name under site root to URL (file or folder existance is not checked)
echo $loc->convertPathToURL('/images/'). "<br>";
--> http://example.com/test/images/

// Get full URL with parameters
echo $loc->getSelfUrl(). "<br>";
--> http://example.com/test/index.php?query=example
