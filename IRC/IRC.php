<?php

    namespace IdnoPlugins\IRC {
	
	class IRC {
	    
	    private $socket;
	    
	    protected function send($string) {
		if (!$this->socket) {
		    throw new \Exception("Not connected to IRC Server");
		}
		
		error_log("IRC > $string");
				
		$written = fwrite($this->socket, $string."\r\n");
		if ($written===false)
			throw new \Exception("There was a problem writing '$string' to IRC");
		
		// Give server a chance
		sleep(2); 
		
		return $written;
	    }
	    
	    /**
	     * Read a line from the IRC server, stripping the :server.domain.com portion
	     */
	    protected function readline() {
		if (!$this->socket) {
		    throw new \Exception("Not connected to IRC Server");
		}
		
		$string = fgets($this->socket);
		
		error_log("IRC < $string");
		
		//$result = explode(' ', $string, 2);
		//return trim($result[1]);
		return $string;
	    }
	    
	    function privmesg($target, $message) {
		return $this->send("PRIVMSG $target :$message");
	    }
	    
	    function connect($server, $port = 6697) {
		$prefix = '';
		if ($port == 6697)
		    $prefix = 'tls://';
		
		$this->socket = fsockopen($prefix.$server, $port, $errno, $errstr);

		if (!$this->socket) {
		    throw new \Exception("IRC Socket - $errstr");
		}
			
	    }

	    /**
	     * Do a PING/PONG
	     * @param string $string If null, this method will read from the socket until it gets a PING message.
	     */
	    function pingpong($string = null) {
		
		if (!$string) {
		    do {
			$string = $this->readline();
		    } while (strpos($string, 'PING')!==0);
		} 
		
		$components = explode(':', $string, 2);
		return $this->send("PONG :{$components[1]}");
	    }
	    
	    function join($channel) {
		$this->send("JOIN $channel");
	    }
	    
	    function setUsername($username, $password = false) {
		$this->send("USER $username $username bla :$username");
		$this->send("NICK $username");
		if ($password)
		    $this->send("PRIVMSG NickServ :IDENTIFY $username $password"); 
		
	    }
	    
	    function disconnect() {
		$this->send('QUIT');
		$return = fclose($this->socket);
		$this->socket = null;
		
		return $return;
	    }
	    
	}
    }