<?php
/*
 *
 *  PhpFunQuery developed by Rahmat Adistia
 *  Email : rahmatadistia@gmail.com
 *  Jumat 23 Februari 2018
 *
 * */

class PhpFunQuery {

    private $conn;

    function __construct($host='',$username='',$password='',$db='')
    {
	if ( $host == '' ||  $username == '' || $db == '') $this->conn = false;  
	else $this->conn = new mysqli($host,$username,$password,$db);
    }

    public function __call($method='',$args=array())
    {
	if ($this->conn == false) return die('not-available');

	preg_match_all('/((?:^|[A-Z])[a-z]+)/',$method,$params);
	if (count($params)==0) return die('Format Salah');
	$calls = $params[0];

	if (! $calls[0] == 'get') return die('Format Salah');
	$table = $calls[1];

	$sql   = "SELECT * FROM ".$table;
	if (count($calls) > 2) { 

            $sqlString = '';
            if ($calls[2] == 'Filter') $sqlString = $sqlString . " WHERE ";

	    if (count($calls)==3) return die('Format Salah');
         
	    $counter_args = 0;
	    for($i = 3; $i<count($calls); $i++ ) {
                
		if ( isset($args[$counter_args])) {
                    if ($calls[$i] == 'And') $sqlString = $sqlString . ' and ';
		    else if ($calls[$i] == 'Or') $sqlString .= ' or ';
		    else {
                        $arg_char = str_split($args[$counter_args]);

			if ($arg_char[0] == '%') {
		            array_splice($arg_char,0,1);
                            $sqlString .= ($calls[$i] . " LIKE '%".join('',$arg_char)."%'");
			} else if ($arg_char[0] == '!') {
		            array_splice($arg_char,0,1);
				$sqlString .= ($calls[$i] . " != '".join('',$arg_char)."'");
			} else
				$sqlString .= ($calls[$i] . " = '".$args[$counter_args]."'");

			$counter_args++;

		    }
		}
	    }
	    
            $sql .= $sqlString;

	}
	
	$query = $this->conn->query($sql);
	$result = array() ;
	if(isset($query) && $query->num_rows > 0){
            while($row = $query->fetch_assoc()){
		$temp = array();
                foreach($row as $key => $value) {
                    $temp[$key] = $value;
		}
		$result[] = (object)$temp;
            }
	}
	return $result;
    }

}

?>
