<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
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
            'title' => ['required'],
            'content' => ['required'],
            'image'=> ['string','nullable'],
            'likes' => ['numeric','nullable'],
            'publishDate' => ['required','date_format:Y-m-d H:i:s'],
            'isPublished' => ['required', Rule::in([0,1])],
            'tags.*' => 'exists:tags,id',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'publish_date' => $this->publishDate,
            'is_published' => $this->isPublished,
            'user_id' => Auth::id(),
        ]);
    }
}
