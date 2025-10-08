<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImageUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'images' => 'required|array|max:10',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // حداکثر 5MB
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'images.required' => 'حداقل یک تصویر انتخاب کنید',
            'images.array' => 'تصاویر باید به صورت آرایه ارسال شوند',
            'images.max' => 'حداکثر 10 تصویر می‌توانید آپلود کنید',
            'images.*.required' => 'هر تصویر الزامی است',
            'images.*.image' => 'فایل باید تصویر باشد',
            'images.*.mimes' => 'فرمت تصویر باید jpeg, png, jpg, gif یا webp باشد',
            'images.*.max' => 'حجم هر تصویر نباید از 5 مگابایت بیشتر باشد',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'images' => 'تصاویر',
            'images.*' => 'تصویر',
        ];
    }
}
