<?php
/**
* 
*/
class Twitter
{
	protected $baseUrl = 'http://api.twitter.com/1/users/lookup.json?&count=1';
	protected $userList = array(
		'realtracymorgan',
		'jakefolio',
		'jimmyfallon',
		'shaq',
		'iamdiddy',
		'lancearmstrong',
		'officialTila',
		'aplusk',
		'hodgman',
		'azizansari'
	);
	
	protected $format;
	
	public function __construct($format = 'json', $users = array())
	{
		$this->userList = array_merge($this->userList, $users);
		$this->format = $format;
	}
	
	protected function getUser($screenName)
	{
		$url = $this->baseUrl . "&screen_name={$screenName}";
		
		$session = curl_init($url);
        
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        
        $results = curl_exec($session);
        
        curl_close($session);
        
        return json_decode($results);
	}
	
	public function getQuestion()
	{
		$choices = array_rand($this->userList, 4);
		$celebs = array();
		foreach ($choices AS $celeb) {
			$celebs[] = $this->getUser($this->userList[$celeb]);
		}
		
		$correctId = array_rand($choices);
		$correct = $this->getUser($this->userList[$correctId]);
		$tweet = $correct[0]->status->text;
		
		return array(
			'question' => "Which celebrity said the following: <br>{$tweet}",
			'photo' => null,
			'answers' => array_map(function($v) {
				return $v[0]->name;
			}, $celebs),
			'correctAnswer' => $correctId;
		);
	}
}
