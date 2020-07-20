<?php

namespace App;

use function AlibabaCloud\Client\json;
use App\Exceptions\NotificationException;
//use App\Traits\ModelLogger;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * App\File
 *
 * @property-read mixed $url
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File query()
 * @mixin \Eloquent
 */
class File extends Model
{
//    use ModelLogger;

    protected $allowImageMimeType = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp'];

    protected $appends = ['url'];

    protected $fillable = [
        'sha1',
        'size',
        'path',
        'mime_type',
        'client_original_name',
    ];

    public function getUrlAttribute()
    {
        if (Str::startsWith($this->path, ['http://', 'https://'])) {
            return $this->path;
        }
        return Str::finish(config('uploadfile.url'), '/') . $this->path;
    }

    /**
     * @string type 0 - web  1 - app(安卓，iOS)
     * @param Request $request
     * @return mixed|void
     * @throws NotificationException
     */
    public function image(Request $request)
    {
        $file = $request->file($request->input('file', 'image'));
        if (is_null($file)) {
            throw new NotificationException('请上传图片');
        }
        $file->getFilename();

        $path = $request->input('path', 'avatars');

        $mime_type = $file->getMimeType();

        // 验证是不是图片
        if (!in_array($mime_type, $this->allowImageMimeType)) {
            throw new NotificationException('图片格式不支持');
        }

        $create = $this->storeFile($file, $path, $mime_type);

        return $create;
    }

    /**
     * @param $url
     * @param string $path
     * @param string $host
     * @return $this
     * @throws NotificationException
     */
    public function imageHttp($url, $path = 'shopv3', $host = 'http://shopv3.cxhshop.com/')
    {

        if (!Str::startsWith($url, ['http://', 'https://'])) {
            $url = ltrim($url, '/');
            $url = Str::start($url, $host);
        } else {
            $path = $url;
        }

        $pathinfo = pathinfo($url);


        $basename = data_get($pathinfo, 'basename');
        $basename = Str::before($basename, '?') ?: Str::random(32);
        if (!Storage::disk('public')->has('temp_file')) {
            Storage::disk('public')->makeDirectory('temp_file');
        }

        $save_to = 'temp_file/' . Str::uuid()->toString() . $basename;

        $save_to = str_replace('-', '_', $save_to);

        $file_name = Storage::disk('public')->path($save_to);

        $client = new Client([
            'verify' => false,
            'timeout' => 45
        ]);
        $response = $client->get($url);
        $body = $response->getBody()->detach();
        Storage::disk('public')->put($save_to, $body);
        $file = new \Illuminate\Http\File($file_name);
        try {
            if (is_null($file)) {
                throw new NotificationException('请上传图片');
            }
            $mime_type = $file->getMimeType();
            // 验证是不是图片
            if (!in_array($mime_type, $this->allowImageMimeType)) {
                throw new NotificationException('图片格式不支持');
            }
            $firstOrCreate = $this->storeFile($file, $path, $mime_type, $basename);
            Storage::disk('public')->delete($save_to);
            return $firstOrCreate;
        } finally {
            Storage::disk('public')->delete($save_to);
        }
    }

    /**
     * @param $file
     * @param $path
     * @param string|null $mime_type
     * @return mixed
     * @throws NotificationException
     */
    protected function storeFile($file, $path, ?string $mime_type, ?string $client_original_name = null)
    {
        $sha1 = sha1_file($file);

        $model_file = File::where('sha1', '=', $sha1)->first();

        if ($model_file) {
            return $model_file;
        }

        $name = $sha1;

        if ($extension = $file->guessExtension()) {
            $name .= '.' . $extension;
        }
        if (config('uploadfile.url')){
            // 这里判断一下，如果是http 网址直接保存就行了
            if (!Str::startsWith($path, ['http://', 'https://'])) {
                $path = Storage::disk('oss')->putFileAs($path, $file, $name);
            }
        }else{
            $path = Storage::disk('public')->putFileAs($path, $file, $name);
        }

        if (!$path) {
            throw new NotificationException('图片未能保存');
        }

        return self::create([
            'sha1' => $sha1,
            'path' => $path,
            'size' => $file->getSize(),
            'mime_type' => $mime_type,
            'client_original_name' => $client_original_name ?? $file->getClientOriginalName()
        ]);
    }

    /**
     * 保存真实地址的图片
     * @param $file
     * @param $path
     * @param string|null $mime_type
     * @param string|null $client_original_name
     * @return File
     * @throws NotificationException
     */
    public function storeRealPathFile($file, $path, $client_original_name, ?string $mime_type = null)
    {

        if (is_string($file)) {
            $file = new  \Illuminate\Http\File($file);
        }

        if (!$file instanceof \Illuminate\Http\File) {
            throw new NotificationException('不是一个 Illuminate\Http\File 文件');
        }

        return $this->storeFile($file, $path, $file->getMimeType(), $client_original_name);
    }

}
