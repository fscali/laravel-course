<?php

namespace App\Services;

use App\Contracts\CounterContract;
use Illuminate\Contracts\Cache\Factory as Cache;
use Illuminate\Contracts\Session\Session as Session;



class DummyCounter implements CounterContract
{




    public function increment(string $key, array $tags = null): int
    {

        dd(
            'I am a dummy counter'
        );

        return 0;
    }
}
