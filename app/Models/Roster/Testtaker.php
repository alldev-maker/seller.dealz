<?php

namespace App\Models\Roster;

use App\Models\Admin\User;
use App\Models\Geography\Country;
use App\Models\Quizzes\Quiz;
use App\Models\Settings\Setting as SystemSetting;
use Illuminate\Database\Eloquent\Builder;
use stdClass as StdClass;
use App\Models\Model;

/**
 * Class Testtaker
 *
 * @package App\Models\Roster
 *
 * @property string  $id
 * @property string  $user_id
 * @property string  $family_name
 * @property string  $given_name
 * @property string  $suffix
 * @property string  $title
 * @property string  $nickname
 * @property string  $nice_name
 * @property string  $email
 * @property string  $school
 * @property string  $address
 * @property string  $locality
 * @property string  $county
 * @property string  $state
 * @property string  $country_id
 * @property string  $postal_code
 * @property string  $phone_mobile
 * @property string  $phone_landline
 * @property string  $notes
 *
 * @property Country $country
 * @property User    $user
 *
 * @method static Testtaker find(string $id)
 * @method static Testtaker first(string $id)
 * @method static Testtaker findOrFail(string $id)
 *
 * @method static Testtaker|Builder where($column, $operator = null, $value = null, $boolean = 'and')
 */
class Testtaker extends Model
{

    const PATH = 'roster.testtakers';
    const SLUG = 'testtaker';

    protected $table = 'roster_testtakers';

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [
        'name_full',
        'user',
        'country',
        'urls',
    ];

    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class, 'roster_testtakers_quizzes')
            ->withPivot('takes')
            ->withTimestamps();
    }

    public function getNameFullAttribute()
    {
        $fullName = new StdClass();

        $fullName->western  = implode(' ', [$this->given_name, $this->family_name]) . (!empty($this->suffix) ? ', ' . $this->suffix : '');
        $fullName->eastern  = implode(' ', [$this->family_name, $this->given_name, $this->suffix]);
        $fullName->clerical = trim(implode(' ', [implode(', ', [$this->family_name, $this->given_name]), $this->suffix]));

        return $fullName;
    }

    public function getUserAttribute()
    {
        $role = SystemSetting::find('testtaker.role');
        $user = User::ofRole($role->value)->where('id', $this->user_id)->first();

        if (!$user) {
            $user = new User();

            $user->name = '';
        }

        $user->makeHidden(['role_id', 'role', 'notes', 'urls', 'device_id', 'app_id', 'app_token']);

        return $user;
    }

    public function getCountryAttribute()
    {
        if (empty($this->country_id)) {
            $country = new Country();

            $country->id            = '';
            $country->slug          = '';
            $country->name_common   = '';
            $country->name_official = '';
        } else {
            $country = Country::find($this->country_id);

            if ($country) {
                $country->makeHidden(['urls']);
            } else {
                $country = new Country();

                $country->id            = '';
                $country->slug          = '';
                $country->name_common   = '';
                $country->name_official = '';
            }
        }

        return $country;
    }

    public function getUrlsAttribute()
    {
        if (!empty($this->id)) {
            return [
                'edit' => route(sprintf('%s.edit', self::PATH), ['id' => $this->id]),
            ];
        } else {
            return [
                'edit' => '',
            ];
        }
    }

}