<?php

namespace App\Http\Requests\Listing;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;

class ListingStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(Request $request)
    {
        $listingImageLimit = packageTotalListingImage($request->user_id ?? Auth::user()->id);

        $rules = [
            'can_listing_add' => ['required'],
            'feature_image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
            'gallery' => array_filter([
                'required',
                'array',
                'min:1',
                $listingImageLimit ? 'max:' . $listingImageLimit : null,
            ]),
            'gallery.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
            'video_background_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
            'video_url' => ['nullable', 'url'],
            'business_name' => ['required', 'string', 'max:30'],
            'postal_address' => ['nullable', 'string', 'max:255'],
            'address_details' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'category_parent_id' => ['required', 'exists:listing_categories,id'],
            'category_id' => ['required', 'exists:listing_categories,id'],
            'short_description' => ['nullable', 'string', 'max:140'],
            'full_description' => ['required', 'string', 'min:30', 'max:1000'],
            'mobile_phone' => ['required', 'regex:/^09\d{9}$/'],
            'landline_phone' => ['nullable', 'regex:/^0\d{10}$/'],
            'website' => ['nullable', 'string', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'telegram' => ['nullable', 'string', 'max:255'],
            'whatsapp' => ['nullable', 'regex:/^\d{10}$/'],
            'working_hours' => ['nullable', 'string'],
        ];

        return $rules;
    }

    protected function failedValidation(Validator $validator)
    {
        Log::warning('ListingStoreRequest validation failed', [
            'user_id' => $this->user_id ?? Auth::user()->id,
            'errors' => $validator->errors()->all(),
            'input' => $this->except(['feature_image', 'gallery', 'video_background_image']),
        ]);

        parent::failedValidation($validator);
    }

    public function messages()
    {
        $messageArray = [];

        $imageLimit = packageTotalListingImage($this->user_id ?? Auth::user()->id);

        $messageArray['feature_image.required'] = 'بارگذاری تصویر اصلی کسب‌وکار الزامی است.';
        $messageArray['gallery.required'] = 'حداقل یک تصویر برای گالری کسب‌وکار انتخاب کنید.';
        $messageArray['gallery.min'] = 'حداقل یک تصویر برای گالری انتخاب کنید.';
        if ($imageLimit) {
            $messageArray['gallery.max'] = 'حداکثر ' . $imageLimit . ' تصویر می‌توانید آپلود کنید.';
        }
        $messageArray['business_name.required'] = 'نام کسب‌وکار الزامی است.';
        $messageArray['business_name.max'] = 'نام کسب‌وکار نباید بیشتر از ۳۰ کاراکتر باشد.';
        $messageArray['address_details.required'] = 'جزئیات آدرس الزامی است.';
        $messageArray['category_parent_id.required'] = 'انتخاب دسته‌بندی اصلی الزامی است.';
        $messageArray['category_id.required'] = 'انتخاب دسته‌بندی فرعی الزامی است.';
        $messageArray['full_description.required'] = 'توضیحات کامل الزامی است.';
        $messageArray['full_description.min'] = 'توضیحات کامل باید حداقل ۳۰ کاراکتر باشد.';
        $messageArray['mobile_phone.required'] = 'شماره موبایل الزامی است.';
        $messageArray['mobile_phone.regex'] = 'شماره موبایل باید با 09 شروع شود و 11 رقم باشد.';
        $messageArray['landline_phone.regex'] = 'شماره تلفن ثابت باید با کد شهر و 11 رقم وارد شود.';
        $messageArray['whatsapp.regex'] = 'شماره واتس‌اپ باید ۱۰ رقم و بدون صفر اول باشد.';

        return $messageArray;
    }
}
