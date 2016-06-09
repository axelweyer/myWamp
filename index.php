<?php
/*
HOMEPAGE WAMP by Axel Weyer - 2016
With the help of
Alex D. : https://github.com/Alex-D/HomeWAMP & http://alex-d.fr/
& OmarBerrayti : https://github.com/OmarBerrayti/Win8Wamp
*/


$admin_ip = array(
	'127.0.0.1',
	'127.0.0.0',
	'::1',
);
// Repertoires à ignorer dans les projets
$projectsListIgnore = array(
	'.',
	'..',
	'_h5ai',
	'css',
	'js',
	'data',
	'mysql',
	'localhost',
);
$admin = in_array($_SERVER['REMOTE_ADDR'], $admin_ip) ? true : false;
$page = (isset($_GET['admin']) && $admin) ? 'admin_'.$_GET['admin'] : 'index';
// Affichage du phpinfo
if(isset($_GET['phpinfo'])){
	if($admin)
		phpinfo();
	else
		header('Location: /');
	exit();
}
// Chemin jusqu'au fichier de conf de WampServer
$wampConfFile = '../wampmanager.conf';
// Chemin jusqu'aux fichiers alias
$aliasDir = '../alias/';
// Liste des nom de variables pour les images ci-dessous
$images = array(
	'pngFolder',
	'pngFolderGo',
	'pngLogo',
	'pngPlugin',
	'pngWrench',
	'pngEmptyFolder',
	'favicon',
);
// Affichage des images (rendu en base64 des image encodées ci-dessus)
if(isset($_GET['img'])){
	if(in_array($_GET['img'], $images)){
		header("Content-type: image/" . ( substr($_GET['img'], 0, 3) == 'png' ? 'png' : 'x-icon' ));
		echo base64_decode(${$_GET['img']});
	}
	exit();
}
// Textes pour les traductions
$langues = array(
	// Anglais
	'en' => array(
		'langue'            => 'English',
		'autreLangue'       => 'Version Française',
		'autreLangueLien'   => 'fr',
		'titreHtml'         => 'WAMPSERVER Homepage',
		'serveurEnLigne'    => 'Online',
		'serveurHorsLigne'  => 'Offline',
		'versa'             => 'Apache Version :',
		'versp'             => 'PHP Version :',
		'versm'             => 'MySQL Version :',
		'phpExt'            => 'Loaded Extensions : ',
		'titrePage'         => 'Tools',
		'txtProjet'         => 'Your Projects',
		'mesProjets'        => 'My Projects',
		'txtNoProjet'       => 'No projects yet.<br />To create a new one, just create a directory in \'www\'.',
		'txtAlias'          => 'Your Aliases',
		'txtNoAlias'        => 'No Alias yet.<br />To create a new one, use the WAMPSERVER menu.',
		'faq'               => 'http://www.en.wampserver.com/faq.php',
		'sur'               => 'on',
		'creePar'           => 'Created by',
		// Admin
		'lesProjets'        => 'Projects',
		'lesAlias'          => 'Aliases',
		'servConf'          => 'Server Configuration',
		'serveur'           => 'Server',
		'passerEnLigne'     => 'Put server online',
		'passerHorsLigne'   => 'Put Offline',
		'phraseScreenshot'  => 'For an overview of the project, you need an image file named "screenshot.jpg" in the project folder, recommended dimensions : 150x100px.',
	),
	// Français
	'fr' => array(
		'langue'            => 'Français',
		'autreLangue'       => 'English Version',
		'autreLangueLien'   => 'en',
		'serveurEnLigne'    => 'En Ligne',
		'serveurHorsLigne'  => 'Hors Ligne',
		'titreHtml'         => 'Accueil WAMPSERVER',
		'titreConf'         => 'Configuration Serveur',
		'versa'             => 'Version de Apache :',
		'versp'             => 'Version de PHP :',
		'versm'             => 'Version de MySQL :',
		'phpExt'            => 'Extensions Chargées : ',
		'titrePage'         => 'Outils',
		'txtProjet'         => 'Vos Projets',
		'mesProjets'        => 'Mes Projets',
		'txtNoProjet'       => 'Aucun projet.<br /> Pour en ajouter un nouveau, créez simplement un répertoire dans \'www\'.',
		'txtAlias'          => 'Vos Alias',
		'txtNoAlias'        => 'Aucun alias.<br /> Pour en ajouter un nouveau, utilisez le menu de WAMPSERVER.',
		'faq'               => 'http://www.wampserver.com/faq.php',
		'sur'               => 'sur',
		'creePar'           => 'Créé par',
		// Admin
		'lesProjets'        => 'Les Projets',
		'lesAlias'          => 'Les Alias',
		'servConf'          => 'Configuration Serveur',
		'serveur'           => 'Serveur',
		'passerEnLigne'     => 'Passer en Ligne',
		'passerHorsLigne'   => 'Passer hors Ligne',
		'phraseScreenshot'  => 'Pour avoir un aperçu du projet, vous devez mettre un fichier image nommé "screenshot.jpg" dans le dossier du projet, dimentions recommandées : 150x100px.',
	)
);

