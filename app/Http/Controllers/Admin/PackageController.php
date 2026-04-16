<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Package\PackageStoreRequest;
use App\Http\Requests\Package\PackageUpdateRequest;
use App\Models\BasicExtended;
use App\Models\Package;
use App\Traits\AdminLanguage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PackageController extends Controller
{
    use AdminLanguage;
    public function settings()
    {
        $data['abe'] = BasicExtended::select('id', 'expiration_reminder')->first();
        return view('admin.packages.settings', $data);
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'expiration_reminder' => 'required|integer',
        ]);
        $be = BasicExtended::first();
        $be->expiration_reminder = $request->expiration_reminder;
        $be->save();

        Session::flash('success', __('Updated successfully!'));
        return back();
    }
    public function features()
    {
        $be = BasicExtended::first();
        $features = json_decode($be->package_features, true);
        $data['features'] = $features;
        return view('admin.packages.features', $data);
    }

    public function updateFeatures(Request $request)
    {
        $features = $request->features ? json_encode($request->features) : NULL;
        $bes = BasicExtended::all();
        foreach ($bes as $key => $be) {
            $be->package_features = $features;
            $be->save();
        }

        Session::flash('success', __('Updated successfully!'));
        return back();
    }
    /**
     * Display a listing of the resource.
     *
     *
     */
    public function index(Request $request)
    {
        if ($request->has('language')) {
            $currentLang = $this->selectLang($request->language);
        } {
            $currentLang = $this->defaultLang();
        }

        $search = $request->search;
        $data['bex'] = $currentLang->basic_extended;
        $data['packages'] = Package::when($search, function ($query, $search) {
            return $query->where('title', 'like', '%' . $search . '%');
        })->orderBy('created_at', 'DESC')->get();

        $data['features'] = json_decode($data['bex']->package_features, true);

        return view('admin.packages.index', $data);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     */
    public function store(PackageStoreRequest $request)
    {
        try {

            if (!isset($request->featured)) $request["featured"] = "0";
            $features = json_encode($request->features);

            $package  = new Package();
            $package->storePackage($request, $features);
            Session::flash('success', __("Added successfully!"));
            return "success";
            // });
        } catch (\Throwable $e) {
            return $e;
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return
     */
    public function edit(Request $request, $id)
    {
        try {
            if ($request->has('language')) {
                $currentLang = $this->selectLang($request->language);
            } {
                $currentLang = $this->defaultLang();
            }
            $data['bex'] = $currentLang->basic_extended;
            $data['package'] = Package::findOrFail($id);
            $data['features'] = json_decode($data['bex']->package_features, true);
            return view("admin.packages.edit", $data);
        } catch (ModelNotFoundException $e) {
            return $e;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     *
     */
    public function update(PackageUpdateRequest $request)
    {
        try {
            if (!array_key_exists('is_trial', $request->all())) {
                $request['is_trial'] = "0";
                $request['trial_days'] = 0;
            }

            if (!isset($request->featured)) $request["featured"] = "0";
            $features = json_encode($request->features);
            $package = Package::findOrFail($request->package_id);
            $package->updatePackage($request, $features);
            Session::flash('success', __("Updated successfully!"));
            return "success";
            // });
        } catch (\Throwable $e) {
            return $e;
        }
    }

    public function delete(Request $request)
    {
        try {
            $package = Package::findOrFail($request->package_id);
            $package->deletePacakage();
            Session::flash('success', __('Deleted successfully!'));
            return back();
        } catch (\Throwable $e) {
            Session::flash('warning', __('Something went wrong!'));
            return $e;
        }
    }

    public function bulkDelete(Request $request)
    {
        try {

            $ids = $request->ids;
            foreach ($ids as $id) {
                $package = Package::findOrFail($id);
                $package->deletePacakage();
                Session::flash('success', __('Bulk deleted successfully!'));
            }
            return "success";
        } catch (\Throwable $e) {
            Session::flash('warning', __('Something went wrong!'));
            return 'success';
        }
    }
}
