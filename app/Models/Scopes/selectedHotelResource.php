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
            $builder->where($table.'.shop_id', $setting->value)
                ->orWhereNull($table.'.shop_id')
                ->orWhere($table.'.shop_id', '');
        } elseif (\Auth::hasUser() && \Auth::user()->shops) {
            // && count(\Auth::user()->shops)
            $user = \Auth::user();
            if ($user->shops()->count() > 0) {
                $shopIds = \Auth::user()->shops()->pluck('shops.id')->toArray();
                $builder->whereIn($table.'.shop_id', $shopIds)
                    ->orWhereNull($table.'.shop_id')
                    ->orWhere($table.'.shop_id', '');
            }
            
        }
    }
}
