<?php

namespace App\Http\Middleware;

use Closure;
use App;
use App\Libraries\API;

/**
 * Class middleware of access token middleware
 *
 * @author cmooy
 */
class OAuthMiddleware
{
	/**
	 * Create a new oauth user middleware instance.
	 *
	 * @param App\Libraries\API $api
	 */
	public function __construct(API $api)
	{
		$this->api = $api;
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \Closure $next
	 *
	 * @throws App
	 *
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$input 					= $request->input();
		$input['HTTP_HOST'] 	= $request->server('HTTP_HOST');

		$is_auth				= json_decode($this->api->OauthMiddleware($input), true);

		if($is_auth['status']!='success')
		{
			App::abort(404);
		}

		return $next($request);
	}
}
