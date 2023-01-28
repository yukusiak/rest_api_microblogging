<?php

namespace App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UpdatePostRequest extends FormRequest
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
        $method = $this->method();

        if($method == 'PUT'){
            return [
                'title' => ['required'],
                'content' => ['required'],
                'image',
                'likes',
                'publishDate' => ['required'],
                'isPublished' => ['required', Rule::in([0,1])],
                'tags.*' => 'exists:tags,id',
            ];
        }
        else {
            return [
                'title' => ['sometime','required'],
                'content' => ['sometime','required'],
                'image' => ['sometime','string'],
                'likes' => ['sometime','numeric'],
                'publishDate' => ['sometime','required','date_format:Y-m-d H:i:s'],
                'isPublished' => ['sometime','required', Rule::in([0,1])],
                'tags.*' => 'exists:tags,id',
            ];
        }

    }

    protected function prepareForValidation()
    {
        if($this->publishDate){
            $this->merge([
                'publish_date' => $this->publishDate,
            ]);
        }

        if($this->publishDate){
            $this->merge([
                'is_published' => $this->isPublished,
            ]);
        }
        $this->merge([
            'user_id' => Auth::id(),
        ]);
    }
}
