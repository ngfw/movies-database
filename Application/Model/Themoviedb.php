<?php

/**
 * Themoviedb
 * @project Movie Database 2
 * @copyright (c) 2013, Nick Gejadze
 */
class Themoviedb extends ngfw\Httpclient{
	const classVersion = "2.0";
	protected $protocol = "http://";
	protected $apiUrl = "api.themoviedb.org";
	protected $apiVersion = "3";
	protected $language = "en"; //(ISO 3166-1)
	protected $apiKey;
	protected $cacheEnabled;
	protected $cache;
	protected $cacheTime = 86400;
	protected $apiConfiguration;
	
	public function __construct($key){
		$this->apiKey = $key;
		$requestedLanugage = ngfw\Registry::get('requestedLanuage');
		if(isset($requestedLanugage) and !empty($requestedLanugage) and strlen($requestedLanugage) == 2 ):			
			$this->setLanguage(strtolower($requestedLanugage));
		endif;		
	}

	

	public function setLanguage($lang){
		$this->language = $lang;
	}

	/** Configuration **/

	/**
	* Get the system wide configuration information. Some elements of the API require some knowledge of this configuration data. 
	* The purpose of this is to try and keep the actual API responses as light as possible. 
	* It is recommended you store this data within your application and check for updates every so often.
	* This method currently holds the data relevant to building image URLs as well as the change key map.
	* To build an image URL, you will need 3 pieces of data. The base_url, size and file_path. 
	* Simply combine them all and you will have a fully qualified URL. Here’s an example URL:
	* @access public
	* @return array|null
	*/
	public function getConfiguration(){
		return $this->getData("/configuration");
	}

	
	/** Movies **/

	/**
	* Get the basic movie information for a specific movie id.
	* @access public
	* @return array|null
	*/
	public function getMovie($movieID){
		return $this->getData("/movie/".$movieID);
	}

	/**
	* Get the alternative titles for a specific movie id.
	* @access public
	* @return array|null
	*/
	public function getMovieAlternativeTitles($movieID){
		return $this->getData("/movie/".$movieID."/alternative_titles");
	}

	/**
	* Get the cast information for a specific movie id.
	* @access public
	* @return array|null
	*/
	public function getMovieCasts($movieID){
		return $this->getData("/movie/".$movieID."/casts");
	}

	/**
	* Get the images (posters and backdrops) for a specific movie id.
	* @access public
	* @return array|null
	*/
	public function getMovieImages($movieID){
		return $this->getData("/movie/".$movieID."/images");
	}	

	/**
	* Get the plot keywords for a specific movie id.
	* @access public
	* @return array|null
	*/
	public function getMovieKeywords($movieID){
		return $this->getData("/movie/".$movieID."/keywords");
	}

	/**
	* Get the release date by country for a specific movie id.
	* @access public
	* @return array|null
	*/
	public function getMovieReleases($movieID){
		return $this->getData("/movie/".$movieID."/releases");
	}

	/**
	* Get the trailers for a specific movie id.
	* @access public
	* @return array|null
	*/
	public function getMovieTrailers($movieID){
		return $this->getData("/movie/".$movieID."/trailers");
	}

	/**
	* Get the translations for a specific movie id.
	* @access public
	* @return array|null
	*/
	public function getMovieTranslations($movieID){
		return $this->getData("/movie/".$movieID."/translations");
	}

	/**
	* Get the similar movies for a specific movie id.
	* @access public
	* @return array|null
	*/
	public function getMovieSimilarMovies($movieID, $page=1){
		$params = array(			
			'page' => (int) $page			
		);
		return $this->getData("/movie/".$movieID."/similar_movies", $params);
	}

	/**
	* Get the lists that the movie belongs to.
	* @access public
	* @return array|null
	*/
	public function getMovieLists($movieID, $page=1){
		$params = array(			
			'page' => (int) $page			
		);
		return $this->getData("/movie/".$movieID."/lists", $params);
	}

