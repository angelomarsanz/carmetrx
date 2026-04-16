<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use App\Models\Language;
use App\Models\BasicSetting as BS;
use App\Models\BasicExtended as BE;

use App\Traits\LanguageKeywords;
use Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;



class LanguageController extends Controller
{
    use LanguageKeywords;
    public function changeDashboardLanguage(Request $request)
    {
        Session::put('admin_lang',  $request->code);
        app()->setLocale('admin_' . $request->code);
        return $request->code;
    }

    public function index($lang = false)
    {
        $data['languages'] = Language::all();
        return view('admin.language.index', $data);
    }


    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|max:255',
            'code' => [
                'required',
                'max:255',
                'unique:languages'
            ],
            'direction' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $data = file_get_contents(resource_path('lang/') . 'default.json');
        $json_file = trim(strtolower($request->code)) . '.json';
        $path = resource_path('lang/') . $json_file;

        File::put($path, $data);

        // admin localization file
        $this->adminLanguageKeywords(trim($request->code));
        // user localization file
        $this->userLanguageKeywords(trim($request->code));

        $in['name'] = $request->name;
        $in['code'] = $request->code;
        $in['rtl'] = $request->direction;
        $userFrontkeywords =  file_get_contents(resource_path('lang/') . 'user_frontend_default.json');
        $in['user_front_keywords'] = $userFrontkeywords;
        if (Language::where('is_default', 1)->count() > 0) {
            $in['is_default'] = 0;
        } else {
            $in['is_default'] = 1;
        }
        $lang = Language::create($in);

