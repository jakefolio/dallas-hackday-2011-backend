<?php
/**
* Retrieve movie information and photos
*/
class RottenTomato
{
    protected $apiKey = '3m57srra9bnfkzzxfk378wtp';

	public $format;
	protected $movie;
    
    public function __construct($format = 'php', $movieId = 0)
    {
        $this->movie = $this->getMovie($movieId);
		$this->format = $format;
    }
    
    protected function query($url)
    {   
        $session = curl_init($url);
        
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        
        $results = curl_exec($session);
        
        curl_close($session);
        
        return json_decode($results);
    }

	public function getMovies()
	{
		$results =  $this->query("http://api.rottentomatoes.com/api/public/v1.0/lists/movies/in_theaters.json?apikey={$this->apiKey}");
		
		switch($this->format) {
			case 'json':
				return json_encode($results);
			break;
			
			case 'php':
			default:
				return $results;
		}
	}
	
	public function getMovie($movieId = null)
	{	
		if (!$movieId && $this->movie) {
			$results = $this->movie;
		} else {
			$results = $this->movie = $this->query("http://api.rottentomatoes.com/api/public/v1.0/movies/{$movieId}.json?apikey={$this->apiKey}");
		}
		
		switch($this->format) {
			case 'json':
				return json_encode($results);
			break;
			
			case 'php':
			default:
				return $results;
		}
	}
	
	public function getCharacters($movieId = null)
	{
		
		if (!$movieId && $this->movie) {
			$results = $this->movie->abridged_cast;
		} else {
			$results = $this->movie = $this->query("http://api.rottentomatoes.com/api/public/v1.0/movies/{$movieId}/cast.json?apikey={$this->apiKey}");
			$results->cast;
			
		}
		
		switch($this->format) {
			case 'json':
				return json_encode($results);
			break;
			
			case 'php':
			default:
				return $results;
		}
	}
    
    public function getQuestion()
    {
		$result = $this->getMovies();
		
		$movie = $this->getMovie($result->movies[array_rand($result->movies)]->id);
		
		$characters = $this->getCharacters();
		
		$selectedCharacters = array_rand($characters, 4);
		
		foreach ($selectedCharacters AS $answer) {
			$answers[] = $characters[$answer];
		}
		
		shuffle($answers);
		$correctId = array_rand($answers);
		
		return array(
			'question' => "What character did {$answers[$correctId]->name} play in {$movie->title}?",
			'photo' => null,
			'answers' => array_map(function($v) {
				return $v->characters[0];
			}, $answers),
			'correctAnswer' => $correctId
		);
    }
}