	/**
	* Get the changes for a specific movie id.
	* Changes are grouped by key, and ordered by date in descending order. 
	* By default, only the last 24 hours of changes are returned. 
	* The maximum number of days that can be returned in a single request is 14. 
	* The language is present on fields that are translatable.
	* @access public
	* @return array|null
	*/
	public function getMovieChanges($movieID, $startDate, $endDate){
		$params = array(			
			'start_date' => $startDate,
			'end_date'	=> $startDate
		);
		return $this->getData("/movie/".$movieID."/changes", $params);
	}

	/**
	* Get the latest movie id.
	* @access public
	* @return array|null
	*/
	public function getMovieLatest(){
		return $this->getData("/movie/upcoming", $params);
	}

	/**
	* Get the list of upcoming movies. This list refreshes every day(On Themoviedb.org). 
	* The maximum number of items this list will include is 100.
	* @access public
	* @return array|null
	*/
	public function getUpcomingMovies($page = 1){
		$params = array(			
			'page' => (int) $page			
		);
		return $this->getData("/movie/upcoming", $params);
	}

	/**
	* Get the list of movies playing in theatres. This list refreshes every day. 
	* The maximum number of items this list will include is 100.
	* @access public
	* @return array|null
	*/
	public function getNowPlaying($page = 1){
		$params = array(			
			'page' => (int) $page
		);
		return $this->getData("/movie/now_playing", $params);
	}

	/**
	* Get the list of popular movies on The Movie Database. This list refreshes every day.
	* @access public
	* @return array|null
	*/
	public function getPopular($page = 1){
		$params = array(			
			'page' => (int) $page
		);
		return $this->getData("/movie/popular", $params);
	}

	/**
	* Get the list of top rated movies. By default, this list will only include movies that have 10 or more votes. 
	* This list refreshes every day.
	* @access public
	* @return array|null
	*/
	public function getTopRated($page = 1){
		$params = array(			
			'page' => (int) $page
		);
		return $this->getData("/movie/top_rated", $params);
	}

	/** Collection **/

	/**
	* Get the basic collection information for a specific collection id. You can get the ID needed for this method by making a /movie/{id} 
	* request and paying attention to the belongs_to_collection hash.
	* Movie parts are not sorted in any particular order. If you would like to sort them yourself you can use the provided release_date.
	* @access public
	* @return array|null
	*/
	public function getCollection($collectionID){
		return $this->getData("/collection/" . $collectionID);
	}

	/**
	* Get the basic collection information for a specific collection id. You can get the ID needed for this method by making a /movie/{id} 
	* request and paying attention to the belongs_to_collection hash.
	* Movie parts are not sorted in any particular order. If you would like to sort them yourself you can use the provided release_date.
	* @access public
	* @return array|null
	*/
	public function getCollectionImageS($collectionID){
		return $this->getData("/collection/" . $collectionID."/images");
	}

	/** People **/

	/**
	* Get the general person information for a specific id.
	* @access public
	* @return array|null
	*/
	public function getPerson($personID){
		return $this->getData("/person/" . $personID);
	}

	/**
	* Get the images for a specific person id.
	* @access public
	* @return array|null
	*/
	public function getPersonImages($personID){
		return $this->getData("/person/" . $personID."/images");
	}



	/**
	* Get the credits for a specific person id.
	* @access public
	* @return array|null
	*/
	public function getPersonCredits($personID){
		return $this->getData("/person/" . $personID."/credits");
	}

	/**
	* Get the changes for a specific person id.
	* Changes are grouped by key, and ordered by date in descending order. 
	* By default, only the last 24 hours of changes are returned. 
	* The maximum number of days that can be returned in a single request is 14. The language is present on fields that are translatable.
	* @access public
	* @return array|null
	*/
	public function getPersonChanges($personID, $startDate, $endDate){
		$params = array(			
			'start_date' => $startDate,
			'end_date'	=> $startDate
		);
		return $this->getData("/person/".$personID."/changes", $params);
	}

	/**
	* Get the list of popular people on The Movie Database. This list refreshes every day.
	* @access public
	* @return array|null
	*/
	public function getPopularPersons($page = 1){
		$params = array(			
			'page' => (int) $page
		);
		return $this->getData("/person/popular", $params);
	}

	/**
	* Get the list of popular people on The Movie Database. This list refreshes every day.
	* @access public
	* @return array|null
	*/
	public function getPersonLatest(){		
		return $this->getData("/person/latest");
	}

