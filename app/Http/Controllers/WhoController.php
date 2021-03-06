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
                    if($id == '1'){
                        $name = 'photo_doctor_title';
                    }if($id == '2'){
                        $name = "kid_doctor_title";
                    }if($id == '3'){
                        $name = "clown_title";
                    }if($id == '4'){
                        $name = "sleep_doctor_title";
                    }if($id == '5'){
                        $name = "nurse_title";
                    }if($id == '6'){
                        $name = "game_leader_title";
                    }if($id == '7'){
                        $name = "kid_psychology_title";
                    }
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
                    if($id == '1'){
                        $name = 'photo_doctor_image';
                    }if($id == '2'){
                        $name = "kid_doctor_image";
                    }if($id == '3'){
                        $name = "clown_image";
                    }if($id == '4'){
                        $name = "sleep_doctor_image";
                    }if($id == '5'){
                        $name = "nurse_image";
                    }if($id == '6'){
                        $name = "game_leader_image";
                    }if($id == '7'){
                        $name = "kid_psychology_image";
                    }
                $file = $request->file('file');
                //$name = $file->getClientOriginalName();

                
                $extension = $file->getClientOriginalExtension();
                //$path = $request->file('file')->store('upload');
                $fullname = $name.'.'.$extension;
                $fullpathname = 'images/persons/' . $fullname;
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
                        'image' => $fullname
                    ]);
                    }
                }if($request->hasFile('audio-tags')){
                    if($id == '1'){
                        $name = 'photo_doctor_tags';
                    }if($id == '2'){
                        $name = "kid_doctor_tags";
                    }if($id == '3'){
                        $name = "clown_tags";
                    }if($id == '4'){
                        $name = "sleep_doctor_tags";
                    }if($id == '5'){
                        $name = "nurse_tags";
                    }if($id == '6'){
                        $name = "game_leader_tags";
                    }if($id == '7'){
                        $name = "kid_psychology_tags";
                    }
    
                    $file = $request->file('audio-tags');
                    $extension = $file->getClientOriginalExtension();
                    $fullname = $name .'.'. $extension;

                    $path = Storage::disk('public')->putFileAs('', $file, $name);
                    $content = Storage::disk('public')->get($path);
                    
                    $defaultBucket = $storage->getBucket();
                    $fullpathname = 'audios/' . $fullname;
                    $defaultBucket->upload(
                        $content,
                        [
                            'name' => $fullpathname
                        ]);
                        $database->getReference('who_is_who/'.$id)->update([
                            'audio-tags' => $fullname
                        ]);
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
