<?php

namespace App\Http\Controllers\Offers;

use App\Http\Controllers\Controller;
use App\Http\Requests\OfferRequest;
use App\Models\Offer;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CrudController extends Controller
{
    public function getLocalizedLangsForNavBar() {
        $getLocalized = '';
        foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties) {
            $getLocalized .= "<li><a href='".LaravelLocalization::getLocalizedURL($localeCode, null, [], true)."'>$properties[native]</a></li>";
        };
        return $getLocalized;
    }

    public function showOffer()
    {
        $currentLocale = LaravelLocalization::getCurrentLocale();
        $allOffers = Offer::select(
            "id",
            "offerName_$currentLocale as offerName",
            "price",
            "details_$currentLocale  as details"
        )->get();

        return Inertia::render('Offers/offers', [
            'langs' => __('messages'),
            'getLocalizedURL' => $this->getLocalizedLangsForNavBar(),
            'allOffers' => $allOffers,
        ]);
    }

    public function createOffer()
    {
        return Inertia::render('Offers/offers', [
            'createOffer' => Route::has('offer.create'),
            'langs' => __('messages'),
            'getLocalizedURL' => $this->getLocalizedLangsForNavBar(),
        ]);
    }

    public function storeOffer(OfferRequest $request)
    {
        Offer::create($request -> all());
    }

    public function editOffer($id)
    {
        $offer = Offer::find($id);

        if($offer) {
            return Inertia::render('Offers/offers', [
                'createOffer' => true,
                'langs' => __('messages'),
                'getLocalizedURL' => $this->getLocalizedLangsForNavBar(),
                'update' => true,
                'updateData' => $offer,
            ]);
        }else {
            return redirect('offers');
        }
    }
    public function updateOffer(OfferRequest $request)
    {
        $offer = Offer::find($request->id);
        $offer->update($request -> all());
        return redirect()->route('offers');
    }
    public function deleteOffer($id)
    {
        $offer = Offer::find($id);
        if($offer) {
            $offer->delete();
        }else {
            return redirect()->back();
        }
    }
}