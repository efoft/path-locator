<?php
/**
 * Determines site root and base url and supplies convertion
 * of the pathes and URLs. DOES NOT support Server Aliases.
 *
 * IMPORTANT! The class must be initialized from php script residing
 * strictly in the top site directory. Otherwise things will be
 * broken.
 *
 * Methods:
 * - public function getRootPath()
 * - public function getBaseDir()
 * - public function getConnParam($paramname)
 * - public function convertPathToURL($path)
 * - public function getSelfURL()
 */
class PathLocator {

  // see __construct() for explanation
  private $rootpath;
  private $basedir;
  private $conn = array();

  /**
   * Detects self location and calculates all the rest.
   * @access  public
   */
  public function __construct()
  {
    // getcwd() returns the path of the "main" script referenced in the URL, where execution started
    // same result gives realpath(NULL) as for PHP 5.3.3
    $cwd = getcwd();

    // this is site root absolute path
    $rootpath  = $cwd;

    // also can be retrieved with $_SERVER['DOCUMENT_ROOT'] but there are reports that not all
    // web servers returns that
    $docroot   = preg_replace("!${_SERVER['SCRIPT_NAME']}$!", '', $_SERVER['SCRIPT_FILENAME']);

    // subfolder relative path if the site is under subfolder of the web-server, otherwise this is '/'
    $basedir   = preg_replace("!^${docroot}!", '', $rootpath);

    // some more connection parameters
    $proto     = empty($_SERVER['HTTPS']) ? 'http' : 'https';
    $port      = $_SERVER['SERVER_PORT'];
    $disp_port = ($proto == 'http' && $port == 80 || $proto == 'https' && $port == 443) ? '' : ":$port";
    $domain    = $_SERVER['SERVER_NAME'];
    $fullurl   = "${proto}://${domain}${disp_port}${basedir}";

    $this->rootpath = $rootpath;
    $this->basedir  = $basedir;
    $this->conn     = compact('proto','port','disp_port','domain','fullurl');
  }

  /**
   * Removes double slashes and append trailing slash to the given string.
   * NB! Existance of the path is not checked.
   *
   * @param   string  $path  This must be path not URL
   *
   * @return  string  Sanitized output
   */
  private function sanitizePath($path)
  {
    if ( ! is_string($path) )
      throw new InvalidArgumentException('String is expected but ' . gettype($path) . ' was given.');

    return preg_replace('!/{2,}!', '/', $path . '/');
  }

  /**
   * @return  string  Absolute path of the site root directory with trailing slash.
   */
  public function getRootPath()
  {
    return $this->sanitizePath($this->rootpath);
  }

  /**
   * @return  string  Path of site root directory relative to docroot with trailing slash.
   */
  public function getBaseDir()
  {
    return $this->sanitizePath($this->basedir);
  }

  /**
   * @param   string  $paramname  The name of the requested parameter.
   *
   * @return  string              Value of the parameter of NULL if parameter name is invalid.
   */
  public function getConnParam($paramname)
  {
    if ( ! is_string($paramname) )
      throw new InvalidArgumentException('String is expected but ' . gettype($paramname) . ' was given.');

    return ( isset($this->conn[$paramname]) ) ? $this->conn[$paramname] : NULL;
  }

  /**
   * Convert absolute path to URL.
   *
   * @param   string  $path  Absolute path or url relative to site root
   *
   * @return  string         Full URL.
   */
  public function convertPathToURL($path)
  {
    $relpath = preg_replace("!^$this->basedir!", '', $path);
    return $this->getConnParam('fullurl') . preg_replace('!/{2,}!', '/', '/' . $relpath);
  }

  /**
   * @return  string        Full URL of the running script with parameters.
   */
  public function getSelfURL() {
    return preg_replace("!$this->basedir$!", '', $this->getConnParam('fullurl')) . $_SERVER['REQUEST_URI'];
  }
}
?>