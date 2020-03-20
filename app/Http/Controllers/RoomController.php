<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;

class RoomController extends Controller
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
        ->getReference('rooms');
        
        $posts = $references->getValue();
        
        foreach($posts as $post){
            $all_post[] = $post;
        }

        //$value = $references->getValue();
        /*
        $newPost = $database
            ->getReference('post')
            ->push([
                'title' => 'Post title',
                'body' => 'This should probably be longer.'
            ]);

        $newPost->getKey(); // => -KVr5eu8gcTv7_AHb-3-
        $newPost->getUri(); // => https://my-project.firebaseio.com/blog/posts/-KVr5eu8gcTv7_AHb-3-
        */
        //return json_encode($all_post);
        return view('rooms.allrooms', compact('all_post'));
    
    }

    public function room($id){
             
        $factory = (new Factory)
            ->withServiceAccount(__DIR__.'/firebasekey.json');  
        $database = $factory->createDatabase();

        // get all posts
        $references = $database
        ->getReference('rooms/'.$id);
        
        $posts = $references->getValue();
        
        foreach($posts as $post){
            $all_post[] = $post;
        }
        //return $all_post;
        return view('rooms.dashboard', compact('all_post'))->with('kamer', $id); ;
    }

    public function edit($id){
        $url =  url()->previous();

        $var = preg_split("/\//", $url);
        $found_post;
        $factory = (new Factory)
        ->withServiceAccount(__DIR__.'/firebasekey.json');  
        $database = $factory->createDatabase();

        // get all posts
        $references = $database
        ->getReference('rooms/'.$var[4]);
        $posts = $references->getValue();
        foreach($posts as $post){
            if($post != null)

           if($post['id'] == $id){
                $found_post = $post;
           }
        
        }

        return view('rooms.edit')
                ->with('post', $found_post);
                

    }

    public function update(Request $request, $room){
        $factory = (new Factory)
        ->withServiceAccount(__DIR__.'/firebasekey.json');
        $database = $factory->createDatabase();
         // get all posts
         $url =  url()->previous();

         $var = preg_split("/\//", $url);
         $spot;
         $references = $database
        ->getReference('rooms/'. $room);
        $posts = $references->getValue();
        foreach($posts as $post){
            if($post != null)

           if($post['id'] == $var[4]){
                $spot = $post['spot'];
           }
        
        }
        $database->getReference('rooms/'. $room . '/' . $spot)->update([
            'description' => $request->get('description'),
        ]); 

        notify()->success('Room updated successfully');
        return view('rooms.allrooms');
    }
}
