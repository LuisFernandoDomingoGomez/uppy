<?php

namespace Database\Seeders;

use App\Models\CardType;
use Illuminate\Database\Seeder;

class CardTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'code' => 'coupon',
                'name' => 'Cupón',
                'description' => 'Ofrece cupones digitales de un solo uso para atraer nuevos clientes.',
                'supports_rewards' => true,
                'supports_balance' => false,
                'supports_stamps' => false,
                'supports_notifications' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'cashback',
                'name' => 'Cashback',
                'description' => 'Devuelve un porcentaje de cada compra a tus clientes.',
                'supports_rewards' => true,
                'supports_balance' => true,
                'supports_stamps' => false,
                'supports_notifications' => true,
                'sort_order' => 2,
            ],
            [
                'code' => 'giftcard',
                'name' => 'Tarjeta de regalo',
                'description' => 'Permite vender y compartir saldo o beneficios como regalo.',
                'supports_rewards' => true,
                'supports_balance' => true,
                'supports_stamps' => false,
                'supports_notifications' => true,
                'sort_order' => 3,
            ],
            [
                'code' => 'stamps',
                'name' => 'Tarjeta de sellos',
                'description' => 'Sistema de sellos digitales para recompensar la lealtad de tus clientes.',
                'supports_rewards' => true,
                'supports_balance' => false,
                'supports_stamps' => true,
                'supports_notifications' => true,
                'sort_order' => 4,
            ],
            [
                'code' => 'points',
                'name' => 'Tarjeta de puntos',
                'description' => 'Acumula puntos por compra o visita y canjéalos por recompensas.',
                'supports_rewards' => true,
                'supports_balance' => true,
                'supports_stamps' => false,
                'supports_notifications' => true,
                'sort_order' => 5,
            ],
            [
                'code' => 'discount_levels',
                'name' => 'Descuento por niveles',
                'description' => 'Asigna descuentos por niveles según el gasto acumulado del cliente.',
                'supports_rewards' => true,
                'supports_balance' => false,
                'supports_stamps' => false,
                'supports_notifications' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($types as $type) {
            CardType::updateOrCreate(
                ['code' => $type['code']],
                $type
            );
        }
    }
}
