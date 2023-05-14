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
    
    
    public function events(){
        return Events::query();       
    }
    /**
    * Display a listing of the resource.
    */
    public function show(){
        
        $events=Events::all();
        return (compact("events"));
    }
    
    public function verify($element,$queryUrl){
        (array_key_exists("$element",$queryUrl)&& $queryUrl["$element"]) ? true : false; 
        
    }
    
    public function filterLimit($limit, $events){
        try {
            $num=number_format($limit,0);
        } catch(Throwable $e) {
            $num=0;
        }
        return $events->take($num);
    }

    public function filterName($name,$events){

        return $events->where("name","like", "%".$name."%");
    }
    
    public function filterPrice($value,$events){
        return in_array($value, ['asc', 'desc']) ? $events->orderBy("price",$value) : $events;
    }

    public function filterDate($value, $events) {
        return in_array($value, ['asc', 'desc']) ? $events->orderBy("date",$value) : $events;
    }

    public function filterLocation($value, $events) {
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
        // dump( $filters[$filterKey]);
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    /**
    * Show the form for creating a new resource.
    */
    public function create(Request $request){
        $event=$request->getContent();
        $ernesto=json_decode($event,true);
        if( is_array($ernesto)){
            Events::create([
                "name"=>$ernesto["name"],
                "description"=>$ernesto["description"],
                "date"=>$ernesto["date"],
                "place"=>$ernesto["place"],
                "price"=>$ernesto["price"],
            ]);
        }
    }
    
    
    
    /**
    * Store a newly created resource in storage.
    */
    public function store(Request $request)
    {
        $validate= $request->validate([
            "name"=>"max:255",
            "description"=>"max:255",
            "date"=>"nullable",
            "place"=>"max:255",
            "price"=>"nullable"
        ]);
        // log($request->getContent()->name);
        // $output = $request;
        // if (is_array($output))
        //     $output = implode(',', $output);
        return $request->getContent();
        //    return "$event";
    }
    
    /**
    * Display the specified resource.
    */
    // public function show(Events $events)
    // {
        //     //
        // }
        
        /**
        * Show the form for editing the specified resource.
        */
        public function edit(Events $events)
        {
            //
        }
        
        /**
        * Update the specified resource in storage.
        */
        public function update(Request $request, Events $events)
        {
            //
        }
        
        /**
        * Remove the specified resource from storage.
        */
        public function destroy(Events $events)
        {
            //
        }
    }
    