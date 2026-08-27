<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Repositories\Contracts\SettingRepositoryInterface;
use App\Models\Setting;
class SettingController extends Controller
{
     /**
      * Create a new controller instance.
      *
      * @return void
      */
    public function __construct(SettingRepositoryInterface $settingRepository)
    { 
        /**
         * Setting Repository
         */
        $this->settingRepository = $settingRepository;
    }
    
      /**
       * Display a listing of the resource.
       * 
       * @return View
       * 
        */
      public function index(): View
    {
        $settings = $this->settingRepository->getSettings();

        return view('backend.admin.settings', [
            'title' => 'Settings',
            'settings' => $settings,
        ]);
    }


     /**
      * Update the specified resource in storage.
      *
      * @param  \Illuminate\Http\Request  $request
      * @return \Illuminate\Http\Response
      */
      public function updateNotification(Request $request)
    {
        $request->validate([
            'setting' => [
                'required',
                'in:email_notification,sms_notification'
            ],
            'value' => [
                'required',
                'boolean'
            ],
        ]);

        $this->settingRepository->updateNotification(
            $request->setting,
            $request->boolean('value')
        );

        return response()->json([
            'success' => true,
            'message' => 'Notification setting updated successfully.',
        ]);
    }
}
