<?php namespace Plugins\Commands\Command;

use Dan\Core\Dan;
use Dan\Irc\Channel;
use Dan\Irc\User;
use Plugins\Commands\CommandInterface;

class Plugin implements CommandInterface {

    /**
     * Runs the command.
     *
     * @param \Dan\Irc\Channel $channel
     * @param \Dan\Irc\User    $user
     * @param                  $message
     *
     * @return void
     */
    public function run(Channel $channel, User $user, $message)
    {
        $data = explode(' ', $message);

        try
        {
            switch ($data[0])
            {
                case 'load':
                    Dan::getApp('pluginManager')->loadPlugin($data[1]);
                    break;

                case 'reload':
                    Dan::getApp('pluginManager')->unloadPlugin($data[1]);
                    Dan::getApp('pluginManager')->loadPlugin($data[1]);
                    break;

                case 'unload':
                    Dan::getApp('pluginManager')->unloadPlugin($data[1]);
                    break;
            }

        }
        catch(\Exception $e)
        {
            $user->sendNotice($e->getMessage());
        }
    }

}