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
		
		$id = array_rand($characters);
		$correct = $characters[$id];
		unset($characters[$id]);
		$others = array_rand($characters, 3);
		$others[] = $id;
		$characters[$id] = $correct;
		shuffle($others);
		
		$correctId = array_keys($characters, $correct);
		
		return array(
			'question' => "What character did {$correct->name} play in {$movie->title}?",
			'photo' => null,
			'answers' => array(
				0 => $characters[$others[0]]->characters[0],
				1 => $characters[$others[1]]->characters[0],
				2 => $characters[$others[2]]->characters[0],
				3 => $characters[$others[3]]->characters[0]
			),
			'correctAnswer' => $correctId[0]
		);
    }
}
