<?php

namespace Database\Seeders;

use App\Models\Action;
use App\Models\Partner;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ActionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        $html = '<div class="col-md-4 col-sm-12 image float-start me-3"><img src="/images/placeholder.jpg"></div><p>Maiores ea consequatur omnis numquam cum in. Odio dicta expedita blanditiis magni illum ex voluptas. Quia officiis nemo aperiam numquam dolores aspernatur debitis. Quae praesentium illum veritatis totam vel. Reprehenderit atque porro sint. Omnis aut iure cum natus odit non. Aut at suscipit mollitia dolore perspiciatis autem error asperiores. Maxime aperiam sed ex quaerat. Est sed ipsam alias alias dolores. Sunt quam quaerat quaerat et officia facere. Facilis necessitatibus dolores saepe recusandae non repellat eum. Ab enim et sint at rerum rem enim. Fugit ipsum non sunt rem et. Temporibus laboriosam omnis et asperiores atque dolorum. Nesciunt perferendis aperiam fuga. Qui commodi aut odio quis temporibus. Expedita enim et quia eum quia est numquam quidem. Consectetur fugiat rerum odio reiciendis et. Eaque aspernatur voluptatibus omnis ratione deserunt. Ut fuga ut in quod. Nam at reiciendis in laboriosam maxime in. Autem iste esse iusto ut eum nihil consequatur. Ipsum officia sunt quidem non eum. Autem dignissimos et modi labore ut quo repudiandae nulla. Eos perspiciatis qui molestiae quia quasi perferendis inventore. In temporibus harum est. Voluptates sequi est minima sequi excepturi est. Non magni qui tempore facere. Rerum mollitia incidunt tempora qui aut quis. Quia adipisci autem et doloremque et placeat. Eos sed doloribus quo consequatur repellat autem. Rerum dignissimos exercitationem quidem quia saepe dicta ut. Amet qui amet officia consequatur aut. Sit debitis dignissimos laboriosam repellendus veritatis. Itaque at aspernatur aliquam ratione nobis nesciunt facilis. Voluptas officiis similique ducimus. Totam est voluptatum explicabo et. Voluptas sequi neque nihil fugiat consequatur distinctio. Eos et sit libero vel asperiores laboriosam. Adipisci reprehenderit est et et optio. Harum excepturi itaque sit. Ea quas officiis rem exercitationem. Molestiae dolor unde suscipit quas vero ut. Voluptatem iste sit nobis molestiae dignissimos nam. Accusamus quas et veniam. Perspiciatis quae quo quia nostrum. Amet facilis nemo mollitia corporis voluptate. Aut fugiat id asperiores est. Beatae cumque eum debitis id. Voluptatem illum quia error nihil id provident. Repellendus occaecati et quia ut. Enim maiores dolor aut maxime voluptatem. Sit rem sunt hic est nisi tempore provident. Quidem molestiae et a doloribus. Libero esse architecto voluptas voluptates libero. Dignissimos nostrum veniam sunt doloremque. Ut ut et et aperiam voluptatem et aut. Aut magnam doloribus nemo. Beatae nam ea nostrum omnis mollitia non distinctio. Velit vel neque consequatur commodi debitis exercitationem deleniti. Sapiente itaque aut ut aspernatur aut voluptates. Numquam quam deserunt aspernatur amet. Optio occaecati consequatur ipsa ut maxime assumenda est. Vitae voluptas consectetur dolor recusandae quas. Cumque saepe eligendi consectetur beatae nesciunt. Accusamus quo quia possimus dolore voluptatem et.</p><div class="col-md-4 col-sm-12 image float-end ms-3"><img src="/images/placeholder.jpg"></div><p>Id soluta quia voluptatem quos ut rerum est. Quaerat eligendi expedita unde eos ut atque. Dicta aperiam facere totam in qui repellat qui. Aperiam vitae cumque et labore a harum. Veritatis commodi rerum quibusdam ut consequatur. Possimus est doloremque corporis rerum minima corrupti aliquid repellat. Ipsam odio corrupti vitae debitis mollitia quod assumenda. Optio aperiam assumenda et quia nulla. Quod nostrum temporibus qui rem quia. Ad quisquam qui hic et voluptatibus. Facilis consectetur ut quia. Beatae dolores suscipit repellat dignissimos itaque cupiditate. Necessitatibus iusto soluta similique sit tempore. Expedita accusamus non laborum sed cupiditate sint hic. Aut minima quos reprehenderit eveniet consequuntur tempora. Molestiae eum et nulla omnis. Hic accusamus iure porro ullam. Quidem modi tempore nisi debitis et. Labore ea eos voluptate maxime. Provident sint aut consequuntur dolores non. Id doloremque in occaecati. Sit et in occaecati consequuntur. Possimus voluptate perferendis qui quibusdam esse. Aliquid adipisci enim sint architecto. Ut rerum laboriosam rerum et iure. Esse natus facilis voluptatibus. Recusandae veniam corporis omnis est illum. Rerum consequuntur nihil officiis deleniti quod. Sit similique dolores consequatur quis dolor explicabo velit. Et omnis eos pariatur quia iste sed cupiditate.</p>';
        $ids = Partner::pluck('id')->toArray();
        foreach ($ids as $id) {
            Action::create([
                'name' => 'Акция-кооперация',
                'html' => '',
                'start' => Carbon::now(),
                'end' => Carbon::now()->addDays(30),
                'rating' => rand(1,2),
                'partner_id' => $id
            ]);
        }
    }
}
