<?php
require 'protected/vendor/autoload.php';

ORM::configure('mysql:host=xx.xx;dbname=xxx');
ORM::configure('username', 'xxx');
ORM::configure('password', 'xxx');
ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
ORM::configure('return_result_sets', true);

$app = new \Slim\Slim(array(
	'view' => new \Slim\Extras\Views\Twig()
));

\Slim\Extras\Views\Twig::$twigTemplateDirs = array(
	realpath('protected/templates')
);

\Slim\Extras\Views\Twig::$twigOptions = array(
    'cache' => 'protected/templates/cache',
);

// Index
$app->get('/', function () use ($app) {
	$app->redirect('/thearcade');
});

// Home
$app->get('/thearcade', function () use ($app) {
        $collection = ORM::for_table('collection')
		->distinct()
		->select(system)
		->order_by_asc(system)
                ->find_many();
	$latest = ORM::for_table('collection')
		->order_by_desc(date)
		->limit(5)
		->find_many();
	$lastmod = ORM::for_table('collection')
		->order_by_desc(date)
		->find_one();
	echo $app->render('index.htm', array('collection' => $collection, 
						 'system' => $select, 
						 'latest' => $latest, 
						'lastmod' => $lastmod,
						   'home' => $home));
}); 

// humans.txt
$app->get('/thearcade/humans.txt', function () use ($app) {
	$app->contentType('text/plain');
        $lastmod = ORM::for_table('collection')
                ->order_by_desc(date)
                ->find_one();
        echo $app->render('humans.txt', array('lastmod' => $lastmod));
});

// sitemap.xml
$app->get('/thearcade/sitemap.xml', function () use ($app) {
        $app->contentType('text/xml;charset=utf-8');
        $lastmod = ORM::for_table('collection')
                ->order_by_desc(date)
                ->find_one();
	$collection = ORM::for_table('collection')
                ->distinct()
                ->select(system)
                ->find_many ();
        echo $app->render('sitemap.xml', array('collection' => $collection, 
						  'lastmod' => $lastmod, 
						   'system' => $select));
});

// About
$app->get('/thearcade/about', function () use ($app) {
	$system = 'about';
        echo $app->render('about.htm', array('system' => $system));
});

// Collection Listing
$app->get('/thearcade/:system', function ($system) use ($app) {
	$collection = ORM::for_table('collection')
		->order_by_asc(title)
		->where('system', $system)
		->find_many();
	if (empty($collection)) {
		$app->notFound();
	}
              echo $app->render('stuff.htm', array('collection' => $collection,
						       'system' => $system));
});

$app->run();
