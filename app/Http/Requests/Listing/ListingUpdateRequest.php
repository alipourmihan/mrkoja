<?php

namespace App\Http\Requests\Listing;

use Illuminate\Foundation\Http\FormRequest;

use App\Models\Language;
use App\Models\Listing\Listing;
use App\Models\Listing\ListingImage;
use App\Models\Location\Country;
use App\Models\Location\State;
use App\Rules\ImageMimeTypeRule;
use Illuminate\Http\Request;

class ListingUpdateRequest extends FormRequest
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
        // بررسی اینکه آیا فرم جدید (business_name) ارسال شده یا فرم قدیمی
        $isNewForm = $request->has('business_name');
        
        if ($isNewForm) {
            // Validation برای فرم جدید - در حالت edit برخی فیلدها اختیاری هستند
            $rules = [
                'business_name' => 'required|max:255',
                'short_description' => 'required|max:140',
                'full_description' => 'required|min:30|max:1000',
                'category_id' => 'required_without:category_ids',
                'category_ids' => 'sometimes|array',
                'state_id' => 'required',
                'city_id' => 'required',
                'address_details' => 'required',
                'latitude' => ['required', 'numeric', 'between:-90,90'],
                'longitude' => ['required', 'numeric', 'between:-180,180'],
                'mobile_phone' => 'required',
                'working_hours' => 'sometimes|string',
                'amenities' => 'sometimes|array',
            ];
            
            // Validation برای تصاویر (اختیاری در edit)
            if ($request->hasFile('feature_image')) {
                $rules['feature_image'] = [
                    'image',
                    'mimes:jpeg,jpg,png',
                    'max:2048',
                ];
            }
            
            if ($request->hasFile('video_background_image')) {
                $rules['video_background_image'] = [
                    'image',
                    'mimes:jpeg,jpg,png',
                    'max:2048',
                ];
            }
            
            return $rules;
        }
        
        if ($request->user_id == null || $request->user_id == 0) {
            if ($request->video_url) {
                $video = true;
            } else {
                $video = false;
            }
            $videoImage = Listing::findorfail($request->listing_id)->video_background_image;
            if ($videoImage) {
                $videohave = false;
            } else {
                $videohave = true;
            }
            $rules = [
                'video_background_image' => [
                    $video ? ($videohave ? 'required' : '') : '',
                    new ImageMimeTypeRule(),
                ],
                'thumbnail' => [
                    'dimensions:width=600,height=400',
                    new ImageMimeTypeRule(),
                ],
                'mail' => 'required',
                'phone' => 'required',
                'status' => 'required',
                'latitude' => ['required', 'numeric', 'between:-90,90'],
                'longitude' => ['required', 'numeric', 'between:-180,180'],
            ];

            $languages = Language::all();

            foreach ($languages as $language) {

                $States = applyLanguageFilter(State::query(), 'states', $language->id)->count();
                $State = $States != 0;

                $rules[$language->code . '_title'] = 'required|max:255';
                $rules[$language->code . '_address'] = 'required';
                $rules[$language->code . '_category_id'] = 'required';
                $rules[$language->code . '_city_id'] = 'required';
                $rules[$language->code . '_state_id'] = $State ? 'required' : '';
                $rules[$language->code . '_description'] = 'required|min:15';
                $rules[$language->code . '_aminities'] = 'required';
            }

            return $rules;
        } else {
            if ($request->video_url) {
                $video = true;
            } else {
                $video = false;
            }
            $videoImage = Listing::findorfail($request->listing_id)->video_background_image;
            if ($videoImage) {
                $videohave = false;
            } else {
                $videohave = true;
            }
            $vendorId = $request->user_id;
            $listingImageLimit = packageTotalListingImage($vendorId);
            $siderImageCount = ListingImage::where('listing_id', $request->listing_id)->count();
            $siders = $listingImageLimit - $siderImageCount;
            $additionalFeatureLimit = packageTotalAdditionalSpecification($vendorId);
            $aminitiesLimit = packageTotalAminities($vendorId);
            $SocialLinkLimit = packageTotalSocialLink($vendorId);


            $permissions = currentPackageFeatures($vendorId);

            if (!empty(currentPackageFeatures($vendorId))) {
                $permissions = json_decode($permissions, true);
            }

            if (is_array($permissions) && in_array('Amenities', $permissions)) {
                $Amenities = true;
            } else {
                $Amenities = false;
            }

            $rules = [
                'slider_images' => 'sometimes|array|max:' . $siders,
                'video_background_image' => [
                    $video ? ($videohave ? 'required' : '') : '',
                    new ImageMimeTypeRule(),
                ],
                'thumbnail' => [
                    'dimensions:width=600,height=400',
                    new ImageMimeTypeRule(),
                ],

                'mail' => 'required',
                'phone' => 'required',
                'status' => 'required',
                'latitude' => ['required', 'numeric', 'between:-90,90'],
                'longitude' => ['required', 'numeric', 'between:-180,180'],

            ];

            $languages = Language::all();

            foreach ($languages as $language) {
                $States = applyLanguageFilter(State::query(), 'states', $language->id)->count();
                $State = $States != 0;

                $rules[$language->code . '_title'] = 'required|max:255';
                $rules[$language->code . '_address'] = 'required';
                $rules[$language->code . '_category_id'] = 'required';
                $rules[$language->code . '_state_id'] = $State ? 'required' : '';
                $rules[$language->code . '_city_id'] = 'required';
                $rules[$language->code . '_description'] = 'required|min:15';
                $rules[$language->code . '_aminities'] = $Amenities ? 'required|array|max:' . $aminitiesLimit : '';
                $rules[$language->code . '_feature_heading'] = 'sometimes|array|max:' . $additionalFeatureLimit;
            }

            return $rules;
        }
    }
    public function messages()
    {
        $messageArray = [];

        $languages = Language::all();

        foreach ($languages as $language) {
            $messageArray[$language->code . '_title.required'] = 'The title field is required for ' . $language->name . ' language';
            $messageArray[$language->code . '_title.max'] = 'The title field cannot contain more than 255 characters for ' . $language->name . ' language';
            $messageArray[$language->code . '_address.required'] = 'The address field is required for ' . $language->name . ' language';
            $messageArray[$language->code . '_category_id.required'] = 'The category field is required for ' . $language->name . ' language';
            $messageArray[$language->code . '_city_id.required'] = 'The city field is required for ' . $language->name . ' language';
            $messageArray[$language->code . '_state_id.required'] = 'The state field is required for ' . $language->name . ' language';
            $messageArray[$language->code . '_description.required'] = 'The description field is required for ' . $language->name . ' language';
            $messageArray[$language->code . '_description.min'] = 'The description field at least have 15 characters for ' . $language->name . ' language';
            $messageArray[$language->code . '_aminities.required'] = 'The Amenities field is required for ' . $language->name . ' language';
            $messageArray[$language->code . '_aminities.max'] = 'Maximum ' . $this->aminitiesLimit() . ' aminities can be added per listing (for ' . $language->name . ' Language)';
        }

        return $messageArray;
    }
    private function aminitiesLimit()
    {
        $vendorId = $this->user_id;
        if ($vendorId == 0) {
            return PHP_INT_MAX;
        } else {
            return  packageTotalAminities($vendorId);
        }
    }
}
