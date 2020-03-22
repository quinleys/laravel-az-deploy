<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Facades\Storage;
use Validator;

class WhoController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(){
        $factory = (new Factory)
        ->withServiceAccount(__DIR__.'/firebasekey.json');  
    $database = $factory->createDatabase();

    // get all posts
    $references = $database
    ->getReference('who_is_who');
    
    $posts = $references->getValue();
    
    foreach($posts as $post){
        if($post != null)
        $all_post[] = $post;
    }
    return view('who.dashboard', compact('all_post'));
    }
    public function create(){

        $newPost = $database
        ->getReference('who_is_who')
        ->push([
            'title' => 'Post title',
            'body' => 'This should probably be longer.'
        ]);
        return view('who.dashboard');
    }

    public function edit($id){
        $found_post;
        $factory = (new Factory)
        ->withServiceAccount(__DIR__.'/firebasekey.json');  
        $database = $factory->createDatabase();

        // get all posts
        $references = $database
        ->getReference('who_is_who');
        $posts = $references->getValue();

        foreach($posts as $post){
            if($post != null)
           if($post['id'] == $id){
                $found_post = $post;
           }
        }

        return view('who.edit')->with('post', $found_post);
    }
    
    public function update(Request $request, $id){

        
        $factory = (new Factory)
        ->withServiceAccount(__DIR__.'/firebasekey.json');

        $database = $factory->createDatabase();

        $storage = (new Factory())
        ->withServiceAccount(__DIR__.'/firebasekey.json')
        ->createStorage();

        $storageClient = $storage->getStorageClient([
            'projectId' => 'az-sint-lucas-gent'
        ]);

        if ($request->hasFile('file') || $request->hasFile('audio') || $request->hasFile('audio-tags')) {
                if($request->hasFile('audio')){

                    $validator = Validator::make($request->hasFile('audio'), [
                        'audio' => 'required|mimes:mpga',
                    ]);
            
                        if($validator->fails()){
                            notify()->error('Audio not updated successfully');
                            return redirect()->back();
                            //return redirect()->back()->withErrors($validator)->withInput();
                        }else {
                    $file = $request->file('audio');
                    $extension = $file->getClientOriginalExtension();
                    $name = $id .'.'. $extension;

                    $path = Storage::disk('public')->putFileAs('', $file, $name);
                    $content = Storage::disk('public')->get($path);
                    
                    $defaultBucket = $storage->getBucket();
                    $fullpathname = 'audios/' . $name;
                    $defaultBucket->upload(
                        $content,
                        [
                            'name' => $fullpathname
                        ]);
                        $database->getReference('who_is_who/'.$id)->update([
                            'audio' => $name
                        ]);
                    }
                }
                if($request->hasFile('file')){
                
                $file = $request->file('file');
                $name = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                //$path = $request->file('file')->store('upload');
                $fullpathname = 'images/persons/' . $name;
                $path = Storage::disk('public')->putFileAs('', $file, $name);
                $content = Storage::disk('public')->get($path);
                
                $defaultBucket = $storage->getBucket();
                // Upload a file to the bucket.
                $defaultBucket->upload(
                    $content,
                    [
                        'name' => $fullpathname
                    ]);
                    $database->getReference('who_is_who/'.$id)->update([
                        'image' => $name
                    ]);
                    }
                }if($request->hasFile('audio-tags')){

                    $validator = Validator::make($request->hasFile('audio-tags'), [
                        'audio' => 'required|mimes:mpga',
                    ]);
            
                        if($validator->fails()){
                            notify()->error('Audio not updated successfully');
                            return redirect()->back();
                            //return redirect()->back()->withErrors($validator)->withInput();
                        }else {
                    $file = $request->file('audio-tags');
                    $extension = $file->getClientOriginalExtension();
                    $name = $id .'.'. $extension;

                    $path = Storage::disk('public')->putFileAs('', $file, $name);
                    $content = Storage::disk('public')->get($path);
                    
                    $defaultBucket = $storage->getBucket();
                    $fullpathname = 'audios/' . $name;
                    $defaultBucket->upload(
                        $content,
                        [
                            'name' => $fullpathname
                        ]);
                        $database->getReference('who_is_who/'.$id)->update([
                            'audio' => $name
                        ]);
                    }
                }
                $database->getReference('who_is_who/'.$id)->update([
                    'title' => $request->get('title'),
                    'description' => $request->get('description'),
                    'tags' => $request->get('tags'),
                ]); 
        
                notify()->success('Character updated successfully');
                return redirect()->route('whoiswho.index');
                
        }

    
}
