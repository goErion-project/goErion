<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $countries_option
 * @property mixed $country_from
 * @property string $countries
 * @property mixed $product
 */
class PhysicalProduct extends User
{
    use Uuids;

    public $incrementing = false;

    protected $keyType = 'string';
    protected $primaryKey = 'id';

    public bool $generateManualy = true;

    public static array $countriesOptions =
        [
            'all' => 'All countries',
            'include' => 'Included countries',
            'exclude' => 'All except excluded countries'
        ];

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'id');
    }

    public function countriesArray(): array
    {
        if (!empty($this->countries))
            return explode(',', $this->countries);
        return [];
    }

    public function countriesLongArray(): array
    {
        $countries = [];
        foreach ($this->countriesArray() as $country)
        {
            $countries[] = config('countries.'.$country);
        }
        return $countries;
    }

    public function shipTo(): string
    {
        if ($this->countries_option == 'all')
            return 'all';
        if ($this->countries_option == 'include')
            return 'only to countries';
        return 'except to countries';
    }

    public function countriesLong(): string
    {
        if (!empty($this->countriesLongArray()))
            return implode(', ', $this->countriesLongArray());
        return '';
    }

    public function shipsFrom()
    {
        return config('countries.' . $this->country_from);
    }

    public function setCountries(?array $countries): void
    {
        if (!empty($countries))
            $this->countries = implode(',', $countries);
        else
            $this->countries = '';
    }

    public function shippings()
    {
        return $this->hasMany(Shipping::class, 'product_id', 'id')
        ->where('deleted','=',0);
    }
}
