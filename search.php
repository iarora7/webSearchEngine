<?php 
  
	header('Content-Type: text/html; charset=utf-8'); 	 
	$limit = 10;  
	$query = isset($_REQUEST['q']) ? $_REQUEST['q'] : false; 
	$results = false; 
 
	if ($query) 
	{  
	  require_once('Apache/Solr/Service.php'); 
	 
	  // create a new solr service instance O host, port, and corename 
	  // path (all defaults in this example) 
	  $solr = new Apache_Solr_Service('localhost', 8983, '/solr/isha/'); 
	 
	  // if magic quotes is enabled then stripslashes will be needed 
	  if (get_magic_quotes_gpc() == 1) 
	  { 
	    $query = stripslashes($query); 
	  } 

	  // in production code you'll always want to use a try /catch for any    
	  // possible exceptions emitted  by searching (i.e. connection 
	  // problems or a query parsing error) 
	  try 
	  { 
	  	if(isset($_REQUEST['file']))
	  {
	  	$query1 = $query;
	  	$add=array('sort'=>'pageRankFile desc');
		$results = $solr->search($query1, 0, $limit,$add);
	  	#$results = $solr->search($query1, 0, $limit); 
	  } else 
	  {
	  	$results = $solr->search($query, 0, $limit); 
	  }
	 
	    
	  } 
	  catch (Exception $e) 
	  {       
	    die("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");    
	  } 
	} 
	 
	?> 
	<html> 
	  <head> 
	    <title>PHP Solr Client Example</title> 
	  </head> 
	  <body> 
	    <form  accept-charset="utf-8" method="get"> 
	      <label for="q">Search:</label>        
	      <input id="q" name="q" type="text" value="<?php echo htmlspecialchars($query, ENT_QUOTES, 'utf-8'); ?>"/>        
	      <br><br>
	      Sort according to Page Rank <input type="radio" name="file" value="file"><br><br>
	      <input type="submit"/> <br>
	    </form> 
	<?php 
	// display results 
	if ($results) 
	{ 
	  $total = (int) $results->response->numFound; 
	  $start = min(1, $total); 
	  $end = min($limit, $total); 
	?>      
	<div>Results <?php echo $start; ?> - <?php echo $end;?> of <?php echo $total; ?>:</div>      
	<ol> 
	<?php 
	  // iterate result documents 
	  foreach ($results->response->docs as $doc) 
	  { 
	?> 
	      <li> 
	<?php 
	    // iterate document fields / values 
		$docId = "N/A";
		$docTitle = "N/A";
		$docSize = "N/A";
		$docAuthor = "N/A";
		$docCreateDate = "N/A";
	    foreach ($doc as $field => $value) 
	    { 
	    	if($field == "id"){
	    		$docId = $value; // crop "/Users/isha/solr-5.3.1/crawl_data/" and use rest as hyperlink
	    		if(substr( $docId, 0, 34 ) === "/Users/isha/solr-5.3.1/crawl_data/") {
	    			echo "doc in crawl data"   ; 			
	    		} elseif {
	    			echo "Doc not in crawl data";
	    		}
	
	    		$docId = substr($docId,34);	
	    	} elseif ($field == "dc_title") {
	    		$docTitle = $value;		// Use as Title of document
	    	} elseif ($field == "stream_size") {
	    		$docSize = $value;
	    		$docSize /= 1024;
	    		$docSize = round($docSize,3);
	    	} elseif ($field == "author") {
	    		$docAuthor = $value;
	    	}
	
	    }
		$docId = str_replace("@","/",strstr($docId, "@.html", true));
	     echo "<a href='$docId'>Document</a> ".$docTitle ."<br> <br>";
	     echo "Size: " . $docSize."KB; Author: ". $docAuthor. "; date_created: ". $docCreateDate."<br><br>";
	?> 
	      </li> 
	<?php 
	  } 
	?> 
	    </ol> 
	<?php 
	} 
	?> 
	  </body> 
	</html> 