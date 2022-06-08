<?php

namespace App\Http\Controllers\Api;

use App\Models\Column;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ColumnResource;
use \Symfony\Component\HttpFoundation\Response as ResponseCode;

class ColumnsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ColumnResource::collection(Column::with([
            'cards' => function($q){
                 $q->orderBy('sort_order', 'asc');
             }])->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $column = Column::create($request->all());


        return new ColumnResource($column->load('cards'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Column  $column
     * @return \Illuminate\Http\Response
     */
    public function show(Column $column)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Column  $column
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Column $column)
    {
        $column->update($request->all());
        return new ColumnResource($column->load('cards'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Column  $column
     * @return \Illuminate\Http\Response
     */
    public function destroy(Column $column)
    {
        $column->cards()->delete();
        $column->delete();
        return response(null,ResponseCode::HTTP_NO_CONTENT);
    }
}
