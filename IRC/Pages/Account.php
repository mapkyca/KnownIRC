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
		    
		    $id = sha1($server.$channel.$username);
		    
		    $user = \Idno\Core\site()->session()->currentUser();
		    
		    if (!isset($user->irc) || !is_array($user->irc)) {
			$user->irc = [];
		    }
		    
		    $user->irc[$id] = [
			'id' => $id,
			'server' => $server,
			'channel' => $channel,
			'username' => $username
		    ];
		    
		    $user->save();
		}
                $this->forward('/account/irc/');
            }

        }

    }