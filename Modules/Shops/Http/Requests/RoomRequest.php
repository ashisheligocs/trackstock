<?php

namespace Modules\Shops\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Shops\Entities\Room;

class RoomRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $roomNos = Room::where('id', '!=', request()->room_id)->where('hotel_id', $this->hotel_id)->pluck('room_name');
        return [
            "room_name"       => ["nullable",
                Rule::notIn($roomNos)],
            "room_categorary" => "nullable",
            "hotel_room_category_id" => "nullable",
            "hotel_id"        => "nullable",
            "bed_type_id"     => "nullable",
            // "no_of_person"    => "nullable",
            // "room_rate"       => 'nullable',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
