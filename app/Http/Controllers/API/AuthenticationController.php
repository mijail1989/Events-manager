<?php

namespace App\Http\Controllers\API;

use App\Actions\Fortify\CreateNewUser;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    
    public function login(Request $request){
        //Controlla la validazione dei dati in Entrata
        $validated=$request
        ->validate(["username"=>["required","string","max:100"],
                    "password"=>["required","string","min:8"],]);
        //Confronta e cerca l'user dall'username
        $user=User::where("username",$request->username)->first();

        //Errore se $user non trova corrispondenza dall'username della richiesta
        if(is_null($user)){abort(404,"Non sei ancora registrato");}

        //Cancella ogni token dell'user per evitare che ci sia un eccesso di tokens
        $user->tokens()->delete();

        //Errore se $user non da true e se non corrispondono le password date con il $request
        if(!$user||!Hash::check($request->password,$user->password))
        {
            throw ValidationException::withMessages(["email"=>["Le credenziali sono incorrette"]]);
        }
        //Creazione Token con l'id dell'Username
        $token=$user->createToken($request->username)->plainTextToken;
        return response()->json([
            "message"=>"Login con successo",
            "authToken"=>$token]);
    }
     public function logout(){
        $user=auth()->user();
        $user->tokens()->delete();
        return response()->json(["message"=>"sei sloggato"],200);
    }
    public function register(Request $request){
        $user=(new CreateNewUser)->create($request->all());
        return response()->json([],204);
    }

    public function status(){
        $utente=auth()->user();
        print($utente);
        if($utente){
            return "sei loggato $utente->username ";
        }
        return "non sei loggato coglione";
    }

}