	/** LIST **/
	/**
	* Get the list of popular people on The Movie Database. This list refreshes every day.
	* @access public
	* @return array|null
	*/
	public function getList($listID){		
		return $this->getData("/list/".$listID);
	}

	/** Companies **/
	/**
	* This method is used to retrieve all of the basic information about a company.
	* @access public
	* @return array|null
	*/
	public function getCompany($companyID){
		return $this->getData("/company/".$companyID);
	}

	/**
	* Get the list of movies associated with a particular company.
	* @access public
	* @return array|null
	*/
	public function getCompanyMovies($companyID){
		return $this->getData("/company/".$companyID."/movies");
	}

	/** Genres **/
	/**
	* Get the list of genres.
	* @access public
	* @return array|null
	*/
	public function getGenres(){
		return $this->getData("/genre/list");
	}

	/**
	* Get the list of movies for a particular genre by id. By default, only movies with 10 or more votes are included.
	* @access public
	* @return array|null
	*/
	public function getGenreMovies($genreID, $page=1, $inludeAllMovies = "false", $includeAdult = "false"){
		$params = array(			
			'page' => $page,
			'include_all_movies' => $inludeAllMovies,
			'include_adult' => $includeAdult
		);
		return $this->getData("/genre/".$genreID."/movies", $params);
	}

	/** Keywords **/

	/**
	* Get the basic information for a specific keyword id.
	* @access public
	* @return array|null
	*/
	public function getKeywords($keywordID){
		return $this->getData("/keyword/" . $keywordID);
	}

	/**
	* Get the list of movies for a particular keyword by id.
	* @access public
	* @return array|null
	*/
	public function getKeywordsMovies($keywordID){
		return $this->getData("/keyword/" . $keywordID . "/movies");
	}

	/**
	* Search for movies by title.
	* @access public
	* @return array|null
	*/
	public function search($query, $page=1, $adult="false", $year = null){
		$params = array(
			'query' => $query,
			'page' => $page,
			'include_adult' => $adult,
			'year' => $year
		);
		return $this->getData("/search/movie", $params);
	}

	/**
	* Search for collections by name.
	* @access public
	* @return array|null
	*/
	public function searchCollection($query, $page=1){
		$params = array(
			'query' => $query,
			'page' => $page
		);
		return $this->getData("/search/collection", $params);
	}

	/**
	* Search for lists by name and description.
	* @access public
	* @return array|null
	*/
	public function searchList($query, $page=1, $adult="false"){
		$params = array(
			'query' => $query,
			'page' => $page,
			'include_adult' => $adult,
		);
		return $this->getData("/search/list", $params);
	}

	/**
	* Search for companies by name.
	* @access public
	* @return array|null
	*/
	public function searchCompany($query, $page=1){
		$params = array(
			'query' => $query,
			'page' => $page
		);
		return $this->getData("/search/company", $params);
	}

	/**
	* Search for person by name.
	* @access public
	* @return array|null
	*/
	public function searchPerson($query, $page=1, $adult="false"){
		$params = array(
			'query' => $query,
			'page' => $page,
			'include_adult' => $adult
		);
		return $this->getData("/search/person", $params);
	}

	/**
	* Search for keywords by name.
	* @access public
	* @return array|null
	*/
	public function searchkeyword($query, $page=1){
		$params = array(
			'query' => $query,
			'page' => $page
		);
		return $this->getData("/search/keyword", $params);
	}

