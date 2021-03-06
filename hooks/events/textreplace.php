<?php

use Carbon\Carbon;
use Dan\Irc\Location\Channel;
use Dan\Irc\Location\User;

hook('textreplace')
    ->on('irc.packets.message.public')
    ->anon(new class {
        /** @var array  */
        protected $messages = [];

        /**
         * @param \Dan\Events\EventArgs $eventArgs
         * @return null
         */
        public function run(\Dan\Events\EventArgs $eventArgs)
        {
            $message = $eventArgs->get('message');

            /** @var Channel $channel */
            $channel = $eventArgs->get('channel');

            if (!preg_match("/^s\/([^\/]+)\/([^\/]+)?(?:\/(g)?)?/i", $message, $matches)) {
                $this->addLine($channel, $eventArgs->get('user'), $message);

                return null;
            }

            if (count($matches) < 2) {
                return false;
            }

            $key = connection()->getName() . ':' . $channel->getLocation();

            $messages = $this->messages[$key];

            krsort($messages);

            $global = isset($matches[3]) ? $matches[3] == 'g' : false;

            foreach ($messages as $time => $data) {
                $new = preg_replace("/{$matches[1]}/", ($matches[2] ?? ''), $data['message'], ($global ? -1 : 1));

                if ($new == $data['message']) {
                    continue;
                }

                $this->messages[$key][$time]['message'] = $new;

                /** @var Carbon $carbon */
                $carbon = $data['carbon'];
                $ago    = $carbon->diffForHumans();

                $channel->message("[ <cyan>{$ago}</cyan> ] {$data['user']}: {$new}");

                return false;
            }
        }

        /**
         * @param \Dan\Irc\Location\Channel $channel
         * @param \Dan\Irc\Location\User $user
         * @param $message
         */
        public function addLine(Channel $channel, User $user, $message)
        {
            $key = connection()->getName() . ':' . $channel->getLocation();

            foreach ($this->messages as $chan => $lines) {
                if (count($lines) > 30) {
                    array_shift($this->messages[$chan]);
                }
            }

            $this->messages[$key][time()] = [
                'message'   => $message,
                'user'      => $user->getLocation(),
                'carbon'    => new Carbon(),
            ];
        }
    });