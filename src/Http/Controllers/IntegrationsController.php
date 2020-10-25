<?php
namespace SpiritSystems\DayByDay\Core\Http\Controllers;

use App\Http\Controllers\IntegrationsController as ControllersIntegrationsController;
use App\Models\Integration;
use App\Services\Storage\Authentication\GoogleDriveAuthenticator;

class IntegrationsController extends ControllersIntegrationsController {



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $billing_integration = Integration::whereApiType('billing')->first();
        $filesystem_integration = Integration::whereApiType('file')->first();

        return view('integrations.index')
        ->with('billing_integration', $billing_integration)
        ->with('filesystem_integration', $filesystem_integration)
        ->with('google_drive_auth_url', null)
        ->with('dropbox_auth_url', null);
    }

}