<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Coupon\StoreCouponRequest;
use App\Models\Coupons;
use App\Services\CouponsService;
use Illuminate\Http\Request;

class CouponsController extends Controller
{
    protected $couponsService;
    public function __construct(CouponsService $couponsService)
    {
        $this->couponsService = $couponsService;
    }
    public function index(){
        $coupons = Coupons::all();
        return response()->json($coupons);
    }
    public function store (StoreCouponRequest $r){
        $coupon = Coupons::create($r->validated());
        return response()->json(["message"=> "Coupon created!", "data" => $coupon], 201);
    }
    public function destroy($id) {
        $coupons = Coupons::findOrFail($id);
        $coupons->delete();
        return response()->json(["message" => "Coupon deleted!", "data" => $coupons]);
    }
}
