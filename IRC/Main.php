<?php

namespace IdnoPlugins\IRC {

    class Main extends \Idno\Common\Plugin {

	function send(array $userdetails, $message) {
	    $irc = new IRC();

	    $irc->connect($userdetails['server'], $userdetails['port']);
	    $irc->setUsername($userdetails['username'], $userdetails['password'] ? $userdetails['password'] : false);
	    //$irc->pingpong();
	    $irc->join($userdetails['channel']);
	    $written = $irc->privmesg($userdetails['channel'], $message);
	    $irc->disconnect();

	    
	    return $written;
	}

	function registerPages() {
	    // Register settings page
	    \Idno\Core\site()->addPageHandler('account/irc', '\IdnoPlugins\IRC\Pages\Account');

	    /** Template extensions */
	    // Add menu items to account & administration screens
	    \Idno\Core\site()->template()->extendTemplate('account/menu/items', 'account/irc/menu');
	}

	function registerEventHooks() {

	    // Register syndication services
	    \Idno\Core\site()->syndication()->registerService('irc', function () {
		return $this->hasIRC();
	    }, ['note', 'article', 'image']);

	    if ($this->hasIRC()) {
		if (is_array(\Idno\Core\site()->session()->currentUser()->irc)) {
		    foreach (\Idno\Core\site()->session()->currentUser()->irc as $id => $details) {
			if ($details['channel'] && $details['username'] && $details['server'] && $details['port'])
			    \Idno\Core\site()->syndication()->registerServiceAccount('irc', $id, $details['channel']);
		    }
		}
	    }

	    // Push "notes" to IRC
	    \Idno\Core\site()->addEventHook('post/note/irc', function (\Idno\Core\Event $event) {
		$eventdata = $event->data();
		$object = $eventdata['object'];
		if ($this->hasIRC()) {

		    if (!empty(\Idno\Core\site()->session()->currentUser()->irc[$eventdata['syndication_account']])) {

			$message = strip_tags($object->getDescription());

			if (!empty($message) && substr($message, 0, 1) != '@') {

			    try {

				$userdetails = \Idno\Core\site()->session()->currentUser()->irc[$eventdata['syndication_account']];

				$written = $this->send($userdetails, $message);

				if (!$written)
				    throw new \Exception('No data written to IRC channel ' . $userdetails['channel']);

				$link='#';
				if (strpos($userdetails['server'], '.freenode.')!==false) {
				    // We deduce we're on freenode, so use their webchat interface
				    $link = "https://webchat.freenode.net/?channels=" . trim($userdetails['channel'], ' #');
				}
				
				$object->setPosseLink('linkedin', $link, $userdetails['channel']); 
				$object->save();
				
			    } catch (\Exception $e) {
				\Idno\Core\site()->session()->addErrorMessage('There was a problem posting to IRC: ' . $e->getMessage());
			    }
			}
		    }
		}
	    });

	    // Push "articles" to IRC
	    \Idno\Core\site()->addEventHook('post/article/irc', function (\Idno\Core\Event $event) {
		$eventdata = $event->data();
		$object = $eventdata['object'];
		if ($this->hasIRC()) {
		    if (!empty(\Idno\Core\site()->session()->currentUser()->irc[$eventdata['syndication_account']])) {
			
			$message = htmlentities(strip_tags($object->getTitle())) . ': ' .htmlentities($object->getUrl());
			
			try {

			    $userdetails = \Idno\Core\site()->session()->currentUser()->irc[$eventdata['syndication_account']];

			    $written = $this->send($userdetails, $message);

			    if (!$written)
				throw new \Exception('No data written to IRC channel ' . $userdetails['channel']);

			    $link='#';
			    if (strpos($userdetails['server'], '.freenode.')!==false) {
				// We deduce we're on freenode, so use their webchat interface
				$link = "https://webchat.freenode.net/?channels=" . trim($userdetails['channel'], ' #');
			    }

			    $object->setPosseLink('linkedin', $link, $userdetails['channel']); 
			    $object->save();

			} catch (\Exception $e) {
			    \Idno\Core\site()->session()->addErrorMessage('There was a problem posting to IRC: ' . $e->getMessage());
			}
		    }
		}
	    });

	    // Push "images" to IRC
	    \Idno\Core\site()->addEventHook('post/image/irc', function (\Idno\Core\Event $event) {
		$eventdata = $event->data();
		$object = $eventdata['object'];
		if ($attachments = $object->getAttachments()) {
		    foreach ($attachments as $attachment) {
			if ($this->hasIRC()) {

			    if (!empty(\Idno\Core\site()->session()->currentUser()->irc[$eventdata['syndication_account']])) {
				$message = htmlentities(strip_tags($object->getTitle())) . ': ' .htmlentities($object->getUrl());
			
				try {

				    $userdetails = \Idno\Core\site()->session()->currentUser()->irc[$eventdata['syndication_account']];

				    $written = $this->send($userdetails, $message);

				    if (!$written)
					throw new \Exception('No data written to IRC channel ' . $userdetails['channel']);

				    $link='#';
				    if (strpos($userdetails['server'], '.freenode.')!==false) {
					// We deduce we're on freenode, so use their webchat interface
					$link = "https://webchat.freenode.net/?channels=" . trim($userdetails['channel'], ' #');
				    }

				    $object->setPosseLink('linkedin', $link, $userdetails['channel']); 
				    $object->save();

				} catch (\Exception $e) {
				    \Idno\Core\site()->session()->addErrorMessage('There was a problem posting to IRC: ' . $e->getMessage());
				}
			    }
			}
		    }
		}
	    });
	}

	/**
	 * Can the current user use IRC?
	 * @return bool
	 */
	function hasIRC() {
	    if (!(\Idno\Core\site()->session()->currentUser())) {
		return false;
	    }
	    if (\Idno\Core\site()->session()->currentUser()->irc) {
		return true;
	    }

	    return false;
	}

    }

}
