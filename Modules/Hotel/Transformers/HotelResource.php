<?php

namespace Modules\Hotel\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Hotel\Entities\BookingDetails;
use Modules\Hotel\Entities\Roomfacility;
use Modules\Hotel\Entities\Room;
class HotelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    
    public function toArray($request)
    {
       
        // $Allfacilities = Roomfacility::select('room_facility.room_facility_title','facility_img','room_facilitytdatas.price')
        // ->join('room_facilitytdatas','room_facilitytdatas.facilityId','=','room_facility.id')
        // ->get();
        $Allfacilities = Roomfacility::with(['facilityRate' => function ($query) use ($request) {
            $query->where('hotel_id', $this->id);
        }, 'facilityRate.taxRate.taxName'])->get();
        
        return [
            'id' => $this->id,
            "hotel_name" => $this->hotel_name,
            "contact_name" => $this->contact_name,
            "hotel_address" => $this->hotel_address,
            "hotelcategory_id" => $this->hotelcategory_id,
            "hotel_phone" => $this->hotel_phone,
            "hotel_phone1" => $this->hotel_phone1,
            "hotel_email" => $this->hotel_email,
            "hotel_prefix" => $this->hotel_prefix,
            "total_no_of_rooms" => $this->totalRooms(),
            "no_of_floor" => $this->no_of_floor,
            "contact_phone" => $this->contact_phone ?? $this->hotel_phone,
            "facilities" => $this->facilityName,
            "Allfacilities" => $Allfacilities ?? [],
            "category" => $this->category,
            "images" => json_decode($this->images),
            "state" => $this->state ?? '',
            "city" => $this->city ?? '',
            "image_path" => $this->image_path ?? '',
            "roomCategories" => $this->getRoomCategories($request->check_in_date),
            "hotelRoomCategories" => $this->hotelRoomCategory,
        ];
    }

    public function getRoomCategories($checkInDate)
    {
        $categories = collect();
        
        foreach ($this->hotelRoomCategory as $room) {
                
            if ($room->roomCategory) {
                // $totalRoom = Room::where('room_categorary', $room->roomCategory->id)->where('hotel_id', $room->hotel_id)->count();
                // $totalConfirm = Room::occupied($checkInDate)->where('room_categorary',$room->roomCategory->id)->where('hotel_id', $room->hotel_id)->count();
                // $rooms = Room::available($checkInDate)->where('room_categorary',2)->where('hotel_id', 4)->get();
                
                $finalRooms = [];
                $totalRoom = Room::where('room_categorary', $room->roomCategory->id)->where('hotel_id', $room->hotel_id)->get();
                 $rooms = Room::occupied($checkInDate)->where('room_categorary',$room->roomCategory->id)->where('hotel_id',$room->hotel_id)->get();
                $finalRooms = $this->getAvailbleRoom($totalRoom,$rooms);
               
                // $rooms = Room::available($checkInDate)->where('room_categorary',$room->roomCategory->id)->where('hotel_id', $room->hotel_id)->get();
               
                // foreach($rooms as $finalRoom){
                //     $check = BookingDetails::where('room_id',$finalRoom->id)->whereIn('booking_status',[2,3,5])->count();
                    
                //     if($check == 0){
                //         $finalRooms[] = $finalRoom;
                //     }   
                // }
                $room->roomCategory['count'] = count($finalRooms);
                $categories->push($room->roomCategory);
            }
        }
        return $categories;
    }

    public function getAvailbleRoom($totalRooms,$occupiedRooms){
        $finalArray = [];
        if(count($occupiedRooms) === 0){
            $finalArray = $totalRooms;
        } else {

            foreach ($totalRooms as $totalRoom) {
                $isAvailable = true;
                foreach ($occupiedRooms as $occupiedRoom) {
                    if ($totalRoom->id == $occupiedRoom->id) {
                        $isAvailable = false;
                        break; 
                    }
                }
                if ($isAvailable) {
                    $finalArray[] = $totalRoom;
                }
            }
        }
        return $finalArray;
    }

    public function totalRooms()
    {
        $totalRooms = 0;
        foreach ($this->hotelRoomCategory as $room) {
            $totalRooms += $room->rooms->count();
        }
        return $totalRooms;
        // return $this->rooms->count();
    }
}
