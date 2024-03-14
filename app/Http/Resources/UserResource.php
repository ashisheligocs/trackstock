<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\GeneralSetting;
use App\Models\Client;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $userRoles = $this->roles()->with('permissions')->get();
        $client = Client::where('user_id',$this->id)->first();
        
        $roles = $userRoles->pluck('slug');
        $rolesPermissions = $userRoles->pluck('permissions')->flatten(1)->pluck('slug');
        
        $generalsetting = GeneralSetting::get();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'company_details' => $generalsetting ?? '',
            'photo_url' => $this->photo_url,
            'created_at' => $this->created_at,
            'roles' => $roles,
            'permissions' => $rolesPermissions,
            'back_days' => $this->back_days,
            'client' => new ClientListResource($client),
        ];
    }
}
