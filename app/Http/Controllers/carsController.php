<?php

namespace App\Http\Controllers;

use App\Models\Cars;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class CarsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   //on recupèer toutes les information dans la base de donnée avec la methode all
        $cars = Cars::all();
        if (count($cars) <= 0) {
            return response(["message" => 'aucune voiture de disponible'], 200);
        }
        //on l affiche avec reponse qui renvoye une reponse json
        // return view("merci");
        return response($cars);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   // on effectue la validation avant de les insérer dans la base de donnée
        $CarValidation = $request->validate([
            "model"  => ['required', 'string'],
            "price"  => ['required', 'numeric'],
            "desciption" => ['required', 'string'],
            "user_id" => ['required', 'numeric']
        ]);
        // on effectue la creation d un nouvelle element dans la base de donnée on peut utilisé aussi l instenciation et 
        //et applique la methode save sur l instance
        $car = Cars::create([
            'model'  => $CarValidation['model'],
            'price' => $CarValidation['price'],
            'desciption' => $CarValidation['desciption'],
            'user_id' => $CarValidation['user_id'],
        ]);
        return response(["message" => 'voiture ajouté'], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cars  $cars
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {    //on verifie si la voiture est dans notre base de donnée sinon on renvoye un message avec la methode exists()
        if(Cars::where('id',$id)->exists()){
            $car = DB::table('cars')->join('users', 'cars.user_id', '=', 'users.id')
                ->select('cars.*', 'users.name', 'users.email')
                ->where('cars.id', '=', $id)
                ->get();
            return $car;
        }

            return response(["message"=>"aucune voiture n'est disponible pour l'id $id"]);
      
       
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cars  $cars
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  $id)
    {   //avant de modifier valide les donnée ici c est seulement user_id qui est requit
        $CarValidation = $request->validate([
            "model"  => ['string'],
            "price"  => [ 'numeric'],
            "desciption" => ['string'],
            "user_id" => ['required', 'numeric']
        ]);
        $car = Cars::find($id);
        if (!$car) {
            return response(["message" => "aucune voiture n'est disponible pour l'id $id"], 403);
        }
        if($car->id !=$CarValidation["user_id"]){
            return response(["message" => "Vous n'etes pas autorisé à modifier cet $id car les id sont differents"], 403);

        }
        $car->update($CarValidation);

            return response(["message" => "modification éffectuée sur cet id $id"], 200);
 
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cars  $cars
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $CarValidation = $request->validate([
            "user_id" => ['required', 'integer']
        ]);
        $car = Cars::find($id);
        if (!$car) {
            return response(["message" => "aucune voiture n'est disponible pour l'id $id"], 403);
        }
        if($car->id !=$CarValidation["user_id"]){
            return response(["message" => " pas de correspondance $id"], 403);

        }
        Cars::destroy($id);
        return response(["message" => "Voiture supprimé!!! "], 200);

    }
}
