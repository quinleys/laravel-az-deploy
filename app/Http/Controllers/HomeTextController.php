<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Facades\Storage;
use Validator;

class HomeTextController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $factory = (new Factory)
        ->withServiceAccount(__DIR__.'/firebasekey.json');  
        $database = $factory->createDatabase();

        // get all posts
        $references = $database
        ->getReference('home');
        
        $posts = $references->getValue();
        
        foreach($posts as $post){
            $all_post[] = $post;
        }
        return view('hometext.dashboard',compact('all_post'));
    }
    
    public function edit($id){

        $found_post;
        $factory = (new Factory)
        ->withServiceAccount(__DIR__.'/firebasekey.json');  
        $database = $factory->createDatabase();

        // get all posts
        $references = $database
        ->getReference('home');
        $posts = $references->getValue();

        foreach($posts as $post){
           if($post['id'] == $id){
                $found_post = $post;
           }
        }

        return view('hometext.edit')->with('post', $found_post);
    }

    public function update(Request $request, $id){

        $factory = (new Factory)
        ->withServiceAccount(__DIR__.'/firebasekey.json');

        $database = $factory->createDatabase();

        $storage = (new Factory())
        ->withServiceAccount(__DIR__.'/firebasekey.json')
        ->createStorage();

        $validator = Validator::make($request->all(), [
            'audio' => 'required|mimes:mpga',
        ]);

            if($validator->fails()){
                notify()->error('Audio not updated successfully');
                return redirect()->back();
                //return redirect()->back()->withErrors($validator)->withInput();
            }else {
                if ( $request->hasFile('audio')){

                if($id =="films"){
                    $name = 'films';
                }if($id =="games"){
                    $name = 'games';
                }if($id =="lap"){
                    $name = 'lap_hi';
                }if($id =="lucas"){
                    $name = 'lucas_hi';
                }if($id =="tour"){
                    $name = 'tour';
                }if($id =="welcome"){
                    $name = 'welcome';
                }if($id =="who"){
                    $name = 'who';
                }

                $file = $request->file('audio');
                $extension = $file->getClientOriginalExtension();
                $fullname = $name .'.'. $extension;
                $fullpathname = 'audios/' . $fullname;
                $path = Storage::disk('public')->putFileAs('', $file, $fullname);
                $content = Storage::disk('public')->get($path);
                
                $defaultBucket = $storage->getBucket();

                $defaultBucket->upload(
                    $content,
                    [
                        'name' => $fullpathname
                    ]);
                    $database->getReference('home/'.$id)->update([
                        'audio' => $fullname
                    ]);
                

                notify()->success('Audio updated successfully');
                return redirect()->route('general.index');
                }
                
            }
    }
}
