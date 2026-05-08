<?php

namespace App\Http\Middleware;

use App\Models\Page;
use App\Models\Web\WebPage;
use Closure;
use Illuminate\Support\Facades\URL;

class CheckPageStatus
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
        //check this page is active or not
        $pageSlug = last(explode('/',URL::current()));
        if(!$pageStatus = WebPage::where('slug',$pageSlug)->Active()->first())
            return redirect()->route('home');

        $request->merge(['pageDetail' => $pageStatus]);

        return $next($request);
    }
}