        // duplicate First row of basic_settings for current language
        $dbs = Language::where('is_default', 1)->first()->basic_setting;
        $cols = json_decode($dbs, true);
        $bs = new BS;
        foreach ($cols as $key => $value) {
            // if the column is 'id' [primary key] then skip it
            if ($key == 'id') {
                continue;
            }


            // create favicon image using default language image & save unique name in database
            if ($key == 'favicon') {
                // take default lang image
                $dimg = public_path('assets/front/img/' . $dbs->favicon);

                // copy paste the default language image with different unique name
                $filename = uniqid();
                if (($pos = strpos($dbs->favicon, ".")) !== FALSE) {
                    $ext = substr($dbs->favicon, $pos + 1);
                }
                $newImgName = $filename . '.' . $ext;

                @copy($dimg, public_path('assets/front/img/' . $newImgName));

                // save the unique name in database
                $bs[$key] = $newImgName;

                // continue the loop
                continue;
            }

            // create logo image using default language image & save unique name in database
            if ($key == 'logo') {
                // take default lang image
                $dimg = public_path('assets/front/img/' . $dbs->logo);

                // copy paste the default language image with different unique name
                $filename = uniqid();
                if (($pos = strpos($dbs->logo, ".")) !== FALSE) {
                    $ext = substr($dbs->logo, $pos + 1);
                }
                $newImgName = $filename . '.' . $ext;

                @copy($dimg, public_path('assets/front/img/' . $newImgName));

                // save the unique name in database
                $bs[$key] = $newImgName;

                // continue the loop
                continue;
            }

            // create logo image using default language image & save unique name in database
            if ($key == 'preloader') {
                // take default lang image
                $dimg = public_path('assets/front/img/' . $dbs->preloader);

                // copy paste the default language image with different unique name
                $filename = uniqid();
                if (($pos = strpos($dbs->preloader, ".")) !== FALSE) {
                    $ext = substr($dbs->preloader, $pos + 1);
                }
                $newImgName = $filename . '.' . $ext;

                @copy($dimg, public_path('assets/front/img/' . $newImgName));

                // save the unique name in database
                $bs[$key] = $newImgName;

                // continue the loop
                continue;
            }

            // create logo image using default language image & save unique name in database
            if ($key == 'maintenance_img') {
                // take default lang image
                $dimg = public_path('assets/front/img/' . $dbs->maintenance_img);

                // copy paste the default language image with different unique name
                $filename = uniqid();
                if (($pos = strpos($dbs->maintenance_img, ".")) !== FALSE) {
                    $ext = substr($dbs->maintenance_img, $pos + 1);
                }
                $newImgName = $filename . '.' . $ext;

                @copy($dimg, public_path('assets/front/img/' . $newImgName));

                // save the unique name in database
                $bs[$key] = $newImgName;

                // continue the loop
                continue;
            }

            // create breadcrumb image using default language image & save unique name in database
            if ($key == 'breadcrumb') {
                // take default lang image
                $dimg = public_path('assets/front/img/' . $dbs->breadcrumb);

                // copy paste the default language image with different unique name
                $filename = uniqid();
                if (($pos = strpos($dbs->breadcrumb, ".")) !== FALSE) {
                    $ext = substr($dbs->breadcrumb, $pos + 1);
                }
                $newImgName = $filename . '.' . $ext;

                @copy($dimg, public_path('assets/front/img/' . $newImgName));

                // save the unique name in database
                $bs[$key] = $newImgName;

                // continue the loop
                continue;
            }

            // create footer_logo image using default language image & save unique name in database
            if ($key == 'footer_logo') {
                // take default lang image
                $dimg = public_path('assets/front/img/' . $dbs->footer_logo);

                // copy paste the default language image with different unique name
                $filename = uniqid();
                if (($pos = strpos($dbs->footer_logo, ".")) !== FALSE) {
                    $ext = substr($dbs->footer_logo, $pos + 1);
                }
                $newImgName = $filename . '.' . $ext;

                @copy($dimg, public_path('assets/front/img/' . $newImgName));

                // save the unique name in database
                $bs[$key] = $newImgName;

                // continue the loop
                continue;
            }

            // create intro_main_image image using default language image & save unique name in database
            if ($key == 'intro_main_image') {
                // take default lang image
                $dimg = public_path('assets/front/img/' . $dbs->intro_main_image);

                // copy paste the default language image with different unique name
                $filename = uniqid();
                if (($pos = strpos($dbs->intro_main_image, ".")) !== FALSE) {
                    $ext = substr($dbs->intro_main_image, $pos + 1);
                }
                $newImgName = $filename . '.' . $ext;

                @copy($dimg, public_path('assets/front/img/' . $newImgName));

                // save the unique name in database
                $bs[$key] = $newImgName;

                // continue the loop
                continue;
            }

            $bs[$key] = $value;
        }
        $bs['language_id'] = $lang->id;
        $bs->save();

        // duplicate First row of basic_extendeds for current language
        $dbe = Language::where('is_default', 1)->first()->basic_extended;
        $be = BE::firstOrFail();
        $cols = json_decode($be, true);
        $be = new BE;
        foreach ($cols as $key => $value) {
            // if the column is 'id' [primary key] then skip it
            if ($key == 'id') {
                continue;
            }

            // create hero image using default language image & save unique name in database
            if ($key == 'hero_img') {
                // take default lang image
                $dimg = public_path('assets/front/img/' . $dbe->hero_img);

                // copy paste the default language image with different unique name
                $filename = uniqid();
                if (($pos = strpos($dbe->hero_img, ".")) !== FALSE) {
                    $ext = substr($dbe->hero_img, $pos + 1);
                }
                $newImgName = $filename . '.' . $ext;

                @copy($dimg, public_path('assets/front/img/' . $newImgName));

                // save the unique name in database
                $be[$key] = $newImgName;

                // continue the loop
                continue;
            }

            $be[$key] = $value;
        }
        $be['language_id'] = $lang->id;
        $be->save();

