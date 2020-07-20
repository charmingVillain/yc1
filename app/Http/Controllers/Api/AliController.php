<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use ShaoZeMing\AliSTS\Services\STSService;

class AliController extends Controller
{
    public function sts()
    {
        return Cache::get('ali_sts_token', function() {
            $config = config('ali.oss');

            $timeout = data_get($config, 'sts.timeout');

            $sts = new STSService($config);

            $token = $sts->getToken();

            $Credentials = get_object_vars(data_get($token, 'Credentials'));

            $Credentials['Timeout'] = $timeout;

            $Credentials['Bucket'] = config('filesystems.disks.oss.bucket');
            $Credentials['Region'] = config('filesystems.disks.oss.endpoint');

            Cache::set('ali_sts_token', $Credentials, Carbon::parse(data_get($Credentials, 'Expiration')));

            return $Credentials;
        });
    }
}
