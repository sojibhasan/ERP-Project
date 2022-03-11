<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Utils\BusinessUtil;
use Illuminate\Support\Facades\Auth;
use Litespeed\LSCache\LSCache;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    use AuthenticatesUsers;
    /**
     * All Utils instance.
     *
     */
    protected $businessUtil;
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = '/home';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil)
    {
        $this->middleware('guest')->except('logout');
        $this->businessUtil = $businessUtil;
    }
    /**
     * Change authentication from email to username
     *
     * @return void
     */
    public function username()
    {
        return 'username';
    }
    public function logout()
    {
        request()->session()->flush();
        LSCache::purge('*');
        \Auth::logout();
        $id = request()->id;
        if(!empty($id)){
            return redirect('/?id='.$id);
        }else{
            return redirect('/');
        }
    }
    /**
     * The user has been authenticated.
     * Check if the business is active or not.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        if (!$user->business->is_active) {
            \Auth::logout();
            return redirect('/login')
              ->with(
                  'status',
                  ['success' => 0, 'msg' => __('lang_v1.business_inactive')]
              );
        } elseif ($user->status != 'active') {
            \Auth::logout();
            return redirect('/login')
              ->with(
                  'status',
                  ['success' => 0, 'msg' => __('lang_v1.user_inactive')]
              );
        }
    }
    protected function redirectTo()
    {
        $user = \Auth::user();
        if($user->is_pump_operator){
            return '/petro/pump-operators/dashboard';
        }
        if (!$user->can('dashboard.data') && $user->can('sell.create')) {
            return '/pos/create';
        }
        return '/home';
    }
	public function login(Request $request)
	{
		$this->validate($request,[
            'username'=>'required',
            'password'=>'required',
			'g-recaptcha-response'=>'required',
        ]);
		$credentials = $request->only('username','password');
		$remember = $request->get('remember');
		if (Auth::attempt($credentials,$remember)) {
			return redirect('/home');
        }
		else
		{
			return redirect('/login');
		}
	}
    public function custom(){
        if(Auth::check()){
        }else{
            $this->showLoginForm();
        }
    }
}
