<?php

namespace Modules\Restaurant\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use DB;
use Modules\Restaurant\Entities\ItemTax;
use App\Models\VatRate;

class RestaurantDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (DB::table('restro_client_type')->count() == 0) {
            DB::table('restro_client_type')->insert([
                [
                    'type_name' => 'Hotel Customer',
                ],
                [
                    'type_name' => 'Walk in Customer',
                ],
            ]);
        }

        if (DB::table('foodcategorys')->count() == 0) {
            DB::table('foodcategorys')->insert([
                [
                    'category_name' => 'Break Fast',
                    'image'=> 'storage/modules/itemImage/missi_roti.jpg',
                    'status'=> '1',
                    'del_status' => 0
                ],
                [
                    'category_name' => 'Lunch',
                    'image'=> 'storage/modules/itemImage/rajma.jpg',
                    'status'=> '1',
                    'del_status' => 0
                ],
                [
                    'category_name' => 'Lunch and dinner',
                    'image'=> 'storage/modules/itemImage/shahi_paneer.jpg',
                    'status'=> '1',
                    'del_status' => 0
                ],
            ]);
        }

        if (DB::table('items')->count() == 0) {
            DB::table('items')->insert([
                [
                    'category_id' => '1',
                    'item_name' => 'Ajwain paratha',
                    'item_image' => 'storage/modules/itemImage/ajwain_paratha.jpg',
                    'description' =>   'Delicious.',
                    'notes' => 'enjoy your meal...',
                    'status' => '1',
                ],
                [
                    'category_id' => '1',
                    'item_name' => 'Aloo kachori',
                    'item_image' => 'storage/modules/itemImage/aloo_kachori.jpg',
                    'description' =>   'Delicious.',
                    'notes' => 'enjoy your meal...',
                    'status' => '1',
                ],
                [
                    'category_id' => '1',
                    'item_name' => 'Aloo paratha',
                    'item_image' => 'storage/modules/itemImage/aloo_paratha.jpg',
                    'description' =>   'Delicious.',
                    'notes' => 'enjoy your meal...',
                    'status' => '1',
                ],
                [
                    'category_id' => '1',
                    'item_name' => 'Bread pakoda',
                    'item_image' => 'storage/modules/itemImage/bread_pakoda.jpg',
                    'description' =>   'Delicious.',
                    'notes' => 'enjoy your meal...',
                    'status' => '1',
                ],
                [
                    'category_id' => '1',
                    'item_name' => 'Bread roll',
                    'item_image' => 'storage/modules/itemImage/bread_roll.jpg',
                    'description' =>   'Delicious.',
                    'notes' => 'enjoy your meal...',
                    'status' => '1',
                ],
                [
                    'category_id' => '1',
                    'item_name' => 'Cabbage paratha',
                    'item_image' => 'storage/modules/itemImage/cabbage_paratha.jpg',
                    'description' =>   'Delicious.',
                    'notes' => 'enjoy your meal...',
                    'status' => '1',
                ],
                [
                    'category_id' => '1',
                    'item_name' => 'Chole bhature',
                    'item_image' => 'storage/modules/itemImage/chole_bhature.jpg',
                    'description' =>   'Delicious.',
                    'notes' => 'enjoy your meal...',
                    'status' => '1',
                ],
                [
                    'category_id' => '1',
                    'item_name' => 'Daliya khichdi',
                    'item_image' => 'storage/modules/itemImage/daliya_khichdi.jpg',
                    'description' =>   'Good Taste.',
                    'notes' => 'enjoy your meal...',
                    'status' => '1',
                ],
                [
                    'category_id' => '1',
                    'item_name' => 'Egg omelette',
                    'item_image' => 'storage/modules/itemImage/egg_omelet.jpg',
                    'description' =>   'Good Taste.',
                    'notes' => 'enjoy your meal...',
                    'status' => '1',
                ],
                [
                    'category_id' => '1',
                    'item_name' => 'Gobi paratha',
                    'item_image' => 'storage/modules/itemImage/gobi_paratha.jpg',
                    'description' =>   'Delicious.',
                    'notes' => 'enjoy your meal...',
                    'status' => '1',
                ],
                [
                    'category_id' => '1',
                    'item_name' => 'Methi paratha',
                    'item_image' => 'storage/modules/itemImage/methi_paratha.jpg',
                    'description' =>   'Delicious.',
                    'notes' => 'enjoy your meal...',
                    'status' => '1',
                ],
                [
                    'category_id' => '1',
                    'item_name' => 'Palak paratha',
                    'item_image' => 'storage/modules/itemImage/palak_paratha.jpg',
                    'description' =>   'Delicious.',
                    'notes' => 'enjoy your meal...',
                    'status' => '1',
                ],
                [
                    'category_id' => '1',
                    'item_name' => 'Paneer paratha',
                    'item_image' => 'storage/modules/itemImage/paneer_paratha.jpg',
                    'description' =>   'Good Taste.',
                    'notes' => 'enjoy your meal...',
                    'status' => '1',
                ],
                [
                    'category_id' => '1',
                    'item_name' => 'Pyaz paratha',
                    'item_image' => 'storage/modules/itemImage/pyaz_paratha.jpg',
                    'description' =>   'Delicious.',
                    'notes' => 'enjoy your meal...',
                    'status' => '1',
                ],
                [
                    'category_id' => '1',
                    'item_name' => 'SANDWICH',
                    'item_image' => 'storage/modules/itemImage/sandwich.jpg',
                    'description' =>   'Delicious.',
                    'notes' => 'enjoy your meal...',
                    'status' => '1',
                ],
                [
                    'category_id' => '2',
                    'item_name' => 'Aloo matar',
                    'item_image' => 'storage/modules/itemImage/aloo_matar.jpg',
                    'description' =>   'Delicious.',
                    'notes' => 'enjoy your meal...',
                    'status' => '1',
                ],
                [
                    'category_id' => '2',
                    'item_name' => 'Aloo palak',
                    'item_image' => 'storage/modules/itemImage/aloo_palak.jpg',
                    'description' =>   'Delicious food.',
                    'notes' => 'Good Taste',
                    'status' => '1',
                ],
                [
                    'category_id' => '2',
                    'item_name' => 'Butter naan',
                    'item_image' => 'storage/modules/itemImage/butter_naan.jpg',
                    'description' =>   'Delicious food.',
                    'notes' => 'Good Taste',
                    'status' => '1',
                ],
                [
                    'category_id' => '2',
                    'item_name' => 'Chana Dal',
                    'item_image' => 'storage/modules/itemImage/chana_dal.jpg',
                    'description' =>   'Delicious food.',
                    'notes' => 'Good Taste',
                    'status' => '1',
                ],
                [
                    'category_id' => '2',
                    'item_name' => 'Chana Masala',
                    'item_image' => 'storage/modules/itemImage/chana_masala.jpg',
                    'description' =>   'Good Taste.',
                    'notes' => 'Good Taste',
                    'status' => '1',
                ],
                [
                    'category_id' => '2',
                    'item_name' => 'Dal Fry',
                    'item_image' => 'storage/modules/itemImage/dal_fry.jpg',
                    'description' =>   'Delicious food.',
                    'notes' => 'Good Taste',
                    'status' => '1',
                ],
                [
                    'category_id' => '2',
                    'item_name' => 'Dal Makhani',
                    'item_image' => 'storage/modules/itemImage/dal_makhani.jpg',
                    'description' =>   'Delicious food.',
                    'notes' => 'Good Taste',
                    'status' => '1',
                ],
                [
                    'category_id' => '2',
                    'item_name' => 'Dal Tadka',
                    'item_image' => 'storage/modules/itemImage/dal_tadka_recipe.jpg',
                    'description' =>   'Delicious food.',
                    'notes' => 'Good Taste',
                    'status' => '1',
                ],
                [
                    'category_id' => '2',
                    'item_name' => 'Dum Aloo',
                    'item_image' => 'storage/modules/itemImage/dum_aloo.jpg',
                    'description' =>   'Delicious food.',
                    'notes' => 'Good Taste',
                    'status' => '1',
                ],
                [
                    'category_id' => '2',
                    'item_name' => 'Gajar Ka Halwa',
                    'item_image' => 'storage/modules/itemImage/gajar_ka_halwa.jpg',
                    'description' =>   'Delicious food.',
                    'notes' => 'Good Taste',
                    'status' => '1',
                ],
                [
                    'category_id' => '2',
                    'item_name' => 'Gobi Matar',
                    'item_image' => 'storage/modules/itemImage/gobi_matar.jpg',
                    'description' =>   'Delicious food.',
                    'notes' => 'Good Taste',
                    'status' => '1',
                ],
                [
                    'category_id' => '2',
                    'item_name' => 'Jeera Rice',
                    'item_image' => 'storage/modules/itemImage/jeera_rice.jpg',
                    'description' =>   'Delicious food.',
                    'notes' => 'Good Taste',
                    'status' => '1',
                ],
                [
                    'category_id' => '2',
                    'item_name' => 'Kadai Paneer',
                    'item_image' => 'storage/modules/itemImage/kadai_paneer.jpg',
                    'description' =>   'Delicious food.',
                    'notes' => 'Good Taste',
                    'status' => '1',
                ],
                [
                    'category_id' => '2',
                    'item_name' => 'Lauki Kofta',
                    'item_image' => 'storage/modules/itemImage/lauki_kofta.jpg',
                    'description' =>   'Delicious food.',
                    'notes' => 'Good Taste',
                    'status' => '1',
                ],
                [
                    'category_id' => '2',
                    'item_name' => 'Maa ki Dal',
                    'item_image' => 'storage/modules/itemImage/maa_ki_dal_kali_dal_black_gram_dal.jpg',
                    'description' =>   'Delicious food.',
                    'notes' => 'Good Taste',
                    'status' => '1',
                ],
                [
                    'category_id' => '2',
                    'item_name' => 'Makki ki Roti',
                    'item_image' => 'storage/modules/itemImage/makki_ki_roti.jpg',
                    'description' =>   'Delicious food.',
                    'notes' => 'Good Taste',
                    'status' => '1',
                ],
                [
                    'category_id' => '2',
                    'item_name' => 'Malai Kofta',
                    'item_image' => 'storage/modules/itemImage/malai_kofta.jpg',
                    'description' =>   'Delicious food.',
                    'notes' => 'Good Taste',
                    'status' => '1',
                ],
                [
                    'category_id' => '2',
                    'item_name' => 'Matar Mushroom',
                    'item_image' => 'storage/modules/itemImage/matar_mushroom.jpg',
                    'description' =>   'Delicious food.',
                    'notes' => 'Good Taste',
                    'status' => '1',
                ],
                [
                    'category_id' => '2',
                    'item_name' => 'Matar Paneer',
                    'item_image' => 'storage/modules/itemImage/matar_paneer.jpg',
                    'description' =>   'Delicious food.',
                    'notes' => 'Good Taste',
                    'status' => '1',
                ],
                [
                    'category_id' => '2',
                    'item_name' => 'Missi Roti',
                    'item_image' => 'storage/modules/itemImage/missi_roti.jpg',
                    'description' =>   'Delicious food.',
                    'notes' => 'Good Taste',
                    'status' => '1',
                ],
                [
                    'category_id' => '2',
                    'item_name' => 'Mix Veg',
                    'item_image' => 'storage/modules/itemImage/1702623260_657bf81cbf751.jpg',
                    'description' =>   'Delicious food.',
                    'notes' => 'Good Taste',
                    'status' => '1',
                ],
                [
                    'category_id' => '2',
                    'item_name' => 'Mushroom Masala',
                    'item_image' => 'storage/modules/itemImage/mix_veg.jpg',
                    'description' =>   'Delicious food.',
                    'notes' => 'Good Taste',
                    'status' => '1',
                ],
                [
                    'category_id' => '2',
                    'item_name' => 'Mushroom Paneer',
                    'item_image' => 'storage/modules/itemImage/mushroom_masala.jpg',
                    'description' =>   'Delicious food.',
                    'notes' => 'Good Taste',
                    'status' => '1',
                ],
                [
                    'category_id' => '2',
                    'item_name' => 'Naan',
                    'item_image' => 'storage/modules/itemImage/naan.jpg',
                    'description' =>   'Delicious food.',
                    'notes' => 'Good Taste',
                    'status' => '1',
                ],
            ]);
        }

    }
}
