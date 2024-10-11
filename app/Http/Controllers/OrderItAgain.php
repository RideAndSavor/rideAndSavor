<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;
use App\Traits\CanLoadRelationships;
use App\Http\Resources\RestaurantResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderItAgain extends Controller
{
    use CanLoadRelationships;
    private array $relations = [
        'ratings',
        'comments',
        'restaurantImages'
    ];
    public function __invoke(): AnonymousResourceCollection|JsonResponse
    {
        $start_date = now()->subDays(config('variable.SEVEN'));
        $end_date = now();

        $restaurantsWithRatingCommentImages = $this->loadRelationships(Restaurant::mostFoodOrderRestaurantsByUser($start_date, $end_date))->get();
        //dd($query->get());
        // $restaurantsWithRatingCommentImages = cache()->remember(
        //     'order-it-again',
        //     now()->addMinutes(5),
        //     fn(): Collection =>
        //     Restaurant::mostFoodOrderRestaurantsByUser($start_date, $end_date)->get()
        // );

        if ($restaurantsWithRatingCommentImages->isEmpty()) {
            return response()->json([
                'message' => 'No restaurant match the given criteria',
                'data' => [],
            ], status: 204);
        }
        return RestaurantResource::collection($restaurantsWithRatingCommentImages);
        // dd($restaurantsWithRatingCommentImages);

    }
}

