<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Events;
use Faker\Core\Number;
use Hamcrest\Arrays\IsArray;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\Print_;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Scheduling\Event;
use Spatie\LaravelIgnition\Recorders\DumpRecorder\Dump;

class EventsController extends Controller
{
    
    
    private function events(){
        return Events::query();       
    }
    /**
    * Display a listing of the resource.
    */
    
    private function filterLimit($limit, $events){
        try {
            $num=number_format($limit,0);
        } catch(Throwable $e) {
            $num=0;
        }
        return $events->take($num);
    }
    
    private function filterName($name,$events){
        
        return $events->where("name","like", "%".$name."%");
    }
    
    private function filterPrice($value,$events){
        return in_array($value, ['asc', 'desc']) ? $events->orderBy("price",$value) : $events;
    }
    
    private function filterDate($value, $events) {
        return in_array($value, ['asc', 'desc']) ? $events->orderBy("date",$value) : $events;
    }
    
    private function filterLocation($value, $events) {
        return $events->where("place","like", "%".$value."%");
    }
    
    
    public function index(Request $request)
    {   
        $events=$this->events();
        $queryUrl=$request->query();
        $filters = array(
            'limit' => 'filterLimit',
            'name'=> 'filterName',
            "price"=>'filterPrice',
            'date'=> 'filterDate',
            'location'=> 'filterLocation'
        );
        $filterKeys=array_keys($filters);
        foreach($filterKeys as $filterKey){
            if(array_key_exists($filterKey,$queryUrl) && $queryUrl[$filterKey]){
                $callback = $filters[$filterKey];
                $events = $this->$callback($queryUrl[$filterKey], $events);
            }
        }
        $events = $events->get();
        return $events;
    }
    
    public function getEvent($id)
    {
        $events=$this->events()->where('id',$id)->first();
        return $events; 
    }
    
    /**
    * Show the form for creating a new resource.
    */
    public function create(Request $request)
    {
        $event=$request->getContent();
        $requestBody=json_decode($event,true);
        if( is_array($requestBody)){
            Events::create([
                "name"=>$requestBody["name"],
                "description"=>$requestBody["description"],
                "date"=>$requestBody["date"],
                "place"=>$requestBody["place"],
                "price"=>$requestBody["price"],
            ]);
        }
    }
}
