<?php

namespace Arnyee\Opcache\Http\Controllers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Arnyee\Opcache\OpcacheFacade as OPcache;

class OpcacheController extends BaseController
{
    /**
     * @throws BindingResolutionException
     */
    public function clear(): JsonResponse
    {
        $result = OPcache::clear();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'OPcache cleared successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to clear OPcache.'], 500);
    }

    /**
     * @throws BindingResolutionException
     */
    public function config(): JsonResponse
    {
        $config = OPcache::getConfig();

        if ($config) {
            return response()->json(['success' => true, 'config' => $config]);
        }

        return response()->json(['success' => false, 'message' => 'Failed to retrieve OPcache configuration.'], 500);
    }

    /**
     * @throws BindingResolutionException
     */
    public function status(): JsonResponse
    {
        $status = OPcache::getStatus();

        if ($status) {
            return response()->json(['success' => true, 'status' => $status]);
        }

        return response()->json(['success' => false, 'message' => 'Failed to retrieve OPcache status.'], 500);
    }

    /**
     * @throws BindingResolutionException
     */
    public function compile(Request $request): JsonResponse
    {
        $force = $request->get('force', false) === 'true';
        $result = OPcache::compile($force);

        if ($result) {
            return response()->json(['result' => $result, 'success' => true, 'message' => 'OPcache compilation initiated successfully.']);
        }

        return response()->json(['result' => null, 'success' => false, 'message' => 'Failed to initiate OPcache compilation.'], 500);
    }
}
