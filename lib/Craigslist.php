<?php

/**
* Scrape results from specific category
*/
class Craigslist
{
	protected $baseImage = 'http://images.craigslist.org/';
    public $category;
    public $query;
    public $location;
    
    public function __construct($category = 'sss', $location = 'dallas', $query = null)
    {
        $this->category = $category;
		$this->location = $location;
        $this->query = $query;
    }
    
    protected function getCurrentUrl()
    {
        if ($this->query !== null) {
            return "http://{$this->location}.craigslist.org/search/{$this->category}?query={$this->query}&srchType=A&minAsk=&maxAsk=";
        }
        
        return "http://{$this->location}.craigslist.org/{$this->category}";
    }
    
    public function getResults()
    {
        $results = array();
        
        $html = new DOMDocument();
		
        $html->loadHTML(file_get_contents($this->getCurrentUrl()));

		$xml = new DOMXpath($html);
		
		foreach($xml->query('//p[@class="row"]') as $list) {
			//echo $list->nodeValue;
			preg_match('/\$[0-9]+/', $list->nodeValue, $price) . '<br>';
			if (isset($price[0])) {
				$child = new DOMElement('price', $price[0]);
				$list->appendChild($child);
			}
			
		};

        $xml = simplexml_import_dom($html);

        $listings = $xml->xpath('//p[@class="row"]');

        $i = 0;
        foreach($listings as $listing) {
			// Check if a listing has an image
			if (strpos($listing->span[0]->attributes()->id, ':')) {
				// Strip the " -" from the end of the title
				$results[$i]['title'] = (string) $listing->a;
				$results[$i]['price'] = (string) $listing->price;
				// Get Image filename from HTML id
				$results[$i]['photo'] = 'http://images.craigslist.org/' . substr(
				  $listing->span[0]->attributes()->id, 
				  strpos($listing->span[0]->attributes()->id, ':')+1
				);
				$i++;
			}
        }
        
        return $results;
        
    }
    
    public function getQuestion()
    {
		$listings = $this->getResults();
		
		$total = count($listings);
		
		$listing = $listings[rand(0, $total-1)];
		
		$price = substr($listing['price'], 1, strlen($listing['price']) - 2);
		
		$correct = 0;
		if ($price <= 25) {
			$correct = 0;
		} elseif ($price <= 50 && $price >= 26) {
			$correct = 1;
		} elseif ($price <= 200 && $price >= 51) {
			$correct = 2;
		} else {
			$correct = 3;
		}
		
		return array(
			'question' => "How much is this item selling for on craigslist?",
			'photo' => $listing['photo'],
			'answers' => array(
				0 => '$0 - $25',
				1 => '$26 - $50',
				2 => '$51 - $200',
				3 => '$201+'
			),
			'correctAnswer' => $correct
		);
    }
}
