<?php

namespace App\Http\Middleware;

use Closure;
use App\Business;
use Illuminate\Support\Facades\Auth;

class DayEnd
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   
        if(Auth::guard('customer')->check()){
            return $next($request);
        }
        $business_id = request()->session()->get('user.business_id');
        $business_exist  = Business::where('id', $business_id)->first();
        if(empty($business_id) || empty($business_exist)){
            return redirect('/logout');
        }
        $day_end = Business::where('id', $business_id)->select('day_end')->first()->day_end;
        $day_end_enable = Business::where('id', $business_id)->select('day_end_enable')->first()->day_end_enable;

        if($day_end_enable == 1){
            if($day_end == 0  || auth()->user()->can('day_end.bypass')){
                return $next($request);
            }else{
                return abort(403, 'Day has been Ended');
            }

        }else{
            return $next($request);
        }
    }
}
