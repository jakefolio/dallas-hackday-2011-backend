<?php
/**
* 
*/
class MusixMatch
{
	protected $apiKey = 'dcdbeb14bbe1cc0529d0067d151cb422';
	protected $baseURL = 'http://api.musixmatch.com/ws/1.1/';
	public $pageSize = 25;
	public $page = 1;
	
	public $format;
	public $country;
	
	public function __construct($format = 'php', $country = 'us')
	{
		$this->format = $format;
		$this->country = $country;
	}
	
	public function getCurrentUrl($method = 'track.chart.get')
	{
		return $this->baseURL . $method . "?format=json&apikey={$this->apiKey}&page={$this->page}&page_size={$this->pageSize}";
	}
	
	protected function query($url)
	{
		$session = curl_init($url);
        
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        $results = curl_exec($session);
        
		curl_close($session);
        
        return json_decode($results);
	}
	
	public function getLyrics($trackId)
	{
		$results = $this->query($this->getCurrentUrl('track.lyrics.get') . "&track_id={$trackId}");
		
		switch($this->format)
		{
			case 'json':
				return json_encode($results->message->body->lyrics->lyrics_body);
				
			case 'php':
			default:
				return $results->message->body->lyrics->lyrics_body;
		}
	}
	
	public function getTopCharts()
	{
		$results = $this->query($this->getCurrentUrl());
		
		switch($this->format)
		{
			case 'json':
				return json_encode($results->message->body->track_list);
				
			case 'php':
			default:
				return $results->message->body->track_list;
		}
	}
	
	public function getArtists($trackList = null) {
		if ($trackList === null) {
			$trackList = $this->getTopCharts();
		}
		$artists = array();
		foreach($trackList AS $song) {
			$artists[] = $song->track->artist_name;
		}
		
		return $artists;
	}
		
	public function getLyricChunk($lyric) {
		$lyricWords = explode("\n", $lyric);
		
		$chunks = array_chunk($lyricWords, 4);
		$segment = implode(' ', $chunks[rand(0, count($chunks)-1)]);
		
		return $segment;
	}
	
	public function getQuestion()
	{
		$trackList = $this->getTopCharts();
		
		$track = array_rand($trackList);
		$trackId = $trackList[$track]->track->track_id;
		$correct = $trackList[$track]->track->artist_name;
		unset($trackList[$track]);
		
		$lyric = $this->getLyrics($trackId);
		$lyricChunk = utf8_encode(utf8_decode($this->getLyricChunk($lyric)));
		
		$artists = $this->getArtists($trackList);
		$artists = array_slice($artists, 0, 3);
		$artists[] = $correct;
		
		shuffle($artists);
		
		$correctId = array_keys($artists, $correct);
		
		return array(
			'question' => "Which artist sang the following lyric: <br><span>{$lyricChunk}</span>",
			'photo' => null,
			'answers' => $artists,
			'correctAnswer' => $correctId[0]
		);
	}
}
