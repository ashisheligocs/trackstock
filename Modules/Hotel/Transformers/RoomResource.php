<?php

namespace Modules\Hotel\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Hotel\Entities\Booking;
use Modules\Hotel\Entities\Room;

class RoomResource extends JsonResource
{
    public function toArray($request)
    {
        // dd($this->hotelRoomCategory);
        return [
            "id" => $this->id,
            "room_name" => $this->room_name,
            "room_category" => $this->Roomcategory,
            "persons" => $this->hotelRoomCategory->no_of_person,
            "children" => $this->hotelRoomCategory->no_of_child,
            "infants" => $this->hotelRoomCategory->no_of_infant,
            "floor"  => $this->floor,
            "occupied" => $this->isOccupied($request),
            "status" => $this->bookingStatus($request),
            "hotel" => $this->hotel,
            "booking_id" => $this->bookingDetails($request) ? $this->bookingDetails($request)->id : null,
            "check_in" => $this->bookingDetails($request) ? $this->bookingDetails($request)->check_in_date : null,
            "check_out" => $this->bookingDetails($request) ? $this->bookingDetails($request)->check_out_date : null,
            "days" => $this->totalDays($request),
        ];
    }

    public function isOccupied($request)
    {
        $date = $request->date ?? Carbon::now();
        return Room::where('id', $this->id)->whereHas('roomCheckInOut', function ($q) use ($date) {
            $q->whereNotIn('booking_status', [1, 4])->whereHas('booking', function ($bookingQuery) use ($date){
                $bookingQuery->whereDate('check_out_date', '>=', $date ?? Carbon::now())->whereIn('booking_status_main', [Booking::CHECKIN, Booking::BOOKED]);
            });
        })->exists();
    }

    public function bookingStatus($request)
    {
        $date = $request->date ?? Carbon::now();
        if ($this->isOccupied($request)) {
            $bookingDetail = $this->roomCheckInOut()->with('booking')->whereNotIn('booking_status', [1, 4])->whereHas('booking', function ($bookingQuery) use ($date){
                $bookingQuery->whereDate('check_out_date', '>=', $date ?? Carbon::now())->whereIn('booking_status_main', [Booking::CHECKIN, Booking::BOOKED]);
            })->first();

            return $bookingDetail->booking->booking_status_main;
        }

        return null;
    }

    public function bookingDetails($request)
    {
        $date = $request->date ?? Carbon::now();
        if ($this->isOccupied($request)) {
            $bookingDetail = $this->roomCheckInOut()->with('booking')->whereNotIn('booking_status', [1, 4])->whereHas('booking', function ($bookingQuery) use ($date){
                $bookingQuery->whereDate('check_out_date', '>=', $date ?? Carbon::now())->whereIn('booking_status_main', [Booking::CHECKIN, Booking::BOOKED]);
            })->first();

            return $bookingDetail->booking;
        }

        return null;
    }

    public function totalDays($request)
    {
        $start = $this->bookingDetails($request) ? $this->bookingDetails($request)->check_in_date : null;
        $end = $this->bookingDetails($request) ? $this->bookingDetails($request)->check_out_date : null;

        if ($start && $end) {
            return Carbon::parse($start)->diffInDays(Carbon::parse($end));
        }

        return 0;
    }
}
