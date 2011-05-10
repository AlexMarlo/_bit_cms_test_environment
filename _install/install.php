<?php

class ProjectInstaller
{
  protected $_project_repo;
  protected $_project_db;
  protected $_project_path;

  protected $_bitcms_modules;

  protected $_limb_src;
  protected $_limb_tag;

  protected $_bitcms_src;
  protected $_bitcms_branch;

  public function __construct($conf)
  {
    foreach($conf as $var => $value)
      $this->$var = $value;

    $this->_project_path = rtrim($conf['_project_path'], '/');
  }

  public function install()
  {
    chdir($this->_project_path);

    passthru('git init');
    passthru('git remote add origin ' . $this->_project_repo);
    passthru('git fetch origin');
    passthru('git branch --track master origin/master');
    passthru('git checkout master');

    $this->_setGitIgnores();

    $this->_exportExternalProjects();
    $this->_exportProjectSkel();

    require_once(dirname(__FILE__) . '/../setup.php');


    $this->_installProjectDB();
    $this->_installCmsModules();

    $this->_createExternalLibsConf();
    $this->_displayHostSettings();
  }

  protected function _setGitIgnores()
  {
    @mkdir($this->_project_path . '/settings');

    @mkdir($this->_project_path . '/www');
    @mkdir($this->_project_path . '/www/media');
    passthru("chmod 777 -R www/media");

    @mkdir($this->_project_path . '/lib');
    @mkdir($this->_project_path . '/lib/limb');
    @mkdir($this->_project_path . '/lib/bitcms');

    @mkdir($this->_project_path . '/var');
    passthru("chmod 777 -R var");

    passthru('git add settings');

    passthru('git add www');

    passthru('git add lib');
    passthru('git add lib/limb');
    passthru('git add lib/bitcms');

    $file_ignore =
      'settings/*.conf.override.php' . PHP_EOL .
      'www/media' . PHP_EOL .
      'lib/limb' . PHP_EOL .
      'lib/bitcms' . PHP_EOL .
      'setup.override.php' . PHP_EOL .
      '_install' . PHP_EOL .
      'var' . PHP_EOL .
      'conf.php' . PHP_EOL .
      'install.php' . PHP_EOL
    ;

    file_put_contents('.gitignore', $file_ignore);
    passthru('git add .gitignore');

    passthru('git commit -m "Installer: set git ignores"');
    passthru('git push origin master');
  }

  protected function _exportProjectSkel()
  {
    echo "Export project skel..." . PHP_EOL;

    passthru('cp -R lib/bitcms/_skel/* ./');

    @mkdir($this->_project_path . '/init/migrate');
    @mkdir($this->_project_path . '/www/images/button');
    @mkdir($this->_project_path . '/www/images/external_links');
    @mkdir($this->_project_path . '/www/images/icon');
    @mkdir($this->_project_path . '/www/images/marker');
    @mkdir($this->_project_path . '/www/images/menu');
    @mkdir($this->_project_path . '/www/images/pic');
    @mkdir($this->_project_path . '/www/images/swf');
    @mkdir($this->_project_path . '/www/images/title');

    $this->_makeReadmeFile();

    passthru('git add .');
    passthru('git commit -m "Installer: export project skel"');
    passthru('git push origin master');
  }

  protected function _makeReadmeFile()
  {
    $file = $this->_project_path . "/README";
    $msg = file_get_contents($file);
    $msg = str_replace("%limb_tag%", $this->_limb_tag, $msg);
    $msg = str_replace("%bitcms_branch%", $this->_bitcms_branch, $msg);

    file_put_contents($file, $msg);
  }

  protected function _exportExternalProjects()
  {
    echo "Export Limb..." . PHP_EOL;

    chdir($this->_project_path . '/lib/limb');

    passthru('git init');
    passthru('git remote add origin ' . $this->_limb_src);
    passthru('git fetch origin');
    passthru('git checkout ' . $this->_limb_tag);

    chdir($this->_project_path);

    echo "Export BitCms..." . PHP_EOL;

    chdir($this->_project_path . '/lib/bitcms');

    passthru('git init');
    passthru('git remote add origin ' . $this->_bitcms_src);
    passthru('git fetch origin');
    passthru('git branch --track ' . $this->_bitcms_branch . ' origin/' . $this->_bitcms_branch);
    passthru('git checkout ' . $this->_bitcms_branch);

    chdir($this->_project_path);
  }

  protected function _installProjectDB()
  {
    require_once('cli/mysql.inc.php');

    $dsn = new lmbDbDSN($this->_project_db);

    $host = $dsn->getHost();
    $user = $dsn->getUser();
    $password = $dsn->getPassword();
    $database = $dsn->getDatabase();
    $charset = $dsn->getCharset();

    if(mysql_db_exists($host, $user, $password, $database))
      mysql_db_cleanup($host, $user, $password, $database);
    else
      mysql_exec($host, $user, $password, '', "CREATE DATABASE $database CHARACTER SET $charset");

    $this->_makeDbSettingsFile();

    include('cli/load.php');
    include('cli/dump.php');

    passthru('git add .');
    passthru('git commit -m "Installer: up projects database"');
    passthru('git push origin master');
  }

  protected function _makeDbSettingsFile()
  {
    $content = "<?php" . PHP_EOL . "\$conf = array('dsn' => '{$this->_project_db}'); ";
    file_put_contents($this->_project_path . '/settings/db.conf.php', $content);
  }

  protected function _installCmsModules()
  {
    foreach($this->_bitcms_modules as $module)
      passthru('php cli/module.php install ' . $module);
  }

  protected function _createExternalLibsConf()
  {
    $content = <<<EOD
<?php
  \$limb_src = '{$this->_limb_src}';
  \$limb_tag = '{$this->_limb_tag}';
  \$bitcms_src = '{$this->_bitcms_src}';
  \$bitcms_branch = '{$this->_bitcms_branch}';
EOD;

    file_put_contents($this->_project_path . '/settings/external_libs.conf.php', $content);

    passthru('git add ' . $this->_project_path . '/settings/external_libs.conf.php');
    passthru('git commit -m "Installer: created external libs conf"');
    passthru('git push origin master');

    echo PHP_EOL . "External Libs Conf created" . PHP_EOL;
  }

  protected function _displayHostSettings()
  {
    $project_path = $this->_project_path;

    $message = <<<EOD
You should add to your hosts file:
  127.0.0.1	project.dev

You should add to your apache config file:
  <VirtualHost *:80>
    ServerName project.dev
    DocumentRoot {$project_path}/www

    <Directory {$project_path}/www>
      Options Indexes FollowSymLinks
      AllowOverride All
    </Directory>

    Alias /shared/calendar "{$project_path}/lib/limb/calendar/shared"
    Alias /shared/js "{$project_path}/lib/limb/js/shared"
    Alias /shared/wysiwyg "{$project_path}/lib/limb/wysiwyg/shared"

    Alias /shared/base "{$project_path}/lib/bitcms/base/shared"
  </VirtualHost>

Don't forget to restart your server!
EOD;

    echo PHP_EOL . PHP_EOL . $message . PHP_EOL;
    echo PHP_EOL . "Install complete" . PHP_EOL;
  }
}

//-- run installer
if((sizeof($argv) > 1) && (file_exists($argv[1])))
  include($argv[1]);
else
  include(dirname(__FILE__) . '/conf.php');

$project_installer = new ProjectInstaller($conf);
$project_installer->install();
