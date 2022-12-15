<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\MediaService;
use Illuminate\Support\Facades\Storage;

class AdminCardController extends Controller
{

    public function __construct()
    {
        $this->middleware('isAdmin');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.cards.index')->with([
            'cards' => Card::paginate(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // use make() in order to not persist model in database
        $card = Card::factory()->make();
        $categories = Category::all();
        return view('admin.cards.create')->with([
            'card' => $card,
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Card $card)
    {
        // no need to filter file since collect is used
        $filtered = $request->collect()
                            ->except(['_token', 'categories']);

        $filtered->each(         // on ->each, the order of $key $value gets flipped
            fn ($value, $key) => $card->$key = $value
        );
        $card->save();

        // get images
        MediaService::addMedia($card, collect($request->file('image')));

        // attach categories to card
        $card->categories()->attach($request->categories);
        
        return redirect()->route('cards.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Card $card)
    {
        return view('admin.cards.show')->with(['card' => $card]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Card $card)
    {
        $card->makehidden('id');
        return view('admin.cards.edit')->with([
            'card' => $card
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Card $card)
    {
        $filtered = $request->collect()
                            ->except(['_token', '_method']);

        $filtered->each(         // on ->each, the order of $key $value gets flipped
            function ($value, $key) use ($card) {
                if ($card) {
                    $card->$key = $value;
                }
            });

        $card->save();
        return redirect()->route('cards.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Card::destroy($id);
        return redirect()->route('cards.index');
    }


}
