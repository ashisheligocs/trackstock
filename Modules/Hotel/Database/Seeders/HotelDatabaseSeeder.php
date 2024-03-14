<?php

namespace Modules\Hotel\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use DB;
use Modules\Hotel\Entities\HotelRoomCategory;
use Modules\Hotel\Entities\Roomcategory;
use Modules\Hotel\Entities\RoomCategoryTax;

class HotelDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (DB::table('booking_status')->count() == 0) {
            DB::table('booking_status')->insert([
                [
                    'name' => 'Pending',
                ],
                [
                    'name' => 'Hold',
                ],
                [
                    'name' => 'Confirmed',
                ],
                [
                    'name' => 'Available',
                ],
            ]);
        }

        if (DB::table('hotelcategories')->count() == 0) {
            DB::table('hotelcategories')->insert([
                [
                    'category_name' => '1 Star Hotels',
                ],
                [
                    'category_name' => '2 Star Hotels',
                ],
                [
                    'category_name' => '3 Star Hotels',
                ],
                [
                    'category_name' => '4 Star Hotels',
                ],
                [
                    'category_name' => '5 Star Hotels',
                ],
                [
                    'category_name' => 'Home Stay',
                ],
                [
                    'category_name' => 'Cottage',
                ],
            ]);
        }
        if (DB::table('meal_plans')->count() == 0) {
            DB::table('meal_plans')->insert([
                [
                    'plan_name' => 'American Plan',
                    'short_name' => 'AP'
                ],
                [
                    'plan_name' => 'Modified American Plan',
                    'short_name' => 'MAP'
                ],
                [
                    'plan_name' => 'Continental Plan',
                    'short_name' => 'CP'
                ]
            ]);
        }
        if (DB::table('floors')->count() == 0) {
            DB::table('floors')->insert([
                [
                    'floorsName' => '1st Floor',
                ],
                [
                    'floorsName' => '2nd Floor',
                ],
                [
                    'floorsName' => '3rd Floor',
                ],
                [
                    'floorsName' => '4th Floor',
                ],
                [
                    'floorsName' => '5th Floor',
                ],
                [
                    'floorsName' => '6th Floor',
                ],
                [
                    'floorsName' => '7th Floor',
                ]
            ]);
        }
        if (DB::table('room_facility')->count() == 0) {
            DB::table('room_facility')->insert([
                [
                    'room_facility_title' => 'Additional Amenities',
                    'facility_description' => 'Additional Amenities',
                    'facility_img' => "/storage/modules/icons/fa-ad.svg",
                ],
                [
                    'room_facility_title' => 'Meeting Rooms',
                    'facility_description' => 'Meeting Rooms or Conference Facilities',
                    'facility_img' => "/storage/modules/icons/meeting.svg",
                ],
                [
                    'room_facility_title' => 'Travel Desk',
                    'facility_description' => 'Travel Desk',
                    'facility_img' => "/storage/modules/icons/service-desk-icon.svg",
                ],
                [
                    'room_facility_title' => 'Laundry Service',
                    'facility_description' => 'Laundry Service',
                    'facility_img' => "/storage/modules/icons/laundry.svg",
                ],
                [
                    'room_facility_title' => 'Parking',
                    'facility_description' => 'Parking',
                    'facility_img' => '/storage/modules/icons/parking.svg',
                ],
                [
                    'room_facility_title' => 'WiFi',
                    'facility_description' => 'WiFi',
                    'facility_img' => '/storage/modules/icons/fa-wifi.svg',
                ],
                [
                    'room_facility_title' => 'Room Service',
                    'facility_description' => 'Room Service',
                    'facility_img' => '/storage/modules/icons/room-service.svg',
                ],
                [
                    'room_facility_title' => 'Linen and Towels',
                    'facility_description' => 'Linen and Towels',
                    'facility_img' => '/storage/modules/icons/towel-icon.svg',
                ],
                [
                    'room_facility_title' => 'Air Conditioning',
                    'facility_description' => 'Air Conditioning',
                    'facility_img' => '/storage/modules/icons/air-conditioner-icon.svg',
                ],
                [
                    'room_facility_title' => 'Other',
                    'facility_description' => 'Other',
                    'facility_img' => '/storage/modules/icons/others.svg',
                ],
            ]);
        }
        if (DB::table('room_categories')->count() == 0) {
            DB::table('room_categories')->insert([
                [
                    'room_category_name' => 'Suite',
                ],
                [
                    'room_category_name' => 'Family Room',
                ],
                [
                    'room_category_name' => 'Triple Room',
                ],
                [
                    'room_category_name' => 'Twin Room',
                ],
                [
                    'room_category_name' => 'Double Room',
                ],
                [
                    'room_category_name' => 'Single Room',
                ],
            ]);
        }
        if (DB::table('beds')->count() == 0) {
            DB::table('beds')->insert([
                [
                    'bedtypetitle' => 'Double Bed',
                ],
                [
                    'bedtypetitle' => 'Twin Beds',
                ],
                [
                    'bedtypetitle' => 'King Bed',
                ],
                [
                    'bedtypetitle' => 'Queen Bed',
                ],
                [
                    'bedtypetitle' => 'Double Bed',
                ],
                [
                    'bedtypetitle' => 'Single Bed',
                ],
                [
                    'bedtypetitle' => 'Family Room',
                ],
                [
                    'bedtypetitle' => 'Triple Room',
                ],
            ]);
        }
        if (DB::table('hotels')->count() == 0) {
            DB::table('hotels')->insert([
                [
                    'hotel_name' => 'Hotel demo 1',
                    'hotel_address' => 'Test address',
                    'hotelcategory_id' => 3,
                    'hotel_phone' => '1231231231',
                    'hotel_phone1' => '',
                    'hotel_email' => 'hoteldemo@1.com',
                    'hotel_prefix' => 'HDM',
                    'total_no_of_rooms' => '1',
                    'hotel_Status' => 0,
                    'no_of_floor' => '',
                    'contact_phone' => '1231231231',
                    'contact_name' => 'Test contact name',
                    'del_status' => 0,
                ]
            ]);
        }
        if (DB::table('hotel_room_category')->count() == 0) {
            $roomCategory = Roomcategory::get();
            if($roomCategory){
                foreach($roomCategory as $category){
                    HotelRoomCategory::updateOrCreate([
                        'room_category' => $category->id,
                        'hotel_id' => 1,
                        'rate' => 1000,
                        'extra_bed_capacity' => 1,
                        'extra_bed_rate' => 100,
                        'extra_person_capacity' => 1,
                        'per_person' => 100,
                        'no_of_person' => 2,
                        'no_of_child' => 1,
                        'no_of_infant' => 1,
                    ]);
                }
            }
            // DB::table('hotel_room_category')->insert([
            //     [
            //         'room_category' => 1,
            //         'hotel_id' => 1,
            //         'rate' => 1000,
            //         'extra_bed_capacity' => 1,
            //         'extra_bed_rate' => 100,
            //         'extra_person_capacity' => 1,
            //         'per_person' => 100,
            //         'no_of_person' => 2,
            //         'no_of_child' => 1,
            //         'no_of_infant' => 1,
            //     ]
            // ]);
        }
        if(DB::table('room_category_tax')->count() == 0){
            $hotelRoomCategory = HotelRoomCategory::get();
            if($hotelRoomCategory){
                foreach($hotelRoomCategory as $categoryTax){
                    RoomCategoryTax::updateOrCreate([
                        'hotel_room_category_id' => $categoryTax->id,
                        'tax_id' => 7,
                    ]);
                    RoomCategoryTax::updateOrCreate([
                        'hotel_room_category_id' => $categoryTax->id,
                        'tax_id' => 8,
                    ]);
                }
            }
        }

        if (DB::table('rooms')->count() == 0) {
            DB::table('rooms')->insert([
                [
                    'room_name' => 'Demo Room 1',
                    'room_categorary' => 2,
                    'hotel_room_category_id' => 1,
                    'hotel_id' => 1,
                    'bed_type_id' => 2,
                    'roomdescription' => 'Test room description...',
                    'floor_id' => 1,
                    'del_status' => 0,
                    'room_status' => 0,
                ]
            ]);
        }
        if (DB::table('hotel_meal_plans')->count() == 0) {
            DB::table('hotel_meal_plans')->insert([
                [
                    'hotel_id' => 1,
                    'meal_id' => 1,
                    'price' => 0,
                    'status' => 0,
                ],
                [
                    'hotel_id' => 1,
                    'meal_id' => 2,
                    'price' => 0,
                    'status' => 0,
                ],
                [
                    'hotel_id' => 1,
                    'meal_id' => 3,
                    'price' => 0,
                    'status' => 0,
                ]
            ]);
        }
        if (DB::table('varients')->count() == 0) {
            DB::table('varients')->insert([
                [
                    'varient_name' => 'Full',
                    'del_status' => 0,
                ],
                [
                    'varient_name' => 'Half',
                    'del_status' => 0,
                ]
            ]);
        }
        if (DB::table('restaurants')->count() == 0) {
            DB::table('restaurants')->insert([
                [
                    'hotel_id' => '1',
                ],
            ]);
        }
    }
}
