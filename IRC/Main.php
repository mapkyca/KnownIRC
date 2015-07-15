<?php

    namespace IdnoPlugins\IRC {

        class Main extends \Idno\Common\Plugin
        {
            function registerPages()
            {
                // Register settings page
                \Idno\Core\site()->addPageHandler('account/irc', '\IdnoPlugins\IRC\Pages\Account');

                /** Template extensions */
                // Add menu items to account & administration screens
                \Idno\Core\site()->template()->extendTemplate('account/menu/items', 'account/irc/menu');
            }

            function registerEventHooks()
            {

                // Register syndication services
                \Idno\Core\site()->syndication()->registerService('irc', function () {
                    return $this->hasIRC();
                }, ['note','article','image']);

                if ($this->hasIRC()) {
                    if (is_array(\Idno\Core\site()->session()->currentUser()->irc)) {
                        foreach (\Idno\Core\site()->session()->currentUser()->irc as $id => $details) {
                            if ($id != 'access_token') {
                                \Idno\Core\site()->syndication()->registerServiceAccount('irc', $id, $details['name']);
                            } else {
                                \Idno\Core\site()->syndication()->registerServiceAccount('irc', $id, 'IRC');
                            }
                        }
                    }
                }

                // Push "notes" to IRC
                \Idno\Core\site()->addEventHook('post/note/irc', function (\Idno\Core\Event $event) {
                    $eventdata = $event->data();
                    $object    = $eventdata['object'];
                    if ($this->hasIRC()) {
                        if ($ircAPI = $this->connect($eventdata['syndication_account'])) {
                            if (!empty(\Idno\Core\site()->session()->currentUser()->irc[$eventdata['syndication_account']]['name'])) {
                                $name = \Idno\Core\site()->session()->currentUser()->irc[$eventdata['syndication_account']]['name'];
                            } else {
                                $name = 'IRC';
                            }
                            $message = strip_tags($object->getDescription());
                            //$message .= "\n\n" . $object->getURL();
                            if (!empty($message) && substr($message, 0, 1) != '@') {

                                try {


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
                    $object    = $eventdata['object'];
                    if ($this->hasIRC()) {
                        if ($ircAPI = $this->connect($eventdata['syndication_account'])) {

                            if (!empty(\Idno\Core\site()->session()->currentUser()->irc[$eventdata['syndication_account']]['name'])) {
                                $name = \Idno\Core\site()->session()->currentUser()->irc[$eventdata['syndication_account']]['name'];
                            } else {
                                $name = 'IRC';
                            }


                        }
                    }
                });

                // Push "images" to IRC
                \Idno\Core\site()->addEventHook('post/image/irc', function (\Idno\Core\Event $event) {
                    $eventdata = $event->data();
                    $object    = $eventdata['object'];
                    if ($attachments = $object->getAttachments()) {
                        foreach ($attachments as $attachment) {
                            if ($this->hasIRC()) {

                                if ($ircAPI = $this->connect($eventdata['syndication_account'])) {

                                    if (!empty(\Idno\Core\site()->session()->currentUser()->irc[$eventdata['syndication_account']]['name'])) {
                                        $name = \Idno\Core\site()->session()->currentUser()->irc[$eventdata['syndication_account']]['name'];
                                    } else {
                                        $name = 'IRC';
                                    }

                                    

                                }
                            }
                        }
                    }
                });
            }

            /**
             * Can the current user use Linkedin?
             * @return bool
             */
            function hasIRC()
            {
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
