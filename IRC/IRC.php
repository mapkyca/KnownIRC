<?php

    namespace IdnoPlugins\IRC {
	
	class IRC {
	    
	    private $socket;
	    
	    protected function send($string) {
		if ($written = fwrite($this->socket, $string."\r\n")===false)
			throw new \Exception('There was a problem writing to IRC');
		
		return $written;
	    }
	    
	    function connect($server, $port = 6697) {
		$prefix = '';
		if ($port == 6697)
		    $prefix = 'tls://';
		
		if ($this->socket = fsockopen($prefix.$server, $port, $errno, $errstr)) {
		    throw new \Exception($errstr);
		}
			
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
		return fclose($this->socket);
	    }
	}
    }