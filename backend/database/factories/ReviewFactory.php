<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        $texts = [
            "Application très rapide et intuitive, j'adore le design.",
            "Bonne expérience mais quelques bugs sur mobile.",
            "Service client excellent, livraison rapide, très satisfait.",
            "Trop cher pour ce que c'est, déçu par la qualité.",
            "Interface propre mais il manque des fonctionnalités importantes.",
            "Super performance, je recommande à 100%.",
            "Temps de réponse lent et navigation confuse.",
            "Très bon rapport qualité/prix, facile à utiliser.",
            "Moyen : ça marche mais sans plus.",
            "Incroyable ! Support réactif et interface moderne.",
        ];

        return [
            'user_id' => User::factory(),
            'content' => $this->faker->randomElement($texts),
        ];
    }
}
