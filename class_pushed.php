<?php

class pushedmq extends  Mosquitto\Client {
	
public $topics;
public $parameters;
    
    function __construct($parameters,$topics) {
 	
    parent::__construct();
    $this->setCredentials($parameters["user"], $parameters["password"]);
    $this->connect($parameters["broker"], $parameters["port"], 60);
    $this->onConnect([$this,'oconnect']);
    $this->onMessage([$this, 'message']);
 
    foreach($topics as $topic) {
    	
	       $this->subscribe($topic["topic"],1);
	       
    }
	
	$this->topics = $topics;
	$this->parameters = $parameters;
	$this->loopForever(1000);
	
	
    }
	
	
	public function message($message) {

		 $ai = 0;
		 foreach($this->topics as $topic) {

		 	if($topic["topic"] == $message->topic)
		 	{    
		 		if (is_numeric($topic["condition_value"])) {
		 			
		 		$dyncode = '$c = ('.$message->payload.$topic["condition"].$topic["condition_value"].')'.' ? true : false;';
		 			
		 		} else {
		 			
		 		$dyncode = '$c = ("'.$message->payload.'"'.$topic["condition"].'"'.$topic["condition_value"].'")'.' ? true : false;';
		 		
		 		}
		 		
		 	
		 		eval($dyncode);
	             	 		
	
		 	    /*
		 		echo "-------------------------------\n";
		 	        echo "lastval:" . $this->topics[$ai]["lastval"] . "\n";
		 	        echo "\$c:" . $c . "\n";
		 		echo "\$topic once:".$topic["once"] . "\n";
		 		echo "-------------------------------\n";
		 	    */
		 		
		 		
		 		if ($this->topics[$ai]["lastval"] != $c && $topic["once"] == true) {
		 			
		 	         
		 	         $this->pushmsg($topic["message"], $message->payload);
		 			
		 		}
		 		else if($topic["once"] == false) {
		 			
		 		     
		 		     $this->pushmsg($topic["message"], $message->payload);
		 			
		 		}
		 		
		 		$this->topics[$ai]["lastval"] = $c;
		 		
		 	}
		 	$ai++;
		 }
		
		
	}

    
    public function oconnect($r) {
       echo "Received response code {$r}\n";
    }
    
    public function pushmsg($content, $value) {
    	 $ch = curl_init();
    	 
    
         
         $fields = array( 'app_key'=>$this->parameters["p_app_key"],
                          'app_secret'=>$this->parameters["p_app_secret"],
                          'target_type'=>$this->parameters["p_target_type"],
                          'target_alias'=>$this->parameters["p_target_alias"],
                          'content'=>$content . $value
                          );
         
    $postvars = '';
    foreach($fields as $key=>$value) {
    $postvars .= $key . "=" . $value . "&";
  }
  
  $url = "https://api.pushed.co/1/push";
  
  curl_setopt($ch,CURLOPT_URL,$url);
  curl_setopt($ch,CURLOPT_POST, 1);               
  curl_setopt($ch,CURLOPT_POSTFIELDS,$postvars);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);
  curl_setopt($ch,CURLOPT_TIMEOUT, 20);
  $response = curl_exec($ch);
  
  print "pushed reponse:" . $response;
  
  curl_close ($ch);
    }
}


?>
