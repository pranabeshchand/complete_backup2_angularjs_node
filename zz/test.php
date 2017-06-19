<?php
function multiexplode ($string) {
	$result = '';
   $delimiters = array("<img","src","<iframe");
    $ready = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);
    foreach($launch as $explodeds){
	if(!empty($explodeds)){
		$aa = explode('"', $explodeds);
		// print_r($aa);
		foreach($aa as $aas){
			if(!empty($aas)){
				if(pathinfo($aas, PATHINFO_EXTENSION)){
					$result .= $aas;
					// echo $aas; echo "<br/>";
				}elseif (!filter_var($aas, FILTER_VALIDATE_URL) === false) {
					$result .= " ".$aas;
					// echo $aas; echo "<br/>";
				}
			}
		}
		// print_r($aa);
		// print_r($explodeds); echo "<br/>";
	}
	
}
    return  $result;
}

$text = 'here is a sample: this text, and this will be exploded. this also | this one too   <img src="aa.png" > <iframe width="560" height="315" src="https://www.youtube.com/embed/-zW1zHqsdyc" frameborder="0" allowfullscreen></iframe> xcvssfsdf';
// preg_match_all('/<img[^>]+>/i',$text, $result); 
//   print_r( $result); exit;
$exploded = multiexplode($text);
echo $exploded;
// print_r($exploded);

