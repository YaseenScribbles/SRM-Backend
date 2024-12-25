<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Http\Requests\StoreVisitRequest;
use App\Http\Requests\UpdateVisitRequest;
use App\Models\VisitImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VisitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sql = "select v.id, c.name [contact], p.name [purpose], v.description, v.response
        from visits v
        inner join contacts c on c.id = v.contact_id
        inner join purposes p on p.id = v.purpose_id";

        $visits = DB::select($sql);
        return response()->json(compact('visits'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVisitRequest $request)
    {
        $request->validated();
        try {
            DB::beginTransaction();

            $masterData = $request->except('visit_images');
            $visit = Visit::create($masterData);

            $images = $request->visit_images;

            foreach ($images as $image) {
                $filePath = $image->store('images', 'public');
                VisitImage::create([
                    'visit_id' => $visit->id,
                    'image_path' => $filePath,
                    'user_id' => $visit->user_id
                ]);
            }

            DB::commit();

            return response()->json(['message' => 'Visit added successfully']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Visit $visit)
    {
        return $visit->load('visit_images');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVisitRequest $request, Visit $visit)
    {
        $request->validated();
        try {
            DB::beginTransaction();

            $masterData = $request->except(['visit_images', 'existing_images']);
            $visit->update($masterData);

            // Get existing images from the request
            $existingImages = $request->input('existing_images', []);

            $imagesToDelete = $visit->visit_images()
                ->whereNotIn('image_path', $existingImages)
                ->get(); // Get the records that will be deleted

            // Loop through and delete the files from storage
            if ($imagesToDelete) {
                foreach ($imagesToDelete as $image) {
                    Storage::disk('public')->delete($image->image_path); // Delete file from 'public' storage
                }
            }

            // Delete images not present in the existingImages array
            $visit->visit_images()
                ->whereNotIn('image_path', $existingImages)
                ->delete();

            $images = $request->visit_images;

            if ($images) {
                foreach ($images as $image) {
                    $filePath = $image->store('images', 'public');
                    VisitImage::create([
                        'visit_id' => $visit->id,
                        'image_path' => $filePath,
                        'user_id' => $visit->user_id
                    ]);
                }
            }

            DB::commit();

            return response()->json(['message' => 'Visit updated successfully']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Visit $visit)
    {
        try {
            DB::beginTransaction();
            VisitImage::where('visit_id', $visit->id)->delete();
            $visit->delete();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()]);
        }
    }
}
