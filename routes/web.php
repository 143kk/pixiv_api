<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Illuminate\Support\Facades\DB;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'img'], function () use ($router){
	$router->get('date/{date}', function ($date){
		return DB::collection('img')->where('wdate', $date)->orderBy('rank', 'asc')->get();
	});
	$router->get('artist/{id}', function ($id){
		return DB::collection('img')->where('user_id', (int)$id)->get();
	});
	$router->get('shuffle', function () {
		return DB::collection('img')->orderBy('random', 'desc')->where('random', '<', lcg_value())->first();
	});
	$router->get('id/{id}', function ($id){
		return DB::collection('img')->where('illust_id',  (int)$id)->get();
	});
	$router->get('crossone', function (){
		$res = DB::collection('img')->raw(function ($collection) {
			return $collection->aggregate([
				['$group' => array('_id' => '$illust_id', 'num_tutorial' => array('$sum' => 1))],
				['$sort' => array('num_tutorial'=> -1)],
				['$match' => array('num_tutorial' => array('$gt' => 1))]
			]);
		});
		return $res->toArray();
	});
	$router->group(['prefix' => 'search'], function () use ($router){
		$router->get('tag/{tags}', function ($tags) {
			return DB::collection('img')->where('tags', 'like', '%'.$tags.'%')->get();
		});
		$router->get('id/{id}', function ($id) {
			return DB::collection('img')->where('illust_id', '=', (int)$id)->get();
		});
		$router->get('userid/{id}', function ($id) {
			return DB::collection('img')->where('user_id', '=', (int)$id)->get();
		});
	});
});

$router->group(['prefix' => 'date'], function () use ($router){
	$router->get('latest', function (){
		return DB::collection('date')->orderBy('date', 'desc')->first();
	});
	$router->get('normal', function (){
		return DB::collection('date')->orderBy('date', 'desc')->take(5)->get();
	});
	$router->get('page/{page}', function ($page) {
		$p = $page -1;
		return DB::collection('date')->orderBy('date','desc')->offset($p*5)->limit(5)->get();
	});
	$router->get('next/{date}', function ($date) {
		return DB::collection('date')->orderBy('date','asc')->where('date', '>', $date)->first();
	});
	$router->get('pre/{date}', function ($date) {
		return DB::collection('date')->orderBy('date','desc')->where('date', '<', $date)->first();
	});
});
