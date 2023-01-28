<?php

namespace App\Services;

use Illuminate\Http\Request;

class PostQuery {
    protected $safeParms = [
        'userId' => ['eq'],
        'tagId' => ['eq']
    ];

    protected $columnMap = [
        'userId' => 'user_id',
        'tagId' => 'tag_id'
    ];

    protected $operatorMap = [
        'eq' => '='
    ];

    public function transform(Request $request){

        $eloQuery = [];
        $eloQueryIn = [];

        foreach ($this->safeParms as $parm => $operators){

            $query = $request->query($parm);

            if(!isset($query)){
                continue;
            }

            $column = $this->columnMap[$parm] ?? $parm;

            foreach ($operators as $operator){
                if(isset($query[$operator])){
                    $values = explode(',',$query[$operator]);
                    if(count($values) > 1){
                        $eloQueryIn[] = [$column, $values];
                    } else {
                        $eloQuery[] = [$column, $this->operatorMap[$operator], $query[$operator]];
                    }
                }
            }
        }

        return ['eq' => $eloQuery, 'in' => $eloQueryIn];
    }
}
