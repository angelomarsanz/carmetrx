<?php

use App\Http\Controllers\Agent\AgentController;
use App\Http\Controllers\Agent\AuthController;
use App\Http\Controllers\Agent\ProjectController;
use App\Http\Controllers\Agent\PropertyController;
use App\Http\Controllers\Agent\TypeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Agent Routes
|--------------------------------------------------------------------------
*/



Route::prefix('agent')->middleware(['TFRAcessPermission:Agent'])->group(function () {
  Route::controller(AgentController::class)->group(function () {

    Route::post('/login/submit', 'authentication')->name('agent.login_submit');

    Route::middleware(['auth:agent', 'agentDashboardLang'])->group(function () {
      Route::get('/dashboard', 'dashboard')->name('agent.dashboard');
      Route::get('/change-language', 'change_language')->name('agent.change_language');
      Route::get('/logout', 'logout')->name('agent.logout');
      // change agent-panel theme (dark/light) route
      Route::post('/change-theme', 'changeTheme')->name('agent.change_theme');
    });
  });

  Route::middleware(['auth:agent', 'agentDashboardLang'])->controller(AuthController::class)->group(function () {
    Route::get('/change-password', 'change_password')->name('agent.change_password');
    Route::post('/update-password', 'updated_password')->name('agent.update_password');
    Route::get('/edit-profile', 'edit_profile')->name('agent.edit.profile');
    Route::post('/profile/update', 'update_profile')->name('agent.update_profile');
  });

 Route::middleware(['userHasPackage', 'auth:agent', 'agentDashboardLang'])->group(function () {
    // Property Management 
    Route::prefix('property-management')->controller(PropertyController::class)->group(function () {
      Route::get('/properties', 'index')->name('agent.property_management.properties');
      Route::get('/type', 'type')->name('agent.property_management.type');
      Route::get('/create', 'create')->name('agent.property_management.create_property');
      Route::post('/store', 'store')->name('agent.property_management.store_property')->middleware('packageLimitsCheck:property,store');

      Route::post('/update_featured', 'updateFeatured')->name('agent.property_management.update_featured');
      Route::post('update_status', 'updateStatus')->name('agent.property_management.update_status');
      Route::get('edit-property/{id}', 'edit')->name('agent.property_management.edit');
      Route::post('update/{id}', 'update')->name('agent.property_management.update_property')->middleware('packageLimitsCheck:property,update');
      Route::post('delete', 'delete')->name('agent.property_management.delete_property');
      Route::post('bulk-delete', 'bulkDelete')->name('agent.property_management.bulk_delete_property');
      Route::post('specification/delete', 'specificationDelete')->name('agent.property_management.specification_delete');
      //#========== Property slider image
      Route::post('/img-store', 'imagesstore')->name('agent.property.imagesstore')->middleware('packageLimitsCheck:property,store');
      Route::post('/img-update', 'imagesstore')->name('agent.property.imagesupdate')->middleware('packageLimitsCheck:property,update');
      Route::post('/img-remove', 'imagermv')->name('agent.property.imagermv');
      Route::post('/img-db-remove', 'imagedbrmv')->name('agent.property.imgdbrmv');
      //#==========property slider image end

      Route::get('/get-states-cities', 'getStateCities')->name('agent.property_specification.get_state_cities');
      Route::get('/get-cities', 'getCities')->name('agent.property_specification.get_cities');
    
      Route::post('/property/video-img-rmv', 'videoImgrmv')->name('agent.property_management.videoImgrmv');
      Route::post('/property/floor-img-rmv', 'floorImgrmv')->name('agent.property_management.floorImgrmv');
    });

    // Project Management route start
    Route::prefix('project-management')->group(function () {
      Route::controller(ProjectController::class)->group(function () {

        Route::get('/projects', 'index')->name('agent.project_management.projects');
        Route::get('/create', 'create')->name('agent.project_management.create_project');

        Route::post('/store', 'store')->name('agent.project_management.store_project')->middleware('packageLimitsCheck:project,store');

        Route::post('/update_status', 'updateStatus')->name('agent.project_management.update_status');
        Route::get('/edit-project/{id}', 'edit')->name('agent.project_management.edit');
        Route::post('/update/{id}', 'update')->name('agent.project_management.update_project')->middleware('packageLimitsCheck:project,update');
        Route::post('/delete', 'destroy')->name('agent.project_management.delete_project');
        Route::post('/bulk-delete', 'bulkDestroy')->name('agent.project_management.bulk_delete_project');

        Route::post('specification/delete', 'specificationDelete')->name('agent.project_management.specification_delete');
        //#========== project gallery image
        Route::post('/gallery-img-store', 'galleryImagesStore')->name('agent.project.gallery_image_store')->middleware('packageLimitsCheck:project,update');
        Route::post('/img-remove', 'galleryImageRmv')->name('agent.project.gallery_imagermv');
        Route::post('/img-db-remove', 'galleryImageDbrmv')->name('agent.project.gallery_imgdbrmv');
        //#========== project slider image end
        Route::get('/get-states-cities', 'getStateCities')->name('agent.project_specification.get_state_cities');
        Route::get('/get-cities', 'getCities')->name('agent.project_specification.get_cities');
        //#========== project gallery image ========
        Route::post('/floor-plan-img-store', 'floorPlanImagesStore')->name('agent.project.floor_plan_image_store');
        Route::post('/floor-plan-img-remove', 'floorPlanImageRmv')->name('agent.project.floor_plan_imagermv');
        Route::post('/floor-plan-img-db-remove', 'floorPlanImageDbrmv')->name('agent.project.floor_plan_imgdbrmv');
        //#========== project slider image end

        Route::get('/messages',   'messages')->name('agent.project_management.messages');
        Route::post('/message-delete',  'destroyMessage')->name('agent.project_management.message.delete');
      });
      // Project type routes 
      Route::prefix('project/type')->controller(TypeController::class)->group(function () {
        Route::get('/{id}', 'index')->name('agent.project_management.project_types');
        Route::post('/store', 'store')->name('agent.project_management.project_type.store')->middleware('packageLimitsCheck:projectType,store');
        Route::post('/update', 'update')->name('agent.project_management.project_type.update')->middleware('packageLimitsCheck:projectType,update');

        Route::post('/delete', 'delete')->name('agent.project_management.delete_type');

        Route::post('/bulk-delete', 'bulkDelete')->name('agent.project_management.bulk_delete_type');
        Route::post('/store', 'store')->name('agent.admin.project_management.project_type.store');
        Route::post('/update', 'update')->name('agent.admin.project_management.project_type.update');
      });
    });
    // Project Management Route End

    // property messages 
    Route::get('/messages', [PropertyController::class, 'messages'])->name('agent.property_message.index');
    Route::post('/message-delete', [PropertyController::class, 'destroyMessage'])->name('agent.property_message.delete');
  });
});
