<?php

namespace App\Models\Scopes;

use App\Models\GeneralSetting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class selectedHotelResource implements Scope
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
        $table = $model->getTable();
        
        $setting = GeneralSetting::where('key', 'selected_hotel')->first();
        if ($setting && $setting->value && $setting->value !== 'all') {
            $builder->where($table.'.hotel_id', $setting->value)
                ->orWhereNull($table.'.hotel_id')
                ->orWhere($table.'.hotel_id', '');
        } elseif (\Auth::hasUser() && \Auth::user()->hotels) {
            // && count(\Auth::user()->hotels)
            $user = \Auth::user();
            if ($user->hotels()->count() > 0) {
                $hotelIds = \Auth::user()->hotels()->pluck('hotels.id')->toArray();
                $builder->whereIn($table.'.hotel_id', $hotelIds)
                    ->orWhereNull($table.'.hotel_id')
                    ->orWhere($table.'.hotel_id', '');
            }
            
        }
    }
}
