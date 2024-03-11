<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\WebService;
//use App\Http\Requests\StoreWebServiceRequest;

class AdminController extends Controller
{
    //管理画面
    function index(){
        $web_services = WebService::all();
        return view('web_service.dashboard.dashboard', compact('web_services'));
    }

    //新規登録処理
    function store(Request $request){
        $data = $request->only('lineup', 'description', 'price', 'image');
        $rules = [
            'lineup' => 'required|string|max:40|no_space',
            'description' => 'required|string|max:255|no_space',
            'price' => 'required|string|no_space',
            'image' => 'required|file|image|mimes:jpeg,jpg,png|max:5120',
        ];
        
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return redirect()->route('store_result')->withErrors($validator);
        }

        /*if($request->hasFile('image')){
            $image = $request->file('image');
			$path = $image->store('up-images', 'public');
			if($path){
				$imageData = [
					"file_name" => $image->getClientOriginalName(),
					"file_path" => $path
				];
			}
		}*/
        $path = $data['image']->store('up-images', 'public');
        $data['image'] = $path;
        //dd($data);
        
        $webService = new WebService();
        $webService->lineup = $data['lineup'];
        $webService->description = $data['description'];
        $webService->price = $data['price'];
        $webService->file_path = $data['image'];
        $webService->save();

        return redirect()->route('store_result')->with('message', $data['lineup'] . 'サービスの登録が完了しました。');
    }

    //新規登録完了画面
    function showStoreResult(){
        return view('web_service.dashboard.store_result');
    }

    //編集画面
    function showEdit(WebService $web_service){
        return view('web_service.dashboard.edit', compact('web_service'));
    }

    //更新処理
    function upload(Request $request, WebService $web_service){
        $data = $request->only('lineup', 'description', 'price', 'image');
        $rules = [
            'lineup' => 'required|string|max:40|no_space',
            'description' => 'required|string|max:255|no_space',
            'price' => 'required|string|no_space',
            'image' => 'required|file|image|mimes:jpeg,jpg,png|max:5120',
        ];
        
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return redirect()->route('upload_result', $web_service)->withErrors($validator);
        }

        $path = $data['image']->store('up-images', 'public');
        $data['image'] = $path;
        
        $webService = new WebService();
        $webService->lineup = $data['lineup'];
        $webService->description = $data['description'];
        $webService->price = $data['price'];
        $webService->file_path = $data['image'];
        $webService->save();

        return redirect()->route('upload_result', $web_service)->with('message', $data['lineup'] . 'サービスの更新が完了しました。');
    }

    //更新完了画面
    function showUploadResult(){
        return view('web_service.dashboard.upload_result');
    }

    //削除処理
    function delete(WebService $web_service){
        $image = $web_service->file_path;
        $web_service->delete();
        if ($image && Storage::disk('public')->exists($image)) {
            Storage::disk('public')->delete($image);
        }
        return redirect()->route('delete_result');
    }

    //削除完了画面
    function showDeleteResult(){
        return view('web_service.dashboard.delete_result');
    }
}
