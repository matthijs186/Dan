<?php namespace Dan\Irc\Location;


use Dan\Irc\ModeObject;
use Illuminate\Support\Collection;

class Channel extends Location {

    /** @var User[]|Collection $users */
    protected $users;

    public function __construct($name)
    {
        parent::__construct();

        $this->users = new Collection();
        $this->name     = $name;
        $this->location = $name;

        database()->insertOrUpdate('channels', ['name' => $name], [
           'name'   => $name
        ]);
    }

    public function setTopic($topic)
    {
        send("TOPIC", $this->name, $topic);
    }

    /**
     * Gets a user.
     *
     * @param $user
     * @return \Dan\Irc\Location\User|null
     */
    public function getUser($user)
    {
        if($user instanceof User)
            $user = $user->getLocation();

        if(!$this->users->has($user))
            return null;

        $info = $this->users->get($user);

        $obj = user($info['nick']);

        $obj->setMode($info['modes']);

        return $obj;
    }

    /**
     * Removes a user.
     *
     * @param $user
     */
    public function removeUser($user)
    {
        if($user instanceof User)
            $user = $user->nick();

        $this->users->forget($user);
    }

    /**
     * @param $users
     * @return array
     */
    public function setUsers($users)
    {
        $users = explode(' ', $users);

        foreach($users as $user)
        {
            $rank = '';
            $nick = $user;

            if(!ctype_alnum(substr($user, 0, 1)))
            {
                $rank = substr($user, 0, 1);
                $nick = substr($user, 1);
            }

            $info = database()->get('users', ['nick' => $nick]);

            if(empty($info))
            {
                // Database has no info, request it from IRC.
                send("WHO", $nick);

                // Set default
                $info['nick'] = $nick;
                $info['user'] = '';
                $info['host'] = '';
            }

            $mode = new ModeObject();
            $mode->setPrefix($rank);

            $this->users->put($nick , ['nick' => $info['nick'], 'modes' => $mode]);
        }

        $channel = database()->get('channels', ['name' => $this->name]);

        if($channel['max_users'] < count($this->users))
        {
            database()->update('channels', ['name' => $this->name], [
                'max_users' => $this->users->count()
            ]);
        }

        return array_keys($this->users->toArray());
    }

    /**
     * Updates user modes.
     *
     * @param array $data
     */
    public function updateUserModes(array $data)
    {
        foreach($data as $modes)
        {
            if(!$this->users->has($modes[0]))
                continue;

            $this->users[$modes[0]]->setMode($modes[1]);
        }
    }
}