/*********************************************************************************************************************************************************************/

session_start();

// Définition de la langue et des textes
if(isset($_SESSION['WampServerLang']) && !isset($_GET['lang'])){
	$langue = $_SESSION['WampServerLang'];
}
elseif (isset ($_GET['lang'])) {
	$langue = $_GET['lang'];
	$_SESSION['WampServerLang'] = $langue;
}
elseif (isset ($_SERVER['HTTP_ACCEPT_LANGUAGE']) AND preg_match("/^fr/", $_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
	$langue = 'fr';
}
else {
	$langue = 'en';
}

// Redirection vers l'admin
if(isset($_GET['go_admin'])){
	header("Location: /?admin=home");
	exit();
}
// On charge le fichier de conf locale
if (!is_file($wampConfFile))
	die ('Unable to open WampServer\'s config file, please change path in index.php file');
$fp = fopen($wampConfFile, 'r');
$wampConfFileContents = fread ($fp, filesize($wampConfFile));
fclose($fp);

// On récpères les versions des applis
preg_match('|phpVersion = (.*)\n|',$wampConfFileContents,$result);
$phpVersion = str_replace('"','',$result[1]);
preg_match('|apacheVersion = (.*)\n|',$wampConfFileContents,$result);
$apacheVersion = str_replace('"','',$result[1]);
preg_match('|mysqlVersion = (.*)\n|',$wampConfFileContents,$result);
$mysqlVersion = str_replace('"','',$result[1]);
preg_match('|wampserverVersion = (.*)\n|',$wampConfFileContents,$result);
$wampserverVersion = str_replace('"','',$result[1]);
preg_match('|status = (.*)\n|',$wampConfFileContents,$result);
$serverStatus = str_replace('"','',$result[1]);
if($serverStatus == 'offline'){
	$serverStatusLbl = $langues[$langue]['serveurHorsLigne'];
	$serverStatusCol = 'grey';
	$switch_online_offline = $langues[$langue]['passerEnLigne'];
} else {
	$serverStatusLbl = $langues[$langue]['serveurEnLigne'];
	$serverStatusCol = '#62bf21';
	$switch_online_offline = $langues[$langue]['passerHorsLigne'];
}

// Recuperation des projets
$folder=opendir(".");
$projectContents = '';
while($file = readdir($folder)){
	if(is_dir($file) && !in_array($file, $projectsListIgnore)){
		$screenshot = file_exists($file.'/screenshot.png') ? $file.'/screenshot.png' : "holder.js/260x200/text:".$file;
		$favicon = 'check.png';
		if(file_exists($file.'/favicon.png')){
			$favicon = $file.'/favicon.png';
			$projectContents .= '<div class="col-lg-3 col-md-3 col-sm-4 col-xs-6">
			<a href="/'.$file.'" class="project">
				<span class="favicon"><img src="'.$favicon.'"></span>
				<img src="'.$screenshot.'">
				<span class="overlay"><h1>'.$file.'</h1></span>
			</a>
		</div>';
		}
		else if(file_exists($file.'/favicon.ico')){
			$favicon = $file.'/favicon.ico';
			$projectContents .= '<div class="col-lg-3 col-md-3 col-sm-4 col-xs-6">
			<a href="/'.$file.'" class="project">
				<span class="favicon"><img src="'.$favicon.'"></span>
				<img src="'.$screenshot.'">
				<span class="overlay"><h1>'.$file.'</h1></span>
			</a>
		</div>';
		}
		else
		{
			$projectContents .= '<div class="col-lg-3 col-md-3 col-sm-4 col-xs-6">
			<a href="/'.$file.'" class="project">
				<span class="favicon"></span>
				<img src="'.$screenshot.'">
				<span class="overlay"><h1>'.$file.'</h1></span>
			</a>
		</div>';
		}
	}

}
closedir($folder);

if (!isset($projectContents))
	$projectContents = $langues[$langue]['txtNoProjet'];
if($admin && $page == 'admin_home'){
	// Recuperation des alias
	$aliasContents = '';
	if (is_dir($aliasDir)) {
		$handle=opendir($aliasDir);
		while ($file = readdir($handle)){
			if (is_file($aliasDir.$file) && strstr($file, '.conf')){
				$msg = '';
				$aliasContents .= '<li><a href="'.str_replace('.conf','',$file).'/">'.str_replace('.conf','',$file).'</a></li>';
			}
		}
		closedir($handle);
	}
	if (!isset($aliasContents))
		$aliasContents = $langues[$langue]['txtNoAlias'];
	// Recuperation des extensions PHP
	$phpExtContents = '';
	$loaded_extensions = get_loaded_extensions();
	foreach ($loaded_extensions as $extension)
		$phpExtContents .= "<li>${extension}</li>";
}

$admin_link = ($admin) ? '<li id="admin_link"><a href="?admin=home">Administration</a></li>' : '';

// Gestion des pages d'administration
if($page != ''){
	$sidebar = '<div id="sidebar">
					<h2>Admin</h2>
					<ul>
						<li><a href="#conf_serv">'.$langues[$langue]["servConf"].'</a></li>
						<li class="empty"></li>
						<li><a href="#vos_projets">'.$langues[$langue]["lesProjets"].'</a></li>
						<li><a href="#alias">'.$langues[$langue]["lesAlias"].'</a></li>
					</ul>
				</div>';
	$admin_links = '<li><a href="/phpmyadmin/">PhpMyAdmin</a></li>
					<li><a href="?phpinfo=1">phpinfo()</a></li>
					<li class="empty"></li>';
}



if($admin){
	if($page == 'admin_home')
	{
		$admin_link = '<li><a href="?admin=home" id="active">Administration</a></li>';
	}
	else{
		$admin_link = '<li class="navlink"><a href="?admin=home">Administration</a></li>';
	}
}
else{
	$admin_link = "";
}

// Gestion des pages d'administration
if($page != ''){
	$admin_links = '<li class="navlink"><a href="/phpmyadmin/">PhpMyAdmin</a></li>
					<li class="navlink"><a href="?phpinfo=1">phpinfo()</a></li>';
}


switch($page){
	case "admin_home":
		// ADMINISTRATION - HOME
		header("Content-type: text/html");
		$pageContents = <<< EOPAGE
		<!DOCTYPE html>
		<html lang="{$langue}" xml:lang="{$langue}">

		<head>
			<meta charset="utf-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<meta name="description" content="">
			<meta name="author" content="">
			<title>Localhost</title>
			<link href="localhost/css/bootstrap.css" rel="stylesheet">
			<link href="localhost/css/portfolio-item.css" rel="stylesheet">
		</head>

		<body>
			<!-- Navigation -->
			<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation" style="background:#282f33;">
				<div class="container">
					<!-- Brand and toggle get grouped for better mobile display -->
					<div class="navbar-header">
						<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<img src="localhost/img/wamp.png" id="img" onmouseover="this.src='localhost/img/wamp2.png';" onmouseout="this.src='localhost/img/wamp.png';">
					</div>
					<!-- Collect the nav links, forms, and other content for toggling -->
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
						<ul class="nav navbar-nav">
							<li class="navlink">
								<a href="index.php">Home</a>
							</li>
							{$admin_links}
							<li class="navlink">
								<a href="?lang={$langues[$langue]['autreLangueLien']}">{$langues[$langue]['autreLangue']}</a>
							</li>
							{$admin_link}
						</ul>
					</div>
					<!-- /.navbar-collapse -->
				</div>
				<!-- /.container -->
			</nav>

			<!-- Page Content -->
			<div class="container" style="background:#1a1a1a;">
				<div class="row">
					<div class="col-lg-12">
						<h1 class="page-header">{$langues[$langue]['servConf']}
						</h1>
					</div>
				</div>
				<div id="conf_serv" class="bloc">
					<div class="bloc_in">
						<div style="margin: 0 auto; padding: 0 1.5em; color: ${serverStatusCol}; font-size: 21px; font-weight: bold; width: 350px; text-align: center;">
							<span style="display: inline-block; background: ${serverStatusCol}; height: 16px; width: 16px; border-radius: 20px;"> </span>
							<span style="display: inline-block;">{$langues[$langue]['serveur']} ${serverStatusLbl}</span>
							<div id="btn_switch_status">
								<a href="?admin=online_offline" style="float: none; margin: 0 3em 1em;">
									${switch_online_offline}
								</a>
								<hr class="clear" />
							</div>
						</div>
						<div class="content">
							<dl>
							<dt>{$langues[$langue]['versa']}</dt>
							<dd>${apacheVersion}  </dd>
							<dt>{$langues[$langue]['versm']}</dt>
							<dd>${mysqlVersion}  </dd>
							<dt>{$langues[$langue]['versp']}</dt>
							<dd>${phpVersion}  </dd>
							<dt>{$langues[$langue]['phpExt']}</dt>
							<dd>
								<ul>
									${phpExtContents}
								</ul>
							</dd>
							</dl>
						</div>
					</div>
				</div>

				<!-- Portfolio Item Heading -->
				<div class="row">
					<div class="col-lg-12">
						<h1 class="page-header">Projects <small>{$serverStatusLbl}</small>
						</h1>
					</div>
				</div>
				<!-- /.row -->

				<br>

				<!-- Related Projects Row -->
				<div class="row">
					<div class="projects">
						{$projectContents}
					</div>
				</div>
				<!-- /.row -->


				<div class="row">
					<div class="col-lg-12">
						<h1 class="page-header">{$langues[$langue]['txtAlias']}
						</h1>
					</div>
				</div>
				<div id="alias" class="bloc">
					<div class="bloc_in">
						<ul class="aliases">
							${aliasContents}
						</ul>
					</div>
				</div><!-- Footer -->
				<footer>
					<div class="row">
						<div class="col-lg-12">
							<p>Copyright &copy; AW 2016</p>
						</div>
					</div>
					<!-- /.row -->
				</footer>
			</div>
			<!-- /.container -->

			<!-- jQuery -->
			<script src="localhost/js/jquery.js"></script>
			<script src="localhost/js/bootstrap.min.js"></script>
			<script type="text/javascript" src="localhost/js/holder.js"></script>
		</body>

		</html>
EOPAGE;
		break;
	case 'admin_online_offline':
		$_SERVER['argv'][1] = ($serverStatus == "offline") ? "on" : "off";
		require("../scripts/onlineOffline.php");
		header('Location: /?admin=refresh');
		exit();
		break;
	case 'admin_refresh':
		require("../scripts/refresh.php");
		header('Location: /?admin=home');
		exit();
		break;
	case 'index':
		$pageContents = <<< EOPAGE
		<!DOCTYPE html>
		<html lang="{$langue}" xml:lang="{$langue}">

		<head>
			<meta charset="utf-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<meta name="description" content="">
			<meta name="author" content="">
			<title>Localhost</title>
			<link href="localhost/css/bootstrap.css" rel="stylesheet">
			<link href="localhost/css/portfolio-item.css" rel="stylesheet">
		</head>

		<body>
			<!-- Navigation -->
			<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation" style="background:#282f33;">
				<div class="container">
					<!-- Brand and toggle get grouped for better mobile display -->
					<div class="navbar-header">
						<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<img src="localhost/img/wamp.png" id="img" onmouseover="this.src='localhost/img/wamp2.png';" onmouseout="this.src='localhost/img/wamp.png';">
					</div>
					<!-- Collect the nav links, forms, and other content for toggling -->
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
						<ul class="nav navbar-nav">
							<li>
								<a href="index.php" id="active">Home</a>
							</li>
							{$admin_links}
							<li class="navlink">
								<a href="?lang={$langues[$langue]['autreLangueLien']}">{$langues[$langue]['autreLangue']}</a>
							</li>
							{$admin_link}
						</ul>
					</div>
					<!-- /.navbar-collapse -->
				</div>
				<!-- /.container -->
			</nav>

			<!-- Page Content -->
			<div class="container" style="background:#1a1a1a;">
				<!-- Portfolio Item Heading -->
				<div class="row">
					<div class="col-lg-12">
						<h1 class="page-header">Projects <small>{$serverStatusLbl}</small></h1>
					</div>
				</div>
				<!-- /.row -->

				<br>

				<!-- Related Projects Row -->
				<div class="row">
					<div class="projects">
						{$projectContents}
					</div>
				</div>
				<!-- /.row -->

				<!-- Footer -->
				<footer>
					<div class="row">
						<div class="col-lg-12">
							<p>Copyright &copy; AW 2016</p>
						</div>
					</div>
					<!-- /.row -->
				</footer>
			</div>
			<!-- /.container -->

			<!-- jQuery -->
			<script src="localhost/js/jquery.js"></script>
			<script src="localhost/js/bootstrap.min.js"></script>
			<script type="text/javascript" src="localhost/js/holder.js"></script>
		</body>

		</html>
EOPAGE;
		break;
	default:
		header('Location: /');
		exit();
		break;
}
echo $pageContents;

?>
