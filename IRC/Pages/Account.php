<?php

    /**
     * IRC pages
     */

    namespace IdnoPlugins\IRC\Pages {

        /**
         * Default class to serve IRC-related account settings
         */
        class Account extends \Idno\Common\Page
        {

            function getContent()
            {
                $this->gatekeeper(); // Logged-in users only
                
                $t = \Idno\Core\site()->template();
                $body = $t->draw('account/irc');
                $t->__(['title' => 'IRC', 'body' => $body])->drawPage();
            }

            function postContent() {
                $this->gatekeeper(); // Logged-in users only
                if (($id = $this->getInput('remove'))) {
                    $user           = \Idno\Core\site()->session()->currentUser();
                    if (array_key_exists($id, $user->irc)) {
                        unset($user->irc[$id]);
                    } else {
                        $user->irc = [];
                    }
                    $user->save();
                    \Idno\Core\site()->session()->addMessage('Your IRC settings have been removed from your account.');
                } else {
		    
		    $server = trim($this->getInput('server'));
		    $channel = trim($this->getInput('channel'));
		    $username = trim($this->getInput('username'));
		    $password = trim($this->getInput('password'));
		    $port = 6697;
		    
		    // Extract port
		    $channelport = explode(':', $channel);
		    $channel = $channelport[0];
		    if (isset($channelport[1]))
			$port = $channelport[1];
		    
		    $id = sha1($server.$port.$channel.$username);
		    
		    $user = \Idno\Core\site()->session()->currentUser();
		    
		    if (!isset($user->irc) || !is_array($user->irc)) {
			$user->irc = [];
		    }
		    
		    $user->irc[$id] = [
			'id' => $id,
			'server' => $server,
			'channel' => $channel,
			'username' => $username,
			'password' => $password,
			'port' => $port
		    ];
		    
		    $user->save();
		}
                $this->forward('/account/irc/');
            }

        }

    }