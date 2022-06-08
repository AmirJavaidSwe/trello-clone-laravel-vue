<?php

namespace App\Http\Controllers\Api;

use App\Models\Card;
use App\Models\Column;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CardResource;
use App\Http\Resources\ColumnResource;
use \Symfony\Component\HttpFoundation\Response as ResponseCode;

class SortingController extends Controller
{
    /**
     * update darag and drop.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateSort(Request $request)
    {
        $_ids = collect($request->data)->pluck('id')->toArray();
        if(count($_ids)) {
            foreach($request->data as $key => $value) {
                // dd($value);

                Card::where('id', $value['id'])->update([
                    'column_id' => $request->column_id,
                    'sort_order' => $key+1,
                ]);
            }
            return ColumnResource::collection(Column::with([
                'cards' => function($q){
                     $q->orderBy('sort_order', 'asc');
                 }])->get());
        }
        return response(null,ResponseCode::HTTP_NO_CONTENT);
    }
}
