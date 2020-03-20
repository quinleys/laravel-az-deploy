<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Facades\Storage;
use Validator;

class GamesController extends Controller
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
        ->getReference('games');
        
        $posts = $references->getValue();
        
        foreach($posts as $post){
            $all_post[] = $post;
        }
        return view('games.dashboard',compact('all_post'));
    }

    public function edit($id){


        $found_post;
        $factory = (new Factory)
        ->withServiceAccount(__DIR__.'/firebasekey.json');  
        $database = $factory->createDatabase();

        // get all posts
        $references = $database
        ->getReference('games');
        $posts = $references->getValue();

        foreach($posts as $post){
           if($post['id'] == $id){
                $found_post = $post;
           }
        }

        return view('games.edit')->with('post', $found_post);
    }

    public function update(Request $request, $id){

        $factory = (new Factory)
        ->withServiceAccount(__DIR__.'/firebasekey.json');

        $storage = (new Factory())
        ->withServiceAccount(__DIR__.'/firebasekey.json')
        ->createStorage();

        $database = $factory->createDatabase();

        $database->getReference('games/'.  $id )->update([
            'title' => $request->get('title'),
        ]); 

        $validator = Validator::make($request->all(), [
            'audio' => 'required|mimes:mpga',
        ]);

            if($validator->fails()){
                notify()->error('Audio not updated successfully');
                return redirect()->back();
                //return redirect()->back()->withErrors($validator)->withInput();
            }else {
                if ( $request->hasFile('audio')){

                $file = $request->file('audio');
                $extension = $file->getClientOriginalExtension();
                $name = $id .'.'. $extension;
                

                $path = Storage::disk('public')->putFileAs('', $file, $name);
                $content = Storage::disk('public')->get($path);
                
                $defaultBucket = $storage->getBucket();

                $defaultBucket->upload(
                    $content,
                    [
                        'name' => $name
                    ]);
                    $database->getReference('games/'.$id)->update([
                        'audio' => $name
                    ]);
                

                notify()->success('Audio updated successfully');
                return redirect()->route('games.index');
                }
                
            }
    }
}
