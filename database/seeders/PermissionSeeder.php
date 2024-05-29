<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();


        $MarketPermissions = [
            //delivery
            'add delivery',
            'delete delivery',
            'update delivery',
            'show delivery',

            //product
            'add product',
            'delete product',
            'update product',
            'show product',

            //post
            'add post',
            'delete post',
            'update post',
            'index my posts',
            'show post',

            //follow request
            'accept follow request',
            'reject follow request',
            'list my followers',
            'list my follow requests',

            //order
            'accept Order',
            'reject Order',
            'show The Orders',
            'show The Accepted Order',
            'show The Rejected Order',
            'show The Waiting Order',

            //chat
            'market send message',
            'market show messages',
            'market show_my_conversations',

        ];

        foreach ($MarketPermissions as $permission) {
            Permission::create(['guard_name' => 'market-api', 'name' => $permission])->toArray();

        }

        $CustomerPermissions = [

            //market
            'show all market',
            'show markets products',
            'show specific product',

            //post
            'index all posts',
            'show specific post',

            //follow request
            'send follow request',
            'cancel follow request',
            'list my following',

            //like
            'delete Like',
            'add Like',

            //order
            'send Order',
            'show My Orders',
            'show My Accepted Order',
            'show My Rejected Order',

            //chat
            'user send message',
            'user show messages',
            'user show_my_conversations',

        ];

        foreach ($CustomerPermissions as $permission) {
            Permission::create(['guard_name' => 'api', 'name' => $permission])->toArray();
        }

        //مبدأيا
        $deliveryPermission = [
            'delivery show The Accepted Order',
            'delivery show The Rejected Order',
            'delivery show The Waiting Order',
        ];

        foreach ($deliveryPermission as $permission) {
            Permission::create(['guard_name' => 'delivery-api', 'name' => $permission])->toArray();
        }


        Role::create(['guard_name' => 'market-api', 'name' => 'market'])->givePermissionTo($MarketPermissions);
        Role::create(['guard_name' => 'api', 'name' => 'customer'])->givePermissionTo($CustomerPermissions);
        Role::create(['guard_name' => 'delivery-api', 'name' => 'delivery'])->givePermissionTo($deliveryPermission);

    }
}