	/**
	 *
	 * @access public
	 * @param $page	Minimum value is 1, expected value is an integer.
	 * @param $sort_by	Available options are vote_average.desc, vote_average.asc, release_date.desc, release_date.asc, popularity.desc, popularity.asc
	 * @param $include_adult	Toggle the inclusion of adult titles. Expected value is a boolean, true or false
	 * @param $year	Filter the results release dates to matches that include this value. Expected value is a year.
	 * @param $primary_release_year	Filter the results so that only the primary release date year has this value. Expected value is a year.
	 * @param $vote_count_gte	Only include movies that are equal to, or have a vote count higher than this value. Expected value is an integer.
	 * @param $vote_average_gte	Only include movies that are equal to, or have a higher average rating than this value. Expected value is a float.
	 * @param $with_genres	Only include movies with the specified genres. Expected value is an integer (the id of a genre). Multiple values can be specified. Comma separated indicates an 'AND' query, while a pipe (|) separated value indicates an 'OR'.
	 * @param $release_date_gte	The minimum release to include. Expected format is YYYY-MM-DD.
	 * @param $release_date_lte	The maximum release to include. Expected format is YYYY-MM-DD.
	 * @param $certification_country	Only include movies with certifications for a specific country. When this value is specified, 'certification.lte' is required. A ISO 3166-1 is expected.
	 * @param $certification_lte	Only include movies with this certification and lower. Expected value is a valid certification for the specificed 'certification_country'.
	 * @param $with_companies	Filter movies to include a specific company. Expected valu is an integer (the id of a company). They can be comma separated to indicate an 'AND' query.
	 * @return array | null
	 */
	public function discover($page=1,$sort_by,$include_adult,$year,$primary_release_year,$vote_count_gte,$vote_average_gte,$with_genres,$release_date_gte,$release_date_lte,$certification_country,$certification_lte,$with_companies){
		$params['page'] = $page;
		if(isset($sort_by) and !empty($sort_by)):
			$params['sort_by'] = $sort_by;
		endif;
		if(isset($include_adult) and !empty($include_adult)):
			$params['include_adult'] = $include_adult;
		endif;
		if(isset($year) and !empty($year)):
			$params['year'] = $year;
		endif;
		if(isset($primary_release_year) and !empty($primary_release_year)):
			$params['primary_release_year'] = $primary_release_year;
		endif;
		if(isset($vote_count_gte) and !empty($vote_count_gte)):
			$params['vote_count.gte'] = $vote_count_gte;
		endif;
		if(isset($vote_average_gte) and !empty($vote_average_gte)):
			$params['vote_average.gte'] = $vote_average_gte;
		endif;
		if(isset($with_genres) and !empty($with_genres)):
			$params['with_genres'] = $with_genres;
		endif;
		if(isset($release_date_gte) and !empty($release_date_gte)):
			$params['release_date.gte'] = $release_date_gte;
		endif;
		if(isset($release_date_lte) and !empty($release_date_lte)):
			$params['release_date.lte'] = $release_date_lte;
		endif;
		if(isset($certification_country) and !empty($certification_country)):
			$params['certification_country'] = $certification_country;
		endif;
		if(isset($certification_lte) and !empty($certification_lte)):
			$params['certification.lte'] = $certification_lte;
		endif;
		if(isset($with_companies) and !empty($with_companies)):
			$params['with_companies'] = $with_companies;
		endif;
		return $this->getData("/discover/movie", $params);
	}

	/** Movie Database Pages **/
	public function getMoviePage($movieID){
		//This method does not return Images for translated page
		/*
		$params = array(
			'append_to_response' => 'casts,images,keywords,similar_movies'
		);
		return $this->getData("/movie/".$movieID, $params);
		*/

		$params = array(
			'append_to_response' => 'casts,images,keywords,similar_movies'
		);
		$data = $this->getData("/movie/".$movieID, $params);
		//hack it
		$this->language = "en";
		$data['images'] = $this->getData("/movie/".$movieID."/images");
		return $data;
	}

	/** People Pages **/
	public function getPersonPage($personID){
		$params = array(
			'append_to_response' => 'images,credits'
		);
		return $this->getData("/person/" . $personID, $params);
	}
	
	/** Fetch Themoviedb.org **/
	private function getData($type, $params = array()){
		$array_to_add = array(
			"api_key" => $this->apiKey
		);
		if($this->language != "en"):
			$array_to_add = array_merge(array("language" => $this->language), $array_to_add);
		endif;
		$array_to_request = array_merge($array_to_add, $params);
		$uri = $this->protocol.$this->apiUrl."/".$this->apiVersion.$type."?".http_build_query($array_to_request, '', '&');
		$this->setUri($uri);
		$request = $this->request();
		$response = $request["content"];
		return json_decode($response, true);
	}

}
