<?php

require_once __DIR__.'/silex.phar'; 

$app = new Silex\Application(); 
$app['debug'] = true;

$app->register(new Silex\Extension\DoctrineExtension(), array(
    'db.options' => array(
        'driver'    => 'pdo_mysql',
		'dbname' 	=> 'hackday',
        'user'      => 'root',
		'password' 	=> '',
		'host'		=> 'localhost'
    ),
    'db.dbal.class_path'    => __DIR__.'/vendor/doctrine-dbal/lib',
    'db.common.class_path'  => __DIR__.'/vendor/doctrine-common/lib',
));

$app->get('/twitter', function() use ($app) { 
    header('application/json');
	require 'lib/Twitter.php';
	$resource = new Twitter();
	
	echo json_encode($resource->getQuestion());
});

$app->get('/craigslist', function() use ($app) { 
    header('application/json');
	require 'lib/Craigslist.php';
	$resource = new Craigslist();
	
	echo json_encode($resource->getQuestion());
});

$app->get('/rottentomato', function() use ($app) { 
    header('application/json');
	require 'lib/RottenTomato.php';
	$resource = new RottenTomato();
	
	echo json_encode($resource->getQuestion());
});

$app->get('/musixmatch', function() use ($app) { 
    header('application/json');
	require 'lib/MusixMatch.php';
	$resource = new MusixMatch();
	
	echo json_encode($resource->getQuestion());
});

$app->get('/question', function() use ($app) {
	$feeds = array('Twitter','MusixMatch','RottenTomato','Craigslist');
	$source = $feeds[array_rand($feeds)];
	header('application/json');
	require "lib/{$source}.php";
	
	$resource = new $source();
	
	echo json_encode($resource->getQuestion());
});

$app->get('/leaderboard', function() use ($app) {
    header('application/json');
	$return = array(
		23525 => array(
			'score' => 62351351,
			'name' => 'Jake Smith'
		),
		231413 => array(
			'score' => 2342355,
			'name' => 'Geoff Tran'
		),
		123 => array(
			'score' => 21242,
			'name' => 'Ben Gatzke'
		)
	);
	
	echo json_encode($return);
});

$app->get('/submissions', function() use ($app) {
    header('application/json');
	$return = array(
		23525 => array(
			'correct' => true,
			'name' => 'Jake Smith'
		),
		231413 => array(
			'score' => true,
			'name' => 'Geoff Tran'
		),
		123 => array(
			'score' => false,
			'name' => 'Ben Gatzke'
		)
	);
	
	echo json_encode($return);
});

$app->post('/user', function() use ($app) {
	// Add User;
	$answerId = $app['db']->insert('users', array(
		'name' 		=> 'Jake Smith',
		'email' 	=> 'jake.smith92@gmail.com'
	));
	header('application/json');
	
	$return = array(
		12515 => array(
			'name' => 'Jake Smith',
			'email' => 'jake.smith92@gmail.com'
		)
	);
	
	echo json_encode($return);
});

$app->post('/submit', function() use ($app) {
	// Submit User Score
	$answerId = $app['db']->insert('answers', array(
		'answer' 		=> 'Yes',
		'question_id' 	=> 1,
		'correct' 		=> true,
		'quiz_id' 		=> 1,
		'user_id' 		=> 1
	));
	header('application/json');
	
	$return = array(
		1245133 => true
	);
	
	echo json_encode($return);
});

$app->run();
/*

require 'lib/RottenTomato.php';

$resource = new RottenTomato('php');
$question = $resource->getQuestion();

echo "<h2>{$question['question']}</h2>";
echo "<ul>";
foreach ($question['answers'] as $answer) {
	echo "<li>{$answer}</li>";
}
echo "</ul>";

echo "<p>Correct Answer: {$question['correctAnswer']}</p>";

require 'lib/Craigslist.php';

$clist = new Craigslist();

$cquestion = $clist->getQuestion();

echo "<h2>{$cquestion['question']}</h2>";
echo "<img src='{$cquestion['photo']}' alt=''><br>";
echo "<ul>";
foreach ($cquestion['answers'] as $answer) {
	echo "<li>{$answer}</li>";
}
echo "</ul>";

echo "<p>Correct Answer: {$cquestion['correctAnswer']}</p>";

require 'lib/MusixMatch.php';

$music = new MusixMatch();

$mquestion = $music->getQuestion();

echo "<h2>{$mquestion['question']}</h2>";
echo "<ul>";
foreach ($mquestion['answers'] as $answer) {
	echo "<li>{$answer}</li>";
}
echo "</ul>";

echo "<p>Correct Answer: {$mquestion['correctAnswer']}</p>";

require 'lib/Twitter.php';

$twitter = new Twitter();

$tquestion = $twitter->getQuestion();

echo "<h2>{$tquestion['question']}</h2>";
echo "<ul>";
foreach ($tquestion['answers'] as $answer) {
	echo "<li>{$answer}</li>";
}
echo "</ul>";

echo "<p>Correct Answer: {$tquestion['correctAnswer']}</p>";
*/