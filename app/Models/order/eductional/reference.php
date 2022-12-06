<?php

namespace App\Models\order\eductional;

use App\Models\users\customer;
use App\Models\users\serviceProvider;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class reference extends Model
{
    use HasFactory;
    public  $timestamps  = false;



    public static function boot()
    {
        parent::boot();
        static::retrieved(function ($item) {
            $item->cover = env('REFERENCE_COVER_URL') . $item->cover;
        });
    }


    public $fillable = [
        "id", "title", "description", "file", "cover", "university_id", "price", "college_id", "department_id", "semester_id", "year_id", "instructor_id", "created_at"
    ];

    public function store()
    {
        $validator = Validator::make(request()->all(), [
            "title" => "required",
            "file" => "required",
            "price" => "required",
            "college_id" => "required",
            "department_id" => "required",
            "semester_id" => "required",
            "year_id" => "required",
            "university_id" => "required",
            'instructor_id' => [Rule::requiredIf(Auth('sanctum')->user()->category_id == 1)],

        ]);


        if ($validator->fails())
            return apiresponse(false, 200, $validator->errors()->first());

        $file = handleFile(env('file_VALID_EXTENSIONS'), 'file');
        if (!$file['valid'])
            return apiresponse(false, 200, "select a valid file");

        $cover_path = env('REFERENCE_COVER_DEFAULT');
        if (isset(request()->cover)) {
            $cover = handleFile(env('IMAGE_VALID_EXTENSIONS'), 'cover');

            if (!$cover['valid'])
                return apiresponse(false, 200, "select a valid cover photo");
            $cover_path = $cover['fileName'];
            file_put_contents(public_path('../../assets/references/cover/') . $cover['fileName'], $cover['content']);
        }


        $instructor_id = Auth('sanctum')->user()->category_id == 1 ?  request()->instructor_id  : Auth('sanctum')->user()->id;

        $path =  Storage::disk('order')->put('/references/' . $file['fileName'], $file['content']);

        $details =   $this->create([
            "title" => request()->title,
            "description" => request()->description,
            "file" => $file['fileName'],
            "cover" => $cover_path,
            'instructor_id' => $instructor_id,
            "price" => request()->price,
            "college_id" => request()->college_id,
            "university_id" => request()->university_id,
            "department_id" => request()->department_id,
            "semester_id" => request()->semester_id,
            "year_id" => request()->year_id,
        ]);

        return apiresponse(true, 200, "reference uploaded");
    }


    public function instructor()
    {
        return $this->belongsTo(serviceProvider::class, 'instructor_id');
    }



    public function downloads()
    {
        return $this->belongsToMany(customer::class, "client_reference", "reference_id", "client_id")->withPivot('at');
    }
    public $hidden = [
        'file'
    ];
}
