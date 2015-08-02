<?php

class LogsAdminController extends \BaseController {

    /**
     * Display a listing of logs.
     *
     * @return Response
     */
    public function index()
    {
        return View::make('admin.logs')
            ->with('logs', LogDao::getLogs());
    }


}
