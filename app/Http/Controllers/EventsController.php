<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Events;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
            'place'=> 'filterLocation'
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
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255|min:4',
            'description' => 'required|string|max:255|min:4',
            'date' => 'required|date',
            'place' => 'required|string|max:255|min:4',
            'price' => 'required|numeric'
        ]);        
        if ($validator->fails()) {
            return response(['message' => 'Hai fallito la creazione di un Evento'], 403);
        }
        
        Events::create([
            "name"=>$request["name"],
            "description"=>$request["description"],
            "date"=>$request["date"],
            "place"=>$request["place"],
            "price"=>$request["price"],
        ]);
        
    }
    public function update(Request $request,$id){ 
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|min:4',
            'description' => 'required|string|max:255|min:4',
            'date' => 'required|date',
            'place' => 'required|string|max:255|min:4',
            'price' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return response(['message' => 'Errore'], 403);
        }
        $event=Events::where("id",$id)->first();
        $event->name=$request->name;
        $event->description=$request->description;
        $event->date=$request->date;
        $event->place=$request->place;
        $event->price=$request->price;
        $event->save();
    }
}
