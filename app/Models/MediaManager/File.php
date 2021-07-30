<?php

namespace App\Models\MediaManager;

use App\Models\Admin\User;
use App\Models\Filezes\File\Section;
use Carbon\Carbon;
use App\Models\Model;

/**
 * Class File
 *
 * @package App\Models\MediaManager
 *
 * @property string $id
 * @property string $slug
 * @property string $mime_type
 * @property string $name
 * @property string $extension
 * @property string $description
 * @property string $path
 * @property int    $owned_by
 *
 * @property bool   $is_image
 * @property bool   $is_video
 *
 * @method static File find(int $id)
 * @method static File findOrFail(int $id)
 */
class File extends Model
{

    const PATH = '';
    const SLUG = 'file';

    protected $table = 'mmm_media_files';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = [
        'owned_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [
        'owner',
        'urls',
        'is_image',
        'is_video'
    ];

    /**
     * An accessor method to get the User attribute created the quiz.
     *
     * Usage: $file->owner
     *
     * @return User
     */
    public function getOwnerAttribute()
    {
        $user = User::find($this->owned_by);

        if (!$user) {
            $user = new User();

            $user->name = '';
        }

        $user->makeHidden(['role_id', 'role', 'notes', 'urls', 'device_id', 'app_id', 'app_token']);

        return $user;
    }

    /**
     * An accessor method to return the URLs of the File.
     *
     * Usage: $quiz->urls
     *
     * @return array
     */
    public function getUrlsAttribute()
    {
        $fileNew = $this->id . '.' . $this->extension;

        $dirSrc = $this->path . 'source';
        $dirThm = $this->path . 'thumbnail';

        return [
            'source'    => asset('storage' . DIRECTORY_SEPARATOR . $dirSrc . DIRECTORY_SEPARATOR . $fileNew),
            'thumbnail' => asset('storage' . DIRECTORY_SEPARATOR . $dirThm . DIRECTORY_SEPARATOR . $fileNew),
        ];
    }

    public function getIsImageAttribute()
    {
        $formats = ['image/jpeg', 'image/png', 'image/gif'];

        return in_array($this->mime_type, $formats);
    }

    public function getIsVideoAttribute()
    {
        $formats = ['video/mp4', 'video/quicktime'];

        return in_array($this->mime_type, $formats);
    }

}