        Session::flash('success', __('Added successfully!'));
        return "success";
    }

    public function edit($id)
    {
        if ($id > 0) {
            $data['language'] = Language::findOrFail($id);
        }
        $data['id'] = $id;

        return view('admin.language.edit', $data);
    }


    public function update(Request $request)
    {
        $language = Language::findOrFail($request->language_id);

        $rules = [
            'name' => 'required|max:255',
            'code' => [
                'required',
                'max:255',
                Rule::unique('languages')->ignore($language->id),
            ],
            'direction' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }


        $language->name = $request->name;
        $language->code = $request->code;
        $language->rtl = $request->direction;
        $language->save();

        Session::flash('success', __('Updated successfully!'));
        return "success";
    }

    public function editAdminFrontKeyword($id)
    {
        if ($id > 0) {
            $la = Language::findOrFail($id);
            $json = file_get_contents(resource_path('lang/') . $la->code . '.json');
            $json = json_decode($json, true);
            $list_lang = Language::all();

            if (empty($json)) {
                return back()->with('alert', 'File Not Found.');
            }
            // dd($json);

            return view('admin.language.edit-keyword', compact('json', 'la'));
        } elseif ($id == 0) {
            $json = file_get_contents(resource_path('lang/') . 'default.json');
            $json = json_decode($json, true);
            if (empty($json)) {
                return back()->with('alert', 'File Not Found.');
            }

            return view('admin.language.edit-keyword', compact('json'));
        }
    }

    public function updateAdminFrontKeyword(Request $request, $id)
    {
        $lang = Language::findOrFail($id);
        $content = json_encode($request->keys);
        if ($content === 'null') {
            return back()->with('alert', 'At Least One Field Should Be Fill-up');
        }
        file_put_contents(resource_path('lang/') . $lang->code . '.json', $content);

        $validationFilePath = resource_path('lang/' . $lang->code . '/validation.php');

        $this->updateNameAttributeforAdminFrontend($validationFilePath, $content);
        file_put_contents(resource_path('lang/') . $lang->code . '.json', $content);

        return back()->with('success', __('Updated Successfully'));
    }

    // public function addKeyword(Request $request, $id)
    // {
    //     $request->validate([
    //         'keyword' => 'required'
    //     ]);

    //     $language = Language::find($id);
    //     $json = file_get_contents(resource_path('lang/') . $language->code . '.json');
    //     $json = json_decode($json, true);

    //     $json[$request->keyword] = $request->keyword;
    //     $jsonData = json_encode($json);

    //     file_put_contents(resource_path('lang/') . $language->code . '.json', $jsonData);


    //     Session::flash('success', 'A new keyword add successfully for ' . $language->name . ' language!');
    //     // return response()->json(['status' => 'success'], 200);
    //     return 'success';
    // }


    // public function updateKeyword(Request $request, $id)
    // {
    //     $lang = Language::findOrFail($id);
    //     $content = json_encode($request->keys);
    //     if ($content === 'null') {
    //         return back()->with('alert', 'At Least One Field Should Be Fill-up');
    //     }
    //     file_put_contents(resource_path('lang/') . $lang->code . '.json', $content);
    //     return back()->with('success', 'Updated Successfully');
    // }



    public function editAdminDashboardKeyword($id)
    {

        if ($id > 0) {
            $la = Language::findOrFail($id);
            $json = file_get_contents(resource_path('lang/admin_') . $la->code . '.json');
            $json = json_decode($json, true);
            $list_lang = Language::all();

            if (empty($json)) {
                return back()->with('alert', 'File Not Found.');
            }

            return view('admin.language.edit-admin-keyword', compact('json', 'la'));
        } elseif ($id == 0) {
            $json = file_get_contents(resource_path('lang/admin_') . 'default.json');
            $json = json_decode($json, true);
            if (empty($json)) {
                return back()->with('alert', 'File Not Found.');
            }

            return view('admin.language.edit-admin-keyword', compact('json'));
        }
    }

    public function updateAdminDashboardKeyword(Request $request, $id)
    {
        $language = Language::find($id);
        $newkeywordsArr = $request['keys'];
        if (count($newkeywordsArr) === 0) {
            return back()->with('alert', __('At Least One Field Should Be Fill-up'));
        }

        //=== language messages
        $existingkeywordsArr = [];
        $fileLocated = resource_path('lang/') . 'admin_' . $language->code . '.json';
        if (file_exists($fileLocated)) {
            $existingkeywordsArr = json_decode(file_get_contents($fileLocated), true) ?? [];
        }

        //json file override
        $requestKeywordsArr = array_merge($existingkeywordsArr, $newkeywordsArr);
        file_put_contents(resource_path('lang/') . 'admin_' . $language->code . '.json', json_encode($requestKeywordsArr));

        //=====validation attribute override
        $adminValidationFilePath = resource_path('lang/admin_' . $language->code . '/validation.php');
        $this->updateNameAttributeforAdmin($adminValidationFilePath, $requestKeywordsArr);

        return back()->with('success', __('Updated Successfully'));
    }



    public function editUserDashboardKeyword($id)
    {
        // $la = Language::findOrFail($id);
        // $json = json_decode($la->user_keywords, true);

        // if (empty($json)) {
        //     return back()->with('warning', 'File Not Found.');
        // }

        // return view('admin.language.edit-user-dashboard-keyword', compact('json', 'la'));

        if ($id > 0) {
            $la = Language::findOrFail($id);
            $filePath = resource_path('lang/tenant_' . $la->code . '.json');
            if (!file_exists($filePath)) {
                $this->userLanguageKeywords(trim($la->code));
            }
            $json = file_get_contents($filePath);

            $json = json_decode($json, true);
            if (empty($json)) {
                return back()->with('warning', __('File Not Found.'));
            }
            return view('admin.language.edit-user-dashboard-keyword', compact('json', 'la'));
        } elseif ($id == 0) {
            $json = file_get_contents(resource_path('lang/') . 'tenant_default.json');
            $json = json_decode($json, true);
            if (empty($json)) {
                return back()->with('warning', __('File Not Found.'));
            }
            return view('admin.language.edit-user-dashboard-keyword', compact('json'));
        }
    }
    public function updateUserDashboardKeyword(Request $request, $id)
    {
        $language = Language::query()->find($id);
        $newkeywordsArr = $request['keys'];
        if (count($newkeywordsArr) === 0) {
            return back()->with('alert', __('At Least One Field Should Be Fill-up'));
        }

        //=== language messages
        $existingkeywordsArr = [];
        $fileLocated = resource_path('lang/') . 'tenant_' . $language->code . '.json';
        if (file_exists($fileLocated)) {
            $existingkeywordsArr = json_decode(file_get_contents($fileLocated), true) ?? [];
        }

        //override json file
        $requestKeywordsArr = array_merge($existingkeywordsArr, $newkeywordsArr);
        file_put_contents(resource_path('lang/') . 'tenant_' . $language->code . '.json', json_encode($requestKeywordsArr));

        //override validation attribute file

        $useruValidationFilePath = resource_path('lang/tenant_' . $language->code . '/validation.php');
        $this->updateNameAttributeForUser($useruValidationFilePath, $requestKeywordsArr);
        return back()->with('success', __('Updated Successfully'));
    }

    public function editUserFrontendKeyword($id)
    {
        $la = Language::findOrFail($id);
        $json = json_decode($la->user_front_keywords, true);

        if (empty($json)) {
            return back()->with('warning', 'File Not Found.');
        }

        return view('admin.language.edit-user-front-keyword', compact('json', 'la'));
    }
    public function updateUserFrontendKeyword(Request $request, $id)
    {
        $lang = Language::findOrFail($id);
        $content = json_encode($request->keys);
        if ($content === 'null') {
            return back()->with('warning', 'At Least One Field Should Be Fill-up');
        }

        $lang->user_front_keywords = $content;
        $lang->save();
        return back()->with('success', __('Updated successfully!'));
    }
    public function delete($id)
    {
        $la = Language::findOrFail($id);
        if ($la->is_default == 1) {
            return back()->with('warning', __('Default language cannot be deleted!'));
        }

        // frontend langauge 
        @unlink(resource_path('lang/') . $la->code . '.json');
        if (session()->get('frontend_lang') == $la->code) {
            session()->forget('frontend_lang');
        }

        //admin language
        if (session()->get('admin_lang') == $la->code) {
            session()->forget('admin_lang');
        }
        @unlink(resource_path('lang/admin_') . $la->code . '.json');
        $adminPath = resource_path('lang/admin_' . $la->code);
        $this->deleteFolder($adminPath);


        //tenant language
        if (session()->get('tenant_dashboard_lang') == $la->code) {
            session()->forget('tenant_dashboard_lang');
        }
        @unlink(resource_path('lang/tenant_') . $la->code . '.json');
        $userPath = resource_path('lang/tenant_' . $la->code);
        $this->deleteFolder($userPath);

        // deleting basic_settings and basic_extended for corresponding language & unlink images
        $bs = $la->basic_setting;
        if (!empty($bs)) {

            @unlink(public_path('assets/front/img/' . $bs->favicon));

            @unlink(public_path('assets/front/img/' . $bs->logo));

            @unlink(public_path('assets/front/img/' . $bs->preloader));

            @unlink(public_path('assets/front/img/' . $bs->breadcrumb));

            @unlink(public_path('assets/front/img/' . $bs->intro_main_image));

            @unlink(public_path('assets/front/img/' . $bs->footer_logo));

            @unlink(public_path('assets/front/img/' . $bs->maintenance_img));

            $bs->delete();
        }
        $be = $la->basic_extended;
        if (!empty($be)) {
            $be->delete();
        }


        // deleting services for corresponding language
        if (!empty($la->blogs)) {
            $blogs = $la->blogs;
            foreach ($blogs as $blog) {
                @unlink(public_path('assets/front/img/blogs/' . $blog->main_image));
                $blog->delete();
            }
        }

        // deleting blog categories for corresponding language
        if (!empty($la->bcategories)) {
            $bcategories = $la->bcategories;
            foreach ($bcategories as $bcat) {
                $bcat->delete();
            }
        }

        // deleting faqs for corresponding language
        if (!empty($la->faqs)) {
            $la->faqs()->delete();
        }


        // deleting feature for corresponding language
        if (!empty($la->features)) {
            $features = $la->features;
            foreach ($features as $feature) {
                $feature->delete();
            }
        }

        // deleting menus for corresponding language
        if (!empty($la->menus)) {
            $la->menus()->delete();
        }

        // deleting pages for corresponding language
        if (!empty($la->pages)) {
            $la->pages()->delete();
        }

        // deleting partners for corresponding language
        if (!empty($la->partners)) {
            $partners = $la->partners;
            foreach ($partners as $partner) {
                @unlink(public_path('assets/front/img/partners/' . $partner->image));
                $partner->delete();
            }
        }

        // deleting partners for corresponding language
        if (!empty($la->popups)) {
            $popups = $la->popups;
            foreach ($popups as $popup) {
                @unlink(public_path('assets/front/img/popups/' . $popup->background_image));
                @unlink(public_path('assets/front/img/popups/' . $popup->image));
                $popup->delete();
            }
        }

        // deleting processes for corresponding language
        if (!empty($la->processes)) {
            $processes = $la->processes;
            foreach ($processes as $process) {
                @unlink(public_path('assets/front/img/process/' . $process->image));
                $process->delete();
            }
        }

        // deleting seo for corresponding language
        if (!empty($la->seo)) {
            $la->seo->delete();
        }

        // deleting testimonials for corresponding language
        if (!empty($la->testimonials)) {
            $testimonials = $la->testimonials;
            foreach ($testimonials as $testimonial) {
                @unlink(public_path('assets/front/img/testimonials/' . $testimonial->image));
                $testimonial->delete();
            }
        }

        // deleting useful links for corresponding language
        if (!empty($la->ulinks)) {
            $la->ulinks()->delete();
        }

        // if the the deletable language is the currently selected language in frontend then forget the selected language from session
        session()->forget('lang');

        $la->delete();
        return back()->with('success', __('Delete successfully!'));
    }


    public function default(Request $request, $id)
    {
        Language::where('is_default', 1)->update(['is_default' => 0]);
        $lang = Language::find($id);
        $lang->is_default = 1;
        $lang->save();
        return back()->with('success', $lang->name . ' laguage is set as defualt.');
    }

    public function rtlcheck($langid)
    {
        if ($langid > 0) {
            $lang = Language::find($langid);
        } else {
            return 0;
        }

        return $lang->rtl;
    }

    protected function adminLanguageKeywords($code)
    {
        $admin_data = file_get_contents(resource_path('lang/') . 'admin_default.json');
        $admin_json_file = 'admin_' . $code . '.json';
        $admin_path = resource_path('lang/') . $admin_json_file;
        File::put($admin_path, $admin_data);

        //copy folder
        $adminSourceFolder = resource_path('lang/' . $code);
        $adminNewFolder = resource_path('lang/' . 'admin_' . $code);
        $this->duplicateFolderAndRename($adminSourceFolder, $adminNewFolder);
        $adminValidationSrc = resource_path('lang/admin_' . $code . '/validation.php');
        $this->addNameAttributeForAdmin($adminValidationSrc);

        // admin frontend validation file
        $admin_frontend_json_data = file_get_contents(resource_path('lang/') . 'default.json');
        $validationFilePath = resource_path('lang/' . $code . '/validation.php');
        $this->updateNameAttributeforAdminFrontend($validationFilePath, $admin_frontend_json_data);
    }

    protected function userLanguageKeywords($code)
    {
        $tenant_data = file_get_contents(resource_path('lang/') . 'tenant_default.json');
        $tenant_json_file = 'tenant_' . $code . '.json';
        $tenant_path = resource_path('lang/') . $tenant_json_file;
        File::put($tenant_path, $tenant_data);
        //copy folder
        $userSourceFolder = resource_path('lang/' . $code);
        $userNewFolder = resource_path('lang/' . 'tenant_' . $code);
        $this->duplicateFolderAndRename($userSourceFolder, $userNewFolder);
        $userValidationSrc = resource_path('lang/tenant_' . $code . '/validation.php');
        $this->addNameAttributeForUser($userValidationSrc);
    }


    protected function duplicateAndRenameFolder($source, $destination)
    {

        // Create the destination folder if it doesn't exist
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        // Open the source folder
        $directory = opendir($source);

        // Copy each file and subfolder
        while (($file = readdir($directory)) !== false) {
            if ($file !== '.' && $file !== '..') {
                $sourcePath = $source . DIRECTORY_SEPARATOR . $file;
                $destinationPath = $destination . DIRECTORY_SEPARATOR . $file;

                if (is_dir($sourcePath)) {
                    // Recursively copy subfolders
                    $this->duplicateAndRenameFolder($sourcePath, $destinationPath);
                } else {
                    // Copy files
                    copy($sourcePath, $destinationPath);
                }
            }
        }

        closedir($directory);
    }

    protected function duplicateFolderAndRename($sourceFolder, $newFolder)
    {
        if (is_dir($sourceFolder)) {

            $this->duplicateAndRenameFolder($sourceFolder, $newFolder);
        }

        return true;
    }

    protected function addNameAttributeForUser($validationFilePath)
    {
        if (file_exists($validationFilePath)) {
            $validationData = include $validationFilePath;
            $validationAttributes = [];

            if (is_array($validationAttributes)) {
                foreach (LanguageKeywords::userNameAttribute() as $key => $value) {
                    if (!array_key_exists($key, $validationAttributes)) {
                        $validationAttributes[$key] = $value;
                    }
                }
            }

            $validationData['attributes'] = $validationAttributes;
            $validationContent = "<?php\n\nreturn " . var_export($validationData, true) . ";\n";
            file_put_contents($validationFilePath, $validationContent);
        }
    }

    protected function updateNameAttributeForUser($validationFilePath, $requestKeywordsArr)
    {
        if (file_exists($validationFilePath)) {
            $validationData = include $validationFilePath;
            $validationAttributes = $validationData['attributes'];

            if (is_array($validationAttributes)) {
                foreach (LanguageKeywords::userNameAttribute() as $key => $value) {
                    if (!array_key_exists($key, $validationAttributes)) {
                        $validationAttributes[$key] = $value;
                    }
                }
            }

            foreach ($requestKeywordsArr as $key => $value) {
                if (array_key_exists($key, $validationAttributes)) {
                    $validationAttributes[$key] = $value;
                }
            }

            $validationData['attributes'] = $validationAttributes;
            $validationContent = "<?php\n\nreturn " . var_export($validationData, true) . ";\n";
            file_put_contents($validationFilePath, $validationContent);
        }
    }

    protected function addNameAttributeForAdmin($validationFilePath)
    {
        if (file_exists($validationFilePath)) {
            $validationData = include $validationFilePath;
            $validationAttributes = [];

            if (is_array($validationAttributes)) {
                foreach (LanguageKeywords::adminNameAttribute() as $key => $value) {
                    if (!array_key_exists($key, $validationAttributes)) {
                        $validationAttributes[$key] = $value;
                    }
                }
            }

            $validationData['attributes'] = $validationAttributes;
            $validationContent = "<?php\n\nreturn " . var_export($validationData, true) . ";\n";
            file_put_contents($validationFilePath, $validationContent);
        }
    }

    protected function updateNameAttributeforAdmin($validationFilePath, $requestKeywordsArr)
    {
        if (file_exists($validationFilePath)) {
            $validationData = include $validationFilePath;
            $validationAttributes = $validationData['attributes'];
            if (is_array($validationAttributes)) {
                foreach (LanguageKeywords::adminNameAttribute() as $key => $value) {
                    if (!array_key_exists($key, $validationAttributes)) {
                        $validationAttributes[$key] = $value;
                    }
                }
            }

            foreach ($requestKeywordsArr as $key => $value) {
                if (array_key_exists($key, $validationAttributes)) {
                    $validationAttributes[$key] = $value;
                }
            }

            $validationData['attributes'] = $validationAttributes;
            $validationContent = "<?php\n\nreturn " . var_export($validationData, true) . ";\n";
            file_put_contents($validationFilePath, $validationContent);
        }
    }

    protected function deleteFolder($dirname)
    {
        $dir_handle = null;
        if (is_dir($dirname)) {
            $dir_handle = opendir($dirname);
        }
        if (!$dir_handle) {
            return false;
        }
        while ($file = readdir($dir_handle)) {
            if ($file != '.' && $file != '..') {
                if (!is_dir($dirname . '/' . $file)) {
                    unlink($dirname . '/' . $file);
                } else {
                    $this->deleteFolder($dirname . '/' . $file);
                }
            }
        }
        closedir($dir_handle);
        rmdir($dirname);
        return true;
    }

    protected function updateNameAttributeforAdminFrontend($validationFilePath, $requestKeywordsArr)
    {
        if (file_exists($validationFilePath)) {
            $validationData = include $validationFilePath;

            // Initialize 'attributes' if it doesn't exist
            if (!isset($validationData['attributes'])) {
                $validationData['attributes'] = [];
            }

            $validationAttributes = $validationData['attributes'];

            if (is_array($validationAttributes)) {
                foreach (LanguageKeywords::adminFrontendValidationAttribute() as $key => $value) {
                    if (!array_key_exists($key, $validationAttributes)) {
                        $validationAttributes[$key] = $value;
                    }
                }
            }

            foreach (json_decode($requestKeywordsArr, true) as $key => $value) {
                if (array_key_exists($key, $validationAttributes)) {
                    $validationAttributes[$key] = $value;
                }
            }

            $validationData['attributes'] = $validationAttributes;
            $validationContent = "<?php\n\nreturn " . var_export($validationData, true) . ";\n";
            file_put_contents($validationFilePath, $validationContent);
        }
    }
}
