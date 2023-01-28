<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkStorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // $user = $this->user();
        // return $user != null;
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            '*.title' => ['required'],
            '*.content' => ['required'],
            '*.image'=>['required'],
            '*.likes' => ['numeric','nullable'],
            '*.publishDate' => ['required','date_format:Y-m-d H:i:s'],
            '*.isPublished' => ['required', Rule::in([0,1])],
            '*.tags.*' => 'exists:tags,id',
        ];
    }

    protected function prepareForValidation()
    {
        $data = [];
        foreach($this->toArray() as $obj){
            $obj['publish_date'] = $obj['publishDate'] ?? null;
            $obj['is_published'] = $obj['isPublished'] ?? null;
            $obj['user_id'] = Auth::id();
            $data[] = $obj;
        }
        $this->merge($data);
    }
}
