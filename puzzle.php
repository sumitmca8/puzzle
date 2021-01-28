<?php

class Puzzle{
	
	private $listOfWine;
	private $personWineWishlist;
	private	$wineAllotmentList;
    private	$totalWineSold;
	
	/**
     * Constructor 
     *
     * declare the data types of the variables.
     */
	 
	function __construct(){
	    
		$this->listOfWine 		      	= [];
		$this->personWineWishlist	   	= [];
		$this->wineAllotmentList  	   	= [];
		$this->totalWineSold 		   	= 0;
	}
    
	/**
     * function generateWineList
     *
     * This Function create the list of all the wines availables in Vineyards Shop and also create list of wishlist of wines.
     *
     */
	
	public function generateWineList($file_name){
	
	    $file = fopen($file_name,"r");
		while (($line = fgets($file)) !== false) {
		    $data = explode("\t", $line);
			if($data[0]!=''){
				$personName = trim($data[0]);
				$wineCode   = trim($data[1]);
				if(!array_key_exists($wineCode, $this->personWineWishlist)){
					$this->personWineWishlist[$wineCode] = [];
				}
				$this->personWineWishlist[$wineCode][] = $personName;
				$this->listOfWine[]=$wineCode;
			}
		} 
		fclose($file); 
		$this->listOfWine = array_unique($this->listOfWine);

		foreach ($this->listOfWine as $key => $wineCode){
		    foreach ($this->personWineWishlist[$wineCode] as $keys => $personCode){
		      	if(!array_key_exists($personCode, $this->wineAllotmentList)){
					$this->wineAllotmentList[$personCode][] = $wineCode;
					$this->totalWineSold++;
					break;
			   	}else{
					if(count($this->wineAllotmentList[$personCode])<3){
						$this->wineAllotmentList[$personCode][] = $wineCode;
						$this->totalWineSold++;
						break;
					}
			   }
			}
		}
	}
	
	/**
     * function exportWineAllotList
     *
     * This function is used to generate tsv file.
     *
     */
	public function exportWineAllotList($exportFilename){
	   
	    $file 		 = fopen($exportFilename, "w");
	    $heading     = "Total number of wines Sold by vineyards: ".$this->totalWineSold;
	    fwrite($file, $heading );

	    ksort($this->wineAllotmentList);
	    foreach (array_keys($this->wineAllotmentList) as $personCode=>$winelist){
			foreach ($this->wineAllotmentList[$personCode] as $key => $wineCode){
				fwrite($file, "\n".$personCode." \t ".$wineCode);
			}
		}
		fclose($file);
	}
}

$wine = new Puzzle;
$wine->generateWineList("https://s3.amazonaws.com/br-user/puzzles/person_wine_3.txt");
$wine->exportWineAllotList('puzzle_output.txt');