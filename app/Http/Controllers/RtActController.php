<?php

namespace App\Http\Controllers;

use App\RtAct;
use App\Driver;
use App\Sto;
use App\Car;
use App\CarCard;
use App\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mail;
use Dompdf\Dompdf;

class RtActController extends Controller
{
    private $htmlToPdf = '';
    /**
     * 
     */
    public function getById($id)
    {
        $act = RtAct::where('id', $id)->with('checklist_items.rt_act_checklist')->first();

        return response()->json([
            'act' => $act
        ]);
    }
    /**
     * Store a newly created RT act in the db.
     */
    public function store(Request $request)
    {
        if($request->company_to_type === 'inner') {
            $company = Company::where('company_name', $request->company_to)->first();
            $card = CarCard::find($request->car_card_id);
            $carId = $card->car_id;
            DB::table('car_company')->where('car_id', $card->car_id)->update([ 'company_id' => $company->id ]);
        } else if($request->company_to_type === 'outer') {
            $card = CarCard::find($request->car_card_id);
            $car = Car::find($card->car_id);
            $car->sold = 1;
            $car->save();
        }

        $files = [];
        if($request->hasFile('attachments')) {
            foreach($request->attachments as $file) {
                $fileFullName = $file->getClientOriginalName();                 
                $fileExtension = $file->getClientOriginalExtension();
                $fileNameToStore = uniqid().'.'.$fileExtension;
                $path = $file->move(public_path('/uploads/rt_act_files'), $fileNameToStore);
                
                array_push($files, [
                    'file' => $fileNameToStore,
                    'name' => $fileFullName
                ]);
            }
        } else {
            $files = null;
        }

        $act = new RtAct($request->all());
        $act->files = json_encode($files, JSON_UNESCAPED_UNICODE);
        $act->save();        

        $this->htmlToPdf = $request->htmlToPdf;
            
        $fileName = $this->generateActFile();

        $driver = Driver::find($request->driver_id);
        if($driver->email !== null) {
            Mail::send('welcome', [], function($message) use ($driver, $fileName) {
                $message->subject('Акт приема передачи автомобиля');
                $message->from('sto@the55group.com');
                $message->to($driver->email);
                $message->attach(public_path('/uploads/rt_acts/'.$fileName), [
                    'as' => 'Акт приема передачи',
                    'mime' => 'application/pdf'
                ]);
            });
        }                

        $values = json_decode($request->values);
        for($i = 0; $i < count($values); $i++) {
            if($values[$i] !== null) {
                $act->checklist_items()->attach($values[$i]->id, [
                    'status' => $values[$i]->status,
                    'comment' => $values[$i]->comment
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Акт приема передачи успешно создан.',
            'file' => $fileName,
            'data' => $request->all()
        ]);
    }

    /**
     * 
     */
    public function getFullInfo(Request $request)
    {
        $sto = Sto::where('slug', $request->sto_slug)->with('companies')->first();
        $car = Car::select('cars.*', 'shape_name', 'brand_name', 'model_name', 'engine_type_name', 'transmission_name')
                        ->join('car_shapes', 'car_shapes.id', '=', 'cars.shape_id')
                        ->join('car_models', 'car_models.id', '=', 'cars.model_id')
                        ->join('car_brands', 'car_brands.id', '=', 'cars.brand_id')
                        ->join('engine_types', 'engine_types.id', '=', 'cars.engine_type_id')
                        ->join('transmissions', 'transmissions.id', '=', 'cars.transmission_id')
                        ->with('attachments', 'card.comments.user', 'card.defect_acts.defect_options.defect', 'card.defect_acts.equipment')
                        ->where('sold', 0)
                        ->where('cars.id', $request->car_id)->with([ 'companies', 'drivers' => function($q) {
                            $q->where('active', 1)->get();
                        }])->first(); 
        $companies = $sto->companies;

        return response()->json([
            'car' => $car,
            'companies' => $companies
        ]);
    }

    public function downloadFile(Request $request) 
    {
        return response()->download(public_path('uploads/car_cover_images/1528122979.jpg'));
    }

    public function generateActFile()
    {   
        $fileName = uniqid().'.pdf';
        $dompdf = new Dompdf();
        $dompdf->loadHtml($this->htmlToPdf);
        $dompdf->render();
        file_put_contents(public_path('/uploads/rt_acts/'.$fileName), $dompdf->output());

        return $fileName;
    }
}
