<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Actions\Fortify\PasswordValidationRules;

class AuthenticationController extends Controller
{
    use PasswordValidationRules;
    public function login(Request $request){
        //Controlla la validazione dei dati in Entrata
        $validated=$request->validate(["username"=>["required","string","max:100"],"password"=>["required","string","min:8"],]);
        //Confronta e cerca l'user dall'username
        $user=User::where("username",$request->username)->first();
        
        //Errore se $user non trova corrispondenza dall'username della richiesta
        if(is_null($user)){
            abort(404,"Non sei ancora registrato");
        }
        
        //Cancella ogni token dell'user per evitare che ci sia un eccesso di tokens
        $user->tokens()->delete();
        
        //Errore se $user non da true e se non corrispondono le password date con il $request
        if(!$user||!Hash::check($request->password,$user->password)){
            throw ValidationException::withMessages(["email"=>["Le credenziali sono incorrette"]]);
        }
        //Creazione Token con l'id dell'Username
        $token=$user->createToken($request->username)->plainTextToken;
        return response()->json([
            "message"=>"Login con successo",
            "authToken"=>$token
        ]);
    }
    public function logout(){
        $user=auth('sanctum')->user();
        $user->tokens()->delete();
        return response()->json(["message"=>"sei sloggato"],200);
    }
    public function register(Request $request){
        $validated=$request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username'=>['required','string','min:3','max:60', Rule::unique(User::class)],
            'email' => ['required','string','email','max:255',Rule::unique(User::class)],
            'password' => $this->passwordRules(),
        ]);
        if($validated){
            $user=User::create($request->all());
            return response([
                'message' => 'Success'
            ], 200);
        }
        return response([
            'message' => 'Failed'
        ], 403);
    }
}
