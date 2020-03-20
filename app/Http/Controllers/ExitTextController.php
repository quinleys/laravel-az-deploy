<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Facades\Storage;

class ExitTextController extends Controller
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
        ->getReference('exit');
        
        $posts = $references->getValue();
        
        foreach($posts as $post){
            $all_post[] = $post;
        }
        return view('exittext.dashboard',compact('all_post'));
    }
    
    public function edit($id){

        $found_post;
        $factory = (new Factory)
        ->withServiceAccount(__DIR__.'/firebasekey.json');  
        $database = $factory->createDatabase();

        // get all posts
        $references = $database
        ->getReference('exit');
        $posts = $references->getValue();

        foreach($posts as $post){
           if($post['id'] == $id){
                $found_post = $post;
           }
        }

        return view('exittext.edit')->with('post', $found_post);
    }
    
    public function update(Request $request, $id){

        $factory = (new Factory)
        ->withServiceAccount(__DIR__.'/firebasekey.json');

        $database = $factory->createDatabase();

        $database->getReference('exit/exit_message')->update([
            'title' => $request->get('title'),
        ]); 

        notify()->success('Character updated successfully');
        return redirect()->route('exittext.index');
    }
}


