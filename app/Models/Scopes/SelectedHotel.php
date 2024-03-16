<?php

namespace App\Models\Scopes;

use App\Models\GeneralSetting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class SelectedHotel implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $setting = GeneralSetting::where('key', 'selected_hotel')->first();
        if ($setting && $setting->value && $setting->value !== 'all') {
            $builder->where('shops.id', $setting->value);
        }
    }
}
