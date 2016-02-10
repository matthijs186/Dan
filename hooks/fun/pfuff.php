<?php

/**
 * @author matthijs186
 *
 * PINK FLUFFY UNICORNS DANCING ON RAINBOWS
 * pomf pomf pomf
 *
 * Do not directly edit this file.
 * If you want to change the rank, see commands.permissions in the configuration.
 */

use Illuminate\Support\Collection;

hook('pfuff')
    ->command(['pfuff'])
    ->help('Pink fluffy unicorns')
    ->func(function(Collection $args) {
        $channel = $args->get('channel');
        $channel->message('PINK FLUFFY UNICORNS DANCING ON RAINBOWS');
        $channel->message('https://www.youtube.com/watch?v=qRC4Vk6kisY');
    });